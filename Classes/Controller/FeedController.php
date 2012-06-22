<?php

/***************************************************************
 *  Copyright notice
 *  (c) 2011 Fabien Udriot <fabien.udriot
 * @ecodev.ch>, Ecodev
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/** @define "t3lib_extMgm::extPath('rss_output')" "typo3conf/ext/rss_output/" */
require_once(t3lib_extMgm::extPath('rss_output') . 'Resources/Private/PHP/Symphony/sfYaml.php');
require_once(t3lib_extMgm::extPath('rss_output') . 'Resources/Private/PHP/Symphony/sfYamlParser.php');

/**
 * @package rss_output
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later

 */
class Tx_RssOutput_Controller_FeedController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_RssOutput_Domain_Repository_RecordRepository
	 */
	protected $recordRepository;

	/**
	 * @var Tx_RssOutput_Domain_Repository_FeedRepository
	 */
	protected $feedRepository;

	/**
	 * configuration
	 *
	 * @var array
	 */
	protected $configuration;

	/**
	 * Frontend Configuration
	 *
	 * @var array
	 */
	protected $frontendConfiguration;

	/**
	 * inject Record Repository
	 *
	 * @param Tx_RssOutput_Domain_Repository_RecordRepository $recordRepository
	 * @return void
	 */
	public function injectRecordRepository(Tx_RssOutput_Domain_Repository_RecordRepository $recordRepository) {
		$this->recordRepository = $recordRepository;
	}

	/**
	 * inject Feed Repository
	 *
	 * @param Tx_RssOutput_Domain_Repository_FeedRepository $feedRepository
	 * @return void
	 */
	public function injectFeedRepository(Tx_RssOutput_Domain_Repository_FeedRepository $feedRepository) {
		$this->feedRepository = $feedRepository;
	}

	/**
	 * Initializes default settings for all actions.
	 */
	public function initializeAction() {
		// TypoScript configuration
		$this->frontendConfiguration = $GLOBALS['TSFE']->tmpl->setup['config.'];

		// Perhaps a better way to do that ...
		$configuration = $this->configurationManager->getConfiguration('FullTypoScript');
		$this->configuration = $configuration['page_9090.']['10.']['10.'];

		if (empty($this->configuration)) {
			throw new Exception('Exception 1323945007: configuration empty. Check existence of TS key "page_9090"', 1323945007);
		}
		//		$this->contentObjectData = $this->configurationManager->getcontentObject()->data;
	}

	/**
	 * Return language string
	 *
	 * @return string
	 */
	protected function getLanguage() {

		// search for language configuration
		$language = '';
		if (!empty($this->frontendConfiguration['language'])) {
			$language = $this->frontendConfiguration['language'];
		} elseif ($this->frontendConfiguration['sys_language_uid']) {
			$languageInfo = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'sys_language', 'uid = ' . intval($this->frontendConfiguration['sys_language_uid']));

			if (!empty($languageInfo)) {
				$language = $languageInfo['title'];
			}
		}
		return $language;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function indexAction() {

		$uid = (int)t3lib_div::_GET('uid');

		// Possibly overrides value
		if (!empty($this->configuration['uid'])) {
			$uid = (int)$this->configuration['uid'];
		}

		// Makes sure the uid is valid
		if ($uid < 1) {
			throw new Exception('Exception 1323921433: no uid parameter given in the URL.', 1323921433);
		}

		$feed = $this->feedRepository->findByUid($uid);

		$yaml = new sfYamlParser();
		$configuration = $yaml->parse($feed['configuration']);
		$configuration['sys_language_uid'] = $this->getLanguage(); // Add dynamic configuration
		$this->checkConfiguration($configuration);

		$entries = $this->recordRepository->find($configuration);

		$this->view->assign('title', $feed['title']);
		$this->view->assign('description', $feed['description']);
		$this->view->assign('language', $this->getLanguage());
		$this->view->assign('entries', $entries);
		$this->view->assign('date', date('c'));
		$this->view->assign('url', t3lib_div::getIndpEnv('REQUEST_URI'));
		$this->view->assign('configuration', $configuration);
		//$host = !empty($configuration['host']) ? $configuration['host'] : t3lib_div::getIndpEnv('TYPO3_SITE_URL');
		$this->view->assign('host', rtrim($configuration['baseURL'], '/'));
	}

	/**
	 * Check whether the configuration is correct
	 *
	 * @param array $configuration
	 */
	protected function checkConfiguration(array $configuration = array()) {
		if (empty($configuration['baseURL'])) {
			throw new Tx_RssOutput_Exception_InvalidConfigurationException('Exception 1325478745: missing baseURL setting ', 1325478745);
		}
	}

	/**
	 * Returns HTML to advertize a feed on the Frontend
	 *
	 * @return string
	 */
	public function advertizeAction() {
		// Init variables
		$result = '';
		$yaml = new sfYamlParser();

		// Get settings
		$settings = $this->configurationManager->getConfiguration('Settings');

		$listOfUids = explode(',', $settings['listOfUid']);
		foreach ($listOfUids as $uid) {
			$feed = $this->feedRepository->findByUid($uid);

			$configuration = $yaml->parse($feed['configuration']);
			$this->checkConfiguration($configuration);

			$feedUrl = $configuration['baseURL'];

			/** @var $contentObject tslib_cObj */
			$config['returnLast'] = 'url';
			$config['parameter.']['data'] = 'leveluid:0';
			$config['additionalParams'] = '&type=9090&uid=' . $uid;
			$contentObject = $this->configurationManager->getcontentObject();
			$feedUrl =

			$result .= '<link rel="alternate"
				type="application/atom+xml"
				title="' . $feed['title'] . '"
				href="' . $contentObject->typolink('', $config) . '" />' . chr(10);
		}

		return $result;
	}
}

?>
