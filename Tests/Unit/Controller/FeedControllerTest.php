<?php

/* * *************************************************************
 *  Copyright notice
 *  (c) 2011 Fabien Udriot <fabien.udriot
 * @ecodev.ch>, Ecodev
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Test case for class Tx_RssOutput_Controller_FeedControllerTest.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package TYPO3
 * @subpackage rss_output
 * @author Fabien Udriot <fabien.udriot@ecodev.ch>
 */
class Tx_RssOutput_Controller_FeedControllerTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Phpunit_Framework
	 */
	protected $testingFramework = NULL;

	/**
	 * @var Tx_RssOutput_Controller_FeedController
	 */
	protected $fixture;

	public function setUp() {
		$this->testingFramework = new Tx_Phpunit_Framework('tx_rssoutput');
		$this->fixture = new Tx_RssOutput_Controller_FeedController();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function constructObject() {

		$this->testingFramework->createFakeFrontEnd();

		// still looking how to test the $this->configuration property
		$this->markTestIncomplete();
	}

	/**
	 * @test
	 */
	public function getLanguage() {

		$this->testingFramework->createFakeFrontEnd(1);
		// still looking how to test a protected function
		#var_dump($this->fixture->getLanguage());
		// how can we get the language config? 'config.' is empty
		#var_dump($GLOBALS['TSFE']->tmpl->setup['config.']);

		$this->markTestIncomplete();
	}

	/**
	 * @test
	 * @expectedException Tx_RssOutput_Exception_InvalidConfigurationException
	 */
	public function checkConfigurationException() {

		$method = new ReflectionMethod(
			'Tx_RssOutput_Controller_FeedController', 'checkConfiguration'
		);

		$method->setAccessible(TRUE);

		$defaultConfiguration = array();
		#$defaultConfiguration['baseURL'] = 'http://test.com';

		$method->invokeArgs(new Tx_RssOutput_Controller_FeedController, $defaultConfiguration);
	}

}

?>