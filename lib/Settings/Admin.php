<?php
/**
 * @copyright Copyright (c) 2016 Julius Härtl <jus@bitgrid.net>
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
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\IssueTemplate\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\ISettings;
use OC\IntegrityCheck\Checker;
use OCP\App\IAppManager;
use OC\SystemConfig;
use OCP\IDBConnection;



class Admin implements ISettings {
	/** @var IConfig */
	private $config;
	/** @var IL10N */
	private $l;
	/** @var IURLGenerator */
	private $urlGenerator;
	/** @var Checker */
	private $checker;
	/** @var IAppManager */
	private $appManager;
	/** @var SystemConfig */
	private $systemConfig;

	public function __construct(
								IConfig $config,
								IL10N $l,
								IURLGenerator $urlGenerator,
								Checker $checker,
								IAppManager $appManager,
								IDBConnection $connection
) {
		$this->config = $config;
		$this->l = $l;
		$this->urlGenerator = $urlGenerator;
		$this->checker = $checker;
		$this->appManager = $appManager;
		$this->systemConfig = \OC::$server->query("SystemConfig");
		$this->connection = $connection;
	}

	public function getForm() {
		$data = array(
			'version' => $this->getNextcloudVersion(),
			'os' => $this->getOsVersion(),
			'php' => $this->getPhpVersion(),
			'dbserver' => $this->getDatabaseInfo(),
			'webserver' => $_SERVER['SERVER_SOFTWARE'] . " (" . php_sapi_name() . ")",
			'installMethod' => $this->getInstallMethod(),
			'integrity' => $this->getIntegrityResults(),
			'apps' => $this->getAppList(),
			'config' => $this->getConfig(),
			'encryption' => $this->getEncryptionInfo(),
			'external' => $this->getExternalStorageInfo(),
			'browser' => $this->getBrowser()
		);

		$issueTemplate = new TemplateResponse('issuetemplate', 'issuetemplate', $data, '');
		$parameters = [
			'issueTemplate' => $issueTemplate->render(),
			'repos' => $this->getAppRepos(),
		];
		\OC_Util::addScript('issuetemplate','script');
		\OC_Util::addStyle('issuetemplate','style');
		return new TemplateResponse('issuetemplate', 'settings-admin', $parameters, '');
	}

	public function getSection() {
		return 'issuetemplate';
	}

	public function getPriority() {
		return 10;
	}

	private function getNextcloudVersion() {
		return \OC_Util::getHumanVersion() . " - " . $this->config->getSystemValue('version');
	}
	private function getOsVersion() {
		return php_uname();
	}
	private function getPhpVersion() {
		return PHP_VERSION . "\nModules loaded: " . implode(", ", get_loaded_extensions());
	}

	protected function getDatabaseInfo() {
		return $this->config->getSystemValue('dbtype') ." " . $this->getDatabaseVersion();
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
			return "git";
		}
	}

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
			$apps['enabled'][$app] = (isset($versions[$app])) ? $versions[$app] : true;
		}
		sort($disabledApps);
		foreach ($disabledApps as $app) {
			$apps['disabled'][$app] = null;
		}
		return $apps;
	}

	public function getAppRepos() {
		$apps = \OC_App::getAllApps();
		$repos = array(
			"Nextcloud server repository" => "https://github.com/nextcloud/server/issues"
		);
		foreach ($apps as $app) {
			if ($this->appManager->isInstalled($app)) {
				$appinfo = \OC_App::getAppInfo($app);
				if (array_key_exists('bugs', $appinfo) && preg_match("/https:\/\/(www.)?github.com\/(.*)\/issues/i", $appinfo['bugs'])) {
					$appTitle = $appinfo['name'];
					$repos[$appTitle] = $appinfo['bugs'];
				}

			}
		}
		return $repos;

	}

	protected function getEncryptionInfo() {
		return $this->config->getAppValue('core', 'encryption_enabled', 'no');
	}

	protected function getExternalStorageInfo() {
		if(\OC::$server->getAppManager()->isEnabledForUser('files_external')) {
			// $mounts = $this->globalService->getStorageForAllUsers();
			// Global storage services
			// https://github.com/nextcloud/server/blob/8c7d7d7746e76b77ad86cee3aae5dbd4d1bcd896/apps/files_external/lib/Command/ListCommand.php
			$backendService = \OC::$server->query('OCA\Files_External\Service\BackendService');
			$result = array();
			foreach ($backendService->getAvailableBackends() as $backend) {
				$result[] = $backend->getStorageClass();
			}
			return $result;
		}
		return "files_external is disabled";
	}
	private function getConfig() {

		$keys = $this->systemConfig->getKeys();
		$configs = [];
		foreach ($keys as $key) {
			if (true) {
				$value = $this->systemConfig->getFilteredValue($key, serialize(null));
			} else {
				$value = $this->systemConfig->getValue($key, serialize(null));
			}
			if ($value !== 'N;') {
				$configs[$key] = $value;
			}
		}
		return $configs;
	}

	private function getBrowser() {
		$browser = @get_browser(null, true);
		if(!$browser) {
			return $_SERVER['HTTP_USER_AGENT'];
		} else {
			$string = ' ' . $browser['browser'] . ' ' . $browser['version'] . ' ' . $browser['plattform'];
			return $string;
		}
	}

}
