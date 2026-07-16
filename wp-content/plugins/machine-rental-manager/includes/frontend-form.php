<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', 'mrm_register_frontend_scripts' );
function mrm_register_frontend_scripts() {
    wp_register_script( 
        'mrm-frontend-js', 
        plugin_dir_url( dirname(__FILE__) ) . 'assets/js/machine-rental.js', 
        array(), 
        '1.5', 
        true 
    );

    // Pass the REST URL and our standard WP REST Nonce to the JavaScript
    wp_localize_script( 'mrm-frontend-js', 'mrm_api_config', array(
        'rest_url' => esc_url_raw( rest_url() ),
        'nonce'    => wp_create_nonce( 'wp_rest' ) 
    ) );
}

add_shortcode( 'rental_request_form', 'mrm_frontend_form' );

function mrm_frontend_form() {
    wp_enqueue_script( 'mrm-frontend-js' );

    ob_start();
    ?>
    
    <div id="mrm-message" style="margin-bottom: 15px;"></div>

    <form id="mrm-rental-form" novalidate>
        <p>
            <label>Full Name *</label><br>
            <input type="text" id="mrm_full_name">
        </p>
        <p>
            <label>Email *</label><br>
            <input type="email" id="mrm_email">
        </p>
        <p>
            <label>Phone Number *</label><br>
            <input type="number" id="mrm_phone">
        </p>
        
        <p>
            <label>Rental Period *</label><br>
            <span style="display:inline-block; margin-right:10px;">
                <label style="font-size: 0.9em; color:#666;">From:</label><br>
                <input type="date" id="mrm_date_from">
            </span>
            <span style="display:inline-block;">
                <label style="font-size: 0.9em; color:#666;">To:</label><br>
                <input type="date" id="mrm_date_to">
            </span>
        </p>

        <p>
            <label>Location *</label><br>
            <input type="text" id="mrm_location">
        </p>
        <p>
            <label>Machine Type *</label><br>
            <input type="text" id="mrm_machine_type" placeholder="e.g. Snack Vending Machine">
        </p>
        <p>
            <input type="submit" id="mrm_submit_btn" value="Submit Request">
        </p>
    </form>
    
    <?php
    return ob_get_clean();
}