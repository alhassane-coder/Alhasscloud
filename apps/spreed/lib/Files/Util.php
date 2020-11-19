<?php

declare(strict_types=1);
/**
 *
 * @copyright Copyright (c) 2018, Daniel Calviño Sánchez (danxuliu@gmail.com)
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

namespace OCA\Talk\Files;

use OCA\GroupFolders\Mount\GroupFolderStorage;
use OCP\Files\FileInfo;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\ISession;
use OCP\Share\Exceptions\ShareNotFound;
use OCP\Share\IManager as IShareManager;
use OCP\Share\IShare;

class Util {

	/** @var IRootFolder */
	private $rootFolder;
	/** @var ISession */
	private $session;
	/** @var IShareManager */
	private $shareManager;
	/** @var array[] */
	private $accessLists = [];

	public function __construct(IRootFolder $rootFolder,
			ISession $session,
			IShareManager $shareManager) {
		$this->rootFolder = $rootFolder;
		$this->session = $session;
		$this->shareManager = $shareManager;
	}

	public function getUsersWithAccessFile(string $fileId): array {
		if (!isset($this->accessLists[$fileId])) {
			$nodes = $this->rootFolder->getById($fileId);

			if (empty($nodes)) {
				return [];
			}

			$node = array_shift($nodes);
			$accessList = $this->shareManager->getAccessList($node);

			$this->accessLists[$fileId] = $accessList['users'];
		}

		return $this->accessLists[$fileId];
	}

	public function canUserAccessFile(string $fileId, string $userId): bool {
		return \in_array($userId, $this->getUsersWithAccessFile($fileId), true);
	}

	public function canGuestAccessFile(string $shareToken): bool {
		try {
			$share = $this->shareManager->getShareByToken($shareToken);
			if ($share->getPassword() !== null) {
				$shareId = $this->session->get('public_link_authenticated');
				if ($share->getId() !== $shareId) {
					throw new ShareNotFound();
				}
			}
			return true;
		} catch (ShareNotFound $e) {
			return false;
		}
	}

	/**
	 * Returns any share of the file that is public and owned by the user, or
	 * that the user has direct access to.
	 *
	 * A public share is one accessible by any user, including guests, like a
	 * share by link. Note that only a share of the file itself is taken into
	 * account; if an ancestor folder is shared publicly that share will not be
	 * returned.
	 *
	 * A user has direct access to a share and, thus, to a file, if she received
	 * the file through a user, group, circle or room share (but not through a
	 * public link, for example), or if she is the owner of such a share.
	 * Note that this includes too files received as a descendant of a folder
	 * that meets the above conditions.
	 *
	 * Only files are taken into account; folders are ignored.
	 *
	 * @param string $fileId
	 * @param string $userId
	 * @return IShare|null
	 */
	public function getAnyPublicShareOfFileOwnedByUserOrAnyDirectShareOfFileAccessibleByUser(string $fileId, string $userId): ?IShare {
		$userFolder = $this->rootFolder->getUserFolder($userId);
		$nodes = $userFolder->getById($fileId);
		if (empty($nodes)) {
			return null;
		}

		$nodes = array_filter($nodes, function ($node) {
			return $node->getType() === FileInfo::TYPE_FILE;
		});

		if (!empty($nodes)) {
			$share = $this->getAnyPublicShareOfNodeOwnedByUser($nodes[0], $userId);
			if ($share) {
				return $share;
			}
		}

		while (!empty($nodes)) {
			$node = array_pop($nodes);

			$share = $this->getAnyDirectShareOfNodeAccessibleByUser($node, $userId);
			if ($share) {
				return $share;
			}

			try {
				$nodes[] = $node->getParent();
			} catch (NotFoundException $e) {
			}
		}

		return null;
	}

	/**
	 * Returns any public share of the node (like a link share) created by the
	 * user.
	 *
	 * @param Node $node
	 * @param string $userId
	 * @return IShare|null
	 */
	private function getAnyPublicShareOfNodeOwnedByUser(Node $node, string $userId): ?IShare {
		$reshares = false;
		$limit = 1;

		$shares = $this->shareManager->getSharesBy($userId, IShare::TYPE_LINK, $node, $reshares, $limit);
		if (!empty($shares)) {
			return $shares[0];
		}

		$shares = $this->shareManager->getSharesBy($userId, IShare::TYPE_EMAIL, $node, $reshares, $limit);
		if (!empty($shares)) {
			return $shares[0];
		}

		return null;
	}

	/**
	 * Returns any share of the node that the user has direct access to.
	 *
	 * @param Node $node
	 * @param string $userId
	 * @return IShare|null
	 */
	private function getAnyDirectShareOfNodeAccessibleByUser(Node $node, string $userId): ?IShare {
		$reshares = false;
		$limit = 1;

		$shares = $this->shareManager->getSharesBy($userId, IShare::TYPE_USER, $node, $reshares, $limit);
		if (!empty($shares)) {
			return $shares[0];
		}

		$shares = $this->shareManager->getSharesBy($userId, IShare::TYPE_GROUP, $node, $reshares, $limit);
		if (!empty($shares)) {
			return $shares[0];
		}

		$shares = $this->shareManager->getSharesBy($userId, IShare::TYPE_CIRCLE, $node, $reshares, $limit);
		if (!empty($shares)) {
			return $shares[0];
		}

		$shares = $this->shareManager->getSharesBy($userId, IShare::TYPE_ROOM, $node, $reshares, $limit);
		if (!empty($shares)) {
			return $shares[0];
		}

		// If the node is not shared then there is no need for further checks.
		// Note that "isShared()" returns false for owned shares, so the check
		// can not be moved above.
		if (!$node->isShared()) {
			return null;
		}

		$shares = $this->shareManager->getSharedWith($userId, IShare::TYPE_USER, $node, $limit);
		if (!empty($shares)) {
			return $shares[0];
		}

		$shares = $this->shareManager->getSharedWith($userId, IShare::TYPE_GROUP, $node, $limit);
		if (!empty($shares)) {
			return $shares[0];
		}

		$shares = $this->shareManager->getSharedWith($userId, IShare::TYPE_CIRCLE, $node, $limit);
		if (!empty($shares)) {
			return $shares[0];
		}

		$shares = $this->shareManager->getSharedWith($userId, IShare::TYPE_ROOM, $node, $limit);
		if (!empty($shares)) {
			return $shares[0];
		}

		return null;
	}

	/**
	 * ...
	 *
	 * @param string $fileId
	 * @param string $userId
	 * @return Node|null
	 */
	public function getGroupFolderNode(string $fileId, string $userId): ?Node {
		$userFolder = $this->rootFolder->getUserFolder($userId);
		$nodes = $userFolder->getById($fileId);
		if (empty($nodes)) {
			return null;
		}

		$nodes = array_filter($nodes, function (Node $node) {
			return $node->getType() === FileInfo::TYPE_FILE;
		});
		if (empty($nodes)) {
			return null;
		}

		/** @var Node $node */
		$node = array_shift($nodes);
		try {
			$storage = $node->getStorage();
			if ($storage->instanceOfStorage(GroupFolderStorage::class)) {
				return $node;
			}
		} catch (NotFoundException $e) {
		}

		return null;
	}
}
