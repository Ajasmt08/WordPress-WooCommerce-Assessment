<?php
/**
 * Plugin Name: Machine Rental Manager
 * Description: Manages machine rental requests via a custom frontend form and REST API.
 * Version: 1.5
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Define a constant for the plugin path to easily require files
define( 'MRM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Include the separated functional files
require_once MRM_PLUGIN_DIR . 'includes/db-setup.php';
require_once MRM_PLUGIN_DIR . 'includes/frontend-form.php';
require_once MRM_PLUGIN_DIR . 'includes/admin-page.php';
require_once MRM_PLUGIN_DIR . 'includes/api-endpoint.php';

// Trigger the database creation on activation
register_activation_hook( __FILE__, 'mrm_create_db_table' );

// DEV ONLY — Local environment without SSL:
// Uncomment the line below to allow WordPress Application Passwords to work over
// plain HTTP.
// add_filter( 'wp_is_application_passwords_available', '__return_true' );