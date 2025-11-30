<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * Addon API class
 * 
 * Provides API methods for addons to interact with WC Invoice
 */
class Addon_API
{
    /**
     * Register addon hook
     *
     * @param string $hook_name Hook name
     * @param callable $callback Callback function
     * @param int $priority Priority
     * @param int $accepted_args Number of arguments
     * @return void
     */
    public static function addHook(string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1): void
    {
        add_action('wc_invoice_' . $hook_name, $callback, $priority, $accepted_args);
    }

    /**
     * Register addon filter
     *
     * @param string $filter_name Filter name
     * @param callable $callback Callback function
     * @param int $priority Priority
     * @param int $accepted_args Number of arguments
     * @return void
     */
    public static function addFilter(string $filter_name, callable $callback, int $priority = 10, int $accepted_args = 1): void
    {
        add_filter('wc_invoice_' . $filter_name, $callback, $priority, $accepted_args);
    }

    /**
     * Get invoice data
     *
     * @param int $invoice_id Invoice ID
     * @return object|null
     */
    public static function getInvoice(int $invoice_id): ?object
    {
        return Database::instance()->getInvoiceById($invoice_id);
    }

    /**
     * Get invoice by order ID
     *
     * @param int $order_id Order ID
     * @return object|null
     */
    public static function getInvoiceByOrder(int $order_id): ?object
    {
        return Database::instance()->getInvoiceByOrderId($order_id);
    }

    /**
     * Generate invoice
     *
     * @param int $order_id Order ID
     * @return int|false Invoice ID or false on failure
     */
    public static function generateInvoice(int $order_id)
    {
        return Generator::instance()->generate($order_id);
    }

    /**
     * Get invoice HTML
     *
     * @param int $invoice_id Invoice ID
     * @return string
     */
    public static function getInvoiceHTML(int $invoice_id): string
    {
        return Generator::instance()->getInvoiceHTML($invoice_id);
    }

    /**
     * Get settings
     *
     * @param string $key Setting key
     * @param mixed $default Default value
     * @return mixed
     */
    public static function getSetting(string $key, $default = '')
    {
        $options = get_option('wc_invoice_settings', []);
        return $options[$key] ?? $default;
    }

    /**
     * Update setting
     *
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @return bool
     */
    public static function updateSetting(string $key, $value): bool
    {
        $options = get_option('wc_invoice_settings', []);
        $options[$key] = $value;
        return update_option('wc_invoice_settings', $options);
    }

    /**
     * Add admin notice
     *
     * @param string $message Notice message
     * @param string $type Notice type (error, warning, success, info)
     * @return void
     */
    public static function addNotice(string $message, string $type = 'info'): void
    {
        Notices::instance()->render($type, $message);
    }

    /**
     * Get plugin version
     *
     * @return string
     */
    public static function getVersion(): string
    {
        return WC_INVOICE_VERSION;
    }

    /**
     * Get plugin directory path
     *
     * @return string
     */
    public static function getPluginDir(): string
    {
        return WC_INVOICE_DIR;
    }

    /**
     * Get plugin URL
     *
     * @return string
     */
    public static function getPluginUrl(): string
    {
        return WC_INVOICE_URL;
    }
}

