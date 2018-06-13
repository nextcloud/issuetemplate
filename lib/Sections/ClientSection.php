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

use OCP\IRequest;
use OCA\IssueTemplate\Section;

class ClientSection extends Section {

	public function __construct(IRequest $request) {
		parent::__construct('client-detail', 'Client configuration');

		$app = $request->getParam('app');

		if($app !== 'ios' && $app !== 'android') {
			$this->createDetail('Browser', $this->getBrowser());
			$this->createDetail('Operating system', '');
		}

		if($app === 'android') {
			$this->createDetail('Android version', '');
			$this->createDetail('Device model', '');
			$this->createDetail('Stock or customized system:', '');
			$this->createDetail('Nextcloud app version:', '');
			$this->createDetail('Nextcloud server version:', '');
		}
	}

	private function getBrowser() {
		$browser = @get_browser(null, true);
		$browserString = '';
		if($browser) {
			if(array_key_exists('browser', $browser)) {
				$browserString .= $browser['browser'] . ' ';
			}
			if(array_key_exists('version', $browser)) {
				$browserString .= $browser['version'] . ' ';
			}
			if(array_key_exists('plattform', $browser)) {
				$browserString .= $browser['plattform'] . ' ';
			}
		}
		if(isset($_SERVER['HTTP_USER_AGENT']) && empty($browserString)) {
			return $_SERVER['HTTP_USER_AGENT'];
		}
		return $browserString;
	}

}
