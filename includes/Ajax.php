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
        add_action('wp_ajax_wc_invoice_save_settings', [$this, 'saveSettings']);
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

    /**
     * Save settings via AJAX
     *
     * @return void
     */
    public function saveSettings(): void
    {
        check_ajax_referer('wc_invoice_settings', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'wc-invoice')]);
        }

        $settings = isset($_POST['settings']) ? $_POST['settings'] : [];

        if (empty($settings) && !is_array($settings)) {
            wp_send_json_error(['message' => __('No settings provided.', 'wc-invoice')]);
        }
        
        // Debug: Log settings received (remove in production)
        // error_log('WC Invoice Settings Received: ' . print_r($settings, true));

        // Get framework instance and ensure it's initialized
        $framework = \WC_Invoice\Admin\Framework::instance();
        
        // Initialize framework if not already initialized
        if (empty($framework->getTabs())) {
            $config = \WC_Invoice\Admin\Config::getConfig();
            $framework->init($config);
        }
        
        // Get current settings first
        $option_name = $framework->getOptionName();
        $current_settings = get_option($option_name, []);
        
        // Sanitize settings using framework
        $sanitized = $framework->sanitizeSettings($settings);
        
        // Merge: Start with current settings, then overwrite with form data
        // This ensures form values (including empty/removed values) take priority
        $merged_settings = $current_settings;
        foreach ($sanitized as $key => $value) {
            // Always use form value, even if it's empty or 0 (for removals)
            // This is critical for media removal (logo_id = 0)
            $merged_settings[$key] = $value;
        }
        
        // Also process any fields that were sent but might not be in framework config
        // This handles edge cases where fields exist in form but not in config
        foreach ($settings as $key => $value) {
            if (!isset($sanitized[$key])) {
                // Field not in framework config, but exists in form - sanitize and add it
                $merged_settings[$key] = sanitize_text_field($value);
            }
        }
        
        // Force update settings - always update even if values are the same
        // Use direct database update to bypass WordPress's value comparison
        global $wpdb;
        
        $serialized_value = maybe_serialize($merged_settings);
        
        // Check if option exists
        $option_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT option_id FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
            $option_name
        ));
        
        if ($option_exists) {
            // Update existing option directly in database
            $wpdb->update(
                $wpdb->options,
                [
                    'option_value' => $serialized_value
                ],
                [
                    'option_name' => $option_name
                ],
                ['%s'],
                ['%s']
            );
            
            // Clear cache
            wp_cache_delete($option_name, 'options');
            $alloptions = wp_load_alloptions(true);
            if (isset($alloptions[$option_name])) {
                $alloptions[$option_name] = $serialized_value;
                wp_cache_set('alloptions', $alloptions, 'options');
            } else {
                wp_cache_set($option_name, $serialized_value, 'options');
            }
        } else {
            // Add new option
            add_option($option_name, $merged_settings);
        }
        
        // Fire update action
        do_action('update_option', $option_name, $current_settings, $merged_settings);

        // Allow modification after save
        do_action('wc_invoice_settings_saved', $merged_settings, true);

        // Always return success - settings are saved
        wp_send_json_success([
            'message' => __('Settings saved successfully.', 'wc-invoice')
        ]);
    }
}

