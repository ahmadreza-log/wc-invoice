<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * Ajax handler class
 */
class Ajax
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
        add_action('wp_ajax_wc_invoice_generate', [$this, 'generateInvoice']);
        add_action('wp_ajax_wc_invoice_download', [$this, 'downloadInvoice']);
    }

    /**
     * Generate invoice
     *
     * @return void
     */
    public function generateInvoice(): void
    {
        check_ajax_referer('wc_invoice_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wc-invoice')]);
        }

        $order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;

        if (!$order_id) {
            wp_send_json_error(['message' => __('Invalid order ID.', 'wc-invoice')]);
        }

        // Generate invoice logic will be implemented in Generator
        wp_send_json_success(['message' => __('Invoice generated successfully.', 'wc-invoice')]);
    }

    /**
     * Download invoice
     *
     * @return void
     */
    public function downloadInvoice(): void
    {
        check_ajax_referer('wc_invoice_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('Permission denied.', 'wc-invoice'));
        }

        $invoice_id = isset($_GET['invoice_id']) ? absint($_GET['invoice_id']) : 0;

        if (!$invoice_id) {
            wp_die(__('Invalid invoice ID.', 'wc-invoice'));
        }

        // Download logic will be implemented
        wp_die(__('Download functionality will be implemented.', 'wc-invoice'));
    }
}

