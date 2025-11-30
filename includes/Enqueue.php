<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * Enqueue assets class
 */
class Enqueue
{
    private static $instance = null;

    /**
     * Get instance
     *
     * @return self
     */
    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->hooks();
    }

    /**
     * Register hooks
     *
     * @return void
     */
    private function hooks(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets']);
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook
     * @return void
     */
    public function enqueueAdminAssets(string $hook): void
    {
        // Load on WooCommerce admin pages and our settings page
        if (strpos($hook, 'wc-invoice') === false && strpos($hook, 'woocommerce') === false) {
            return;
        }

        wp_enqueue_style(
            'wc-invoice-admin',
            WC_INVOICE_URL . 'assets/css/admin.css',
            [],
            WC_INVOICE_VERSION
        );
        
        // Add inline style to ensure specificity
        $inline_css = "
            .wc-invoice-settings-wrapper input[type='text'],
            .wc-invoice-settings-wrapper input[type='number'],
            .wc-invoice-settings-wrapper input[type='email'],
            .wc-invoice-settings-wrapper input[type='url'],
            .wc-invoice-settings-wrapper select,
            .wc-invoice-settings-wrapper textarea {
                width: 100% !important;
                padding: 12px 16px !important;
                border: 1px solid rgba(0, 0, 0, 0.1) !important;
                border-radius: 10px !important;
                background: rgba(255, 255, 255, 0.8) !important;
                backdrop-filter: blur(10px) !important;
                -webkit-backdrop-filter: blur(10px) !important;
                font-size: 14px !important;
                color: #2d3748 !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
                box-sizing: border-box !important;
            }
            .wc-invoice-settings-wrapper input:focus,
            .wc-invoice-settings-wrapper select:focus,
            .wc-invoice-settings-wrapper textarea:focus {
                outline: none !important;
                border-color: #667eea !important;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
                background: rgba(255, 255, 255, 0.95) !important;
            }
        ";
        wp_add_inline_style('wc-invoice-admin', $inline_css);

        wp_enqueue_media(); // For media uploader

        wp_enqueue_script(
            'wc-invoice-admin',
            WC_INVOICE_URL . 'assets/js/admin.js',
            ['jquery'],
            WC_INVOICE_VERSION,
            true
        );

        wp_localize_script('wc-invoice-admin', 'wcInvoice', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wc_invoice_nonce'),
        ]);
    }

    /**
     * Enqueue frontend assets
     *
     * @return void
     */
    public function enqueueFrontendAssets(): void
    {
        wp_enqueue_style(
            'wc-invoice-frontend',
            WC_INVOICE_URL . 'assets/css/frontend.css',
            [],
            WC_INVOICE_VERSION
        );
    }
}

