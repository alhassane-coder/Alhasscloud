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

namespace OCA\Talk\Controller;

use OCA\Talk\Exceptions\RoomNotFoundException;
use OCA\Talk\Files\Util;
use OCA\Talk\Manager;
use OCA\Talk\TalkSession;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCS\OCSException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\AppFramework\OCSController;
use OCP\Files\FileInfo;
use OCP\Files\NotFoundException;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;
use OCP\ISession;
use OCP\IUser;
use OCP\IUserSession;
use OCP\Share\Exceptions\ShareNotFound;
use OCP\Share\IManager as IShareManager;
use OCP\Share\IShare;

class FilesIntegrationController extends OCSController {

	/** @var Manager */
	private $manager;
	/** @var IShareManager */
	private $shareManager;
	/** @var ISession */
	private $session;
	/** @var IUserSession */
	private $userSession;
	/** @var TalkSession */
	private $talkSession;
	/** @var Util */
	private $util;
	/** @var IConfig */
	private $config;
	/** @var IL10N */
	private $l;

	public function __construct(
			string $appName,
			IRequest $request,
			Manager $manager,
			IShareManager $shareManager,
			ISession $session,
			IUserSession $userSession,
			TalkSession $talkSession,
			Util $util,
			IConfig $config,
			IL10N $l10n
	) {
		parent::__construct($appName, $request);
		$this->manager = $manager;
		$this->shareManager = $shareManager;
		$this->session = $session;
		$this->userSession = $userSession;
		$this->talkSession = $talkSession;
		$this->util = $util;
		$this->config = $config;
		$this->l = $l10n;
	}

	/**
	 * @NoAdminRequired
	 *
	 * Returns the token of the room associated to the given file id.
	 *
	 * This is the counterpart of self::getRoomByShareToken() for file ids
	 * instead of share tokens, although both return the same room token if the
	 * given file id and share token refer to the same file.
	 *
	 * If there is no room associated to the given file id a new room is
	 * created; the new room is a public room associated with a "file" object
	 * with the given file id. Unlike normal rooms in which the owner is the
	 * user that created the room these are special rooms without owner
	 * (although self joined users with direct access to the file become
	 * persistent participants automatically when they join until they
	 * explicitly leave or no longer have access to the file).
	 *
	 * In any case, to create or even get the token of the room, the file must
	 * be shared and the user must be the owner of a public share of the file
	 * (like a link share, for example) or have direct access to that file; an
	 * error is returned otherwise. A user has direct access to a file if she
	 * has access to it (or to an ancestor) through a user, group, circle or
	 * room share (but not through a link share, for example), or if she is the
	 * owner of such a file.
	 *
	 * @param string $fileId
	 * @return DataResponse the status code is "200 OK" if a room is returned,
	 *         or "404 Not found" if the given file id was invalid.
	 * @throws OCSNotFoundException
	 */
	public function getRoomByFileId(string $fileId): DataResponse {
		if ($this->config->getAppValue('spreed', 'conversations_files', '1') !== '1') {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}

		$currentUser = $this->userSession->getUser();
		if (!$currentUser instanceof IUser) {
			throw new OCSException($this->l->t('File is not shared, or shared but not with the user'), Http::STATUS_UNAUTHORIZED);
		}


		$share = $this->util->getAnyPublicShareOfFileOwnedByUserOrAnyDirectShareOfFileAccessibleByUser($fileId, $currentUser->getUID());
		$groupFolder = null;
		if (!$share) {
			$groupFolder = $this->util->getGroupFolderNode($fileId, $currentUser->getUID());
			if (!$groupFolder) {
				throw new OCSNotFoundException($this->l->t('File is not shared, or shared but not with the user'));
			}
		}

		try {
			$room = $this->manager->getRoomByObject('file', $fileId);
		} catch (RoomNotFoundException $e) {
			if ($share) {
				try {
					$name = $this->getFileName($share, $fileId);
				} catch (NotFoundException $e) {
					throw new OCSNotFoundException($this->l->t('File is not shared, or shared but not with the user'));
				}
			} else {
				$name = $groupFolder->getName();
			}
			$room = $this->manager->createPublicRoom($name, 'file', $fileId);
		}

		return new DataResponse([
			'token' => $room->getToken()
		]);
	}

