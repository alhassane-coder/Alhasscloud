<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2019 Joas Schilling <coding@schilljs.com>
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

use OCA\Talk\Chat\ChatManager;
use OCA\Talk\Config;
use OCA\Talk\Events\AddParticipantsEvent;
use OCA\Talk\Events\ChatEvent;
use OCA\Talk\Events\ChatParticipantEvent;
use OCA\Talk\Events\ModifyParticipantEvent;
use OCA\Talk\Events\ParticipantEvent;
use OCA\Talk\Events\RemoveParticipantEvent;
use OCA\Talk\Events\RemoveUserEvent;
use OCA\Talk\Events\RoomEvent;
use OCA\Talk\GuestManager;
use OCA\Talk\Participant;
use OCA\Talk\Room;
use OCP\EventDispatcher\IEventDispatcher;

class Listener {
	public static function register(IEventDispatcher $dispatcher): void {
		self::registerInternalSignaling($dispatcher);
		self::registerExternalSignaling($dispatcher);
	}

	protected static function isUsingInternalSignaling(): bool {
		/** @var Config $config */
		$config = \OC::$server->query(Config::class);
		return $config->getSignalingMode() === Config::SIGNALING_INTERNAL;
	}

	protected static function registerInternalSignaling(IEventDispatcher $dispatcher): void {
		$listener = static function (RoomEvent $event) {
			if (!self::isUsingInternalSignaling()) {
				return;
			}

			/** @var Messages $messages */
			$messages = \OC::$server->query(Messages::class);
			$messages->addMessageForAllParticipants($event->getRoom(), 'refresh-participant-list');
		};
		$dispatcher->addListener(Room::EVENT_AFTER_ROOM_CONNECT, $listener);
		$dispatcher->addListener(Room::EVENT_AFTER_GUEST_CONNECT, $listener);
		$dispatcher->addListener(Room::EVENT_AFTER_SESSION_JOIN_CALL, $listener);
		$dispatcher->addListener(Room::EVENT_AFTER_SESSION_LEAVE_CALL, $listener);
		$dispatcher->addListener(GuestManager::EVENT_AFTER_NAME_UPDATE, $listener);

		$listener = static function (ParticipantEvent $event) {
			if (!self::isUsingInternalSignaling()) {
				return;
			}

			$room = $event->getRoom();

			/** @var Messages $messages */
			$messages = \OC::$server->query(Messages::class);
			$messages->addMessageForAllParticipants($room, 'refresh-participant-list');

			// When "addMessageForAllParticipants" is called the participant is
			// no longer in the room, so the message needs to be explicitly
			// added for the participant.
			/** @var Participant $participant */
			$participant = $event->getParticipant();
			if ($participant->getSessionId() !== '0') {
				$messages->addMessage($participant->getSessionId(), $participant->getSessionId(), 'refresh-participant-list');
			}
		};
		$dispatcher->addListener(Room::EVENT_AFTER_USER_REMOVE, $listener);
		$dispatcher->addListener(Room::EVENT_AFTER_PARTICIPANT_REMOVE, $listener);
		$dispatcher->addListener(Room::EVENT_AFTER_ROOM_DISCONNECT, $listener);

		$listener = static function (RoomEvent $event) {
			$room = $event->getRoom();
			if (!self::isUsingInternalSignaling()) {
				return;
			}

			/** @var Messages $messages */
			$messages = \OC::$server->query(Messages::class);
			$participants = $room->getParticipantsLegacy();
			foreach ($participants['users'] as $participant) {
				$messages->addMessage($participant['sessionId'], $participant['sessionId'], 'refresh-participant-list');
			}
			foreach ($participants['guests'] as $participant) {
				$messages->addMessage($participant['sessionId'], $participant['sessionId'], 'refresh-participant-list');
			}
		};
		$dispatcher->addListener(Room::EVENT_BEFORE_ROOM_DELETE, $listener);
	}

