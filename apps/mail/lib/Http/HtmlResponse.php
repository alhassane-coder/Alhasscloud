<?php

declare(strict_types=1);

/**
 * @author Christoph Wurst <wurst.christoph@gmail.com>
 * @author Lukas Reschke <lukas@owncloud.com>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * Mail
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\Mail\Http;

use OCP\AppFramework\Http\Response;

class HtmlResponse extends Response {

	/** @var string */
	private $content;

	public function __construct(string $content) {
		parent::__construct();
		$this->content = $content;
	}

	/**
	 * Simply sets the headers and returns the file contents
	 *
	 * @return string the file contents
	 */
	public function render(): string {
		return $this->content;
	}
}
