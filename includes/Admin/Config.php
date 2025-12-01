<?php

namespace WC_Invoice\Admin;

defined('ABSPATH') || exit;

/**
 * Settings Configuration
 * 
 * Define all settings fields using array structure
 */
class Config
{
    /**
     * Get settings configuration
     *
     * @return array
     */
    public static function getConfig(): array
    {
        $config = [
            'tabs' => self::getTabs(),
            'fields' => self::getFields(),
        ];

        // Allow modification of config
        return apply_filters('wc_invoice_settings_config_get', $config);
    }

    /**
     * Get tabs configuration
     *
     * @return array
     */
    private static function getTabs(): array
    {
        $tabs = [
            'general' => [
                'title' => __('General', 'wc-invoice'),
                'icon' => '⚙️',
            ],
            'theme' => [
                'title' => __('Theme', 'wc-invoice'),
                'icon' => '🎨',
            ],
        ];

        return apply_filters('wc_invoice_settings_tabs_config', $tabs);
    }

    /**
     * Get fields configuration
     *
     * @return array
     */
    private static function getFields(): array
    {
        $fields = [
            'general' => self::getGeneralFields(),
            'theme' => self::getThemeFields(),
        ];

        return apply_filters('wc_invoice_settings_fields_config_get', $fields);
    }

    /**
     * Get general tab fields
     *
     * @return array
     */
    private static function getGeneralFields(): array
    {
        $fields = [
            [
                'id' => 'invoice_prefix',
                'type' => 'text',
                'label' => __('Invoice Prefix', 'wc-invoice'),
                'description' => __('Prefix for invoice numbers (e.g., INV-, FA-, INVOICE-).', 'wc-invoice'),
                'placeholder' => 'INV-',
                'default' => 'INV-',
                'required' => true,
            ],
            [
                'id' => 'title',
                'type' => 'text',
                'label' => __('Title', 'wc-invoice'),
                'description' => __('Invoice title displayed on the invoice document.', 'wc-invoice'),
                'placeholder' => __('Invoice', 'wc-invoice'),
                'default' => '',
            ],
            [
                'id' => 'logo_id',
                'type' => 'media',
                'label' => __('Logo', 'wc-invoice'),
                'description' => __('Upload your company logo to display on invoices.', 'wc-invoice'),
                'button_text' => __('Upload Logo', 'wc-invoice'),
                'remove_text' => __('Remove Logo', 'wc-invoice'),
                'upload_class' => 'wc-invoice-upload-logo',
                'remove_class' => 'wc-invoice-remove-logo',
                'preview_class' => 'wc-invoice-logo-preview',
                'icon' => '📷',
                'default' => 0,
            ],
            [
                'id' => 'signature_id',
                'type' => 'media',
                'label' => __('Signature', 'wc-invoice'),
                'description' => __('Upload signature image to display on invoices.', 'wc-invoice'),
                'button_text' => __('Upload Signature', 'wc-invoice'),
                'remove_text' => __('Remove Signature', 'wc-invoice'),
                'upload_class' => 'wc-invoice-upload-signature',
                'remove_class' => 'wc-invoice-remove-signature',
                'preview_class' => 'wc-invoice-signature-preview',
                'icon' => '✍️',
                'default' => 0,
            ],
            [
                'id' => 'address',
                'type' => 'textarea',
                'label' => __('Address', 'wc-invoice'),
                'description' => __('Business address displayed on invoices. Default value is taken from WooCommerce store settings and can be edited here without affecting WooCommerce settings.', 'wc-invoice'),
                'placeholder' => __('Enter your business address', 'wc-invoice'),
                'rows' => 4,
                'callback' => [self::class, 'getWooCommerceAddress'],
                'default' => '',
            ],
            [
                'id' => 'date_format',
                'type' => 'select',
                'label' => __('Date Format', 'wc-invoice'),
                'description' => __('Choose how dates are displayed on invoices.', 'wc-invoice'),
                'options' => self::getDateFormats(),
                'default' => 'd/m/Y',
            ],
            [
                'id' => 'fields',
                'type' => 'group',
                'label' => __('Fields', 'wc-invoice'),
                'description' => __('Select which fields to display on invoices.', 'wc-invoice'),
                'fields' => self::getFieldVisibilityFields(),
            ],
        ];

        return apply_filters('wc_invoice_settings_general_fields', $fields);
    }

