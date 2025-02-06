<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Zara
 * @subpackage Zara/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Zara
 * @subpackage Zara/public
 * @author     Your Name <email@example.com>
 */
class Zara_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/zara-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
	
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zara-public.js', array( 'jquery' ), $this->version, false );
		

		$data = [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('zara_ajax_nonce'),
			'welcome_message'=> $this->get_welcome_message()
		  ];
		
		wp_localize_script($this->plugin_name, 'ZaraData', $data);
	}

	function get_welcome_message(){
		$welcome_message_options = get_option( 'zara_message_options', array() );
		$welcome_message     = isset( $welcome_message_options['zara_welcome_message'] ) ? $welcome_message_options['zara_welcome_message'] : '';
		return $welcome_message;
	}
	 // Add floating chat widget to footer
	 public function render_chat_widget() {
        ?>
			<div id="chat-container" class="chat-container">
				<div id="chat-box" class="chat-box" style="display: none">
					<div id="messages" class="messages"></div>
					<div style="display: flex; width: 100%;">
						<input type="text" id="input-message" class="input-message" placeholder="Sampaikan pertanyaanmu..." />
						<button id="send-btn" class="send-btn">Send</button>
					</div>
				</div>
				<button id="chat-btn" class="chat-btn">💬</button>
			</div>
        <?php
    }

	public function register_ajax_handlers() {
		// For logged-in users
		add_action('wp_ajax_process_message', [$this, 'process_message']);
		// For non-logged-in users
		add_action('wp_ajax_nopriv_process_message', [$this, 'process_message']);
	}
	public function process_message() {
		try {
			// Verify nonce
			check_ajax_referer('zara_ajax_nonce', 'security');
	
			// Sanitize input
			$message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
	
			// Validate input
			if (empty($message)) {
				throw new Exception(__('Message cannot be empty', 'zara-plugin'));
			}

			$openai_engine = new OpenAiEngine();
			$pinecone_engine = new PineconeEngine();
			$prompt_constructor = new PromptConstructor();
			$url_sanitizer = new UrlSanitizer();

			$answer_finder = new AnswerFinder(
				$openai_engine,
				$pinecone_engine,
				$openai_engine,
				$prompt_constructor,
				$url_sanitizer
			);
			$response = $answer_finder->find_answer($message);
			
			wp_send_json_success($response);
	
		} catch (Exception $e) {
			wp_send_json_error([
				'message' => $e->getMessage()
			]);
		}
	}
}
