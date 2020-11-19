<?php

/**
 * Circles - Bring cloud-users closer together.
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2017
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


namespace OCA\Circles\Db;


use OCA\Circles\Exceptions\JsonException;
use OCA\Circles\Exceptions\ModelException;
use OCA\Circles\Model\GlobalScale\GSWrapper;
use OCP\DB\QueryBuilder\IQueryBuilder;


/**
 * Class GSEventsRequestBuilder
 *
 * @package OCA\Circles\Db
 */
class GSEventsRequestBuilder extends CoreRequestBuilder {


	/**
	 * Base of the Sql Insert request for Shares
	 *
	 * @return IQueryBuilder
	 */
	protected function getGSEventsInsertSql() {
		$qb = $this->dbConnection->getQueryBuilder();
		$qb->insert(self::TABLE_GSEVENTS);

		return $qb;
	}


	/**
	 * Base of the Sql Update request for Groups
	 *
	 * @return IQueryBuilder
	 */
	protected function getGSEventsUpdateSql() {
		$qb = $this->dbConnection->getQueryBuilder();
		$qb->update(self::TABLE_GSEVENTS);

		return $qb;
	}


	/**
	 * @return IQueryBuilder
	 */
	protected function getGSEventsSelectSql() {
		$qb = $this->dbConnection->getQueryBuilder();

		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$qb->select('gse.token', 'gse.event', 'gse.instance', 'gse.severity', 'gse.status', 'gse.creation')
		   ->from(self::TABLE_GSEVENTS, 'gse');

		$this->default_select_alias = 'gse';

		return $qb;
	}


	/**
	 * Base of the Sql Delete request
	 *
	 * @return IQueryBuilder
	 */
	protected function getGSEventsDeleteSql() {
		$qb = $this->dbConnection->getQueryBuilder();
		$qb->delete(self::TABLE_GSEVENTS);

		return $qb;
	}


	/**
	 * @param array $data
	 *
	 * @return GSWrapper
	 * @throws JsonException
	 * @throws ModelException
	 */
	protected function parseGSEventsSelectSql($data): GSWrapper {
		$wrapper = new GSWrapper();
		$wrapper->import($data);

		return $wrapper;
	}

}