    /**
     * Get theme tab fields
     *
     * @return array
     */
    private static function getThemeFields(): array
    {
        $fields = [
            [
                'id' => 'theme',
                'type' => 'select',
                'label' => __('Select Theme', 'wc-invoice'),
                'description' => __('Choose the theme style for your invoices.', 'wc-invoice'),
                'options' => [
                    'modern' => __('Modern', 'wc-invoice'),
                    'flat' => __('Flat', 'wc-invoice'),
                    'simple' => __('Simple', 'wc-invoice'),
                    'classic' => __('Classic', 'wc-invoice'),
                ],
                'default' => 'modern',
            ],
            [
                'id' => 'primary_color',
                'type' => 'color',
                'label' => __('Primary Color', 'wc-invoice'),
                'description' => __('Primary color used in the invoice theme.', 'wc-invoice'),
                'default' => '#667eea',
            ],
            [
                'id' => 'text_color',
                'type' => 'color',
                'label' => __('Text Color', 'wc-invoice'),
                'description' => __('Main text color for invoice content.', 'wc-invoice'),
                'default' => '#2d3748',
            ],
            [
                'id' => 'fonts',
                'type' => 'group',
                'label' => __('Font Family', 'wc-invoice'),
                'description' => __('Upload custom font files for your invoices. Supported formats: TTF, WOFF, WOFF2, EOT, SVG', 'wc-invoice'),
                'fields' => self::getFontFields(),
            ],
        ];

        return apply_filters('wc_invoice_settings_theme_fields', $fields);
    }

    /**
     * Get date format options
     *
     * @return array
     */
    private static function getDateFormats(): array
    {
        $formats = [
            'd/m/Y' => 'd/m/Y',
            'm/d/Y' => 'm/d/Y',
            'Y-m-d' => 'Y-m-d',
            'F j, Y' => 'F j, Y',
            'j F Y' => 'j F Y',
            'd M Y' => 'd M Y',
        ];

        $options = [];
        foreach ($formats as $format => $label) {
            $options[$format] = date($format) . ' (' . $label . ')';
        }

        return apply_filters('wc_invoice_settings_date_formats', $options);
    }

    /**
     * Get field visibility fields
     *
     * @return array
     */
    private static function getFieldVisibilityFields(): array
    {
        $fields = [
            'first_name' => __('First Name', 'wc-invoice'),
            'last_name' => __('Last Name', 'wc-invoice'),
            'company' => __('Company', 'wc-invoice'),
            'address' => __('Address', 'wc-invoice'),
            'city' => __('City', 'wc-invoice'),
            'country' => __('Country', 'wc-invoice'),
            'state' => __('State', 'wc-invoice'),
            'zip_code' => __('Zip Code', 'wc-invoice'),
            'email' => __('Email', 'wc-invoice'),
            'phone' => __('Phone', 'wc-invoice'),
            'payment_method' => __('Payment Method', 'wc-invoice'),
            'transaction_id' => __('Transaction ID', 'wc-invoice'),
            'customer_note' => __('Customer Note', 'wc-invoice'),
            'order_note' => __('Order Note', 'wc-invoice'),
        ];

        // Allow modification of field list
        $fields = apply_filters('wc_invoice_settings_visibility_fields_list', $fields);

        $field_configs = [];
        foreach ($fields as $field_id => $field_label) {
            $field_configs[] = [
                'id' => $field_id,
                'type' => 'switch',
                'name' => 'show_field_' . $field_id,
                'label' => $field_label,
                'description' => sprintf(__('Display customer %s on invoice', 'wc-invoice'), strtolower($field_label)),
                'default' => in_array($field_id, ['transaction_id', 'customer_note', 'order_note']) ? false : true,
            ];
        }

        return apply_filters('wc_invoice_settings_visibility_fields', $field_configs);
    }

