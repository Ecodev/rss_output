#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_rssoutput_includeinfeed int(11) DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_rssoutput_feed'
#
CREATE TABLE tx_rssoutput_feed (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(250) DEFAULT '' NOT NULL,
	description varchar(250) DEFAULT '' NOT NULL,
	number_of_items int(11) unsigned DEFAULT '0' NOT NULL,
	configuration text NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,

	is_dummy_record int(1) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tt_content'
#
CREATE TABLE tx_rssoutput_cache (
    id int(11) unsigned NOT NULL auto_increment,
    identifier varchar(250) DEFAULT '' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    content mediumblob,
    lifetime int(11) unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (id),
    KEY cache_id (identifier)
) ENGINE=InnoDB;


#
# TABLE structure FOR TABLE 'tx_rssoutput_cache_tags'
#
CREATE TABLE tx_rssoutput_cache_tags (
    id int(11) unsigned NOT NULL auto_increment,
    identifier varchar(250) DEFAULT '' NOT NULL,
    tag varchar(250) DEFAULT '' NOT NULL,
    PRIMARY KEY (id),
    KEY cache_id (identifier),
    KEY cache_tag (tag)
) ENGINE=InnoDB;