<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Zara
 * @subpackage Zara/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Zara
 * @subpackage Zara/admin
 * @author     Your Name <email@example.com>
 */
class Zara_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $Zara    The ID of this plugin.
	 */
	private $Zara;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $Zara       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $Zara, $version ) {

		$this->Zara = $Zara;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->Zara, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->Zara, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function register_manage_posts_and_pages(){
		add_filter('manage_post_posts_columns', [$this,'misha_price_and_featured_columns'] );
		add_action('manage_posts_custom_column', [$this,'misha_populate_both_columns'], 10, 2 );
		add_action('wp_ajax_custom_row_action', [$this, 'handle_custom_row_action']);
	}

	function misha_price_and_featured_columns( $column_array ) {

		$column_array[ 'indexed_zara' ] = 'Zara';
	
		return $column_array;
	}
	function misha_populate_both_columns( $column_name, $post_id ){
		// echo "🗹";
		echo "⚠";
		if($column_name=='indexed_zara'){
			?>
            <div class="row-actions">
                <button 
                    onclick="performCustomAction(<?php echo esc_js($post_id); ?>)" 
                    class="button button-small custom-action-btn"
                    data-post-id="<?php echo esc_attr($post_id); ?>"
                >
                    Custom Action
                </button>
            </div>

            <script>
            function performCustomAction(postId) {
                // Client-side validation or preparation
                if (confirm('Do you want to perform this action?')) {
                    // Use AJAX to call WordPress action
                    jQuery.ajax({
                        url: ajaxurl, // WordPress AJAX endpoint
                        type: 'POST',
                        data: {
                            action: 'custom_row_action', // Must match wp_ajax_* hook
                            post_id: postId,
                            // Add any additional parameters
                            security: '<?php echo wp_create_nonce('custom_row_action_nonce'); ?>'
                        },
                        success: function(response) {
                            // Handle successful response
                            if (response.success) {
                                alert(response.data.message);
                                // Optional: Refresh page or update UI
                                location.reload();
                            } else {
                                alert('Action failed: ' + response.data);
                            }
                        },
                        error: function() {
                            alert('An error occurred');
                        }
                    });
                }
            }
            </script>
            <?php
		}
	}

	public function handle_custom_row_action() {
        // Security check
        check_ajax_referer('custom_row_action_nonce', 'security');

        // Capability check
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('You do not have sufficient permissions');
        }

        // Get post ID from AJAX request
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        if ($post_id <= 0) {
            wp_send_json_error('Invalid Post ID');
        }

        // Your custom logic here
        try {
            // Example: Call an external function or perform custom action
            $result = external_custom_function($post_id);

            // Send success response
            wp_send_json_success([
                'message' => 'Action completed successfully',
                'result' => $result
            ]);
        } catch (Exception $e) {
            // Send error response
            wp_send_json_error($e->getMessage());
        }

        // Always exit after sending JSON
        wp_die();
    }

}
