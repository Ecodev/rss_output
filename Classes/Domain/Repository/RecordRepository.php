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
class Tx_RssOutput_Domain_Repository_RecordRepository {

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
	 * Find custom records
	 *
	 * @param array $config
	 *        An array containing $configuration
	 *
	 * @return array
	 */
	public function find($config) {

		// Makes sure the uid is valid
		if (empty($config['table'])) {
			throw new Tx_RssOutput_Exception_MissingConfigurationException('Exception 1324559684: no table value defined in configuration
			field.', 1324559684);
		}

		/* Defines default value */
		$config['table'] = $config['table'] != '' ? $config['table'] : 'tt_content';

		if ($config['table'] == 'tt_content' && empty($config['rootPid'])) {
			throw new Tx_RssOutput_Exception_MissingConfigurationException('Exception 1324566898: no rootPid value defined in configuration field.', 1324566898);
		}
		///////////////////////////////
		// Select processing
		///////////////////////////////
		$fields = $config['field'];
		$title = !empty($fields['title']) ? $fields['title'] : 'header';
		$summary = !empty($fields['summary']) ? $fields['summary'] : 'bodytext';
		$published = !empty($fields['published']) ? $fields['published'] : 'tstamp';
		$updated = !empty($fields['updated']) ? $fields['updated'] : 'tstamp';
		$uid = !empty($fields['uid']) ? $fields['uid'] : 'uid';
		$headerLayout = $config['table'] == 'tt_content' ? ', header_layout' : '';
		$pid = !empty($fields['pid']) ? $fields['pid'] : 'pid';
		$additionalFields = !empty($config['additionalFields']) ? ", " . $config['additionalFields'] : '';

		$selectPart = $pid . ' as pid, ' . $uid . ' as uid, ' . $title . ' as title, ' . $summary . ' as summary, '
			. $published . ' as published, ' . $updated . ' as updated' . $headerLayout . $additionalFields;

		///////////////////////////////
		// Clause processing
		///////////////////////////////
		$clause = $this->getClause($config);

		///////////////////////////////
		// Order processing
		///////////////////////////////
		$order = $this->getOrder($config);

		///////////////////////////////
		// Limit processing
		///////////////////////////////
		$limitSQL = !empty($config['numberOfItems']) ? $config['numberOfItems'] : '10';

		///////////////////////////////
		// Debug processing
		///////////////////////////////
		$log = !empty($config['log']) ? $config['log'] : FALSE;
		if ($log) {
			$request = $this->db->SELECTquery($selectPart, $config['table'], $clause, '', $order, $limitSQL);
			t3lib_div::devLog('RSS query: ' . $request, 'rss_output', 0);
		}
		$debug = !empty($config['debug']) ? $config['debug'] : FALSE;
		if ($debug) {
			$request = $this->db->SELECTquery($selectPart, $config['table'], $clause, '', $order, $limitSQL);
			t3lib_utility_Debug::debug($request, "debug");
			die();
		}

		$result = $this->db->exec_SELECTgetRows($selectPart, $config['table'], $clause, '', $order, $limitSQL);

		// Raises an error if wrong SQL is detected
		if ($result === NULL) {
			$request = $this->db->SELECTquery($selectPart, $config['table'], $clause, '', $order, $limitSQL);
			throw new Tx_RssOutput_Exception_InvalidQueryException('Exception 1325574994: bad query: ' . $request, 1325574994);
		}
		return $result;
	}

	/**
	 * Get the Order clause
	 *
	 * @param $config
	 * @return string
	 */
	protected function getOrder($config) {
		$order = 'tstamp DESC';
		if (isset($config['orderBy'])) {
			$order = $config['orderBy'];
		}
		return $order;
	}
	/**
	 * Get the a page clause
	 *
	 * @param $pages
	 * @param $config
	 * @return string
	 */
	protected function getPageClause($pages, $config) {
		$_clause = 'pid=' . $config['rootPid'];
		foreach ($pages as $page) {
			$_clause .= ' OR pid=' . $page['uid'];
		}

		// Adds additional pid's
		if (isset($config['additionalPids']) && $config['additionalPids'] != '') {
			foreach (explode(',', $config['additionalPids']) as $pid) {
				$_clause .= ' OR pid=' . $pid;
			}
		}
		$clause = ' AND (' . $_clause . ')'; #merge of the two clauses
		return $clause;
	}
	/**
	 * Get the clause part of the query
	 *
	 * @param array $config An array containing configuration
	 * @return string
	 */
	protected function getClause($config) {
		/** @var $contentObject tslib_cObj  */
		$contentObject = t3lib_div::makeInstance('tslib_cObj');
		$clause = '1=1 ' . $contentObject->enableFields($config['table']);

		// Checks if the page is in the root line
		if ($config['rootPid'] > 0) {
			$pages = $this->getAllPages($config['rootPid']);
			$clause .= $this->getPageClause($pages, $config);
		}

		// Adds additional SQL
		if (!empty($config['additionalConditions'])) {
			$clause .= ' ' . $config['additionalConditions'] . ' ';
		}

		if ($config['table'] == 'tt_content' && !$config['includeAll']) {
			$clause .= ' AND tx_rssoutput_includeinfeed = 1 ';
		}

		// Only return selected language content
		if (!empty($configuration['sys_language_uid'])) {
			$clause .= ' AND sys_language_uid=' . $configuration['sys_language_uid'];
		}

		return $clause;
	}

	/**
	 * Return the list of page's pid being descendant of <tt>$pid</tt>.
	 *
	 * @param	integer		$pid: mother page's pid
	 * @param	array		$arrayOfPid: referenced array of children's pid
	 * @return	array		Array of all pid being children of <tt>$pid</tt>
	 */
	protected function getAllPages($pid, &$arrayOfPid = array()) {
		$pages = tx_div::db()->exec_SELECTgetRows('uid', 'pages', 'deleted = 0 AND hidden = 0 AND pid = ' . $pid);
		$arrayOfPid = array_merge($pages, $arrayOfPid);
		if (count($pages) > 0) {
			foreach ($pages as $page) {
				$this->getAllPages($page['uid'], $arrayOfPid);
			}
		}
		return $arrayOfPid;
	}
}
?>