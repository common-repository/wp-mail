<?php if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$tbl = $wpdb->prefix.'mail_system';
require_once(ABSPATH.'wp-admin/includes/upgrade.php');
            $sql = "CREATE TABLE IF NOT EXISTS ".$tbl." (
			  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
			  `msg_from` int(11) DEFAULT NULL,
			  `msg_to` int(11) DEFAULT NULL,
			  `msg_subject` varchar(500) DEFAULT NULL,
			  `msg_message` varchar(5000) DEFAULT NULL,
			  `msg_attachment` varchar(255) DEFAULT NULL,
			  `msg_is_draft` int(11) DEFAULT NULL,
			  `msg_is_seen` int(11) DEFAULT NULL,
			  `msg_is_trashed_by_from` int(11) DEFAULT NULL,
			  `msg_is_trashed_by_to` int(11) DEFAULT NULL,
			  `msg_date` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`msg_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
			  dbDelta($sql);