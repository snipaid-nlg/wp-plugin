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
    $api_key = $parameters['api_key'];
    
    if ($api_key == get_option('snipaid_api_key')) {
        	// Set the status of the response to OK
        $data['status'] = 'OK';
​
        // Extract the title, teaser and full text from the parameters
        $title = $parameters['title'];
        $teaser = $parameters['teaser'];
        $ftext = $parameters['fulltext'];
    
        // Insert a new post into the WordPress database with the specified title, content, excerpt, status, and post type
        wp_insert_post(
            array(
                'post_title' => $title,
                'post_content' => $ftext,
                'post_excerpt' => $teaser,
                'post_status' => get_option('snipaid_options')['post_status'],
                'post_type' => 'post'
            )
        );
        
    } else {
        
        $data['status'] = 'Failed';
        // $data['message'] = 'Parameters Missing!';
        
    }
	// Return the response
	return $data;
}
​
function generate_and_save_api_key() {
    // Generate API Key
    $api_key = md5(uniqid(rand(), true));
​
    // Save API Key to database
    update_option('snipaid_api_key', $api_key);
​
    return $api_key;
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
// Adds a "Settings" Link in Plugin Overview
add_filter( 'plugin_action_links_snipaid/snipaid.php', 'snipaid_settings_link' );
function snipaid_settings_link( $links ) {
	// Build and escape the URL.
	$url = esc_url( add_query_arg(
		'page',
		'snipaid',
		get_admin_url() . 'options-general.php'
	) );
	// Create the link.
	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}
​
// Renders the content of the settings page.
function snipaid_settings_content() {
    if (isset($_POST['generate_api_key'])) {
        $api_key = generate_and_save_api_key();
    } else {
        $api_key = get_option('snipaid_api_key');
    }
​
    $webhook_url = rest_url( '/snipaid/v1/receive-callback' ) . '?api_key=' . $api_key;
​
    if (isset($_POST['save_settings'])) {
        update_option('snipaid_options', [
          'post_status' => sanitize_text_field($_POST['snipaid_options']['post_status'])
        ]);
      }
      $options = get_option('snipaid_options', ['post_status' => 'draft']);
      $status = $options['post_status'];
    
    ?>
    
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    </div>
​
    <div class="wrap">
        <form method="post">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Post Settings</th>
                        <td>
                            <select name="snipaid_options[post_status]">
                                <option value="draft" <?php selected( $status, 'draft' ); ?>>Draft</option>
                                <option value="publish" <?php selected( $status, 'publish' ); ?>>Publish</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" name="save_settings" class="button" value="Save Changes">
        </form>
    </div>
​
    <div class="wrap">
        <form method="post">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">API Key</th>
                        <td>
                            <input type="text" name="api_key" value="<?php echo $api_key; ?>" class="regular-text" readonly />
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" name="generate_api_key" class="button" value="Generate API Key">
        </form>
    </div>
​
    <div class="wrap">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Webhook URL</th>
                        <td>
                            <input type="text" name="webhook_url" value="<?php echo esc_attr($webhook_url); ?>" class="regular-text" readonly />
                        </td>
                    </tr>
                </tbody>
            </table>
            <button id="copy-webhook-url" type="button" class="button">Copy to Clipboard</button>
    </div>
    <script>
        document.getElementById("copy-webhook-url").addEventListener('click', function() {
        document.querySelector('input[name="webhook_url"]').select();
        document.execCommand('copy');
});
</script>
​
    <?php
}