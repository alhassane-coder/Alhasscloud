<?php
/**
 * Nextcloud - user_sql
 *
 * @copyright 2018 Marcin Łojewski <dev@mlojewski.me>
 * @author    Marcin Łojewski <dev@mlojewski.me>
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
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Tests\UserSQL\Crypto;

use OCA\UserSQL\Crypto\IPasswordAlgorithm;
use OCA\UserSQL\Crypto\SHA256;
use OCP\IL10N;
use Test\TestCase;

/**
 * Unit tests for class <code>SHA256</code>.
 *
 * @author Marcin Łojewski <dev@mlojewski.me>
 */
class SHA256Test extends TestCase
{
    /**
     * @var IPasswordAlgorithm
     */
    private $crypto;

    public function testCheckPassword()
    {
        $this->assertTrue(
            $this->crypto->checkPassword(
                "password", "5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8"
            )
        );
    }

    public function testPasswordHash()
    {
        $hash = $this->crypto->getPasswordHash("password");
        $this->assertTrue($this->crypto->checkPassword("password", $hash));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->crypto = new SHA256($this->createMock(IL10N::class));
    }
}
