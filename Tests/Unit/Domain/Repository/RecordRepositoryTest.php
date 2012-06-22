<?php

/***************************************************************
 *  Copyright notice
 *  (c) 2011 Fabien Udriot <fabien.udriot
 *
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
 ***************************************************************/

/**
 * Testcase for class Tx_RssOutput_Domain_Repository_RecordRepository.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package TYPO3
 * @subpackage rss_output
 * @author Fabien Udriot <fabien.udriot@ecodev.ch>
 */
class Tx_RssOutput_Domain_Repository_RecordRepositoryTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Phpunit_Framework
	 */
	protected $testingFramework = NULL;

	/**
	 * @var Tx_RssOutput_Domain_Repository_RecordRepository
	 */
	protected $fixture;

	/**
	 * @var array
	 */
	protected $configuration;

	public function setUp() {
		$this->configuration = array(
			'table' => 'tt_content',
			'rootPid' => '0',
			'baseURL' => 'http://www.eren.local/',
			'numberOfItems' => '10',
			'includeAll' => FALSE,
		);
		$this->testingFramework = new Tx_Phpunit_Framework('tx_rssoutput');
		$this->fixture = new Tx_RssOutput_Domain_Repository_RecordRepository();
	}

	public function tearDown() {
		$this->testingFramework->cleanUp();
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getOrderPartWithDefaultValue() {

		$method = new ReflectionMethod(
			'Tx_RssOutput_Domain_Repository_RecordRepository', 'getOrder'
		);

		$method->setAccessible(TRUE);

		$order = $method->invokeArgs(new Tx_RssOutput_Domain_Repository_RecordRepository(),
			array($this->configuration));

		$this->assertEquals($order, 'tstamp DESC');
	}

	/**
	 * @test
	 */
	public function getOrderPartWithCustomValue() {

		$method = new ReflectionMethod(
			'Tx_RssOutput_Domain_Repository_RecordRepository', 'getOrder'
		);

		$method->setAccessible(TRUE);

		$this->configuration['orderBy'] = 'create ASC';
		$order = $method->invokeArgs(new Tx_RssOutput_Domain_Repository_RecordRepository(),
			array($this->configuration));

		$this->assertEquals($order, 'create ASC');
	}

	/**
	 * @test
	 */
	public function getPageClauseSimple() {
		$pids = $this->getPageUidWithinATree();

		$method = new ReflectionMethod(
			'Tx_RssOutput_Domain_Repository_RecordRepository', 'getPageClause'
		);

		$method->setAccessible(TRUE);

		$clause = $method->invokeArgs(new Tx_RssOutput_Domain_Repository_RecordRepository(),
			array($pids, $this->configuration));

		$this->assertEquals(count($pids), substr_count($clause, ' OR '));
		$this->assertEquals(count($pids) + 1, substr_count($clause, 'pid='));
	}

	/**
	 * @test
	 */
	public function getPageClauseWithAdditionalPids() {
		$pids = $this->getPageUidWithinATree();

		$method = new ReflectionMethod(
			'Tx_RssOutput_Domain_Repository_RecordRepository', 'getPageClause'
		);

		$method->setAccessible(TRUE);

		$this->configuration['additionalPids'] = '1,2';
		$clause = $method->invokeArgs(new Tx_RssOutput_Domain_Repository_RecordRepository(),
			array($pids, $this->configuration));

		$this->assertEquals(count($pids) + 3, substr_count($clause, 'pid='));
	}

	/**
	 * @test
	 */
	public function getClauseWithMinimumConfiguration() {

		$method = new ReflectionMethod(
			'Tx_RssOutput_Domain_Repository_RecordRepository', 'getClause'
		);

		$method->setAccessible(TRUE);

		$clause = $method->invokeArgs(new Tx_RssOutput_Domain_Repository_RecordRepository(),
			array($this->configuration));

		$defaultPart = tslib_cObj::enableFields($this->configuration['table']);
		$expectedClause = "1=1 " . $defaultPart . ' AND tx_rssoutput_includeinfeed = 1';

		$this->assertEquals(trim($clause), trim($expectedClause));
	}

	/**
	 * @test
	 */
	public function getPageUidWithinATree() {
		// Generate a fake tree structure
		$rootPageId = $this->createFakeTree();

		$method = new ReflectionMethod(
			'Tx_RssOutput_Domain_Repository_RecordRepository', 'getAllPages'
		);

		$method->setAccessible(TRUE);

		$pids = $method->invokeArgs(new Tx_RssOutput_Domain_Repository_RecordRepository(),
			array($rootPageId, array()));

		$this->assertEquals(4, count($pids));
		$this->assertTrue($pids[0]['uid'] > 0);
		return $pids;
	}

	/**
	 * @test
	 */
	public function findFakeRecordsWithEmptyReturn() {
		$this->createFakeTree();
		$records = $this->fixture->find($this->configuration);
		$this->assertEmpty($records);
	}

	/**
	 * @test
	 */
	public function findRecordsWithEmptyReturnBecauseOfIncludeAllEqualFalse() {
		$rootPageId = $this->createFakeTree();

		$recordData = array(
			'header' => 'ITEST',
			'bodytext' => 'ITEST',
			'tx_rssoutput_includeinfeed' => '0',
		);

		$this->testingFramework->createContentElement($rootPageId, $recordData);
		$records = $this->fixture->find($this->configuration);
		$this->assertEmpty($records);
	}

	/**
	 * @test
	 */
	public function findRecordsWithValidReturnBecauseOfIncludeAllEqualTrue() {
		$rootPageId = $this->createFakeTree();

		$recordData = array(
			'header' => 'ITEST',
			'bodytext' => 'ITEST',
			'tx_rssoutput_includeinfeed' => '1',
		);

		$this->testingFramework->createContentElement($rootPageId, $recordData);
		$records = $this->fixture->find($this->configuration);
		$this->assertEquals(1, count($records));
	}

	/**
	 * @test
	 * @expectedException Tx_RssOutput_Exception_MissingConfigurationException
	 */
	public function missingTableException() {
		unset($this->configuration['table']);
		$this->fixture->find($this->configuration);
	}

	/**
	 * @test
	 * @expectedException Tx_RssOutput_Exception_MissingConfigurationException
	 */
	public function missingPidRootException() {
		unset($this->configuration['pidRoot']);
		$this->fixture->find($this->configuration);
	}

	/**
	 * Generate a fake root tree for internal use
	 */
	protected function createFakeTree() {

		// Create datastructure
		$pageId = $this->testingFramework->createFrontEndPage();
		$this->testingFramework->createFrontEndPage($pageId);
		$subPageId = $this->testingFramework->createFrontEndPage($pageId);
		$this->testingFramework->createFrontEndPage($subPageId);
		$this->testingFramework->createFrontEndPage($subPageId);

		$this->configuration['rootPid'] = $pageId;
		return $pageId;
	}
}

?>