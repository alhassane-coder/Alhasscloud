<?php

declare(strict_types=1);
/**
 *
 * @copyright Copyright (c) 2018 Joas Schilling <coding@schilljs.com>
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

namespace OCA\Talk\Chat;

use OCA\Talk\Events\ChatMessageEvent;
use OCA\Talk\Exceptions\ParticipantNotFoundException;
use OCA\Talk\GuestManager;
use OCA\Talk\Model\Message;
use OCA\Talk\Participant;
use OCA\Talk\Room;
use OCP\Comments\IComment;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IL10N;
use OCP\IUser;
use OCP\IUserManager;

/**
 * Helper class to get a rich message from a plain text message.
 */
class MessageParser {
	public const EVENT_MESSAGE_PARSE = self::class . '::parseMessage';

	/** @var IEventDispatcher */
	private $dispatcher;

	/** @var IUserManager */
	private $userManager;

	/** @var GuestManager */
	private $guestManager;

	/** @var array */
	protected $guestNames = [];

	public function __construct(IEventDispatcher $dispatcher,
								IUserManager $userManager,
								GuestManager $guestManager) {
		$this->dispatcher = $dispatcher;
		$this->userManager = $userManager;
		$this->guestManager = $guestManager;
	}

	public function createMessage(Room $room, Participant $participant, IComment $comment, IL10N $l): Message {
		return new Message($room, $participant, $comment, $l);
	}

	public function parseMessage(Message $message): void {
		$message->setMessage($message->getComment()->getMessage(), []);
		$message->setMessageType($message->getComment()->getVerb());
		$this->setActor($message);

		$event = new ChatMessageEvent($message);
		$this->dispatcher->dispatch(self::EVENT_MESSAGE_PARSE, $event);
	}

	protected function setActor(Message $message): void {
		$comment = $message->getComment();

		$displayName = '';
		if ($comment->getActorType() === 'users') {
			$user = $this->userManager->get($comment->getActorId());
			$displayName = $user instanceof IUser ? $user->getDisplayName() : $comment->getActorId();
		} elseif ($comment->getActorType() === 'guests') {
			if (isset($guestNames[$comment->getActorId()])) {
				$displayName = $this->guestNames[$comment->getActorId()];
			} else {
				try {
					$displayName = $this->guestManager->getNameBySessionHash($comment->getActorId());
				} catch (ParticipantNotFoundException $e) {
				}
				$this->guestNames[$comment->getActorId()] = $displayName;
			}
		} elseif ($comment->getActorType() === 'bots') {
			$displayName = $comment->getActorId() . '-bot';
		}

		$message->setActor(
			$comment->getActorType(),
			$comment->getActorId(),
			$displayName
		);
	}
}
