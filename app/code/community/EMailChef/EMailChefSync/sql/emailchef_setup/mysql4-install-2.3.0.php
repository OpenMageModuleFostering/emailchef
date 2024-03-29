<?php
/**
 * Install.
 */
$installer = $this;
$this->startSetup();

$installer->run('CREATE TABLE IF NOT EXISTS `emailchef_filter_hints` (
  `filter_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `hints` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`filter_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('emailchef/sync')};
    CREATE TABLE IF NOT EXISTS {$installer->getTable('emailchef/sync')} (
  `store_id` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `entity` varchar(100) NOT NULL,
  `job_id` int(11) NOT NULL,
  `needs_sync` tinyint(1) DEFAULT 1,
  `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `last_sync` datetime NULL,
  PRIMARY KEY (`customer_id`, `entity`, `job_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

$installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('emailchef/job')};
    CREATE TABLE IF NOT EXISTS {$installer->getTable('emailchef/job')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) DEFAULT NULL,
  `emailchefgroupid` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `queue_datetime` datetime NOT NULL,
  `start_datetime` datetime,
  `finish_datetime` datetime,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

$this->endSetup();
