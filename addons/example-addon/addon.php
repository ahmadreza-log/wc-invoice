<?php
/**
 * Addon Name: Example Addon
 * Addon URI: https://github.com/ahmadreza-log/wc-invoice
 * Description: This is an example addon to demonstrate how to create addons for WC Invoice
 * Version: 1.0.0
 * Author: Ahmadreza Ebrahimi
 * Author URI: https://ahmadreza.me
 * Requires: 0.0.5
 * Text Domain: wc-invoice-example-addon
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

/**
 * Example Addon Class
 * 
 * This is a template for creating addons
 */
class WC_Invoice_Example_Addon
{
    /**
     * Constructor
     */
    public function __construct()
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
        // Example: Add custom field to invoice
        add_filter('wc_invoice_settings_fields', [$this, 'addCustomField'], 10, 1);
        
        // Example: Modify invoice HTML
        add_filter('wc_invoice_html', [$this, 'modifyInvoiceHTML'], 10, 2);
        
        // Example: Add custom action
        add_action('wc_invoice_generated', [$this, 'onInvoiceGenerated'], 10, 2);
    }

    /**
     * Add custom field to settings
     *
     * @param array $fields
     * @return array
     */
    public function addCustomField(array $fields): array
    {
        $fields['example_field'] = [
            'type' => 'text',
            'label' => __('Example Field', 'wc-invoice-example-addon'),
            'default' => '',
        ];
        
        return $fields;
    }

    /**
     * Modify invoice HTML
     *
     * @param string $html
     * @param int $invoice_id
     * @return string
     */
    public function modifyInvoiceHTML(string $html, int $invoice_id): string
    {
        // Add custom content to invoice HTML
        $custom_content = '<div class="example-addon-content">Custom content from addon</div>';
        
        return $html . $custom_content;
    }

    /**
     * Handle invoice generated
     *
     * @param int $invoice_id
     * @param int $order_id
     * @return void
     */
    public function onInvoiceGenerated(int $invoice_id, int $order_id): void
    {
        // Do something when invoice is generated
        error_log("Example Addon: Invoice #{$invoice_id} generated for order #{$order_id}");
    }
}

// Initialize addon when loaded
add_action('wc_invoice_addon_loaded', function($slug, $data) {
    if ($slug === 'example-addon') {
        new WC_Invoice_Example_Addon();
    }
}, 10, 2);

