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

class Section implements ISection {

	/** @var string */
	private $identifier;
	/** @var string */
	private $title;
	/** @var IDetail[]  */
	private $details = [];

	public function __construct($identifier, $title, $order = 0) {
		$this->identifier = $identifier;
		$this->title = $title;
	}

	/**
	 * @inheritdoc
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * @inheritdoc
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @inheritdoc
	 */
	public function addDetail(IDetail $details) {
		$this->details[] = $details;
	}

	/**
	 * @inheritdoc
	 */
	public function getDetails() {
		return $this->details;
	}

	/**
	 * @inheritdoc
	 */
	public function createDetail($title, $information, $type = IDetail::TYPE_SINGLE_LINE) {
		$detail = new Detail($this->getIdentifier(), $title, $information, $type);
		$this->addDetail($detail);
		return $detail;
	}

}