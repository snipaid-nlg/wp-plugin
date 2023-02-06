<?php
/**
 * Plugin Name:     SnipAId
 * Plugin URI:      https://www.snipaid.tech/
 * Description:     SnipAId is an open source tool for generating text snippets from journalistic text.
 * Author:          Hannah Greven
 * Author URI:      https://github.com/hagreven
 * Text Domain:     snipaid
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Snipaid
 */
​
// prevent direct access to PHP files
defined( 'ABSPATH' ) or die( 'Error 404' );
​
​
/**
 * PLUGIN ACTIVATION
 */
​
register_activation_hook( __FILE__, 'snipaid_activation_hook' );
​
register_deactivation_hook(__FILE__, 'snipaid_deactivation_hook');
​
function snipaid_activation_hook() {
    set_transient( 'snipaid-show-notice', true, 5 );
    set_transient('snipaid-activation-redirect', true, 5 );
}
​
function snipaid_deactivation_hook() {
    // actions on plugin deactivation
}
​
add_action('admin_init', 'snipaid_redirect');
function snipaid_redirect() {
    if( get_transient( 'snipaid-activation-redirect' ) ) {
        if(!isset($_GET['activate-multi']))
        {
            wp_safe_redirect("options-general.php?page=snipaid");
        }
        delete_transient( 'snipaid-activation-redirect' );
    }
}
​
add_action( 'admin_notices', 'snipaid_notice' );
function snipaid_notice(){
    /* Check transient, if available display notice */
    if( get_transient( 'snipaid-show-notice' ) ){
        ?>
        <div class="updated notice is-dismissible">
            <p>Thank you for using this plugin! <strong>You are awesome</strong>.</p>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient( 'snipaid-show-notice' );
    }
}
​
/**
 * REST API ENDPOINT
 */
​
add_action( 'rest_api_init', function () {
  register_rest_route( 
    'snipaid/v1/', 
    'receive-callback', array(
        'methods' => 'POST',
        'callback' => 'snipaid_receive_callback',
  ) );
} );
​
function snipaid_receive_callback($request_data) {
	// Initialize an array to store the response
	$data = array();
	// Set the status of the response to OK
	$data['status'] = 'OK';
	
	// Get the parameters from the request data
	$parameters = $request_data->get_params();
​
	// Extract the title, teaser and full text from the parameters
	$title = $parameters['title'];
	$teaser = $parameters['teaser'];
	$ftext = $parameters['fulltext'];
​
	// Insert a new post into the WordPress database with the specified title, content, excerpt, status, and post type
	wp_insert_post(
		array(
			'post_title' => $title,
			'post_content' => $ftext,
			'post_excerpt' => $teaser,
			'post_status' => 'publish',
			'post_type' => 'post'
		)
	);
​
	// Return the response
	return $data;
}
​
/**
 * PLUGIN SETTINGS
 */
​
// Adds a submenu page under the "Settings" menu.
function snipaid_settings_page() {
    add_options_page(
        'SnipAId Settings',
        'SnipAId',
        'manage_options',
        'snipaid',
        'snipaid_settings_content'
    );
}
add_action('admin_menu', 'snipaid_settings_page');
​
// Renders the content of the settings page.
function snipaid_settings_content() {
    $webhook_url = rest_url( '/snipaid/v1/receive-callback' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="webhook_url">Webhook URL:</label>
                        </th>
                        <td>
                            <p><?php echo esc_attr($webhook_url); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>
    <?php
}
