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

        // Signature
        if (isset($input['signature_id'])) {
            $sanitized['signature_id'] = absint($input['signature_id']);
        }

        // Address
        if (isset($input['address'])) {
            $sanitized['address'] = sanitize_textarea_field($input['address']);
        }

        // Date Format
        if (isset($input['date_format'])) {
            $allowed_formats = ['d/m/Y', 'm/d/Y', 'Y-m-d', 'F j, Y', 'j F Y', 'd M Y'];
            $sanitized['date_format'] = in_array($input['date_format'], $allowed_formats) ? $input['date_format'] : 'd/m/Y';
        }

        // Fields visibility
        $fields = ['first_name', 'last_name', 'address', 'email', 'phone', 'payment_method', 'transaction_id', 'customer_note', 'order_note'];
        foreach ($fields as $field) {
            if (isset($input['show_field_' . $field])) {
                $sanitized['show_field_' . $field] = (bool) $input['show_field_' . $field];
            } else {
                $sanitized['show_field_' . $field] = false;
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

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Signature', 'wc-invoice'); ?>
            </label>
            <div class="wc-invoice-signature-upload">
                <?php
                $signature_id = $options['signature_id'] ?? 0;
                $signature_url = $signature_id ? wp_get_attachment_image_url($signature_id, 'full') : '';
                ?>
                <div class="wc-invoice-signature-preview" style="<?php echo $signature_url ? '' : 'display: none;'; ?>">
                    <img src="<?php echo esc_url($signature_url); ?>" alt="<?php esc_attr_e('Signature', 'wc-invoice'); ?>" style="max-width: 200px; max-height: 100px; margin-bottom: 10px;" />
                    <button type="button" class="wc-invoice-btn wc-invoice-btn-secondary wc-invoice-remove-signature" style="margin-left: 10px;">
                        <?php esc_html_e('Remove Signature', 'wc-invoice'); ?>
                    </button>
                </div>
                <input type="hidden" name="wc_invoice_settings[signature_id]" id="wc_invoice_signature_id" value="<?php echo esc_attr($signature_id); ?>" />
                <button type="button" class="wc-invoice-btn wc-invoice-btn-secondary wc-invoice-upload-signature">
                    <span class="wc-invoice-btn-icon">✍️</span>
                    <?php esc_html_e('Upload Signature', 'wc-invoice'); ?>
                </button>
            </div>
            <p class="wc-invoice-description"><?php esc_html_e('Upload signature image to display on invoices.', 'wc-invoice'); ?></p>
        </div>

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Address', 'wc-invoice'); ?>
            </label>
            <?php
            // Get WooCommerce store address as default
            $woocommerce_address = '';
            if (class_exists('WooCommerce') && function_exists('WC')) {
                $countries = WC()->countries;
                if ($countries) {
                    $woocommerce_address = $countries->get_formatted_address([
                        'address_1' => $countries->get_base_address(),
                        'address_2' => $countries->get_base_address_2(),
                        'city' => $countries->get_base_city(),
                        'state' => $countries->get_base_state(),
                        'postcode' => $countries->get_base_postcode(),
                        'country' => $countries->get_base_country(),
                    ], "\n");
                }
            }
            
            // Use saved address or WooCommerce address as default
            $address = !empty($options['address']) ? $options['address'] : $woocommerce_address;
            ?>
            <textarea name="wc_invoice_settings[address]" 
                      class="wc-invoice-textarea" 
                      rows="4" 
                      placeholder="<?php esc_attr_e('Enter your business address', 'wc-invoice'); ?>"><?php echo esc_textarea($address); ?></textarea>
            <p class="wc-invoice-description"><?php esc_html_e('Business address displayed on invoices. Default value is taken from WooCommerce store settings and can be edited here without affecting WooCommerce settings.', 'wc-invoice'); ?></p>
        </div>

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Date Format', 'wc-invoice'); ?>
            </label>
            <select name="wc_invoice_settings[date_format]" class="wc-invoice-select">
                <?php
                $date_formats = [
                    'd/m/Y' => date('d/m/Y') . ' (d/m/Y)',
                    'm/d/Y' => date('m/d/Y') . ' (m/d/Y)',
                    'Y-m-d' => date('Y-m-d') . ' (Y-m-d)',
                    'F j, Y' => date('F j, Y') . ' (F j, Y)',
                    'j F Y' => date('j F Y') . ' (j F Y)',
                    'd M Y' => date('d M Y') . ' (d M Y)',
                ];
                $selected_format = $options['date_format'] ?? 'd/m/Y';
                foreach ($date_formats as $format => $label):
                ?>
                    <option value="<?php echo esc_attr($format); ?>" <?php selected($selected_format, $format); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="wc-invoice-description"><?php esc_html_e('Choose how dates are displayed on invoices.', 'wc-invoice'); ?></p>
        </div>

        <div class="wc-invoice-form-group">
            <label class="wc-invoice-label">
                <?php esc_html_e('Fields', 'wc-invoice'); ?>
            </label>
            <p class="wc-invoice-description" style="margin-bottom: 15px;"><?php esc_html_e('Select which fields to display on invoices.', 'wc-invoice'); ?></p>
            
            <div class="wc-invoice-fields-list">
                <div class="wc-invoice-field-item">
                    <div class="wc-invoice-field-info">
                        <span class="wc-invoice-field-label"><?php esc_html_e('First Name', 'wc-invoice'); ?></span>
                        <span class="wc-invoice-field-description"><?php esc_html_e('Display customer first name on invoice', 'wc-invoice'); ?></span>
                    </div>
                    <label class="wc-invoice-switch">
                        <input type="checkbox" 
                               name="wc_invoice_settings[show_field_first_name]" 
                               value="1" 
                               <?php checked($options['show_field_first_name'] ?? true, true); ?> />
                        <span class="wc-invoice-switch-slider"></span>
                    </label>
                </div>

                <div class="wc-invoice-field-item">
                    <div class="wc-invoice-field-info">
                        <span class="wc-invoice-field-label"><?php esc_html_e('Last Name', 'wc-invoice'); ?></span>
                        <span class="wc-invoice-field-description"><?php esc_html_e('Display customer last name on invoice', 'wc-invoice'); ?></span>
                    </div>
                    <label class="wc-invoice-switch">
                        <input type="checkbox" 
                               name="wc_invoice_settings[show_field_last_name]" 
                               value="1" 
                               <?php checked($options['show_field_last_name'] ?? true, true); ?> />
                        <span class="wc-invoice-switch-slider"></span>
                    </label>
                </div>

                <div class="wc-invoice-field-item">
                    <div class="wc-invoice-field-info">
                        <span class="wc-invoice-field-label"><?php esc_html_e('Address', 'wc-invoice'); ?></span>
                        <span class="wc-invoice-field-description"><?php esc_html_e('Display customer address on invoice', 'wc-invoice'); ?></span>
                    </div>
                    <label class="wc-invoice-switch">
                        <input type="checkbox" 
                               name="wc_invoice_settings[show_field_address]" 
                               value="1" 
                               <?php checked($options['show_field_address'] ?? true, true); ?> />
                        <span class="wc-invoice-switch-slider"></span>
                    </label>
                </div>

                <div class="wc-invoice-field-item">
                    <div class="wc-invoice-field-info">
                        <span class="wc-invoice-field-label"><?php esc_html_e('Email', 'wc-invoice'); ?></span>
                        <span class="wc-invoice-field-description"><?php esc_html_e('Display customer email on invoice', 'wc-invoice'); ?></span>
                    </div>
                    <label class="wc-invoice-switch">
                        <input type="checkbox" 
                               name="wc_invoice_settings[show_field_email]" 
                               value="1" 
                               <?php checked($options['show_field_email'] ?? true, true); ?> />
                        <span class="wc-invoice-switch-slider"></span>
                    </label>
                </div>

                <div class="wc-invoice-field-item">
                    <div class="wc-invoice-field-info">
                        <span class="wc-invoice-field-label"><?php esc_html_e('Phone', 'wc-invoice'); ?></span>
                        <span class="wc-invoice-field-description"><?php esc_html_e('Display customer phone number on invoice', 'wc-invoice'); ?></span>
                    </div>
                    <label class="wc-invoice-switch">
                        <input type="checkbox" 
                               name="wc_invoice_settings[show_field_phone]" 
                               value="1" 
                               <?php checked($options['show_field_phone'] ?? true, true); ?> />
                        <span class="wc-invoice-switch-slider"></span>
                    </label>
                </div>

                <div class="wc-invoice-field-item">
                    <div class="wc-invoice-field-info">
                        <span class="wc-invoice-field-label"><?php esc_html_e('Payment Method', 'wc-invoice'); ?></span>
                        <span class="wc-invoice-field-description"><?php esc_html_e('Display payment method on invoice', 'wc-invoice'); ?></span>
                    </div>
                    <label class="wc-invoice-switch">
                        <input type="checkbox" 
                               name="wc_invoice_settings[show_field_payment_method]" 
                               value="1" 
                               <?php checked($options['show_field_payment_method'] ?? true, true); ?> />
                        <span class="wc-invoice-switch-slider"></span>
                    </label>
                </div>

                <div class="wc-invoice-field-item">
                    <div class="wc-invoice-field-info">
                        <span class="wc-invoice-field-label"><?php esc_html_e('Transaction ID', 'wc-invoice'); ?></span>
                        <span class="wc-invoice-field-description"><?php esc_html_e('Display transaction ID on invoice', 'wc-invoice'); ?></span>
                    </div>
                    <label class="wc-invoice-switch">
                        <input type="checkbox" 
                               name="wc_invoice_settings[show_field_transaction_id]" 
                               value="1" 
                               <?php checked($options['show_field_transaction_id'] ?? false, true); ?> />
                        <span class="wc-invoice-switch-slider"></span>
                    </label>
                </div>

                <div class="wc-invoice-field-item">
                    <div class="wc-invoice-field-info">
                        <span class="wc-invoice-field-label"><?php esc_html_e('Customer Note', 'wc-invoice'); ?></span>
                        <span class="wc-invoice-field-description"><?php esc_html_e('Display customer note on invoice', 'wc-invoice'); ?></span>
                    </div>
                    <label class="wc-invoice-switch">
                        <input type="checkbox" 
                               name="wc_invoice_settings[show_field_customer_note]" 
                               value="1" 
                               <?php checked($options['show_field_customer_note'] ?? false, true); ?> />
                        <span class="wc-invoice-switch-slider"></span>
                    </label>
                </div>

                <div class="wc-invoice-field-item">
                    <div class="wc-invoice-field-info">
                        <span class="wc-invoice-field-label"><?php esc_html_e('Order Note', 'wc-invoice'); ?></span>
                        <span class="wc-invoice-field-description"><?php esc_html_e('Display order notes on invoice', 'wc-invoice'); ?></span>
                    </div>
                    <label class="wc-invoice-switch">
                        <input type="checkbox" 
                               name="wc_invoice_settings[show_field_order_note]" 
                               value="1" 
                               <?php checked($options['show_field_order_note'] ?? false, true); ?> />
                        <span class="wc-invoice-switch-slider"></span>
                    </label>
                </div>
            </div>
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

