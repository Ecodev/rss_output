<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_rssoutput_feed'] = array(
	'ctrl' => $TCA['tx_rssoutput_feed']['ctrl'],
	#'interface' => array(
	#	'showRecordFieldList'	=> 'resources,number',
	#),
	'types' => array(
		'1' => array('showitem' => 'hidden, title, description, number_of_items, configuration'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.php:LGL.default_value', 0)
				),
			)
		),
		'l18n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_rssoutput_feed',
				'foreign_table_where' => 'AND tx_rssoutput_feed.uid=###REC_FIELD_l18n_parent### AND tx_rssoutput_feed.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
			)
		),
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:rss_output/Resources/Private/Language/locallang_db.xml:tx_rssoutput_feed.title',
			'config' => array(
				'type' => 'input',
				'size' => 255,
				'eval' => 'trim',
			),
		),
		'description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:rss_output/Resources/Private/Language/locallang_db.xml:tx_rssoutput_feed.description',
			'config' => array(
				'type' => 'input',
				'size' => 255,
				'eval' => 'trim',
			),
		),
		'number_of_items' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:rss_output/Resources/Private/Language/locallang_db.xml:tx_rssoutput_feed.number_of_items',
			'config' => array(
				'type' => 'input',
				'default' => '10',
				'size' => 255,
				'eval' => 'int,require',
			),
		),
		'configuration' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:rss_output/Resources/Private/Language/locallang_db.xml:tx_rssoutput_feed.configuration',
			'config' => array(
				'type' => 'text',
				'default' => '
# mandatory name of the table to get information from
table: tt_content

# mandatory for tt_content: add a pid root, default null
rootPid: 1

# mandatory hostname
baseURL: http://myhost/

# optional for all: number of items in the feed, default 10
numberOfItems: 10

# optional for tt_content: Override the check to include only marked records, default false.
includeAll: true
',
				'eval' => 'trim',
			),
		),
	),
);
?>