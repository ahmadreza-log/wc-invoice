<?php

namespace WC_Invoice;

use WC_Invoice\Admin\Framework;
use WC_Invoice\Admin\Config;
use WC_Invoice\Admin\Render;

defined('ABSPATH') || exit;

/**
 * Admin class
 */
class Admin
{
    private static $instance = null;
    private Framework $framework;

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
        $this->initFramework();
        $this->hooks();
    }

    /**
     * Initialize settings framework
     *
     * @return void
     */
    private function initFramework(): void
    {
        $this->framework = Framework::instance();
        $config = Config::getConfig();
        $this->framework->init($config);
    }

    /**
     * Register hooks
     *
     * @return void
     */
    private function hooks(): void
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        
        // Allow modification of admin hooks
        do_action('wc_invoice_admin_hooks', $this);
    }

    /**
     * Add admin menu
     *
     * @return void
     */
    public function addAdminMenu(): void
    {
        $page_title = apply_filters('wc_invoice_admin_page_title', __('WC Invoice Settings', 'wc-invoice'));
        $menu_title = apply_filters('wc_invoice_admin_menu_title', __('Invoice Settings', 'wc-invoice'));
        $capability = apply_filters('wc_invoice_admin_capability', 'manage_options');
        $menu_slug = apply_filters('wc_invoice_admin_menu_slug', 'wc-invoice-settings');
        
        // Add submenu to WooCommerce admin menu
        add_submenu_page(
            'woocommerce',
            $page_title,
            $menu_title,
            $capability,
            $menu_slug,
            [$this, 'renderSettingsPage']
        );

        // Allow modification after menu is added
        do_action('wc_invoice_admin_menu_added', $menu_slug);
    }

    /**
     * Render settings page
     *
     * @return void
     */
    public function renderSettingsPage(): void
    {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        $tabs = $this->framework->getTabs();
        $options = $this->framework->getSettings();
        $option_name = $this->framework->getOptionName();

        // Allow modification before rendering
        do_action('wc_invoice_settings_page_before_render', $active_tab, $tabs, $options);
        ?>
        <div class="wc-invoice-settings-wrapper">
            <div class="wc-invoice-settings-header">
                <div class="wc-invoice-header-content">
                    <h1 class="wc-invoice-title">
                        <span class="wc-invoice-icon">🧾</span>
                        <?php echo esc_html(apply_filters('wc_invoice_settings_page_title', __('WC Invoice Settings', 'wc-invoice'))); ?>
                    </h1>
                    <p class="wc-invoice-subtitle"><?php echo esc_html(apply_filters('wc_invoice_settings_page_subtitle', __('Configure your invoice settings with ease', 'wc-invoice'))); ?></p>
                </div>
            </div>

            <div class="wc-invoice-settings-container">
                <aside class="wc-invoice-sidebar">
                    <nav class="wc-invoice-nav">
                        <ul class="wc-invoice-nav-list">
                            <?php foreach ($tabs as $tab_key => $tab_config): ?>
                                <li class="wc-invoice-nav-item">
                                    <a href="?page=wc-invoice-settings&tab=<?php echo esc_attr($tab_key); ?>" 
                                       class="wc-invoice-nav-link <?php echo $active_tab === $tab_key ? 'active' : ''; ?>"
                                       data-tab="<?php echo esc_attr($tab_key); ?>">
                                        <span class="wc-invoice-nav-icon">
                                            <?php echo esc_html($tab_config['icon'] ?? '📋'); ?>
                                        </span>
                                        <span class="wc-invoice-nav-label"><?php echo esc_html($tab_config['title']); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                </aside>

                <main class="wc-invoice-main-content">
                    <form method="post" class="wc-invoice-settings-form" id="wc-invoice-settings-form">
                        <?php
                        // Allow modification of form before fields
                        do_action('wc_invoice_settings_form_before_fields', $active_tab);
                        ?>

                        <?php foreach ($tabs as $tab_key => $tab_config): ?>
                            <div class="wc-invoice-tab-content" 
                                 data-tab="<?php echo esc_attr($tab_key); ?>" 
                                 style="<?php echo $active_tab === $tab_key ? 'display: block;' : 'display: none;'; ?>">
                                <div class="wc-invoice-card">
                                    <div class="wc-invoice-card-header">
                                        <h2 class="wc-invoice-card-title"><?php echo esc_html($tab_config['title']); ?></h2>
                                        <?php if (isset($tab_config['description'])): ?>
                                            <p class="wc-invoice-card-description"><?php echo esc_html($tab_config['description']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="wc-invoice-card-body">
                                        <?php $this->renderTabFields($tab_key, $options, $option_name); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="wc-invoice-form-actions">
                            <?php
                            // Allow modification of form actions
                            do_action('wc_invoice_settings_form_actions_before', $options);
                            ?>
                            <button type="submit" class="wc-invoice-btn wc-invoice-btn-primary">
                                <span class="wc-invoice-btn-icon">💾</span>
                                <?php echo esc_html(apply_filters('wc_invoice_settings_save_button_text', __('Save Settings', 'wc-invoice'))); ?>
                            </button>
                            <button type="button" class="wc-invoice-btn wc-invoice-btn-secondary" id="wc-invoice-reset-settings">
                                <span class="wc-invoice-btn-icon">🔄</span>
                                <?php echo esc_html(apply_filters('wc_invoice_settings_reset_button_text', __('Reset to Defaults', 'wc-invoice'))); ?>
                            </button>
                            <?php
                            // Allow modification of form actions
                            do_action('wc_invoice_settings_form_actions_after', $options);
                            ?>
                        </div>
                    </form>
                </main>
            </div>
        </div>
        <?php
        // Allow modification after rendering
        do_action('wc_invoice_settings_page_after_render', $active_tab, $tabs, $options);
    }

    /**
     * Render fields for a specific tab
     *
     * @param string $tab_id Tab ID
     * @param array $options Current options
     * @param string $option_name Option name
     * @return void
     */
    private function renderTabFields(string $tab_id, array $options, string $option_name): void
    {
        $fields = $this->framework->getFields($tab_id);
        $renderer = new Render($options, $option_name);

        // Allow modification of fields before rendering
        $fields = apply_filters('wc_invoice_settings_fields', $fields, $tab_id);

        // Allow modification before rendering tab
        do_action('wc_invoice_settings_tab_before_render', $tab_id, $fields, $options);

        foreach ($fields as $field) {
            $renderer->render($field);
        }

        // Allow modification after rendering tab
        do_action('wc_invoice_settings_tab_after_render', $tab_id, $fields, $options);
    }
}