	/**
	 * @PublicPage
	 * @UseSession
	 *
	 * Returns the token of the room associated to the file id of the given
	 * share token.
	 *
	 * This is the counterpart of self::getRoomByFileId() for share tokens
	 * instead of file ids, although both return the same room token if the
	 * given file id and share token refer to the same file.
	 *
	 * If there is no room associated to the file id of the given share token a
	 * new room is created; the new room is a public room associated with a
	 * "file" object with the file id of the given share token. Unlike normal
	 * rooms in which the owner is the user that created the room these are
	 * special rooms without owner (although self joined users with direct
	 * access to the file become persistent participants automatically when they
	 * join until they explicitly leave or no longer have access to the file).
	 *
	 * In any case, to create or even get the token of the room, the file must
	 * be publicly shared (like a link share, for example); an error is returned
	 * otherwise.
	 *
	 * Besides the token of the room this also returns the current user ID and
	 * display name, if any; this is needed by the Talk sidebar to know the
	 * actual current user, as the public share page uses the incognito mode and
	 * thus logged in users as seen as guests.
	 *
	 * @param string $shareToken
	 * @return DataResponse the status code is "200 OK" if a room is returned,
	 *         or "404 Not found" if the given share token was invalid.
	 */
	public function getRoomByShareToken(string $shareToken): DataResponse {
		if ($this->config->getAppValue('spreed', 'conversations_files', '1') !== '1' ||
			$this->config->getAppValue('spreed', 'conversations_files_public_shares', '1') !== '1') {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}

		try {
			$share = $this->shareManager->getShareByToken($shareToken);
			if ($share->getPassword() !== null) {
				$shareId = $this->session->get('public_link_authenticated');
				if ($share->getId() !== $shareId) {
					throw new ShareNotFound();
				}
			}
		} catch (ShareNotFound $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}

		try {
			if ($share->getNodeType() !== FileInfo::TYPE_FILE) {
				return new DataResponse([], Http::STATUS_NOT_FOUND);
			}

			$fileId = (string)$share->getNodeId();

			try {
				$room = $this->manager->getRoomByObject('file', $fileId);
			} catch (RoomNotFoundException $e) {
				$name = $share->getNode()->getName();
				$room = $this->manager->createPublicRoom($name, 'file', $fileId);
			}
		} catch (NotFoundException $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}

		$this->talkSession->setFileShareTokenForRoom($room->getToken(), $shareToken);

		$currentUser = $this->userSession->getUser();
		$currentUserId = $currentUser instanceof IUser ? $currentUser->getUID() : '';
		$currentUserDisplayName = $currentUser instanceof IUser ? $currentUser->getDisplayName() : '';

		return new DataResponse([
			'token' => $room->getToken(),
			'userId' => $currentUserId,
			'userDisplayName' => $currentUserDisplayName,
		]);
	}

	/**
	 * Returns the name of the file in the share.
	 *
	 * If the given share itself is a file its name is returned; otherwise the
	 * file is looked for in the given shared folder and its name is returned.
	 *
	 * @param IShare $share
	 * @param string $fileId
	 * @return string
	 * @throws NotFoundException
	 */
	private function getFileName(IShare $share, string $fileId): string {
		$node = $share->getNode();

		if ($node->getType() === FileInfo::TYPE_FILE) {
			return $node->getName();
		}

		$fileById = $node->getById($fileId);

		if (empty($fileById)) {
			throw new NotFoundException('File not found in share');
		}

		$file = array_shift($fileById);
		return $file->getName();
	}
}
