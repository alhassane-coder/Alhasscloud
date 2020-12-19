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

namespace OCA\Talk;

use OCA\Talk\Exceptions\ParticipantNotFoundException;
use OCA\Talk\Exceptions\RoomNotFoundException;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IUser;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\Util;

class Listener {

	/** @var Manager */
	protected $manager;
	/** @var IUserManager */
	protected $userManager;
	/** @var IUserSession */
	protected $userSession;
	/** @var TalkSession */
	protected $talkSession;
	/** @var Config */
	protected $config;

	public function __construct(Manager $manager,
								IUserManager $userManager,
								IUserSession $userSession,
								TalkSession $talkSession,
								Config $config) {
		$this->manager = $manager;
		$this->userManager = $userManager;
		$this->userSession = $userSession;
		$this->talkSession = $talkSession;
		$this->config = $config;
	}

	public static function register(IEventDispatcher $dispatcher): void {
		\OC::$server->getUserManager()->listen('\OC\User', 'postDelete', static function ($user) {
			/** @var self $listener */
			$listener = \OC::$server->query(self::class);
			$listener->deleteUser($user);
		});

		Util::connectHook('OC_User', 'logout', self::class, 'logoutUserStatic');
	}

	/**
	 * @param IUser $user
	 */
	public function deleteUser(IUser $user): void {
		$rooms = $this->manager->getRoomsForParticipant($user->getUID());

		foreach ($rooms as $room) {
			if ($room->getNumberOfParticipants() === 1) {
				$room->deleteRoom();
			} else {
				$room->removeUser($user, Room::PARTICIPANT_REMOVED);
			}
		}
	}

	public static function logoutUserStatic(): void {
		/** @var self $listener */
		$listener = \OC::$server->query(self::class);
		$listener->logoutUser();
	}

	public function logoutUser(): void {
		/** @var IUser $user */
		$user = $this->userSession->getUser();

		/** @var string[] $sessionIds */
		$sessionIds = $this->talkSession->getAllActiveSessions();
		foreach ($sessionIds as $sessionId) {
			try {
				$room = $this->manager->getRoomForSession($user->getUID(), $sessionId);
				$participant = $room->getParticipant($user->getUID());
				if ($participant->getInCallFlags() !== Participant::FLAG_DISCONNECTED) {
					$room->changeInCall($participant, Participant::FLAG_DISCONNECTED);
				}
				$room->leaveRoom($user->getUID(), $sessionId);
			} catch (RoomNotFoundException $e) {
			} catch (ParticipantNotFoundException $e) {
			}
		}
	}
}
