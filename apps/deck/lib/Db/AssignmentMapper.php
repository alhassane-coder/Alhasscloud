<?php
/**
 * @copyright Copyright (c) 2017 Julius Härtl <jus@bitgrid.net>
 *
 * @author Julius Härtl <jus@bitgrid.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=1);

namespace OCA\Deck\Db;

use OCA\Deck\NotFoundException;
use OCA\Deck\Service\CirclesService;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\IUserManager;
use PDO;

class AssignmentMapper extends QBMapper implements IPermissionMapper {

	/** @var CardMapper */
	private $cardMapper;
	/** @var IUserManager */
	private $userManager;
	/** @var IGroupManager */
	private $groupManager;
	/** @var CirclesService */
	private $circleService;

	public function __construct(IDBConnection $db, CardMapper $cardMapper, IUserManager $userManager, IGroupManager $groupManager, CirclesService $circleService) {
		parent::__construct($db, 'deck_assigned_users', Assignment::class);

		$this->cardMapper = $cardMapper;
		$this->userManager = $userManager;
		$this->groupManager = $groupManager;
		$this->circleService = $circleService;
	}

	/**
	 * @return Assignment[]
	 */
	public function findAll(int $cardId): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('deck_assigned_users')
			->where($qb->expr()->eq('card_id', $qb->createNamedParameter($cardId, PDO::PARAM_INT)));
		$users = $this->findEntities($qb);
		foreach ($users as $user) {
			$this->mapParticipant($user);
		}
		return $users;
	}

	public function findByParticipant(string $participant, $type = Assignment::TYPE_USER): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('deck_assigned_users')
			->where($qb->expr()->eq('participant', $qb->createNamedParameter($participant, PDO::PARAM_STR)))
			->andWhere($qb->expr()->eq('type', $qb->createNamedParameter($type, PDO::PARAM_INT)));
		return $this->findEntities($qb);
	}


	public function isOwner($userId, $cardId): bool {
		return $this->cardMapper->isOwner($userId, $cardId);
	}

	public function findBoardId($cardId): ?int {
		return $this->cardMapper->findBoardId($cardId);
	}

	/**
	 * Check if user exists before assigning it to a card
	 *
	 * @param Entity $entity
	 * @return null|Assignment
	 * @throws NotFoundException
	 */
	public function insert(Entity $entity): Entity {
		$origin = $this->getOrigin($entity);
		if ($origin === null) {
			throw new NotFoundException('No origin found for assignment');
		}

		/** @var Assignment $assignment */
		$assignment = parent::insert($entity);
		$this->mapParticipant($assignment);
		return $assignment;
	}

	public function mapParticipant(Assignment $assignment): void {
		$self = $this;
		$assignment->resolveRelation('participant', function () use (&$self, &$assignment) {
			return $self->getOrigin($assignment);
		});
	}

	public function isUserAssigned($cardId, $userId): bool {
		$assignments = $this->findAll($cardId);
		foreach ($assignments as $assignment) {
			$origin = $this->getOrigin($assignment);
			if ($origin instanceof User && $assignment->getParticipant() === $userId) {
				return true;
			}
			if ($origin instanceof Group && $this->groupManager->isInGroup($userId, $assignment->getParticipant())) {
				return true;
			}
			if ($origin instanceof Circle && $this->circleService->isUserInCircle($assignment->getParticipant(), $userId)) {
				return true;
			}
		}

		return false;
	}

	private function getOrigin(Assignment $assignment) {
		if ($assignment->getType() === Assignment::TYPE_USER) {
			$origin = $this->userManager->get($assignment->getParticipant());
			return $origin ? new User($origin) : null;
		}
		if ($assignment->getType() === Assignment::TYPE_GROUP) {
			$origin = $this->groupManager->get($assignment->getParticipant());
			return $origin ? new Group($origin) : null;
		}
		if ($assignment->getType() === Assignment::TYPE_CIRCLE) {
			$origin = $this->circleService->getCircle($assignment->getParticipant());
			return $origin ? new Circle($origin) : null;
		}
		return null;
	}
}
