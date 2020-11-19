<?php

declare(strict_types=1);
/**
 * @author Joachim Bauch <mail@joachim-bauch.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Talk\Collaboration\Collaborators;

use OCA\Talk\Config;
use OCA\Talk\Exceptions\ParticipantNotFoundException;
use OCA\Talk\Exceptions\RoomNotFoundException;
use OCA\Talk\Manager;
use OCA\Talk\Room;
use OCP\Collaboration\AutoComplete\AutoCompleteEvent;
use OCP\Collaboration\AutoComplete\IManager;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IUser;
use OCP\IUserManager;

class Listener {

	/** @var Manager */
	protected $manager;
	/** @var IUserManager */
	protected $userManager;
	/** @var Config */
	protected $config;
	/** @var string[] */
	protected $allowedGroupIds = [];
	/** @var string */
	protected $roomToken;
	/** @var Room */
	protected $room;

	public function __construct(Manager $manager,
								IUserManager $userManager,
								Config $config) {
		$this->manager = $manager;
		$this->userManager = $userManager;
		$this->config = $config;
	}

	public static function register(IEventDispatcher $dispatcher): void {
		$dispatcher->addListener(IManager::class . '::filterResults', static function (AutoCompleteEvent $event) {
			/** @var self $listener */
			$listener = \OC::$server->query(self::class);

			if ($event->getItemType() !== 'call') {
				return;
			}

			$event->setResults($listener->filterUsersAndGroupsWithoutTalk($event->getResults()));

			if ($event->getItemId() !== 'new') {
				$event->setResults($listener->filterExistingParticipants($event->getItemId(), $event->getResults()));
			}
		});
	}

	protected function filterUsersAndGroupsWithoutTalk(array $results): array {
		$this->allowedGroupIds = $this->config->getAllowedTalkGroupIds();
		if (empty($this->allowedGroupIds)) {
			return $results;
		}

		if (!empty($results['groups'])) {
			$results['groups'] = array_filter($results['groups'], [$this, 'filterGroupResult']);
		}
		if (!empty($results['exact']['groups'])) {
			$results['exact']['groups'] = array_filter($results['exact']['groups'], [$this, 'filterGroupResult']);
		}

		if (!empty($results['users'])) {
			$results['users'] = array_filter($results['users'], [$this, 'filterUserResult']);
		}
		if (!empty($results['exact']['users'])) {
			$results['exact']['users'] = array_filter($results['exact']['users'], [$this, 'filterUserResult']);
		}

		return $results;
	}

	protected function filterUserResult(array $result): bool {
		$user = $this->userManager->get($result['value']['shareWith']);
		return $user instanceof IUser && !$this->config->isDisabledForUser($user);
	}

	protected function filterGroupResult(array $result): bool {
		return \in_array($result['value']['shareWith'], $this->allowedGroupIds, true);
	}

	protected function filterExistingParticipants(string $token, array $results): array {
		try {
			$this->room = $this->manager->getRoomByToken($token);
		} catch (RoomNotFoundException $e) {
			return $results;
		}

		if (!empty($results['users'])) {
			$results['users'] = array_filter($results['users'], [$this, 'filterParticipantResult']);
		}
		if (!empty($results['exact']['users'])) {
			$results['exact']['users'] = array_filter($results['exact']['users'], [$this, 'filterParticipantResult']);
		}

		return $results;
	}

	protected function filterParticipantResult(array $result): bool {
		$userId = $result['value']['shareWith'];

		try {
			$this->room->getParticipant($userId);
			return false;
		} catch (ParticipantNotFoundException $e) {
			return true;
		}
	}
}
