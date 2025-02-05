<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Zara
 * @subpackage Zara/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Zara
 * @subpackage Zara/includes
 * @author     Your Name <email@example.com>
 */
class Zara {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Zara_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $Zara    The string used to uniquely identify this plugin.
	 */
	protected $Zara;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ZARA_VERSION' ) ) {
			$this->version = ZARA_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->Zara = 'zara';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Zara_Loader. Orchestrates the hooks of the plugin.
	 * - Zara_i18n. Defines internationalization functionality.
	 * - Zara_Admin. Defines all hooks for the admin area.
	 * - Zara_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-zara-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-zara-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/database-manager.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-zara-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-zara-public.php';


		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/menus.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/tokenize.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/embedding-openai.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/vector-pinecone.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/preprocess.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/handle-question.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/interface/embedding-generator-interface.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/interface/response-generator-interface.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/interface/vector-query-interface.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/openai-engine.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/pinecone-engine.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/prompt-constructor.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/url-sanitizer.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/answer-finder.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rag/indexing-contents.php';

		$this->loader = new Zara_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Zara_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Zara_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		zara_init_database();
		$plugin_admin = new Zara_Admin( $this->get_Zara(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$plugin_admin->register_manage_posts_and_pages();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Zara_Public( $this->get_Zara(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		add_action( 'wp_footer', array( $plugin_public, 'render_chat_widget' ) );
		$plugin_public->register_ajax_handlers();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_Zara() {
		return $this->Zara;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Zara_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
