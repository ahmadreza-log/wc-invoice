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
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        $tabs = [
            'general' => __('General', 'wc-invoice'),
            'invoice' => __('Invoice', 'wc-invoice'),
            'display' => __('Display', 'wc-invoice'),
            'advanced' => __('Advanced', 'wc-invoice'),
        ];
        ?>
        <div class="wc-invoice-settings-wrapper">
            <div class="wc-invoice-settings-header">
                <div class="wc-invoice-header-content">
                    <h1 class="wc-invoice-title">
                        <span class="wc-invoice-icon">🧾</span>
                        <?php esc_html_e('WC Invoice Settings', 'wc-invoice'); ?>
                    </h1>
                    <p class="wc-invoice-subtitle"><?php esc_html_e('Configure your invoice settings with ease', 'wc-invoice'); ?></p>
                </div>
            </div>

            <div class="wc-invoice-settings-container">
                <aside class="wc-invoice-sidebar">
                    <nav class="wc-invoice-nav">
                        <ul class="wc-invoice-nav-list">
                            <?php foreach ($tabs as $tab_key => $tab_label): ?>
                                <li class="wc-invoice-nav-item">
                                    <a href="?page=wc-invoice-settings&tab=<?php echo esc_attr($tab_key); ?>" 
                                       class="wc-invoice-nav-link <?php echo $active_tab === $tab_key ? 'active' : ''; ?>"
                                       data-tab="<?php echo esc_attr($tab_key); ?>">
                                        <span class="wc-invoice-nav-icon">
                                            <?php
                                            $icons = [
                                                'general' => '⚙️',
                                                'invoice' => '📄',
                                                'display' => '🎨',
                                                'advanced' => '🔧',
                                            ];
                                            echo $icons[$tab_key] ?? '📋';
                                            ?>
                                        </span>
                                        <span class="wc-invoice-nav-label"><?php echo esc_html($tab_label); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                </aside>

                <main class="wc-invoice-main-content">
                    <form method="post" action="options.php" class="wc-invoice-settings-form">
                        <?php
                        settings_fields('wc_invoice_settings');
                        wp_nonce_field('wc_invoice_settings', 'wc_invoice_settings_nonce');
                        ?>

                        <div class="wc-invoice-tab-content" data-tab="general" style="<?php echo $active_tab === 'general' ? 'display: block;' : 'display: none;'; ?>">
                            <div class="wc-invoice-card">
                                <div class="wc-invoice-card-header">
                                    <h2 class="wc-invoice-card-title"><?php esc_html_e('General Settings', 'wc-invoice'); ?></h2>
                                    <p class="wc-invoice-card-description"><?php esc_html_e('Configure basic plugin settings', 'wc-invoice'); ?></p>
                                </div>
                                <div class="wc-invoice-card-body">
                                    <?php do_settings_sections('wc-invoice-settings'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="wc-invoice-tab-content" data-tab="invoice" style="<?php echo $active_tab === 'invoice' ? 'display: block;' : 'display: none;'; ?>">
                            <div class="wc-invoice-card">
                                <div class="wc-invoice-card-header">
                                    <h2 class="wc-invoice-card-title"><?php esc_html_e('Invoice Settings', 'wc-invoice'); ?></h2>
                                    <p class="wc-invoice-card-description"><?php esc_html_e('Customize invoice generation and numbering', 'wc-invoice'); ?></p>
                                </div>
                                <div class="wc-invoice-card-body">
                                    <?php $this->renderInvoiceSettings(); ?>
                                </div>
                            </div>
                        </div>

                        <div class="wc-invoice-tab-content" data-tab="display" style="<?php echo $active_tab === 'display' ? 'display: block;' : 'display: none;'; ?>">
                            <div class="wc-invoice-card">
                                <div class="wc-invoice-card-header">
                                    <h2 class="wc-invoice-card-title"><?php esc_html_e('Display Settings', 'wc-invoice'); ?></h2>
                                    <p class="wc-invoice-card-description"><?php esc_html_e('Control how invoices are displayed', 'wc-invoice'); ?></p>
                                </div>
                                <div class="wc-invoice-card-body">
                                    <?php $this->renderDisplaySettings(); ?>
                                </div>
                            </div>
                        </div>

                        <div class="wc-invoice-tab-content" data-tab="advanced" style="<?php echo $active_tab === 'advanced' ? 'display: block;' : 'display: none;'; ?>">
                            <div class="wc-invoice-card">
                                <div class="wc-invoice-card-header">
                                    <h2 class="wc-invoice-card-title"><?php esc_html_e('Advanced Settings', 'wc-invoice'); ?></h2>
                                    <p class="wc-invoice-card-description"><?php esc_html_e('Advanced configuration options', 'wc-invoice'); ?></p>
                                </div>
                                <div class="wc-invoice-card-body">
                                    <?php $this->renderAdvancedSettings(); ?>
                                </div>
                            </div>
                        </div>

                        <div class="wc-invoice-form-actions">
                            <button type="submit" class="wc-invoice-btn wc-invoice-btn-primary">
                                <span class="wc-invoice-btn-icon">💾</span>
                                <?php esc_html_e('Save Settings', 'wc-invoice'); ?>
                            </button>
                            <button type="button" class="wc-invoice-btn wc-invoice-btn-secondary" id="wc-invoice-reset-settings">
                                <span class="wc-invoice-btn-icon">🔄</span>
                                <?php esc_html_e('Reset to Defaults', 'wc-invoice'); ?>
                            </button>
                        </div>
                    </form>
                </main>
            </div>
        </div>
        <?php
    }

    /**
     * Render invoice settings
     *
     * @return void
     */
    private function renderInvoiceSettings(): void
    {
        $options = get_option('wc_invoice_settings', []);
        ?>
        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Invoice Number Prefix', 'wc-invoice'); ?>
                <span class="wc-invoice-label-required">*</span>
            </label>
            <input type="text" 
                   name="wc_invoice_settings[invoice_prefix]" 
                   value="<?php echo esc_attr($options['invoice_prefix'] ?? 'INV-'); ?>" 
                   class="wc-invoice-input" 
                   placeholder="INV-" 
                   required />
            <p class="wc-invoice-description"><?php esc_html_e('Prefix for invoice numbers (e.g., INV-, FA-, INVOICE-).', 'wc-invoice'); ?></p>
        </div>

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Invoice Template', 'wc-invoice'); ?>
            </label>
            <select name="wc_invoice_settings[invoice_template]" class="wc-invoice-select">
                <option value="default" <?php selected($options['invoice_template'] ?? 'default', 'default'); ?>>
                    <?php esc_html_e('Default', 'wc-invoice'); ?>
                </option>
                <option value="modern" <?php selected($options['invoice_template'] ?? 'default', 'modern'); ?>>
                    <?php esc_html_e('Modern', 'wc-invoice'); ?>
                </option>
                <option value="minimal" <?php selected($options['invoice_template'] ?? 'default', 'minimal'); ?>>
                    <?php esc_html_e('Minimal', 'wc-invoice'); ?>
                </option>
            </select>
            <p class="wc-invoice-description"><?php esc_html_e('Choose the template style for your invoices.', 'wc-invoice'); ?></p>
        </div>

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Auto-generate Invoice', 'wc-invoice'); ?>
            </label>
            <label class="wc-invoice-switch">
                <input type="checkbox" 
                       name="wc_invoice_settings[auto_generate]" 
                       value="1" 
                       <?php checked($options['auto_generate'] ?? false, true); ?> />
                <span class="wc-invoice-switch-slider"></span>
            </label>
            <p class="wc-invoice-description"><?php esc_html_e('Automatically generate invoice when order status changes to completed.', 'wc-invoice'); ?></p>
        </div>
        <?php
    }

    /**
     * Render display settings
     *
     * @return void
     */
    private function renderDisplaySettings(): void
    {
        $options = get_option('wc_invoice_settings', []);
        ?>
        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Show Invoice Button in Orders List', 'wc-invoice'); ?>
            </label>
            <label class="wc-invoice-switch">
                <input type="checkbox" 
                       name="wc_invoice_settings[show_in_orders_list]" 
                       value="1" 
                       <?php checked($options['show_in_orders_list'] ?? true, true); ?> />
                <span class="wc-invoice-switch-slider"></span>
            </label>
            <p class="wc-invoice-description"><?php esc_html_e('Display invoice column in WooCommerce orders list.', 'wc-invoice'); ?></p>
        </div>

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Invoice Date Format', 'wc-invoice'); ?>
            </label>
            <select name="wc_invoice_settings[date_format]" class="wc-invoice-select">
                <option value="default" <?php selected($options['date_format'] ?? 'default', 'default'); ?>>
                    <?php esc_html_e('Default (WordPress)', 'wc-invoice'); ?>
                </option>
                <option value="Y-m-d" <?php selected($options['date_format'] ?? 'default', 'Y-m-d'); ?>>
                    <?php echo date('Y-m-d'); ?>
                </option>
                <option value="d/m/Y" <?php selected($options['date_format'] ?? 'default', 'd/m/Y'); ?>>
                    <?php echo date('d/m/Y'); ?>
                </option>
                <option value="m/d/Y" <?php selected($options['date_format'] ?? 'default', 'm/d/Y'); ?>>
                    <?php echo date('m/d/Y'); ?>
                </option>
            </select>
            <p class="wc-invoice-description"><?php esc_html_e('Choose how invoice dates are displayed.', 'wc-invoice'); ?></p>
        </div>
        <?php
    }

    /**
     * Render advanced settings
     *
     * @return void
     */
    private function renderAdvancedSettings(): void
    {
        $options = get_option('wc_invoice_settings', []);
        ?>
        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Enable Debug Mode', 'wc-invoice'); ?>
            </label>
            <label class="wc-invoice-switch">
                <input type="checkbox" 
                       name="wc_invoice_settings[debug_mode]" 
                       value="1" 
                       <?php checked($options['debug_mode'] ?? false, true); ?> />
                <span class="wc-invoice-switch-slider"></span>
            </label>
            <p class="wc-invoice-description"><?php esc_html_e('Enable debug logging for troubleshooting.', 'wc-invoice'); ?></p>
        </div>

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Custom CSS', 'wc-invoice'); ?>
            </label>
            <textarea name="wc_invoice_settings[custom_css]" 
                      class="wc-invoice-textarea" 
                      rows="6" 
                      placeholder="/* Add your custom CSS here */"><?php echo esc_textarea($options['custom_css'] ?? ''); ?></textarea>
            <p class="wc-invoice-description"><?php esc_html_e('Add custom CSS to style your invoices.', 'wc-invoice'); ?></p>
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

