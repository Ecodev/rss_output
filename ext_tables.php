<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$tempColumns = Array(
	'tx_rssoutput_includeinfeed' => Array(
		'exclude' => 1,
		'label' => 'LLL:EXT:rss_output/Resources/Private/Language/locallang_db.xml:tt_content.tx_rssoutput_includeinfeed',
		'config' => Array(
			'type' => 'check',
			"default" => "1",
		)
	),
);

t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('tt_content', 'tx_rssoutput_includeinfeed', '', 'after:hidden');


t3lib_extMgm::addPlugin(Array('LLL:EXT:rss_output/Resources/Private/Language/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'RSS Output'
);

#t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","RSS Feed Display");


t3lib_extMgm::allowTableOnStandardPages('tx_rssoutput_feed');
$TCA['tx_rssoutput_feed'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:rss_output/Resources/Private/Language/locallang_db.xml:tx_rssoutput_feed',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Feed.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_rssoutput_feed.png'
	)
);

?>