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
 * Time: 21:44
 */

namespace OCA\IssueTemplate\Controller;


use OCA\IssueTemplate\DetailManager;
use OCA\IssueTemplate\IDetail;
use OCA\IssueTemplate\ISection;
use OCA\IssueTemplate\Sections\ClientSection;
use OCA\IssueTemplate\Sections\LogSection;
use OCA\IssueTemplate\Sections\ServerSection;
use OCA\IssueTemplate\Service\ComponentService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\QueryException;
use OCP\IRequest;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;

class APIController extends Controller {

	private $componentService;
	private $detailManager;
	private $eventDispatcher;

	public function __construct(string $appName, IRequest $request, ComponentService $componentService, DetailManager $detailManager, EventDispatcher $eventDispatcher) {
		parent::__construct($appName, $request);
		$this->componentService = $componentService;
		$this->detailManager = $detailManager;
		$this->eventDispatcher = $eventDispatcher;

		// Register core details that are used in every report
		try {
			$this->detailManager->addSection(\OC::$server->query(ServerSection::class));
			$this->detailManager->addSection(\OC::$server->query(ClientSection::class));
			$this->detailManager->addSection(\OC::$server->query(LogSection::class));
		} catch (QueryException $e) {
		}
	}

	public function components() {
		return new JSONResponse($this->componentService->getComponents());
	}

	private function queryAppDetails($app) {
		$event = new GenericEvent($this->detailManager, [$app]);
		$this->eventDispatcher->dispatch('\OCA\IssueTemplate::requestInformation', $event);
	}

	public function details($app) {
		$this->queryAppDetails($app);

		$model = [];
		$schema = [];

		$sections = $this->detailManager->getSections();
		/** @var ISection $section */
		foreach ($sections as $section) {
			$model[$section->getIdentifier()] = [];
			$group = [
				'legend' => $section->getTitle(),
				'fields' => []
			];
			/** @var IDetail $detail */
			foreach ($section->getDetails() as $detail) {
				$model[$section->getIdentifier()][$detail->getIdentifier()] = $detail->getInformation();
				$group['fields'][] = [
					'type' => $this->getTypeFieldSchema($detail->getType()),
					'label' => $detail->getTitle(),
					'model' => $section->getIdentifier() . '.' . $detail->getIdentifier()
				];
			}
			$schema['groups'][] = $group;
		}
		return [
			'model' => $model,
			'schema' => $schema
		];
	}

	private function getTypeFieldSchema($type) {
		switch ($type) {
			case IDetail::TYPE_SINGLE_LINE:
				return 'input';
				break;
			default:
				return 'textArea';
				break;
		}
	}

}