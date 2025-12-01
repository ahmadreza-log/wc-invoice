<?php

namespace WC_Invoice\Admin;

defined('ABSPATH') || exit;

/**
 * Settings Framework
 * 
 * A flexible settings framework similar to Codestar/Redux
 * Manages settings through array-based configuration
 */
class Framework
{
    private static $instance = null;
    private array $settings = [];
    private array $tabs = [];
    private array $fields = [];
    private string $option_name;
    private string $option_group;

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
        $this->option_name = 'wc_invoice_settings';
        $this->option_group = 'wc_invoice_settings';
    }

    /**
     * Initialize framework with settings configuration
     *
     * @param array $config Settings configuration array
     * @return void
     */
    public function init(array $config): void
    {
        // Allow modification of config before initialization
        $config = apply_filters('wc_invoice_settings_config', $config);
        
        $this->settings = $config;
        $this->parseConfig();
        $this->hooks();
    }

    /**
     * Parse configuration array
     *
     * @return void
     */
    private function parseConfig(): void
    {
        if (isset($this->settings['tabs'])) {
            $this->tabs = apply_filters('wc_invoice_settings_tabs', $this->settings['tabs']);
        }

        if (isset($this->settings['fields'])) {
            $this->fields = apply_filters('wc_invoice_settings_fields_config', $this->settings['fields']);
        }
    }

    /**
     * Register hooks
     *
     * @return void
     */
    private function hooks(): void
    {
        add_action('admin_init', [$this, 'registerSettings']);
    }

    /**
     * Register WordPress settings
     *
     * @return void
     */
    public function registerSettings(): void
    {
        $args = [
            'sanitize_callback' => [$this, 'sanitizeSettings'],
        ];

        // Allow modification of register_setting arguments
        $args = apply_filters('wc_invoice_register_settings_args', $args);
        
        register_setting($this->option_group, $this->option_name, $args);
    }

    /**
     * Sanitize settings based on field configuration
     *
     * @param array $input Raw input data
     * @return array Sanitized data
     */
    public function sanitizeSettings(array $input): array
    {
        // Allow modification of input before sanitization
        $input = apply_filters('wc_invoice_settings_before_sanitize', $input);
        
        $sanitized = [];
        
        foreach ($this->fields as $tab_id => $tab_fields) {
            foreach ($tab_fields as $field) {
                // Allow modification of field before processing
                $field = apply_filters('wc_invoice_settings_field_before_sanitize', $field, $tab_id);
                
                $field_id = $field['id'] ?? '';
                $field_type = $field['type'] ?? 'text';
                $field_name = $this->getFieldName($field_id, $field);
                
                if (empty($field_name)) {
                    continue;
                }

                // Check if field exists in input (even if value is empty string or '0')
                $value_exists = array_key_exists($field_name, $input);
                $value = $input[$field_name] ?? null;
                
                // Allow modification of value before sanitization
                $value = apply_filters('wc_invoice_settings_field_value_before_sanitize', $value, $field, $field_name);
                
                // If field exists in input (even with empty/0 value), always process it
                // This ensures removed values (like logo_id = '0') are saved
                if ($value_exists) {
                    $sanitized[$field_name] = $this->sanitizeField($value, $field);
                } elseif ($value !== null && $value !== '') {
                    // Field has a non-empty value, sanitize it
                    $sanitized[$field_name] = $this->sanitizeField($value, $field);
                } else {
                    // Field not in input, only set default if specified
                    // Otherwise it will keep current value from merge
                    if (isset($field['default'])) {
                        $sanitized[$field_name] = $field['default'];
                    }
                }

                // Allow modification of sanitized field value
                $sanitized[$field_name] = apply_filters('wc_invoice_settings_field_after_sanitize', $sanitized[$field_name] ?? null, $field, $field_name);
            }
        }

        // Allow modification of sanitized data
        $sanitized = apply_filters('wc_invoice_settings_sanitized', $sanitized, $input);

        return $sanitized;
    }

    /**
     * Get field name for form input
     *
     * @param string $field_id Field ID
     * @param array $field Field configuration
     * @return string
     */
    private function getFieldName(string $field_id, array $field): string
    {
        // Check if field has custom name
        if (isset($field['name'])) {
            return $field['name'];
        }

        // Check if field has prefix
        $prefix = $field['prefix'] ?? '';
        
        if ($prefix) {
            return $prefix . $field_id;
        }

        return $field_id;
    }

    /**
     * Sanitize field value based on type
     *
     * @param mixed $value Field value
     * @param array $field Field configuration
     * @return mixed Sanitized value
     */
    private function sanitizeField($value, array $field)
    {
        $type = $field['type'] ?? 'text';
        $sanitize_callback = $field['sanitize'] ?? null;

        // Use custom sanitize callback if provided
        if ($sanitize_callback && is_callable($sanitize_callback)) {
            return call_user_func($sanitize_callback, $value, $field);
        }

        // Allow custom sanitization by type
        $sanitized = apply_filters('wc_invoice_settings_sanitize_field_' . $type, null, $value, $field);
        if ($sanitized !== null) {
            return $sanitized;
        }

        // Default sanitization based on type
        switch ($type) {
            case 'text':
            case 'email':
            case 'url':
                return sanitize_text_field($value);
            
            case 'textarea':
                return sanitize_textarea_field($value);
            
            case 'number':
                return absint($value);
            
            case 'media':
                // Allow 0 for media removal
                if ($value === '' || $value === null || $value === false) {
                    return 0;
                }
                return absint($value);
            
            case 'color':
                return sanitize_hex_color($value);
            
            case 'select':
            case 'radio':
                $options = $field['options'] ?? [];
                if (is_array($options) && isset($options[$value])) {
                    return $value;
                }
                return $field['default'] ?? '';
            
            case 'switch':
            case 'checkbox':
                return (bool) $value;
            
            case 'group':
                if (is_array($value) && isset($field['fields'])) {
                    $sanitized_group = [];
                    foreach ($field['fields'] as $sub_field) {
                        $sub_id = $sub_field['id'] ?? '';
                        if (isset($value[$sub_id])) {
                            $sanitized_group[$sub_id] = $this->sanitizeField($value[$sub_id], $sub_field);
                        }
                    }
                    return $sanitized_group;
                }
                return [];
            
            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Get all tabs
     *
     * @return array
     */
    public function getTabs(): array
    {
        return apply_filters('wc_invoice_settings_get_tabs', $this->tabs);
    }

    /**
     * Get fields for a specific tab
     *
     * @param string $tab_id Tab ID
     * @return array
     */
    public function getFields(string $tab_id): array
    {
        $fields = $this->fields[$tab_id] ?? [];
        return apply_filters('wc_invoice_settings_get_fields', $fields, $tab_id);
    }

    /**
     * Get option name
     *
     * @return string
     */
    public function getOptionName(): string
    {
        return apply_filters('wc_invoice_settings_option_name', $this->option_name);
    }

    /**
     * Get option group
     *
     * @return string
     */
    public function getOptionGroup(): string
    {
        return apply_filters('wc_invoice_settings_option_group', $this->option_group);
    }

    /**
     * Get current settings values
     *
     * @return array
     */
    public function getSettings(): array
    {
        $settings = get_option($this->option_name, []);
        return apply_filters('wc_invoice_settings_get_all', $settings);
    }

    /**
     * Get a specific setting value
     *
     * @param string $field_id Field ID
     * @param mixed $default Default value
     * @return mixed
     */
    public function getSetting(string $field_id, $default = null)
    {
        $settings = $this->getSettings();
        $value = $settings[$field_id] ?? $default;
        return apply_filters('wc_invoice_settings_get', $value, $field_id, $default);
    }
}

