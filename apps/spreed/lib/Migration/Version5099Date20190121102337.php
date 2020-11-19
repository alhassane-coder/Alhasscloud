<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2019, Joas Schilling <coding@schilljs.com>
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

namespace OCA\Talk\Migration;

use Closure;
use Doctrine\DBAL\Types\Type;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version5099Date20190121102337 extends SimpleMigrationStep {
	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('talk_commands')) {
			$table = $schema->createTable('talk_commands');

			$table->addColumn('id', Type::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('app', Type::STRING, [
				'notnull' => false,
				'length' => 64,
				'default' => '',
			]);
			$table->addColumn('name', Type::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('command', Type::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('script', Type::TEXT, [
				'notnull' => true,
			]);
			$table->addColumn('response', Type::INTEGER, [
				'notnull' => true,
				'length' => 6,
				'default' => 1,
			]);
			$table->addColumn('enabled', Type::INTEGER, [
				'notnull' => true,
				'length' => 6,
				'default' => 1,
			]);

			$table->setPrimaryKey(['id']);
		}

		return $schema;
	}
}
