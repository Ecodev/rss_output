<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Fabien Udriot
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * ************************************************************* */

/**
 * View helper for entry link generation
 *
 * = Examples =
 */
class Tx_RssOutput_ViewHelpers_LinkViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 *
	 * @var tslib_cObj
	 */
	protected $localCObj;

	/**
	 * Initialize
	 *
	 */
	public function initialize() {
		$this->localCObj = t3lib_div::makeInstance('tslib_cObj');
	}

	/**
	 * Generate a link
	 *
	 * @param array $entry the uid of the current entry
	 * @param array $configuration that may contains information to a detail page
	 *
	 * @return string
	 */
	public function render(array $entry, array $configuration) {

		// TRUE means this is a detail view such as News, Party, etc...
		if (!empty($configuration['detailView'])) {
			if (empty($configuration['detailView']['pid'])) {
				throw new Tx_RssOutput_Exception_MissingConfigurationException('Exception 1325478632: missing pid setting in detailView group', 1325478632);
			}
		}

		// Can't use this View Helper because of "Call to a member function getUriBuilder()"
		/** @var $uri Tx_Fluid_ViewHelpers_Uri_PageViewHelper */
		//$uri = t3lib_div::makeInstance('Tx_Fluid_ViewHelpers_Uri_PageViewHelper');

		$baseUrl = ltrim($configuration['baseURL'], '/');

		$this->localCObj->start($entry);
		$config = array();
		$config['returnLast'] = 'url';
		$config['parameter'] = $configuration['detailView']['pid'];

		if (!empty($configuration['detailView']['additionalParams'])) {
			$config['additionalParams'] = '&' . $configuration['detailView']['additionalParams'];
			$config['additionalParams.']['insertData'] = 1;
		}
		$link = $this->localCObj->typolink('', $config);

		return $baseUrl . urlencode($link);
	}

}

?>