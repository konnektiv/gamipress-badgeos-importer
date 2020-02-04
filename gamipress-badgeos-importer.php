<?php
/**
 * Plugin Name:         GamiPress - BadgeOS importer
 * Plugin URI:          https://wordpress.org/plugins/gamipress-badgeos-importer/
 * Description:         Tool to migrate all stored data from BadgeOS to GamiPress.
 * Version:             1.0.8
 * Author:              GamiPress
 * Author URI:          https://gamipress.com/
 * Text Domain:         gamipress-badgeos-importer
 * Domain Path:         /languages/
 * Requires at least:   4.4
 * Tested up to:        5.3
 * License:             GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package             GamiPress\BadgeOS\Importer
 * @author              GamiPress
 * @copyright           Copyright (c) GamiPress
 */

final class GamiPress_BadgeOS_Importer {

    /**
     * @var         GamiPress_BadgeOS_Importer $instance The one true GamiPress_BadgeOS_Importer
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      object self::$instance The one true GamiPress_BadgeOS_Importer
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new GamiPress_BadgeOS_Importer();
            self::$instance->constants();
            self::$instance->includes();
            self::$instance->hooks();
            self::$instance->load_textdomain();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'GAMIPRESS_BADGEOS_IMPORTER_VER', '1.0.8' );

        // Plugin path
        define( 'GAMIPRESS_BADGEOS_IMPORTER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_BADGEOS_IMPORTER_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            require_once GAMIPRESS_BADGEOS_IMPORTER_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_BADGEOS_IMPORTER_DIR . 'includes/tool.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {
        // Setup our activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    function activate() {

        if( $this->meets_requirements() ) {

        }

    }

    /**
     * Deactivation hook for the plugin.
     *
     * @since  1.0.0
     */
    function deactivate() {

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'GAMIPRESS_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'GamiPress - BadgeOS importer requires %s and %s in order to work. Please install and activate them.', 'gamipress-badgeos-importer' ),
                        '<a href="https://wordpress.org/plugins/gamipress/" target="_blank">GamiPress</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'GAMIPRESS_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( class_exists( 'GamiPress' ) ) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {
        // Set filter for language directory
        $lang_dir = GAMIPRESS_BADGEOS_IMPORTER_DIR . '/languages/';
        $lang_dir = apply_filters( 'gamipress_badgeos_importer_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'gamipress-badgeos-importer' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'gamipress-badgeos-importer', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/gamipress-badgeos-importer/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/gamipress/ folder
            load_textdomain( 'gamipress-badgeos-importer', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/gamipress/languages/ folder
            load_textdomain( 'gamipress-badgeos-importer', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'gamipress-badgeos-importer', false, $lang_dir );
        }
    }

}

/**
 * The main function responsible for returning the one true GamiPress_BadgeOS_Importer instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_BadgeOS_Importer The one true GamiPress_BadgeOS_Importer
 */
function GamiPress_BadgeOS_Importer() {
    return GamiPress_BadgeOS_Importer::instance();
}
add_action( 'plugins_loaded', 'GamiPress_BadgeOS_Importer' );
