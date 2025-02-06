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

		wp_enqueue_style( $this->Zara, plugin_dir_url( __FILE__ ) . 'css/zara-admin.css', array(), $this->version, 'all' );

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

		add_filter('manage_post_posts_columns', [$this,'zara_sync_status_columns'] );
		add_action('manage_posts_custom_column', [$this,'zara_sync_status_row'], 10, 2 );

		add_filter('manage_page_posts_columns', [$this,'zara_sync_status_columns'] );
		add_action('manage_page_posts_custom_column', [$this,'zara_sync_status_row'], 10, 2 );
        
		add_action('wp_ajax_custom_row_action', [$this, 'handle_sync_row_action']);
	}

	function zara_sync_status_columns( $column_array ) {

		$column_array[ 'indexed_zara' ] = 'Zara';
	
		return $column_array;
	}
	function zara_sync_status_row( $column_name, $post_id ){
        $data = zara_get_data_by_post( $post_id );
        if ( $data && ! empty( $data->last_synced ) && $data->last_synced !== '0000-00-00 00:00:00' ) {
            echo '<div class="sync-info">🗹 Last Synced: ' . esc_html( $data->last_synced ) . '</div>';
        } else {
            echo '<div class="sync-info">⚠ Not Synced</div>';
        }
		if($column_name=='indexed_zara'){
			?>
            <div class="row-actions">
                <button 
                    id="sync-button-<?php echo esc_attr($post_id); ?>" 
                    onclick="performCustomAction(<?php echo esc_js($post_id); ?>)" 
                    class="button button-small custom-action-btn"
                    data-post-id="<?php echo esc_attr($post_id); ?>"
                >
                    <span class="button-text">Sync</span>
                    <span class="spinner" style="float: none; display: inline-block;"></span>
                </button>
            </div>

            <script>
            function performCustomAction(postId) {
                // Client-side validation or preparation
                if (confirm('Do you want to perform this action?')) {
                    // Get the button by its ID
                    var $btn = jQuery('#sync-button-' + postId);
                    
                    // Disable the button to prevent multiple clicks
                    $btn.attr('disabled', 'disabled');
                    
                    // Show the spinner and optionally hide the button text
                    $btn.find('.spinner').addClass('is-active');
                    $btn.find('.button-text').hide();
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
                        },
                        complete: function() {
                            // Re-enable the button and hide the spinner, show the button text again
                            $btn.removeAttr('disabled');
                            $btn.find('.spinner').removeClass('is-active');
                            $btn.find('.button-text').show();
                        }
                    });
                }
            }
            </script>
            <?php
		}
	}

	public function handle_sync_row_action() {
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
            $openai_engine = new OpenAiEngine();
			$pinecone_engine = new PineconeEngine();
            $indexing = new IndexingContents($openai_engine, $pinecone_engine);
            $result = $indexing->upserting_single_post($post_id);
            zara_insert_or_update($result['post_id'],$result['chunk_total']);
            // Send success response
            wp_send_json_success([
                'message' => 'Action completed successfully',
                'result' => 'Done'
            ]);
        } catch (Exception $e) {
            // Send error response
            wp_send_json_error($e->getMessage());
        }

        // Always exit after sending JSON
        wp_die();
    }

}
