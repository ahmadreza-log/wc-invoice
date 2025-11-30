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
        // Add submenu to WooCommerce admin menu
        add_submenu_page(
            'woocommerce',
            __('WC Invoice Settings', 'wc-invoice'),
            __('Invoice Settings', 'wc-invoice'),
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
        register_setting('wc_invoice_settings', 'wc_invoice_settings', [
            'sanitize_callback' => [$this, 'sanitizeSettings'],
        ]);
    }

    /**
     * Sanitize settings
     *
     * @param array $input Raw input data
     * @return array Sanitized data
     */
    public function sanitizeSettings(array $input): array
    {
        $sanitized = [];

        // Invoice prefix
        if (isset($input['invoice_prefix'])) {
            $sanitized['invoice_prefix'] = sanitize_text_field($input['invoice_prefix']);
        }

        // Title
        if (isset($input['title'])) {
            $sanitized['title'] = sanitize_text_field($input['title']);
        }

        // Logo ID
        if (isset($input['logo_id'])) {
            $sanitized['logo_id'] = absint($input['logo_id']);
        }

        // Theme
        if (isset($input['theme'])) {
            $allowed_themes = ['modern', 'flat', 'simple', 'classic'];
            $sanitized['theme'] = in_array($input['theme'], $allowed_themes) ? $input['theme'] : 'modern';
        }

        // Colors
        if (isset($input['primary_color'])) {
            $sanitized['primary_color'] = sanitize_hex_color($input['primary_color']);
        }
        if (isset($input['text_color'])) {
            $sanitized['text_color'] = sanitize_hex_color($input['text_color']);
        }

        // Font IDs
        $font_formats = ['ttf', 'woff', 'woff2', 'eot', 'svg'];
        foreach ($font_formats as $format) {
            if (isset($input['font_' . $format . '_id'])) {
                $sanitized['font_' . $format . '_id'] = absint($input['font_' . $format . '_id']);
            }
        }

        return $sanitized;
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
            'theme' => __('Theme', 'wc-invoice'),
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
                                                'theme' => '🎨',
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
                                    <p class="wc-invoice-card-description"><?php esc_html_e('Configure basic invoice settings', 'wc-invoice'); ?></p>
                                </div>
                                <div class="wc-invoice-card-body">
                                    <?php $this->renderGeneralSettings(); ?>
                                </div>
                            </div>
                        </div>

                        <div class="wc-invoice-tab-content" data-tab="theme" style="<?php echo $active_tab === 'theme' ? 'display: block;' : 'display: none;'; ?>">
                            <div class="wc-invoice-card">
                                <div class="wc-invoice-card-header">
                                    <h2 class="wc-invoice-card-title"><?php esc_html_e('Theme Settings', 'wc-invoice'); ?></h2>
                                    <p class="wc-invoice-card-description"><?php esc_html_e('Customize invoice appearance and styling', 'wc-invoice'); ?></p>
                                </div>
                                <div class="wc-invoice-card-body">
                                    <?php $this->renderThemeSettings(); ?>
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
     * Render general settings
     *
     * @return void
     */
    private function renderGeneralSettings(): void
    {
        $options = get_option('wc_invoice_settings', []);
        ?>
        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Invoice Prefix', 'wc-invoice'); ?>
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
                <?php esc_html_e('Title', 'wc-invoice'); ?>
            </label>
            <input type="text" 
                   name="wc_invoice_settings[title]" 
                   value="<?php echo esc_attr($options['title'] ?? ''); ?>" 
                   class="wc-invoice-input" 
                   placeholder="<?php esc_attr_e('Invoice', 'wc-invoice'); ?>" />
            <p class="wc-invoice-description"><?php esc_html_e('Invoice title displayed on the invoice document.', 'wc-invoice'); ?></p>
        </div>

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Logo', 'wc-invoice'); ?>
            </label>
            <div class="wc-invoice-logo-upload">
                <?php
                $logo_id = $options['logo_id'] ?? 0;
                $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
                ?>
                <div class="wc-invoice-logo-preview" style="<?php echo $logo_url ? '' : 'display: none;'; ?>">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php esc_attr_e('Logo', 'wc-invoice'); ?>" style="max-width: 200px; max-height: 100px; margin-bottom: 10px;" />
                    <button type="button" class="wc-invoice-btn wc-invoice-btn-secondary wc-invoice-remove-logo" style="margin-left: 10px;">
                        <?php esc_html_e('Remove Logo', 'wc-invoice'); ?>
                    </button>
                </div>
                <input type="hidden" name="wc_invoice_settings[logo_id]" id="wc_invoice_logo_id" value="<?php echo esc_attr($logo_id); ?>" />
                <button type="button" class="wc-invoice-btn wc-invoice-btn-secondary wc-invoice-upload-logo">
                    <span class="wc-invoice-btn-icon">📷</span>
                    <?php esc_html_e('Upload Logo', 'wc-invoice'); ?>
                </button>
            </div>
            <p class="wc-invoice-description"><?php esc_html_e('Upload your company logo to display on invoices.', 'wc-invoice'); ?></p>
        </div>
        <?php
    }

    /**
     * Render theme settings
     *
     * @return void
     */
    private function renderThemeSettings(): void
    {
        $options = get_option('wc_invoice_settings', []);
        ?>
        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Select Theme', 'wc-invoice'); ?>
            </label>
            <select name="wc_invoice_settings[theme]" class="wc-invoice-select" id="wc_invoice_theme_select">
                <option value="modern" <?php selected($options['theme'] ?? 'modern', 'modern'); ?>>
                    <?php esc_html_e('Modern', 'wc-invoice'); ?>
                </option>
                <option value="flat" <?php selected($options['theme'] ?? 'modern', 'flat'); ?>>
                    <?php esc_html_e('Flat', 'wc-invoice'); ?>
                </option>
                <option value="simple" <?php selected($options['theme'] ?? 'modern', 'simple'); ?>>
                    <?php esc_html_e('Simple', 'wc-invoice'); ?>
                </option>
                <option value="classic" <?php selected($options['theme'] ?? 'modern', 'classic'); ?>>
                    <?php esc_html_e('Classic', 'wc-invoice'); ?>
                </option>
            </select>
            <p class="wc-invoice-description"><?php esc_html_e('Choose the theme style for your invoices.', 'wc-invoice'); ?></p>
        </div>

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Primary Color', 'wc-invoice'); ?>
            </label>
            <div class="wc-invoice-color-picker-wrapper">
                <input type="color" 
                       name="wc_invoice_settings[primary_color]" 
                       id="wc_invoice_primary_color" 
                       value="<?php echo esc_attr($options['primary_color'] ?? '#667eea'); ?>" 
                       class="wc-invoice-color-picker" />
                <input type="text" 
                       id="wc_invoice_primary_color_value" 
                       value="<?php echo esc_attr($options['primary_color'] ?? '#667eea'); ?>" 
                       class="wc-invoice-color-value" 
                       readonly />
            </div>
            <p class="wc-invoice-description"><?php esc_html_e('Primary color used in the invoice theme.', 'wc-invoice'); ?></p>
        </div>

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Text Color', 'wc-invoice'); ?>
            </label>
            <div class="wc-invoice-color-picker-wrapper">
                <input type="color" 
                       name="wc_invoice_settings[text_color]" 
                       id="wc_invoice_text_color" 
                       value="<?php echo esc_attr($options['text_color'] ?? '#2d3748'); ?>" 
                       class="wc-invoice-color-picker" />
                <input type="text" 
                       id="wc_invoice_text_color_value" 
                       value="<?php echo esc_attr($options['text_color'] ?? '#2d3748'); ?>" 
                       class="wc-invoice-color-value" 
                       readonly />
            </div>
            <p class="wc-invoice-description"><?php esc_html_e('Main text color for invoice content.', 'wc-invoice'); ?></p>
        </div>

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Font Family', 'wc-invoice'); ?>
            </label>
            <p class="wc-invoice-description" style="margin-bottom: 15px;"><?php esc_html_e('Upload custom font files for your invoices. Supported formats: TTF, WOFF, WOFF2, EOT, SVG', 'wc-invoice'); ?></p>
            
            <div class="wc-invoice-font-uploads">
                <div class="wc-invoice-font-upload-item">
                    <label class="wc-invoice-label-small"><?php esc_html_e('TTF', 'wc-invoice'); ?></label>
                    <?php $this->renderFontUpload('ttf', $options); ?>
                </div>
                
                <div class="wc-invoice-font-upload-item">
                    <label class="wc-invoice-label-small"><?php esc_html_e('WOFF', 'wc-invoice'); ?></label>
                    <?php $this->renderFontUpload('woff', $options); ?>
                </div>
                
                <div class="wc-invoice-font-upload-item">
                    <label class="wc-invoice-label-small"><?php esc_html_e('WOFF2', 'wc-invoice'); ?></label>
                    <?php $this->renderFontUpload('woff2', $options); ?>
                </div>
                
                <div class="wc-invoice-font-upload-item">
                    <label class="wc-invoice-label-small"><?php esc_html_e('EOT', 'wc-invoice'); ?></label>
                    <?php $this->renderFontUpload('eot', $options); ?>
                </div>
                
                <div class="wc-invoice-font-upload-item">
                    <label class="wc-invoice-label-small"><?php esc_html_e('SVG', 'wc-invoice'); ?></label>
                    <?php $this->renderFontUpload('svg', $options); ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render font upload field
     *
     * @param string $format Font format (ttf, woff, woff2, eot, svg)
     * @param array $options Settings options
     * @return void
     */
    private function renderFontUpload(string $format, array $options): void
    {
        $font_id = $options['font_' . $format . '_id'] ?? 0;
        $font_url = $font_id ? wp_get_attachment_url($font_id) : '';
        ?>
        <div class="wc-invoice-font-upload-wrapper">
            <?php if ($font_url): ?>
                <div class="wc-invoice-font-preview">
                    <span class="wc-invoice-font-name"><?php echo esc_html(basename($font_url)); ?></span>
                    <button type="button" class="wc-invoice-btn-remove-font" data-format="<?php echo esc_attr($format); ?>">
                        <?php esc_html_e('Remove', 'wc-invoice'); ?>
                    </button>
                </div>
            <?php endif; ?>
            <input type="hidden" name="wc_invoice_settings[font_<?php echo esc_attr($format); ?>_id]" 
                   id="wc_invoice_font_<?php echo esc_attr($format); ?>_id" 
                   value="<?php echo esc_attr($font_id); ?>" />
            <button type="button" class="wc-invoice-btn wc-invoice-btn-secondary wc-invoice-upload-font" 
                    data-format="<?php echo esc_attr($format); ?>"
                    data-accept="<?php echo esc_attr('.' . $format); ?>">
                <span class="wc-invoice-btn-icon">📁</span>
                <?php echo esc_html(sprintf(__('Upload %s', 'wc-invoice'), strtoupper($format))); ?>
            </button>
        </div>
        <?php
    }

}

