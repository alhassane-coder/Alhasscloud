<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2017 Joachim Bauch <bauch@struktur.de>
 *
 * @author Joachim Bauch <bauch@struktur.de>
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

namespace OCA\Talk\Signaling;

use OCA\Talk\Config;
use OCA\Talk\Participant;
use OCA\Talk\Room;
use OCP\Http\Client\IClientService;
use OCP\ILogger;
use OCP\IUrlGenerator;
use OCP\Security\ISecureRandom;

class BackendNotifier {
	/** @var Config */
	private $config;
	/** @var ILogger */
	private $logger;
	/** @var IClientService */
	private $clientService;
	/** @var ISecureRandom */
	private $secureRandom;
	/** @var Manager */
	private $signalingManager;
	/** @var IUrlGenerator */
	private $urlGenerator;

	public function __construct(Config $config,
								ILogger $logger,
								IClientService $clientService,
								ISecureRandom $secureRandom,
								Manager $signalingManager,
								IURLGenerator $urlGenerator) {
		$this->config = $config;
		$this->logger = $logger;
		$this->clientService = $clientService;
		$this->secureRandom = $secureRandom;
		$this->signalingManager = $signalingManager;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * Perform actual network request to the signaling backend.
	 * This can be overridden in tests.
	 *
	 * @param string $url
	 * @param array $params
	 * @throws \Exception
	 */
	protected function doRequest(string $url, array $params): void {
		if (defined('PHPUNIT_RUN')) {
			// Don't perform network requests when running tests.
			return;
		}

		$client = $this->clientService->newClient();
		try {
			$client->post($url, $params);
		} catch (\Exception $e) {
			$this->logger->logException($e, [
				'app' => 'spreed',
				'message' => 'Failed to send message to signaling server',
			]);
		}
	}

	/**
	 * Perform a request to the signaling backend.
	 *
	 * @param Room $room
	 * @param array $data
	 * @throws \Exception
	 */
	private function backendRequest(Room $room, array $data): void {
		if ($this->config->getSignalingMode() === Config::SIGNALING_INTERNAL) {
			return;
		}

		// FIXME some need to go to all HPBs, but that doesn't scale, so bad luck for now :(
		$signaling = $this->signalingManager->getSignalingServerForConversation($room);
		$signaling['server'] = rtrim($signaling['server'], '/');

		$url = '/api/v1/room/' . $room->getToken();
		$url = $signaling['server'] . $url;
		if (strpos($url, 'wss://') === 0) {
			$url = 'https://' . substr($url, 6);
		} elseif (strpos($url, 'ws://') === 0) {
			$url = 'http://' . substr($url, 5);
		}
		$body = json_encode($data);
		$headers = [
			'Content-Type' => 'application/json',
		];

		$random = $this->secureRandom->generate(64);
		$hash = hash_hmac('sha256', $random . $body, $this->config->getSignalingSecret());
		$headers['Spreed-Signaling-Random'] = $random;
		$headers['Spreed-Signaling-Checksum'] = $hash;
		$headers['Spreed-Signaling-Backend'] = $this->urlGenerator->getBaseUrl();

		$params = [
			'headers' => $headers,
			'body' => $body,
			'nextcloud' => [
				'allow_local_address' => true,
			],
		];
		if (empty($signaling['verify'])) {
			$params['verify'] = false;
		}
		$this->doRequest($url, $params);
	}

	/**
	 * The given users are now invited to a room.
	 *
	 * @param Room $room
	 * @param array[] $users
	 * @throws \Exception
	 */
	public function roomInvited(Room $room, array $users): void {
		$this->logger->info('Now invited to ' . $room->getToken() . ': ' . print_r($users, true), ['app' => 'spreed']);
		$userIds = [];
		foreach ($users as $user) {
			$userIds[] = $user['userId'];
		}
		$this->backendRequest($room, [
			'type' => 'invite',
			'invite' => [
				'userids' => $userIds,
				// TODO(fancycode): We should try to get rid of 'alluserids' and
				// find a better way to notify existing users to update the room.
				'alluserids' => $room->getParticipantUserIds(),
				'properties' => $room->getPropertiesForSignaling(''),
			],
		]);
	}

	/**
	 * The given users are no longer invited to a room.
	 *
	 * @param Room $room
	 * @param string[] $userIds
	 * @throws \Exception
	 */
	public function roomsDisinvited(Room $room, array $userIds): void {
		$this->logger->info('No longer invited to ' . $room->getToken() . ': ' . print_r($userIds, true), ['app' => 'spreed']);
		$this->backendRequest($room, [
			'type' => 'disinvite',
			'disinvite' => [
				'userids' => $userIds,
				// TODO(fancycode): We should try to get rid of 'alluserids' and
				// find a better way to notify existing users to update the room.
				'alluserids' => $room->getParticipantUserIds(),
				'properties' => $room->getPropertiesForSignaling(''),
			],
		]);
	}

	/**
	 * The given sessions have been removed from a room.
	 *
	 * @param Room $room
	 * @param string[] $sessionIds
	 * @throws \Exception
	 */
	public function roomSessionsRemoved(Room $room, array $sessionIds): void {
		$this->logger->info('Removed from ' . $room->getToken() . ': ' . print_r($sessionIds, true), ['app' => 'spreed']);
		$this->backendRequest($room, [
			'type' => 'disinvite',
			'disinvite' => [
				'sessionids' => $sessionIds,
				// TODO(fancycode): We should try to get rid of 'alluserids' and
				// find a better way to notify existing users to update the room.
				'alluserids' => $room->getParticipantUserIds(),
				'properties' => $room->getPropertiesForSignaling(''),
			],
		]);
	}

	/**
	 * The given room has been modified.
	 *
	 * @param Room $room
	 * @throws \Exception
	 */
	public function roomModified(Room $room): void {
		$this->logger->info('Room modified: ' . $room->getToken(), ['app' => 'spreed']);
		$this->backendRequest($room, [
			'type' => 'update',
			'update' => [
				'userids' => $room->getParticipantUserIds(),
				'properties' => $room->getPropertiesForSignaling(''),
			],
		]);
	}

	/**
	 * The given room has been deleted.
	 *
	 * @param Room $room
	 * @param array $participants
	 * @throws \Exception
	 */
	public function roomDeleted(Room $room, array $participants): void {
		$this->logger->info('Room deleted: ' . $room->getToken(), ['app' => 'spreed']);
		$userIds = array_keys($participants['users']);
		$this->backendRequest($room, [
			'type' => 'delete',
			'delete' => [
				'userids' => $userIds,
			],
		]);
	}

	/**
	 * The participant list of the given room has been modified.
	 *
	 * @param Room $room
	 * @param string[] $sessionIds
	 * @throws \Exception
	 */
	public function participantsModified(Room $room, array $sessionIds): void {
		$this->logger->info('Room participants modified: ' . $room->getToken() . ' ' . print_r($sessionIds, true), ['app' => 'spreed']);
		$changed = [];
		$users = [];
		$participants = $room->getParticipantsLegacy();
		foreach ($participants['users'] as $userId => $participant) {
			$participant['userId'] = $userId;
			$users[] = $participant;
			if (\in_array($participant['sessionId'], $sessionIds, true)) {
				$participant['permissions'] = ['publish-media', 'publish-screen'];
				if ($participant['participantType'] === Participant::OWNER ||
						$participant['participantType'] === Participant::MODERATOR) {
					$participant['permissions'][] = 'control';
				}
				$changed[] = $participant;
			}
		}
		foreach ($participants['guests'] as $participant) {
			if (!isset($participant['participantType'])) {
				$participant['participantType'] = Participant::GUEST;
			}
			$users[] = $participant;
			if (\in_array($participant['sessionId'], $sessionIds, true)) {
				$participant['permissions'] = ['publish-media', 'publish-screen'];
				$changed[] = $participant;
			}
		}
		$this->backendRequest($room, [
			'type' => 'participants',
			'participants' => [
				'changed' => $changed,
				'users' => $users
			],
		]);
	}

	/**
	 * The "in-call" status of the given session ids has changed..
	 *
	 * @param Room $room
	 * @param int $flags
	 * @param string[] $sessionIds
	 * @throws \Exception
	 */
	public function roomInCallChanged(Room $room, int $flags, array $sessionIds): void {
		$this->logger->info('Room in-call status changed: ' . $room->getToken() . ' ' . $flags . ' ' . print_r($sessionIds, true), ['app' => 'spreed']);
		$changed = [];
		$users = [];
		$participants = $room->getParticipantsLegacy();
		foreach ($participants['users'] as $userId => $participant) {
			$participant['userId'] = $userId;
			if ($participant['inCall'] !== Participant::FLAG_DISCONNECTED) {
				$users[] = $participant;
			}
			if (\in_array($participant['sessionId'], $sessionIds, true)) {
				$changed[] = $participant;
			}
		}
		foreach ($participants['guests'] as $participant) {
			if (!isset($participant['participantType'])) {
				$participant['participantType'] = Participant::GUEST;
			}
			if ($participant['inCall'] !== Participant::FLAG_DISCONNECTED) {
				$users[] = $participant;
			}
			if (\in_array($participant['sessionId'], $sessionIds, true)) {
				$changed[] = $participant;
			}
		}

		$this->backendRequest($room, [
			'type' => 'incall',
			'incall' => [
				'incall' => $flags,
				'changed' => $changed,
				'users' => $users
			],
		]);
	}

	/**
	 * Send a message to all sessions currently joined in a room. The message
	 * will be received by "processRoomMessageEvent" in "signaling.js".
	 *
	 * @param Room $room
	 * @param array $message
	 * @throws \Exception
	 */
	public function sendRoomMessage(Room $room, array $message): void {
		$this->backendRequest($room, [
			'type' => 'message',
			'message' => [
				'data' => $message,
			],
		]);
	}
}
