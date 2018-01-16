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

use OCA\IssueTemplate\DetailManager;
use OCA\IssueTemplate\Sections\ClientSection;
use OCA\IssueTemplate\Sections\LogSection;
use OCA\IssueTemplate\Sections\ServerSection;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;
use OCP\Settings\ISettings;
use OCP\App\IAppManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;


class Admin implements ISettings {
	/** @var IConfig */
	private $config;
	/** @var IL10N */
	private $l10n;
	/** @var IAppManager */
	private $appManager;
	/** @var DetailManager */
	private $detailManager;
	/** @var ServerSection */
	private $serverSection;
	/** @var ClientSection  */
	private $clientSection;
	/** @var LogSection */
	private $logSection;
	/** @var EventDispatcher */
	private $eventDispatcher;
	/** @var IRequest */
	private $request;

	public function __construct(
		IConfig $config,
		IL10N $l10n,
		IAppManager $appManager,
		EventDispatcher $eventDispatcher,
		DetailManager $detailManager,
		ServerSection $serverSection,
		ClientSection $clientSection,
		LogSection $logSection,
		IRequest $request
	) {
		$this->config = $config;
		$this->l10n = $l10n;
		$this->appManager = $appManager;
		$this->detailManager = $detailManager;
		$this->serverSection = $serverSection;
		$this->clientSection = $clientSection;
		$this->logSection = $logSection;
		$this->eventDispatcher = $eventDispatcher;
		$this->request = $request;

		// Register core details that are used in every report
		$this->detailManager->addSection($this->serverSection);
		$this->detailManager->addSection($this->clientSection);
		$this->detailManager->addSection($this->logSection);

	}

	public function queryAppDetails($app) {
		$event = new GenericEvent($this->detailManager, [$app]);
		$this->eventDispatcher->dispatch('\OCA\IssueTemplate::requestInformation', $event);
	}

	public function getForm() {

		$app = $this->request->getParam('app');
		$this->queryAppDetails($app);

		$data = array(
			'details' => $this->detailManager->getRenderedDetails()
		);

		$issueTemplate = new TemplateResponse('issuetemplate', 'issuetemplate', $data, '');
		$parameters = [
			'issueTemplate' => $issueTemplate->render(),
			'repos' => $this->getAppRepos(),
			'app' => $app
		];
		\OC_Util::addScript('issuetemplate','build/build');
		\OC_Util::addStyle('issuetemplate','style');
		\OC_Util::addStyle('issuetemplate','style');
		return new TemplateResponse('issuetemplate', 'settings-admin', $parameters, '');
	}

	public function getSection() {
		return 'issuetemplate';
	}

	public function getPriority() {
		return 10;
	}

	public function getAppRepos() {
		$apps = \OC_App::getAllApps();
		$repos = array(
			'core' => [
				'name' => $this->l10n->t('Nextcloud server repository'),
				'bugs' => 'https://github.com/nextcloud/server/issues'
			],
			'android' => [
				'name' => $this->l10n->t('Nextcloud Android app repository'),
				'bugs' => 'https://github.com/nextcloud/android/issues'
			],
			'ios' => [
				'name' => $this->l10n->t('Nextcloud iOS app repository'),
				'bugs' => 'https://github.com/nextcloud/ios/issues'
			]
		);
		foreach ($apps as $app) {
			if ($this->appManager->isInstalled($app)) {
				$appinfo = \OC_App::getAppInfo($app);
				if (array_key_exists('name', $appinfo)
					&& array_key_exists('bugs', $appinfo)
					&& preg_match("/https:\/\/(www.)?github.com\/(.*)\/issues/i", $appinfo['bugs'])) {
					$appId = $appinfo['id'];
					if(is_array($appinfo['name'])) {
						$appTitle = $appinfo['name'][0];
					} else {
						$appTitle = $appinfo['name'];
					}
					$repos[$appId] = $appinfo;
					$repos[$appId]['name'] = $appTitle;
				}

			}
		}
		return $repos;
	}

}
