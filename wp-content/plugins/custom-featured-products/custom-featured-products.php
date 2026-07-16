<?php
/**
 * Plugin Name:       Custom Featured Products
 * Description:       A standalone module that dynamically pulls 3 WooCommerce featured products using [custom_fetaured_products].
 * Version:           1.0.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function fpm_enqueue_styles() {
    wp_enqueue_style(
        'fpm-styles',
        plugins_url( 'css/style.css', __FILE__ ),
        array(),
        '1.0.2'
    );
}
add_action( 'wp_enqueue_scripts', 'fpm_enqueue_styles' );

function fpm_products_shortcode() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return '<p style="text-align:center;">WooCommerce is not active. Please activate WooCommerce to display products.</p>';
    }

    $args = array(
        'featured' => true,
        'limit'    => 3,
        'status'   => 'publish',
    );
    $featured_products = wc_get_products( $args );

    if ( empty( $featured_products ) ) {
        return '<p style="text-align:center;">No featured products found. Please mark some products as "Featured" in WooCommerce.</p>';
    }

    ob_start();
    ?>
    <div class="fp-section-wrapper">
        <h2 class="fp-main-title">Featured Products</h2>
        
        <div class="fp-grid-container">
            <?php foreach ( $featured_products as $product ) : 
                
                $product_url   = $product->get_permalink();
                $product_title = $product->get_name();
                $product_price = $product->get_price_html();
                
                $image_id = $product->get_image_id();
                $product_image = $image_id ? wp_get_attachment_image_url( $image_id, 'medium_large' ) : 'https://via.placeholder.com/400x250';
                
                $product_desc = $product->get_short_description();
                if ( empty( $product_desc ) ) {
                    $product_desc = $product->get_description();
                }
                $product_desc = wp_trim_words( $product_desc, 15, '...' );
            ?>
            
            <a href="<?php echo esc_url( $product_url ); ?>" class="fp-card">
                <img loading="lazy" src="<?php echo esc_url( $product_image ); ?>" alt="<?php echo esc_attr( $product_title ); ?>" class="fp-image">
                <div class="fp-content">
                    <h3 class="fp-title"><?php echo esc_html( $product_title ); ?></h3>
                    
                    <div class="fp-price"><?php echo wp_kses_post( $product_price ); ?></div>
                    
                    <p class="fp-desc"><?php echo wp_kses_post( $product_desc ); ?></p>
                </div>
            </a>
            
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode( 'custom_fetaured_products', 'fpm_products_shortcode' );