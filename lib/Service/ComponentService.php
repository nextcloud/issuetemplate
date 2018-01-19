<?php
/**
 * @copyright Copyright (c) 2018 Julius Härtl <jus@bitgrid.net>
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

/**
 * Created by PhpStorm.
 * User: jus
 * Date: 19.01.18
 * Time: 21:45
 */

namespace OCA\IssueTemplate\Service;


use OCP\App\IAppManager;
use OCP\IL10N;
use OCP\IURLGenerator;

class ComponentService {

	/** @var IL10N */
	private $l10n;
	/** @var IAppManager */
	private $appManager;

	public function __construct(
		IL10N $l10n,
		IAppManager $appManager,
		IURLGenerator $urlGenerator
	) {
		$this->l10n = $l10n;
		$this->appManager = $appManager;
		$this->urlGenerator = $urlGenerator;
	}

	public function getComponents() {
		$apps = \OC_App::getAllApps();
		$serverComponents = array(
			$this->getComponent('server', $this->l10n->t('Nextcloud server'), 'https://github.com/nextcloud/server'),
			$this->getComponent('client', $this->l10n->t('Nextcloud desktop client'), 'https://github.com/nextcloud/client'),
			$this->getComponent('android', $this->l10n->t('Nextcloud Android app'), 'https://github.com/nextcloud/android'),
			$this->getComponent('ios', $this->l10n->t('Nextcloud iOS app'), 'https://github.com/nextcloud/ios')
		);
		$appComponents = [];
		$externalComponents = [
			'desktop' => [
				'name' => $this->l10n->t('Nextcloud Android app repository'),
				'bugs' => 'https://github.com/nextcloud/android/issues'
			],
			'android' => [
				'name' => $this->l10n->t('Nextcloud Android app repository'),
				'bugs' => 'https://github.com/nextcloud/android/issues'
			],
			'ios' => [
				'name' => $this->l10n->t('Nextcloud iOS app repository'),
				'bugs' => 'https://github.com/nextcloud/ios/issues'
			]
		];
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
					$appComponents[$appId] = $appinfo;
					$appComponents[$appId]['name'] = $appTitle;
					try {
						$icon = $this->urlGenerator->imagePath($appId, 'app.svg');
					} catch (\RuntimeException $ex) {
						try {
							$icon = $this->urlGenerator->imagePath($appId, $appId . '.svg');
						} catch (\RuntimeException $ex) {
							$icon = $this->urlGenerator->imagePath('core', 'logo.svg');
						}
					}
					$appComponents[$appId]['icon'] = $icon;
				}

			}
		}
		return [
			$this->getComponentSection('core', 'Nextcloud server', $serverComponents),
			$this->getComponentSection('apps', 'Nextcloud apps', $appComponents),

		];
	}

	public function getComponentSection($id, $title, $items) {
		return [
			'id' => $id,
			'title' => $title,
			'items' => $items
		];
	}

	public function getComponent($id, $title, $repo, $logo = '') {
		if ($logo === '') {
			$logo = \OC::$server->getURLGenerator()->imagePath('core','logo.svg');
		}
		return [
			'name' => $title,
			'bugs' => $repo,
			'icon' => $logo,
			'id' => $id
		];
	}
}