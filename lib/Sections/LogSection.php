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

namespace OCA\IssueTemplate\Sections;


use OCA\IssueTemplate\IDetail;
use OCA\IssueTemplate\Section;

class LogSection extends Section {

	public function __construct() {
		parent::__construct('log-detail', 'Logs');
		$this->createDetail('Browser log', 'Insert your webserver log here ', IDetail::TYPE_COLLAPSIBLE_PREFORMAT);
		$this->createDetail('Nextcloud log', 'Insert your Nextcloud log here', IDetail::TYPE_COLLAPSIBLE_PREFORMAT);
		$this->createDetail('Browser log', 'Insert your browser log here, this could for example include:

	a) The javascript console log
	b) The network log
	c) ...', IDetail::TYPE_COLLAPSIBLE);
	}

}