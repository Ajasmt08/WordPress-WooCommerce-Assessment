<?php

/**
 * Plugin Name: Store Locator
 * Version: 1.2.0
 * Plugin URI: https://locatestore.com/
 * Description: Create a store locator for your website in minutes. Add all the store locations in google sheets and embed map on your website. There is no coding involved here.
 * Author: Micro.company
 * Author URI: https://micro.company/
 * License: GPLv2 or later
 */

/**
 * Display instructional notice on top of dashboard pages.
 * Removed when user dismisses the notice.
 */
// exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$storelocator_active_plugin = [];
$base = plugin_basename(__FILE__);
$wp_plugins_dir = defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : $wp_content_dir . '/plugins';
if (is_admin()) {
    if (!function_exists('get_plugin_data')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $storelocator_active_plugin[$base] = get_plugin_data($wp_plugins_dir . '/' . $base, true, false);
}

define('STLR_PLUGIN_DIR', str_replace('\\', '/', dirname(__FILE__)));
define('STLR_DIR_URL', plugin_dir_url(__FILE__));

function storelocator_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=storelocator">Settings</a>';
    $support_link = '<a href="https://reach.at/storelocator" target="_blank">Support</a>';

    array_push($links, $settings_link);
    array_push($links, $support_link);

    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter(
    "plugin_action_links_$plugin",
    'storelocator_settings_link'
);



function storelocator_activation_redirect($plugin)
{
    if ($plugin == plugin_basename(__FILE__)) {
        exit(wp_redirect(admin_url('admin.php?page=storelocator')));
    }
}
add_action(
    'activated_plugin',
    'storelocator_activation_redirect'
);



function storelocator_getting_started_notice()
{

    global $current_user;
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */
    if (!get_user_meta($user_id, 'storelocator_getting_started_notice') && !(isset($_GET['page']) && $_GET['page'] == 'storelocator')) {

        printf(__('<div class="notice" style="display: flex;flex-direction:column;gap:10px;padding:20px;">
        <a href="https://locatestore.com" class="logo" ><img src="%2$s" width="150px"   alt="Storelocator logo"/></a>
        <div>
            <h4 style="margin: 0;">Getting started with your Store Locator 🚀</h4>
        
            <ol>
                <li>Install the <a href="https://workspace.google.com/marketplace/app/store_locator/734551689349" target="_blank" rel="noreferrer">Google marketplace addon.</a></li>
                <li><a href="https://sheets.new" target="_blank" rel="noreferrer">Create a new Google Sheet</a> and go to <em>Extensions > Store Locator > Get started</em>.</li>
                <li>Copy the Store Locator URL / shortcode and visit <a href="options-general.php?page=storelocator">plugin settings</a>.</li>
            </ol>
        </div>
        <a href="%1$s">Dismiss</a>
    </div>'), '?storelocator_notice_ignore=1',STLR_DIR_URL . "storelocator-logo.png");
    }
}
add_action('admin_notices', 'storelocator_getting_started_notice');


function storelocator_notice_ignore()
{
    global $current_user;
    $user_id = $current_user->ID;
    /* If user clicks to ignore the notice, add that to their user meta */
    if (isset($_GET['storelocator_notice_ignore']) && '1' == $_GET['storelocator_notice_ignore']) {
        add_user_meta($user_id, 'storelocator_getting_started_notice', 'true', true);
    }
}
add_action('admin_init', 'storelocator_notice_ignore');

/**
 * Convert shortcode into embedded iframe.
 */


function storelocator_embed_shortcode($atts)
{
    $atts = array_change_key_case((array) $atts, CASE_LOWER);
    extract(shortcode_atts(array(
        'id' => '',
        'width' => '100%',
        'height' => "600"
    ), $atts));
    if (true || !($id == '')) {
        // add source = wp
        return '<iframe src="' . esc_url("https://locatestore.com/" . $id . "?source=wordpress") . '"  allow="geolocation" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" frameBorder="0" allowfullscreen style="width:100% !important;max-width:100% !important;margin:10px;"></iframe>';
    } else {
        return "⚠ Please enter a valid storelocator id.";
    }
}
add_shortcode('storelocator', 'storelocator_embed_shortcode');

/**
 * Register OEmbed config. This automatically loads the iframe from plain url.
 */

wp_oembed_add_provider('https://locatestore.com/*', "https://locatestore.com/services/oembed");

/**
 * Automatically generate storelocator page.
 */

function storelocator_check_page_existence($page_slug)
{
    $page = get_page_by_path($page_slug, OBJECT, 'page');

    return isset($page);
}
function add_storelocator_page($url, $path)
{
    try {
        if (storelocator_check_page_existence($path)) throw new Exception("Page already exists");
        $storelocator_page = array(
            'post_title'    => 'Store Locator',
            'post_name' => $path,
            'post_content'  => '[storelocator id="' . substr($url, 24) . '"]',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'page',
        );
        wp_insert_post($storelocator_page, true);
        update_option("storelocator_url", $url);
        update_option("storelocator_path", $path);

        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Create settings menu
 */

// Refer below link to understand how to add multiple settings section to same page
// http://www.mendoweb.be/blog/wordpress-settings-api-multiple-forms-on-same-page/
function storelocator_add_menu()
{

    add_menu_page(
        'StoreLocator', // page <title>Title</title>
        'StoreLocator', // menu link text
        'manage_options', // capability to access the page
        'storelocator', // page URL slug
        'storelocator_settings_template', // callback function /w content
        'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iNTEyLjAwMDAwMHB0IiBoZWlnaHQ9IjUxMi4wMDAwMDBwdCIgdmlld0JveD0iMCAwIDUxMi4wMDAwMDAgNTEyLjAwMDAwMCIKIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIG1lZXQiPgoKPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC4wMDAwMDAsNTEyLjAwMDAwMCkgc2NhbGUoMC4xMDAwMDAsLTAuMTAwMDAwKSIKZmlsbD0iIzAwMDAwMCIgc3Ryb2tlPSJub25lIj4KPHBhdGggZD0iTTIzODcgNTA0OSBjLTQ4NCAtNDcgLTk2NCAtMjg1IC0xMjYzIC02MjcgLTI0MyAtMjc4IC0zODcgLTYxOSAtNDM0Ci0xMDMxIC0xNiAtMTM1IC03IC00ODkgMTUgLTYzMSAxMDAgLTYzOSA0MTMgLTEzMTAgODkxIC0xOTA4IDI2NyAtMzM1IDYyNAotNjc3IDc4MiAtNzUwIDc3IC0zNSAxNzcgLTQ4IDI1NCAtMzQgMTM1IDI1IDIzOSA5NyA0ODIgMzMzIDczNCA3MTEgMTIyMQoxNjI2IDEzMTYgMjQ3NCAxNCAxMzAgMTIgNDMyIC00IDU1NSAtNTQgMzk5IC0xOTQgNzIyIC00MzAgOTkyIC0zNzggNDMzCi0xMDIxIDY4MyAtMTYwOSA2Mjd6IG00NDMgLTMxNSBjNjM2IC0xMDYgMTE1OSAtNTg5IDEzMTQgLTEyMTQgMzcgLTE1MSA0NgotMjI1IDQ2IC00MDIgMCAtMTEwIC02IC0xOTAgLTIwIC0yNzEgLTEwMCAtNTg5IC01MjggLTEwODcgLTEwOTUgLTEyNzYgLTE4NgotNjIgLTI3OCAtNzUgLTUxNSAtNzUgLTIzNyAwIC0zMjkgMTMgLTUxNSA3NSAtNTcwIDE5MCAtOTk1IDY4NyAtMTA5NiAxMjgyCi0xOCAxMDMgLTIwIDE1MSAtMTcgMzEyIDQgMTYxIDkgMjA4IDMyIDMwOSAxMTEgNDc5IDQwNyA4NjcgODM2IDEwOTQgMzEzIDE2NQo2NzcgMjI0IDEwMzAgMTY2eiIvPgo8cGF0aCBkPSJNMTU2NyA0MDg4IGMtMTkgLTE1IC0xOTUgLTM4NSAtMjA0IC00MjkgLTE1IC03OCAzNyAtMTU5IDEzMCAtMjAwCjQzIC0xOSA2OSAtMjMgMTUyIC0yMyAxNjEgMCAyMjggNDAgMjk4IDE3OSBsMzYgNzAgMSAtNDggYzAgLTI2IDcgLTYxIDE0IC03NgoxOSAtMzYgODAgLTkwIDEyMyAtMTA3IDkwIC0zOCAyMzYgLTMzIDMyMSAxMCA1MSAyNiAxMTIgODYgMTEyIDExMSAwIDggNSAxNQoxMCAxNSA2IDAgMTAgLTcgMTAgLTE1IDAgLTI0IDYxIC04NSAxMTAgLTExMCAxMDMgLTUyIDI0NSAtNTEgMzQ0IDMgNjkgMzgKMTA2IDg3IDExNSAxNTQgbDcgNTMgMzIgLTY1IGMxOCAtMzUgNDUgLTc3IDYwIC05MyA1NiAtNTkgMTY1IC05MyAyNjcgLTg0CjE1NSAxNSAyNTUgOTMgMjU1IDIwMCAwIDM4IC0xODEgNDM2IC0yMDcgNDU1IC0xMyA5IC0yMjggMTIgLTk5MyAxMiAtNzY1IDAKLTk4MCAtMyAtOTkzIC0xMnoiLz4KPHBhdGggZD0iTTE3NjAgMzMyMCBjLTE0IC00IC00OSAtOCAtNzcgLTkgbC01MyAtMSAtMiAtNDIyIC0zIC00MjMgLTgyIC0zCi04MyAtMyAwIC04OSAwIC05MCAxMTAwIDAgMTEwMCAwIDAgOTAgMCA5MCAtODAgMCAtODAgMCAtMiA0MjMgLTMgNDIyIC02NSA2CmMtMzYgMyAtNzcgMTAgLTkyIDE0IGwtMjggNyAtMiAtNDMzIC0zIC00MzQgLTc0NSAwIC03NDUgMCAtMyA0MzMgYy0yIDMzNiAtNQo0MzIgLTE1IDQzMSAtNiAtMSAtMjMgLTUgLTM3IC05eiIvPgo8L2c+Cjwvc3ZnPgo=', // menu icon
        null // priority
    );
}
add_action('admin_menu', 'storelocator_add_menu');


function storelocator_settings_template()
{
    $storelocator_url = sanitize_text_field(get_option('storelocator_url_input'));
    $storelocator_path =  sanitize_text_field(get_option('storelocator_path_input'));

    $current_url = sanitize_text_field(get_option("storelocator_url"));
    $current_path = sanitize_text_field(get_option("storelocator_path"));


    $valid_url_pattern = "/^https:\/\/locatestore.com\/.{3,}$/i";
    $valid_old_url_pattern = "/^https:\/\/storelocator.site\/.{3,}$/i";
    $valid_path_pattern = "/^([A-Za-z0-9-_])+$/";

    $is_valid_url = (preg_match($valid_url_pattern, $storelocator_url) == 1) || (preg_match($valid_old_url_pattern, $storelocator_url) == 1);
    $is_valid_path = preg_match($valid_path_pattern, $storelocator_path) == 1;
    $has_path_changed = ($current_path !== $storelocator_path);
    $has_url_changed = ($current_url !== $storelocator_url);

    // Defaults to true. Is false only when page generation fails.
    $page_generated = true;


    if ($is_valid_url && $is_valid_path) {
        try {
            if ($has_path_changed || !storelocator_check_page_existence($current_path)) {
                $page_generated = add_storelocator_page($storelocator_url, $storelocator_path);
            } elseif ($has_url_changed) {
                $page = get_page_by_path($current_path, OBJECT, 'page');
                $page->post_content = '[storelocator id="' . substr($storelocator_url, 24) . '"]';
                wp_update_post($page, true);
                update_option("storelocator_url", $storelocator_url);
            }
        } catch (Exception $e) {
        }
    }

    $errors = array();

    if (!$is_valid_url && false !== get_option('storelocator_url_input')) array_push($errors, "Invalid store locator URL. Please enter a valid URL of the form <code>https://locatestore.com/&lt;id&gt;</code>");
    if (!$is_valid_path && false !== get_option('storelocator_path_input')) array_push($errors, "Invalid store locator path. Path names can only contain alphabets, digits and the characters <code>-</code> and <code>_</code> ");
    if (!$page_generated) array_push($errors, "Couldn't generate the store locator page. Are you sure the page doesn't already exist ?");



    require_once(STLR_PLUGIN_DIR . '/settings.php');
}

/*
 * Settings template
 */

function storelocator_plugin_settings_init()
{

    add_settings_section('storelocator-settings-section-generate-page', '', '', 'storelocator');

    add_settings_field('storelocator-url-input', 'Store Locator URL', 'storelocator_url_callback', 'storelocator', 'storelocator-settings-section-generate-page');
    register_setting('storelocator-settings-generate-page', 'storelocator_url_input', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));

    add_settings_field('storelocator-path-input', 'Wordpress page URL', 'storelocator_path_callback', 'storelocator', 'storelocator-settings-section-generate-page');
    register_setting('storelocator-settings-generate-page', 'storelocator_path_input', array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'));
}
add_action('admin_init', 'storelocator_plugin_settings_init');



function storelocator_url_callback()
{
    $storelocator_url = get_option('storelocator_url_input');

?>
    <input name="storelocator_url_input" class="regular-text" type="text" style="margin:0" placeholder="https://locatestore.com/<id>" value="<?php echo isset($storelocator_url) ? esc_attr($storelocator_url) : '' ?>" />
<?php
}

function storelocator_path_callback()
{
    $storelocator_path = get_option('storelocator_path_input');

?> <div style="display:flex;flex-wrap:wrap;"> <code style="display:flex;flex-direction:column;justify-content:center;border:1px solid gray;border-right:0;margin:0;">
            <div><?php echo get_site_url(); ?>/</div>
        </code>
        <input name="storelocator_path_input" class="regular-text" id="storelocator_path_input" type="text" style="max-width:147px;margin:0;" placeholder="storelocator" value="<?php echo esc_attr(get_option('storelocator_path_input') === false ? 'storelocator' : get_option('storelocator_path_input'));  ?>" />
    </div>
<?php
}

// For reset. Will be removed in production.

function storelocator_reset_handler()
{
    delete_option('storelocator_path_input');
    delete_option('storelocator_url_input');
    delete_option('storelocator_url');
    delete_option('storelocator_path');
    wp_die();
}
add_action('wp_ajax_reset', 'storelocatorreset_handler');




function storelocator_admin_enqueue_scripts()
{
    require_once(STLR_PLUGIN_DIR . '/feedback-form.php');
    wp_enqueue_style('storelocator-modal-css', plugin_dir_url(__FILE__) . 'css/modal.css');
    storelocator_add_feedback_form();
}
add_action('admin_enqueue_scripts', 'storelocator_admin_enqueue_scripts');

function storelocator_submit_uninstall_reason_action()
{
    global  $wp_version, $storelocator_active_plugin, $current_user;

    wp_verify_nonce($_REQUEST['storelocator_ajax_nonce'], 'storelocator_ajax_nonce');

    $reason_id = isset($_REQUEST['reason_id']) ? stripcslashes(sanitize_text_field($_REQUEST['reason_id'])) : '';
    $basename  = isset($_REQUEST['plugin']) ? stripcslashes(sanitize_text_field($_REQUEST['plugin'])) : '';

    if (empty($reason_id) || empty($basename)) {
        exit;
    }

    $reason_info = isset($_REQUEST['reason_info']) ? stripcslashes(sanitize_textarea_field($_REQUEST['reason_info'])) : '';
    if (!empty($reason_info)) {
        $reason_info = substr($reason_info, 0, 255);
    }
    $is_anonymous = isset($_REQUEST['is_anonymous']) && 1 == $_REQUEST['is_anonymous'];

    $options = array(
        'product'     => 'STORELOCATOR_WP_PLUGIN',
        'reason_id'   => $reason_id,
        'reason_info' => $reason_info,
    );

    if (!$is_anonymous) {


        $options['url']                  = get_site_url();
        $options['wp_version']           = $wp_version;
        $options['plugin_version']       = $storelocator_active_plugin[$basename]['Version'];

        $options['email'] = $current_user->data->user_email;
    }

    /* send data */

    wp_remote_post(
        "https://locatestore.com/services/wordpress/churn",
        array(
            'method'  => 'POST',
            'body'    => $options,
            'timeout' => 15,
        )
    );
    exit;
}
add_action('wp_ajax_storelocator_submit_uninstall_reason_action', 'storelocator_submit_uninstall_reason_action');


/**
 * Proper ob_end_flush() for all levels
 *
 * This replaces the WordPress `wp_ob_end_flush_all()` function
 * with a replacement that doesn't cause PHP notices.
 */
remove_action('shutdown', 'wp_ob_end_flush_all', 1);
add_action('shutdown', function () {
    while (@ob_end_flush());
});
