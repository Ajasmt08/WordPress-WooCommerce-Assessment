<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */  

add_action( 'wp_enqueue_scripts', 'astra_child_style' );
				function astra_child_style() {
					wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
					wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
				}
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );


/**
 * Your code goes below.
 */

add_action( 'woocommerce_get_price_html', 'mrm_add_message_after_price' );

function mrm_add_message_after_price( $price ) {
    if ( is_product() ) {
        $message = '<p style="color: #555;font-weight: bold;margin-top: 10px;padding: 10px;background: var(--ast-global-color-7);color: #fff;">Bulk order pricing available. Contact us for quotation.</p>';
        return $price . $message;
    }
    return $price;
}