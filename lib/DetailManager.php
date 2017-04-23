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

class DetailManager {

	private $sections = [];

	public function __construct($AppName) {
		\OC::$server->getLogger()->error($AppName);
	}

	public function createSection($identifier, $title, $order=0) {
		$section = new Section($identifier, $title, $order);
		$this->addSection($section);
	}

	public function addSection(ISection $section) {
		if(array_key_exists($section->getIdentifier(), $this->sections)) {
			/** @var ISection $existing */
			$existing = $this->sections[$section->getIdentifier()];
			foreach ($section->getDetails() as $detail) {
				$existing->addDetail($detail);
			}
			return;
		}
		$this->sections[$section->getIdentifier()] = $section;
	}

	public function removeSection($section) {
		unset($this->sections[$section]);
	}

	/**
	 * @param string $sectionIdentifier
	 * @param string $title
	 * @param string $information
	 * @param int $type
	 */
	public function createDetail($sectionIdentifier, $title, $information, $type = IDetail::TYPE_MULTI_LINE_PREFORMAT) {
		$detail = new Detail($sectionIdentifier, $title, $information, $type);
		/** @var ISection $sectionObject */
		$sectionObject = $this->sections[$sectionIdentifier];
		$sectionObject->addDetail($detail);
	}

	/**
	 * @return ISection[]
	 */
	public function getSections() {
		return $this->sections;
	}

	public function getRenderedDetails() {
		$result = '';
		/** @var ISection $section */
		foreach ($this->sections as $section) {
			$result .= $this->renderSectionHeader($section);
			/** @var IDetail $detail */
			foreach ($section->getDetails() as $detail) {
				$result .= $this->renderDetail($detail);
			}
		}
		return $result;
	}

	private function renderSectionHeader(ISection $section) {
		return '## ' . $section->getTitle() . "\n\n";
	}

	private function renderDetail(IDetail $detail) {
		switch ($detail->getType()) {
			case IDetail::TYPE_SINGLE_LINE:
				return '**' . $detail->getTitle() . ':** ' . $detail->getInformation() . "\n\n";
				break;
			case IDetail::TYPE_MULTI_LINE:
				return '**' . $detail->getTitle() . ":** \n\n" . $detail->getInformation() . "\n\n";
				break;
			case IDetail::TYPE_MULTI_LINE_PREFORMAT:
				return '**' . $detail->getTitle() . ":** \n\n``` \n" . $detail->getInformation() . "\n```\n\n";
				break;
			case IDetail::TYPE_COLLAPSIBLE:
				return '<details><summary>' . $detail->getTitle() . "</summary>\n\n" . $detail->getInformation() . "\n</details>\n\n";
				break;
			case IDetail::TYPE_COLLAPSIBLE_PREFORMAT:
				return '<details><summary>' . $detail->getTitle() . "</summary>\n\n```\n" . $detail->getInformation() . "\n```\n</details>\n\n";
				break;
			default:
				return '**' . $detail->getTitle() . ':** ' . $detail->getInformation() . "\n\n";
				break;
		}
	}
}