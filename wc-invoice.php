<?php

/**
 * Plugin Name: WC Invoice
 * Plugin URI: https://github.com/ahmadreza-log/wc-invoice
 * Description: Professional invoice generator plugin for WooCommerce with PDF and HTML invoice generation capabilities
 * Version: 0.0.6
 * Author: Ahmadreza Ebrahimi
 * Author URI: https://ahmadreza.me
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wc-invoice
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

use WC_Invoice\Admin;
use WC_Invoice\Ajax;
use WC_Invoice\Addon_Manager;
use WC_Invoice\Database;
use WC_Invoice\Enqueue;
use WC_Invoice\Notices;
use WC_Invoice\Woocommerce;
use WC_Invoice\Generator;

// Define plugin constants
define('WC_INVOICE_VERSION', '0.0.6');
define('WC_INVOICE_FILE', __FILE__);
define('WC_INVOICE_DIR', plugin_dir_path(WC_INVOICE_FILE));
define('WC_INVOICE_URL', plugin_dir_url(WC_INVOICE_FILE));
define('WC_INVOICE_BASENAME', plugin_basename(WC_INVOICE_FILE));

/**
 * Main plugin class
 */
class WC_Invoice
{
    private static $instance = null;
    private ?WC_Invoice\Autoloader $autoloader = null;

    /**
     * Get plugin instance
     *
     * @return self
     */
    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        self::$instance->autoload();
        self::$instance->includes();
        self::$instance->hooks();
        self::$instance->components();
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {}

    /**
     * Prevent cloning
     */
    public function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }

    /**
     * Register PSR-4 autoloader
     *
     * @return void
     */
    public function autoload(): void
    {
        require_once WC_INVOICE_DIR . 'vendor/Autoloader.php';

        if ($this->autoloader instanceof WC_Invoice\Autoloader) {
            return;
        }

        $this->autoloader = new WC_Invoice\Autoloader();
        $this->autoloader->register();

        // Map main namespace to includes folder
        $this->autoloader->namespace('WC_Invoice', WC_INVOICE_DIR . 'includes');
        
        // Map sub-namespaces
        $this->autoloader->namespace('WC_Invoice\\Admin', WC_INVOICE_DIR . 'includes/Admin');
        $this->autoloader->namespace('WC_Invoice\\Helpers', WC_INVOICE_DIR . 'includes/Helpers');
        $this->autoloader->namespace('WC_Invoice\\Integrations', WC_INVOICE_DIR . 'includes/Integrations');
    }

    /**
     * Include helper functions
     *
     * @return void
     */
    public function includes(): void
    {
        require_once WC_INVOICE_DIR . 'includes/helpers/functions.php';
    }

    /**
     * Register WordPress hooks
     *
     * @return void
     */
    public function hooks(): void
    {
        // Initialize WooCommerce integration after WooCommerce is loaded
        add_action('plugins_loaded', [$this, 'initWooCommerceIntegration'], 20);
        
        // Activation and deactivation hooks
        register_activation_hook(WC_INVOICE_FILE, [$this, 'activate']);
        register_deactivation_hook(WC_INVOICE_FILE, [$this, 'deactivate']);
    }

    /**
     * Initialize plugin components
     *
     * @return void
     */
    public function components(): void
    {
        // Database must be initialized first
        Database::instance();

        if (is_admin()) {
            Admin::instance();
            Notices::instance();
            Addon_Manager::instance();
        }

        Enqueue::instance();
        Ajax::instance();
    }

    /**
     * Initialize WooCommerce integration
     *
     * @return void
     */
    public function initWooCommerceIntegration(): void
    {
        if (!$this->isWooCommerceActive()) {
            return;
        }

        // Declare HPOS compatibility
        $this->declareHPOSCompatibility();

        Woocommerce::instance();
        Generator::instance();
    }

    /**
     * Declare HPOS (High-Performance Order Storage) compatibility
     *
     * @return void
     */
    private function declareHPOSCompatibility(): void
    {
        add_action('before_woocommerce_init', function () {
            if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', WC_INVOICE_FILE, true);
            }
        });
    }

    /**
     * Check if WooCommerce is active
     *
     * @return bool
     */
    private function isWooCommerceActive(): bool
    {
        return class_exists('WooCommerce') || 
               function_exists('WC') || 
               defined('WC_VERSION');
    }

    /**
     * Plugin activation
     *
     * @return void
     */
    public function activate(): void
    {
        Database::instance()->createTables();
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     *
     * @return void
     */
    public function deactivate(): void
    {
        flush_rewrite_rules();
    }
}

/**
 * Get plugin instance
 *
 * @return WC_Invoice
 */
function wc_invoice(): WC_Invoice
{
    return WC_Invoice::instance();
}

// Initialize plugin
wc_invoice();

