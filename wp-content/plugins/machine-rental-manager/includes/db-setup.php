<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function mrm_create_db_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'machine_rentals';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(20) NOT NULL,
        rental_period varchar(100) NOT NULL,
        location varchar(100) NOT NULL,
        machine_type varchar(100) NOT NULL,
        status varchar(20) DEFAULT 'Pending' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}