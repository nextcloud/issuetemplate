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

use OC\SystemConfig;
use OCA\IssueTemplate\IDetail;
use OCP\IConfig;
use OC\IntegrityCheck\Checker;
use OCP\App\IAppManager;
use OCP\IDBConnection;
use OCA\IssueTemplate\Section;
use OCA\Files_External\Service\BackendService;

class ServerSection extends Section {

	/** @var IConfig */
	private $config;
	/** @var Checker */
	private $checker;
	/** @var IAppManager */
	private $appManager;
	/** @var SystemConfig */
	private $systemConfig;
	/** @var IDBConnection */
	private $connection;

	public function __construct(IConfig $config,
								Checker $checker,
								IAppManager $appManager,
								IDBConnection $connection) {
		parent::__construct('server-detail', 'Server configuration detail');
		$this->config = $config;
		$this->checker = $checker;
		$this->appManager = $appManager;
		$this->systemConfig = \OC::$server->query('SystemConfig');
		$this->connection = $connection;
		$this->createDetail('Operating system', $this->getOsVersion());
		$this->createDetail('Webserver', $this->getWebserver());
		$this->createDetail('Database', $this->getDatabaseInfo());
		$this->createDetail('PHP version', $this->getPhpVersion());
		$this->createDetail('Nextcloud version', $this->getNextcloudVersion());
		$this->createDetail('Updated from an older Nextcloud/ownCloud or fresh install', '');
		$this->createDetail('Where did you install Nextcloud from', $this->getInstallMethod());
		$this->createDetail('Signing status', $this->getIntegrityResults(), IDetail::TYPE_COLLAPSIBLE);
		$this->createDetail('List of activated apps', $this->renderAppList(), IDetail::TYPE_COLLAPSIBLE_PREFORMAT);

		$this->createDetail('Configuration (config/config.php)', print_r(json_encode($this->getConfig(), JSON_PRETTY_PRINT), true), IDetail::TYPE_COLLAPSIBLE_PREFORMAT);

		$this->createDetail('Are you using external storage, if yes which one', 'local/smb/sftp/...');
		$this->createDetail('Are you using encryption', $this->getEncryptionInfo());
		$this->createDetail('Are you using an external user-backend, if yes which one', 'LDAP/ActiveDirectory/Webdav/...');

		$this->createDetail('LDAP configuration (delete this part if not used)', 'With access to your command line run e.g.:
sudo -u www-data php occ ldap:show-config
from within your Nextcloud installation folder

Without access to your command line download the data/owncloud.db to your local
computer or access your SQL server remotely and run the select query:
SELECT * FROM `oc_appconfig` WHERE `appid` = \'user_ldap\';


Eventually replace sensitive data as the name/IP-address of your LDAP server or groups.', IDetail::TYPE_COLLAPSIBLE_PREFORMAT);
	}
	private function getWebserver() {
		return $_SERVER['SERVER_SOFTWARE'] . ' (' . PHP_SAPI . ')';
	}

	private function getNextcloudVersion() {
		return \OC_Util::getHumanVersion() . ' - ' . $this->config->getSystemValue('version');
	}
	private function getOsVersion() {
		return PHP_OS;
	}
	private function getPhpVersion() {
		return PHP_VERSION . "\nModules loaded: " . implode(', ', get_loaded_extensions());
	}

	protected function getDatabaseInfo() {
		return $this->config->getSystemValue('dbtype') . ' ' . $this->getDatabaseVersion();
	}

	/**
	 * original source from nextcloud/survey_client
	 * @link https://github.com/nextcloud/survey_client/blob/master/lib/Categories/Database.php#L80-L107
	 *
	 * @copyright Copyright (c) 2016, ownCloud, Inc.
	 * @author Joas Schilling <coding@schilljs.com>
	 * @license AGPL-3.0
	 */
	private function getDatabaseVersion() {
		switch ($this->config->getSystemValue('dbtype')) {
			case 'sqlite':
			case 'sqlite3':
				$sql = 'SELECT sqlite_version() AS version';
				break;
			case 'oci':
				$sql = 'SELECT version FROM v$instance';
				break;
			case 'mysql':
			case 'pgsql':
			default:
				$sql = 'SELECT VERSION() AS version';
				break;
		}
		$result = $this->connection->executeQuery($sql);
		$row = $result->fetch();
		$result->closeCursor();
		if ($row) {
			return $this->cleanVersion($row['version']);
		}
		return 'N/A';
	}

	/**
	 * Try to strip away additional information
	 *
	 * @copyright Copyright (c) 2016, ownCloud, Inc.
	 * @author Joas Schilling <coding@schilljs.com>
	 * @license AGPL-3.0
	 *
	 * @param string $version E.g. `5.6.27-0ubuntu0.14.04.1`
	 * @return string `5.6.27`
	 */
	protected function cleanVersion($version) {
		$matches = [];
		preg_match('/^(\d+)(\.\d+)(\.\d+)/', $version, $matches);
		if (isset($matches[0])) {
			return $matches[0];
		}
		return $version;
	}

	private function getIntegrityResults() {
		if(!$this->checker->isCodeCheckEnforced()) {
			return 'Integrity checker has been disabled. Integrity cannot be verified.';
		}
		return $this->checker->getResults();
	}

	private function getInstallMethod() {
		$base = \OC::$SERVERROOT;
		if(file_exists($base . '/.git')) {
			return 'git';
		}
		return 'unknown';
	}

	private function renderAppList() {
		$apps = $this->getAppList();
		$result = "Enabled:\n";
		foreach ($apps['enabled'] as $name => $version) {
			$result .= ' - ' . $name . ': ' . $version . "\n";
		}

		$result .= "Disabled:\n";
		foreach ($apps['disabled'] as $name => $version) {
			$result .= ' - ' . $name . "\n";
		}
		return $result;
	}

	/**
	 * @return string[][]
	 */
	private function getAppList() {
		$apps = \OC_App::getAllApps();
		$enabledApps = $disabledApps = [];
		$versions = \OC_App::getAppVersions();

		//sort enabled apps above disabled apps
		foreach ($apps as $app) {
			if ($this->appManager->isInstalled($app)) {
				$enabledApps[] = $app;
			} else {
				$disabledApps[] = $app;
			}
		}
		$apps = ['enabled' => [], 'disabled' => []];
		sort($enabledApps);
		foreach ($enabledApps as $app) {
			$apps['enabled'][$app] = isset($versions[$app]) ? $versions[$app] : true;
		}
		sort($disabledApps);
		foreach ($disabledApps as $app) {
			$apps['disabled'][$app] = null;
		}
		return $apps;
	}

	protected function getEncryptionInfo() {
		return $this->config->getAppValue('core', 'encryption_enabled', 'no');
	}

	protected function getExternalStorageInfo() {
		if(\OC::$server->getAppManager()->isEnabledForUser('files_external')) {
			// $mounts = $this->globalService->getStorageForAllUsers();
			// Global storage services
			// https://github.com/nextcloud/server/blob/8c7d7d7746e76b77ad86cee3aae5dbd4d1bcd896/apps/files_external/lib/Command/ListCommand.php
			/** @var BackendService $backendService */
			$backendService = \OC::$server->query(BackendService::class);
			$result = array();
			foreach ($backendService->getAvailableBackends() as $backend) {
				$result[] = $backend->getStorageClass();
			}
			return $result;
		}
		return 'files_external is disabled';
	}

	private function getConfig() {

		$keys = $this->systemConfig->getKeys();
		$configs = [];
		foreach ($keys as $key) {
			$value = $this->systemConfig->getFilteredValue($key, serialize(null));
			if ($value !== 'N;') {
				$configs[$key] = $value;
			}
		}
		return $configs;
	}

}