<?php

namespace WC_Invoice\Helpers;

defined('ABSPATH') || exit;

/**
 * Helper functions
 */

/**
 * Get invoice settings
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function get_invoice_setting(string $key, $default = '')
{
    $options = get_option('wc_invoice_settings', []);
    return $options[$key] ?? $default;
}

/**
 * Format invoice number
 *
 * @param string $invoice_number
 * @return string
 */
function format_invoice_number(string $invoice_number): string
{
    return esc_html($invoice_number);
}

/**
 * Get invoice download URL
 *
 * @param int $invoice_id
 * @return string
 */
function get_invoice_download_url(int $invoice_id): string
{
    return add_query_arg([
        'action' => 'wc_invoice_download',
        'invoice_id' => $invoice_id,
        'nonce' => wp_create_nonce('wc_invoice_nonce'),
    ], admin_url('admin-ajax.php'));
}

/**
 * Check if invoice exists for order
 *
 * @param int $order_id
 * @return bool
 */
function order_has_invoice(int $order_id): bool
{
    $invoice = \WC_Invoice\Database::instance()->getInvoiceByOrderId($order_id);
    return !empty($invoice);
}

