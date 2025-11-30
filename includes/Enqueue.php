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
        if (strpos($hook, 'wc-invoice') === false) {
            return;
        }

        wp_enqueue_style(
            'wc-invoice-admin',
            WC_INVOICE_URL . 'assets/css/admin.css',
            [],
            WC_INVOICE_VERSION
        );

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

