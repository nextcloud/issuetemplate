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
								IAppManager $appManager) {
		$this->config = $config;
		$this->l = $l;
		$this->urlGenerator = $urlGenerator;
		$this->checker = $checker;
		$this->appManager = $appManager;
		$this->systemConfig = \OC::$server->query("SystemConfig");
	}

	public function getForm() {
		$data = array(
			'version' => \OC_Util::getHumanVersion() . " - " . $this->config->getSystemValue('version'),
			'os' => php_uname(),
			'php' => PHP_VERSION . "\nModules loaded: " . implode(", ", get_loaded_extensions()),
			'dbserver' => $this->config->getSystemValue('dbtype'),
			'webserver' => $_SERVER['SERVER_SOFTWARE'] . " (" . php_sapi_name() . ")",
			'installMethod' => $this->getInstallMethod(),
			'integrity' => $this->getIntegrityResults(),
			'apps' => $this->getAppList(),
			'config' => $this->getConfig(),
		);

		$issueTemplate = new TemplateResponse('issuetemplate', 'issuetemplate', $data, '');
		$parameters = [
			'issueTemplate' => $issueTemplate->render(),
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

}
