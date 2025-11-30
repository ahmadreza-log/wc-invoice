<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * Admin class
 */
class Admin
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
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    /**
     * Add admin menu
     *
     * @return void
     */
    public function addAdminMenu(): void
    {
        add_menu_page(
            __('WC Invoice', 'wc-invoice'),
            __('WC Invoice', 'wc-invoice'),
            'manage_options',
            'wc-invoice',
            [$this, 'renderDashboardPage'],
            'dashicons-media-document',
            56
        );

        add_submenu_page(
            'wc-invoice',
            __('Settings', 'wc-invoice'),
            __('Settings', 'wc-invoice'),
            'manage_options',
            'wc-invoice-settings',
            [$this, 'renderSettingsPage']
        );
    }

    /**
     * Register settings
     *
     * @return void
     */
    public function registerSettings(): void
    {
        register_setting('wc_invoice_settings', 'wc_invoice_settings');

        add_settings_section(
            'wc_invoice_general_section',
            __('General Settings', 'wc-invoice'),
            [$this, 'renderGeneralSection'],
            'wc-invoice-settings'
        );

        add_settings_field(
            'invoice_prefix',
            __('Invoice Number Prefix', 'wc-invoice'),
            [$this, 'renderInvoicePrefixField'],
            'wc-invoice-settings',
            'wc_invoice_general_section'
        );

        add_settings_field(
            'invoice_template',
            __('Invoice Template', 'wc-invoice'),
            [$this, 'renderInvoiceTemplateField'],
            'wc-invoice-settings',
            'wc_invoice_general_section'
        );
    }

    /**
     * Render dashboard page
     *
     * @return void
     */
    public function renderDashboardPage(): void
    {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('WC Invoice Dashboard', 'wc-invoice'); ?></h1>
            <p><?php esc_html_e('Welcome to WC Invoice plugin dashboard.', 'wc-invoice'); ?></p>
        </div>
        <?php
    }

    /**
     * Render settings page
     *
     * @return void
     */
    public function renderSettingsPage(): void
    {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('WC Invoice Settings', 'wc-invoice'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wc_invoice_settings');
                do_settings_sections('wc-invoice-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render general section
     *
     * @return void
     */
    public function renderGeneralSection(): void
    {
        echo '<p>' . esc_html__('Configure general invoice settings.', 'wc-invoice') . '</p>';
    }

    /**
     * Render invoice prefix field
     *
     * @return void
     */
    public function renderInvoicePrefixField(): void
    {
        $options = get_option('wc_invoice_settings', []);
        $value = $options['invoice_prefix'] ?? 'INV-';
        ?>
        <input type="text" name="wc_invoice_settings[invoice_prefix]" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Prefix for invoice numbers (e.g., INV-, FA-).', 'wc-invoice'); ?></p>
        <?php
    }

    /**
     * Render invoice template field
     *
     * @return void
     */
    public function renderInvoiceTemplateField(): void
    {
        $options = get_option('wc_invoice_settings', []);
        $value = $options['invoice_template'] ?? 'default';
        ?>
        <select name="wc_invoice_settings[invoice_template]">
            <option value="default" <?php selected($value, 'default'); ?>><?php esc_html_e('Default', 'wc-invoice'); ?></option>
            <option value="modern" <?php selected($value, 'modern'); ?>><?php esc_html_e('Modern', 'wc-invoice'); ?></option>
            <option value="minimal" <?php selected($value, 'minimal'); ?>><?php esc_html_e('Minimal', 'wc-invoice'); ?></option>
        </select>
        <?php
    }
}

