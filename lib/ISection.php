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

namespace OCA\IssueTemplate;

/**
 * Interface ISection
 *
 * @package OCA\IssueTemplate
 */
interface ISection {

	/**
	 * Returns a unique identifier for the section
	 *
	 * Predefined sections are server, client and log
	 * @return string
	 */
	public function getIdentifier();

	/**
	 * Returns a human readable title for the section
	 *
	 * @return string
	 */
	public function getTitle();

	/**
	 * Adds a detail entry to a section
	 *
	 * @param IDetail $details
	 * @return void
	 */
	public function addDetail(IDetail $details);

	/**
	 * Get all details contained in the section
	 *
	 * @return IDetail[]
	 */
	public function getDetails();
}