	protected static function registerExternalSignaling(IEventDispatcher $dispatcher): void {
		$dispatcher->addListener(Room::EVENT_AFTER_USERS_ADD, static function (AddParticipantsEvent $event) {
			if (self::isUsingInternalSignaling()) {
				return;
			}

			/** @var BackendNotifier $notifier */
			$notifier = \OC::$server->query(BackendNotifier::class);

			$notifier->roomInvited($event->getRoom(), $event->getParticipants());
		});
		$listener = static function (RoomEvent $event) {
			if (self::isUsingInternalSignaling()) {
				return;
			}

			/** @var BackendNotifier $notifier */
			$notifier = \OC::$server->query(BackendNotifier::class);

			$notifier->roomModified($event->getRoom());
		};
		$dispatcher->addListener(Room::EVENT_AFTER_NAME_SET, $listener);
		$dispatcher->addListener(Room::EVENT_AFTER_PASSWORD_SET, $listener);
		$dispatcher->addListener(Room::EVENT_AFTER_TYPE_SET, $listener);
		$dispatcher->addListener(Room::EVENT_AFTER_READONLY_SET, $listener);
		$dispatcher->addListener(Room::EVENT_AFTER_LOBBY_STATE_SET, $listener);
		// TODO remove handler with "roomModified" in favour of handler with
		// "participantsModified" once the clients no longer expect a
		// "roomModified" message for participant type changes.
		$dispatcher->addListener(Room::EVENT_AFTER_PARTICIPANT_TYPE_SET, $listener);
		$dispatcher->addListener(Room::EVENT_AFTER_PARTICIPANT_TYPE_SET, static function (ModifyParticipantEvent $event) {
			if (self::isUsingInternalSignaling()) {
				return;
			}

			/** @var BackendNotifier $notifier */
			$notifier = \OC::$server->query(BackendNotifier::class);

			$sessionIds = [];
			// If the participant is not active in the room the "participants"
			// request will be sent anyway, although with an empty "changed"
			// property.
			if ($event->getParticipant()->getSessionId()) {
				$sessionIds[] = $event->getParticipant()->getSessionId();
			}
			$notifier->participantsModified($event->getRoom(), $sessionIds);
		});
		$dispatcher->addListener(Room::EVENT_BEFORE_ROOM_DELETE, static function (RoomEvent $event) {
			if (self::isUsingInternalSignaling()) {
				return;
			}

			/** @var BackendNotifier $notifier */
			$notifier = \OC::$server->query(BackendNotifier::class);

			$room = $event->getRoom();
			$participants = $room->getParticipantsLegacy();
			$notifier->roomDeleted($room, $participants);
		});
		$dispatcher->addListener(Room::EVENT_AFTER_USER_REMOVE, static function (RemoveUserEvent $event) {
			if (self::isUsingInternalSignaling()) {
				return;
			}

			/** @var BackendNotifier $notifier */
			$notifier = \OC::$server->query(BackendNotifier::class);

			$notifier->roomsDisinvited($event->getRoom(), [$event->getUser()->getUID()]);
		});
		$dispatcher->addListener(Room::EVENT_AFTER_PARTICIPANT_REMOVE, static function (RemoveParticipantEvent $event) {
			if (self::isUsingInternalSignaling()) {
				return;
			}

			/** @var BackendNotifier $notifier */
			$notifier = \OC::$server->query(BackendNotifier::class);

			$notifier->roomSessionsRemoved($event->getRoom(), [$event->getParticipant()->getSessionId()]);
		});

		$listener = static function (ModifyParticipantEvent $event) {
			if (self::isUsingInternalSignaling()) {
				return;
			}

			/** @var BackendNotifier $notifier */
			$notifier = \OC::$server->query(BackendNotifier::class);

			$notifier->roomInCallChanged(
				$event->getRoom(),
				$event->getNewValue(),
				[$event->getParticipant()->getSessionId()]
			);
		};
		$dispatcher->addListener(Room::EVENT_AFTER_SESSION_JOIN_CALL, $listener);
		$dispatcher->addListener(Room::EVENT_AFTER_SESSION_LEAVE_CALL, $listener);

		$dispatcher->addListener(Room::EVENT_AFTER_GUESTS_CLEAN, static function (RoomEvent $event) {
			if (self::isUsingInternalSignaling()) {
				return;
			}

			/** @var BackendNotifier $notifier */
			$notifier = \OC::$server->query(BackendNotifier::class);

			// TODO: The list of removed session ids should be passed through the event
			// so the signaling server can optimize forwarding the message.
			$sessionIds = [];
			$notifier->participantsModified($event->getRoom(), $sessionIds);
		});
		$dispatcher->addListener(GuestManager::EVENT_AFTER_NAME_UPDATE, static function (ModifyParticipantEvent $event) {
			if (self::isUsingInternalSignaling()) {
				return;
			}

			/** @var BackendNotifier $notifier */
			$notifier = \OC::$server->query(BackendNotifier::class);

			$notifier->participantsModified($event->getRoom(), [$event->getParticipant()->getSessionId()]);
		});
		$dispatcher->addListener(ChatManager::EVENT_AFTER_MESSAGE_SEND , static function (ChatParticipantEvent $event) {
			if (self::isUsingInternalSignaling()) {
				return;
			}

			/** @var BackendNotifier $notifier */
			$notifier = \OC::$server->query(BackendNotifier::class);

			$room = $event->getRoom();
			$message = [
				'type' => 'chat',
				'chat' => [
					'refresh' => true,
				],
			];
			$notifier->sendRoomMessage($room, $message);
		});
		$dispatcher->addListener(ChatManager::EVENT_AFTER_SYSTEM_MESSAGE_SEND, static function (ChatEvent $event) {
			if (self::isUsingInternalSignaling()) {
				return;
			}

			/** @var BackendNotifier $notifier */
			$notifier = \OC::$server->query(BackendNotifier::class);

			$room = $event->getRoom();
			$message = [
				'type' => 'chat',
				'chat' => [
					'refresh' => true,
				],
			];
			$notifier->sendRoomMessage($room, $message);
		});
	}
}
