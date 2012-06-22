<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_rssoutput_pi1.php','_pi1','list_type',0);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'Feed' => 'index,advertize',
	),
	// non-cacheable actions
	array(
		'Feed' => 'index',

	)
);

// Register cache 'rssoutput_cache'
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache'] = array();
}

// Register the cache table to be deleted when all caches are cleared
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearAllCache_additionalTables']['tx_rssoutput_cache'] = 'tx_rssoutput_cache';

// Define string frontend as default frontend, this must be set with TYPO3 4.5 and below
// and overrides the default variable frontend of 4.6
if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache']['frontend'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache']['frontend'] = 't3lib_cache_frontend_StringFrontend';
}
if (t3lib_div::int_from_ver(TYPO3_version) < '4006000') {
    // Define database backend as backend for 4.5 and below (default in 4.6)
    if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache']['backend'])) {
        $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache']['backend'] = 't3lib_cache_backend_DbBackend';
    }

    // Define data table for 4.5 and below (obsolete in 4.6)
    if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache']['options'])) {
        $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache']['options'] = array();
    }
    if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache']['options']['cacheTable'])) {
        $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache']['options']['cacheTable'] = 'tx_rssoutput_cache';
    }
	if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache']['options']['tagsTable'])) {
		$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['rssoutput_cache']['options']['tagsTable'] = 'tx_rssoutput_cache_tags';
	}
}


?>