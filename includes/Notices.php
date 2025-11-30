<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * Admin notices handler class
 */
class Notices
{
    private static $instance = null;

    /**
     * Notice types
     */
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';

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
        add_action('admin_notices', [$this, 'displayNotices']);
        add_action('admin_notices', [$this, 'displayTransientNotices']);
    }

    /**
     * Display all notices
     *
     * @return void
     */
    public function displayNotices(): void
    {
        // WooCommerce missing notice
        if (!$this->isWooCommerceActive()) {
            $this->woocommerceMissing();
        }
    }

    /**
     * WooCommerce missing notice
     *
     * @return void
     */
    public function woocommerceMissing(): void
    {
        $this->render(
            self::TYPE_ERROR,
            sprintf(
                '<strong>%s</strong>: %s',
                esc_html__('WC Invoice', 'wc-invoice'),
                esc_html__('This plugin requires WooCommerce to be installed and activated.', 'wc-invoice')
            )
        );
    }

    /**
     * Invoice generated successfully notice
     *
     * @param int $order_id
     * @return void
     */
    public function invoiceGenerated(int $order_id): void
    {
        $this->render(
            self::TYPE_SUCCESS,
            sprintf(
                '<strong>%s</strong>: %s #%d',
                esc_html__('WC Invoice', 'wc-invoice'),
                esc_html__('Invoice generated successfully for order', 'wc-invoice'),
                $order_id
            )
        );
    }

    /**
     * Invoice generation failed notice
     *
     * @param string $message
     * @return void
     */
    public function invoiceGenerationFailed(string $message = ''): void
    {
        $default_message = esc_html__('Failed to generate invoice. Please try again.', 'wc-invoice');
        $final_message = !empty($message) ? esc_html($message) : $default_message;

        $this->render(
            self::TYPE_ERROR,
            sprintf(
                '<strong>%s</strong>: %s',
                esc_html__('WC Invoice', 'wc-invoice'),
                $final_message
            )
        );
    }

    /**
     * Invoice already exists notice
     *
     * @param int $order_id
     * @return void
     */
    public function invoiceAlreadyExists(int $order_id): void
    {
        $this->render(
            self::TYPE_WARNING,
            sprintf(
                '<strong>%s</strong>: %s #%d',
                esc_html__('WC Invoice', 'wc-invoice'),
                esc_html__('Invoice already exists for order', 'wc-invoice'),
                $order_id
            )
        );
    }

    /**
     * Database table creation failed notice
     *
     * @return void
     */
    public function databaseCreationFailed(): void
    {
        $this->render(
            self::TYPE_ERROR,
            sprintf(
                '<strong>%s</strong>: %s',
                esc_html__('WC Invoice', 'wc-invoice'),
                esc_html__('Failed to create database tables. Please check your database permissions.', 'wc-invoice')
            )
        );
    }

    /**
     * Settings saved notice
     *
     * @return void
     */
    public function settingsSaved(): void
    {
        $this->render(
            self::TYPE_SUCCESS,
            sprintf(
                '<strong>%s</strong>: %s',
                esc_html__('WC Invoice', 'wc-invoice'),
                esc_html__('Settings saved successfully.', 'wc-invoice')
            )
        );
    }

    /**
     * Permission denied notice
     *
     * @return void
     */
    public function permissionDenied(): void
    {
        $this->render(
            self::TYPE_ERROR,
            sprintf(
                '<strong>%s</strong>: %s',
                esc_html__('WC Invoice', 'wc-invoice'),
                esc_html__('You do not have permission to perform this action.', 'wc-invoice')
            )
        );
    }

    /**
     * Invalid order ID notice
     *
     * @return void
     */
    public function invalidOrderId(): void
    {
        $this->render(
            self::TYPE_ERROR,
            sprintf(
                '<strong>%s</strong>: %s',
                esc_html__('WC Invoice', 'wc-invoice'),
                esc_html__('Invalid order ID provided.', 'wc-invoice')
            )
        );
    }

    /**
     * Invoice download failed notice
     *
     * @return void
     */
    public function invoiceDownloadFailed(): void
    {
        $this->render(
            self::TYPE_ERROR,
            sprintf(
                '<strong>%s</strong>: %s',
                esc_html__('WC Invoice', 'wc-invoice'),
                esc_html__('Failed to download invoice. Please try again.', 'wc-invoice')
            )
        );
    }

    /**
     * Render notice
     *
     * @param string $type Notice type (error, warning, success, info)
     * @param string $message Notice message
     * @param bool $dismissible Whether the notice is dismissible
     * @return void
     */
    public function render(string $type, string $message, bool $dismissible = true): void
    {
        $classes = ['notice', "notice-{$type}"];
        
        if ($dismissible) {
            $classes[] = 'is-dismissible';
        }

        printf(
            '<div class="%s"><p>%s</p></div>',
            esc_attr(implode(' ', $classes)),
            $message // Already escaped in calling methods
        );
    }

    /**
     * Add transient notice (for use after redirect)
     *
     * @param string $type Notice type
     * @param string $message Notice message
     * @return void
     */
    public function addTransient(string $type, string $message): void
    {
        $notices = get_transient('wc_invoice_notices') ?: [];
        $notices[] = [
            'type' => $type,
            'message' => $message,
        ];
        set_transient('wc_invoice_notices', $notices, 30);
    }

    /**
     * Display transient notices
     *
     * @return void
     */
    public function displayTransientNotices(): void
    {
        $notices = get_transient('wc_invoice_notices');
        
        if (!$notices || !is_array($notices)) {
            return;
        }

        foreach ($notices as $notice) {
            if (isset($notice['type']) && isset($notice['message'])) {
                $this->render($notice['type'], $notice['message']);
            }
        }

        // Clear transient after display
        delete_transient('wc_invoice_notices');
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
}

