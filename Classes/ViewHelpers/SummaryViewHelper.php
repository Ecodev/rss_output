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
 * View helper that generates a summary
 *
 * = Examples =
 */
class Tx_RssOutput_ViewHelpers_SummaryViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

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
	 * Generate a summary from the RTE
	 *
	 * @param string $content
	 * @param array $configuration
	 *
	 * @return string
	 */
	public function render($content, array $configuration) {

		// RTE
		$config['parseFunc.'] = $GLOBALS['TSFE']->tmpl->setup['lib.']['parseFunc_RTE.'];
		$config['value'] = $content;
		$content = $this->localCObj->TEXT($config);

		// thanks to Marius Mühlberger <mm@co-operation.de> for the regular expressions
		// Remove script-tags with content
		$pattern[] = '/<( *)script([^>]*)type( *)=( *)([^>]*)>(.*)<\/( *)script( *)>/isU';
		$replace[] = '';

		// Remove event handler
		$pattern[] = '/( *)(on[a-z]{4,10})( *)=( *)"([^"]*)"/isU';
		$replace[] = '';

		// Remove javascript in url, etc
		$pattern[] = '/"( *)javascript( *):([^"]*)"/isU';
		$replace[] = '""';

		// Remove trailing
		$pattern[] = '/<p>&nbsp;<\/p>$/isU';
		$replace[] = '';

		#var_dump($content);
		// Replaces baseURL link
		$baseURL = $configuration['baseURL'];
		if($baseURL) {
			// Replace links
			$pattern[] = "/<a([^>]*) href=\"([^http|ftp|https][^\"]*)\"/isU";
			$replace[] = "<a\${1} href=\"" . $baseURL . "\${2}\"";

			// Replace images
			$pattern[] = "/<img([^>]*) src=\"([^http|ftp|https][^\"]*)\"/";
			$replace[] = "<img\${1} src=\"" . $baseURL . "\${2}\" alt=\${2}";
		}

		$content = preg_replace($pattern,$replace, $content);
		$content = '<![CDATA[' . $content . ']]>';

		return $content;
	}

}

?>