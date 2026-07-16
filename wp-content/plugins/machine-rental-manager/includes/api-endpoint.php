<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'rest_api_init', 'mrm_register_api_endpoint' );

function mrm_register_api_endpoint() {
    register_rest_route( 'machine-rental/v1', '/submit', array(
        'methods'  => 'POST',
        'callback' => 'mrm_api_submit_request',
        'permission_callback' => '__return_true'
    ) );
}

function mrm_api_submit_request( WP_REST_Request $request ) {
    global $wpdb;

    // 1. Check for local website Nonce using the standard WP REST structure
    $nonce = $request->get_header( 'x-wp-nonce' );
    $is_valid_nonce = wp_verify_nonce( $nonce, 'wp_rest' );

    // 2. Check for third-party API Token (Bearer / Application Password)
    $is_valid_api_user = is_user_logged_in();

    // 3. The Gatekeeper: Reject if BOTH fail
    if ( ! $is_valid_nonce && ! $is_valid_api_user ) {
        return new WP_Error( 'forbidden', 'Unauthorized: Invalid Nonce or Missing API Credentials.', array( 'status' => 401 ) );
    }
    
    $params = $request->get_json_params() ?: $request->get_body_params();

    // Sanitize parameters
    $name     = sanitize_text_field( $params['full_name'] ?? '' );
    $email    = sanitize_email( $params['email'] ?? '' );
    $phone    = sanitize_text_field( $params['phone'] ?? '' );
    $period   = sanitize_text_field( $params['rental_period'] ?? '' );
    $location = sanitize_text_field( $params['location'] ?? '' );
    $machine  = sanitize_text_field( $params['machine_type'] ?? '' );

    // 1. Validate Name
    if ( empty($name) || strlen($name) > 100 ) {
        return new WP_Error( 'invalid_name', 'Please provide a valid full name (max 100 characters).', array( 'status' => 400 ) );
    }

    // 2. Validate Email
    if ( empty($email) || strlen($email) > 100 || ! is_email( $email ) ) {
        return new WP_Error( 'invalid_email', 'Please provide a valid, correctly formatted email address (max 100 characters).', array( 'status' => 400 ) );
    }

    // 3. Validate Phone Number (Force it to strip non-numeric characters, then check length)
    $clean_phone = preg_replace('/[^0-9]/', '', $phone);
    if ( empty($clean_phone) || strlen($clean_phone) < 8 || strlen($clean_phone) > 13 ) {
        return new WP_Error( 'invalid_phone', 'Please provide a valid phone number containing between 8 and 13 digits.', array( 'status' => 400 ) );
    }

    // 4. Validate Rental Period
    if ( empty($period) || strlen($period) > 100 ) {
        return new WP_Error( 'invalid_period', 'Please provide a valid rental period (max 100 characters).', array( 'status' => 400 ) );
    }

    // 5. Validate Location
    if ( empty($location) || strlen($location) > 100 ) {
        return new WP_Error( 'invalid_location', 'Please provide a valid location (max 100 characters).', array( 'status' => 400 ) );
    }

    // 6. Validate Machine Type
    if ( empty($machine) || strlen($machine) > 100 ) {
        return new WP_Error( 'invalid_machine', 'Please specify a machine type (max 100 characters).', array( 'status' => 400 ) );
    }

    // All validation passed, insert into DB
    $table_name = $wpdb->prefix . 'machine_rentals';
    $inserted = $wpdb->insert( $table_name, array(
        'name'          => $name,
        'email'         => $email,
        'phone'         => $clean_phone,
        'rental_period' => $period,
        'location'      => $location,
        'machine_type'  => $machine
    ) );

    if ( $inserted ) {
        return new WP_REST_Response( array( 'success' => true, 'message' => 'Rental request received successfully.', 'id' => $wpdb->insert_id ), 201 );
    } else {
        $error_message = $wpdb->last_error ? $wpdb->last_error : 'Unknown database error.';
        error_log( 'Machine Rental Manager DB Error: ' . $error_message );
        
        return new WP_Error( 'db_error', 'A database error occurred. Failed to save request.', array( 'status' => 500 ) );
    }
}