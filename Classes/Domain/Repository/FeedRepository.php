<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Fabien Udriot <fabien.udriot@ecodev.ch>, Ecodev
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package rss_output
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_RssOutput_Domain_Repository_FeedRepository {

	/**
	 * @var t3lib_DB
	 */
	protected $db;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Find records
	 *
	 * @param int $uid
	 *        the uid of the record
	 *
	 * @return array
	 */
	public function findByUid($uid) {

		$record = $this->db->exec_SELECTgetSingleRow('*', 'tx_rssoutput_feed', 'deleted = 0 AND hidden = 0 AND uid = ' . $uid);

		// Makes sure the record exists
		if (empty($record)) {
			throw new Tx_RssOutput_Exception_NotExistingRecordException("Exception 1323921662: no record found for '$uid'. Record exists? Hidden or deleted? ", 1323921662);
		}

		return $record;
	}

}
?>