    /**
     * Get font upload fields
     *
     * @return array
     */
    private static function getFontFields(): array
    {
        $formats = ['ttf', 'woff', 'woff2', 'eot', 'svg'];
        
        // Allow modification of font formats
        $formats = apply_filters('wc_invoice_settings_font_formats', $formats);
        
        $font_fields = [];

        foreach ($formats as $format) {
            $font_fields[] = [
                'id' => 'font_' . $format . '_id',
                'type' => 'custom',
                'callback' => [self::class, 'renderFontUpload'],
                'format' => $format,
            ];
        }

        return apply_filters('wc_invoice_settings_font_fields', $font_fields);
    }

    /**
     * Get WooCommerce address as default
     *
     * @param string $current_value Current value
     * @param array $options All options
     * @return string
     */
    public static function getWooCommerceAddress(string $current_value, array $options): string
    {
        if (!empty($current_value)) {
            return $current_value;
        }

        if (!class_exists('WooCommerce') || !function_exists('WC')) {
            return '';
        }

        $countries = WC()->countries;
        if (!$countries) {
            return '';
        }

        $address = $countries->get_formatted_address([
            'address_1' => $countries->get_base_address(),
            'address_2' => $countries->get_base_address_2(),
            'city' => $countries->get_base_city(),
            'state' => $countries->get_base_state(),
            'postcode' => $countries->get_base_postcode(),
            'country' => $countries->get_base_country(),
        ], "\n");

        return apply_filters('wc_invoice_settings_woocommerce_address', $address, $current_value, $options);
    }

    /**
     * Render font upload field
     *
     * @param array $field Field configuration
     * @param array $options Current options
     * @param string $option_name Option name
     * @return void
     */
    public static function renderFontUpload(array $field, array $options, string $option_name): void
    {
        $format = $field['format'] ?? '';
        $field_id = 'font_' . $format . '_id';
        $field_name = $option_name . '[' . $field_id . ']';
        $font_id = $options[$field_id] ?? 0;
        $font_url = $font_id ? wp_get_attachment_url($font_id) : '';

        // Allow modification before rendering
        do_action('wc_invoice_settings_before_render_font_upload', $field, $options, $option_name);
        ?>
        <div class="wc-invoice-font-upload-item">
            <label class="wc-invoice-label-small"><?php echo esc_html(strtoupper($format)); ?></label>
            <div class="wc-invoice-font-upload-wrapper">
                <?php if ($font_url): ?>
                    <div class="wc-invoice-font-preview">
                        <span class="wc-invoice-font-name"><?php echo esc_html(basename($font_url)); ?></span>
                        <button type="button" 
                                class="wc-invoice-btn-remove-font" 
                                data-format="<?php echo esc_attr($format); ?>">
                            <?php esc_html_e('Remove', 'wc-invoice'); ?>
                        </button>
                    </div>
                <?php endif; ?>
                <input type="hidden" 
                       name="<?php echo esc_attr($field_name); ?>" 
                       id="wc_invoice_font_<?php echo esc_attr($format); ?>_id" 
                       value="<?php echo esc_attr($font_id); ?>" />
                <button type="button" 
                        class="wc-invoice-btn wc-invoice-btn-secondary wc-invoice-upload-font" 
                        data-format="<?php echo esc_attr($format); ?>"
                        data-accept="<?php echo esc_attr('.' . $format); ?>">
                    <span class="wc-invoice-btn-icon">📁</span>
                    <?php echo esc_html(sprintf(__('Upload %s', 'wc-invoice'), strtoupper($format))); ?>
                </button>
            </div>
        </div>
        <?php
        // Allow modification after rendering
        do_action('wc_invoice_settings_after_render_font_upload', $field, $options, $option_name);
    }
}

