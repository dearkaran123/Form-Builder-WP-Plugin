<?php 

final class Booking_Hub {

    /**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var Booking_Hub The single instance of the class.
	 */
	private static $_instance = null;

    /**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return Booking_Hub An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
    }

    /**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
    public function __construct() {
       
        add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );

    }

    /**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n() {

		load_plugin_textdomain( 'booking-hub' );

	}

    /**
	 * On Plugins Loaded
	 *
	 * Checks the plugin has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_plugins_loaded() {

		if ( $this->is_compatible() ) {
			$this->init();
		}

	}

    /**
	 * Compatibility Checks
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function is_compatible() {

		return true;

	}

    /**
	 * Initialize the plugin
	 *
	 * Load the files required to run the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
    public function init(){

        $this->i18n();
        $this->include_files();

    } 
    
    public function include_files(){
		require_once BHUB_PATH . 'includes/post-types.php';
		require_once BHUB_PATH . 'includes/dashboard/dashboard.php';
    }

    
} // class
Booking_Hub::instance();

// Pluging activation hook
function bhub_plugin_activate() { 

    flush_rewrite_rules(); // Removes rewrite rules and then recreate rewrite rules.

}
register_activation_hook( __FILE__, 'bhub_plugin_activate' );

// Pluging deactivation hook
function bhub_plugin_deactivate() {

    flush_rewrite_rules(); // Removes rewrite rules and then recreate rewrite rules.

}
register_deactivation_hook( __FILE__, 'bhub_plugin_deactivate' );

