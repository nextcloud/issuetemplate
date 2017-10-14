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

namespace OCA\IssueTemplate\Controller;

use \OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Http\Client\IClientService;
use OCP\IRequest;
use OCP\IConfig;
use OCP\Security\ICrypto;

class GithubController extends Controller {

	/** @var IConfig */
	private $config;
	/** @var ICrypto */
	private $crypto;
	/** @var IClientService */
	private $httpClientService;
	/** @var string */
	private $userId;

	public function __construct($appName, IRequest $request, IConfig $config, ICrypto $crypto, IClientService $clientService, $userId) {
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->crypto = $crypto;
		$this->httpClientService = $clientService;
		$this->userId = $userId;
	}

	public function setAccessToken($token) {
		$client = $this->httpClientService->newClient();
		$url = "https://api.github.com/user";
		$headers = ['headers' => ['Authorization' => 'token ' . $token]];
		try {
			$client->get($url, $headers);
		} catch (\Exception $e) {
			$this->config->setUserValue($this->userId, $this->appName, 'github_access_token', '');
			throw new \Exception('Failed to connect to GitHub. Maybe your access token is invalud');
		}
		$accessTokenSecure = $this->crypto->encrypt($token);
		$this->config->setUserValue($this->userId, $this->appName, 'github_access_token', $accessTokenSecure);
	}

	public function reportIssue($reportUrl, $title, $body) {
		$match = preg_match('^https?\:\/\/(www\.)?github\.com\/([A-z0-9-_]+)\/([A-z0-9-_]+)(\/issues)?', $reportUrl);
		$endpoint = '/repos/' . $match[2] . '/' . $match[3] . '/issues';
		$data = [
			'title' => $title,
			'body' => $body
		];
		$client = $this->httpClientService->newClient();
		$result = $client->post($endpoint,
			[
				'body' => $data,
				'headers' => $this->getAccessTokenHeaders()
			]
		);
		return $result;
	}

	private function getAccessTokenHeaders() {
		$accessToken = $this->config->getUserValue($this->userId, 'issuetemplate', 'github_access_token', '');
		if ($accessToken === '') {
			throw new \Exception('No github access token available');
		}
		$accessToken = $this->crypto->decrypt($accessToken);
		return [
			'Authorization' => 'token ' . $accessToken,
		];
	}
}