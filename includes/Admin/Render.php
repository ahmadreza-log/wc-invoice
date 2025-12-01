<?php

namespace WC_Invoice\Admin;

defined('ABSPATH') || exit;

/**
 * Field Renderer
 * 
 * Handles rendering of different field types
 */
class Render
{
    private array $options;
    private string $option_name;

    /**
     * Constructor
     *
     * @param array $options Current settings values
     * @param string $option_name Option name for form inputs
     */
    public function __construct(array $options, string $option_name)
    {
        $this->options = $options;
        $this->option_name = $option_name;
    }

    /**
     * Render a field based on its type
     *
     * @param array $field Field configuration
     * @return void
     */
    public function render(array $field): void
    {
        // Allow modification of field before rendering
        $field = apply_filters('wc_invoice_settings_field_before_render', $field);
        
        $type = $field['type'] ?? 'text';
        $method = 'render' . ucfirst($type);

        // Allow custom render method
        $custom_render = apply_filters('wc_invoice_settings_render_field_' . $type, null, $field, $this->options, $this->option_name);
        if ($custom_render !== null) {
            return;
        }

        if (method_exists($this, $method)) {
            $this->$method($field);
        } else {
            $this->renderText($field);
        }

        // Allow modification after rendering
        do_action('wc_invoice_settings_field_after_render', $field, $this->options, $this->option_name);
    }

    /**
     * Get field name attribute
     *
     * @param array $field Field configuration
     * @return string
     */
    private function getFieldName(array $field): string
    {
        $field_id = $field['id'] ?? '';
        
        if (isset($field['name'])) {
            return $this->option_name . '[' . $field['name'] . ']';
        }

        $prefix = $field['prefix'] ?? '';
        $name = $prefix ? $prefix . $field_id : $field_id;
        
        return $this->option_name . '[' . $name . ']';
    }

    /**
     * Get field value
     *
     * @param array $field Field configuration
     * @return mixed
     */
    private function getFieldValue(array $field)
    {
        $field_id = $field['id'] ?? '';
        $name = $field['name'] ?? ($field['prefix'] ?? '') . $field_id;
        $default = $field['default'] ?? '';
        
        $value = $this->options[$name] ?? $default;
        
        // Allow modification of field value
        return apply_filters('wc_invoice_settings_field_value', $value, $field, $name);
    }

    /**
     * Render text field
     *
     * @param array $field Field configuration
     * @return void
     */
    private function renderText(array $field): void
    {
        $name = $this->getFieldName($field);
        $value = $this->getFieldValue($field);
        $placeholder = $field['placeholder'] ?? '';
        $required = isset($field['required']) && $field['required'] ? 'required' : '';
        $description = $field['description'] ?? '';
        $label = $field['label'] ?? '';
        $show_required = isset($field['required']) && $field['required'];
        ?>
        <div class="wc-invoice-form-group">
            <?php if ($label): ?>
                <label class="wc-invoice-label">
                    <?php echo esc_html($label); ?>
                    <?php if ($show_required): ?>
                        <span class="wc-invoice-label-required">*</span>
                    <?php endif; ?>
                </label>
            <?php endif; ?>
            <input type="text" 
                   name="<?php echo esc_attr($name); ?>" 
                   value="<?php echo esc_attr($value); ?>" 
                   class="wc-invoice-input" 
                   placeholder="<?php echo esc_attr($placeholder); ?>" 
                   <?php echo $required; ?> />
            <?php if ($description): ?>
                <p class="wc-invoice-description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render textarea field
     *
     * @param array $field Field configuration
     * @return void
     */
    private function renderTextarea(array $field): void
    {
        $name = $this->getFieldName($field);
        $value = $this->getFieldValue($field);
        $placeholder = $field['placeholder'] ?? '';
        $rows = $field['rows'] ?? 4;
        $description = $field['description'] ?? '';
        $label = $field['label'] ?? '';
        $callback = $field['callback'] ?? null;

        // Allow custom value callback
        if ($callback && is_callable($callback)) {
            $value = call_user_func($callback, $value, $this->options);
        }
        ?>
        <div class="wc-invoice-form-group">
            <?php if ($label): ?>
                <label class="wc-invoice-label">
                    <?php echo esc_html($label); ?>
                </label>
            <?php endif; ?>
            <textarea name="<?php echo esc_attr($name); ?>" 
                      class="wc-invoice-textarea" 
                      rows="<?php echo esc_attr($rows); ?>" 
                      placeholder="<?php echo esc_attr($placeholder); ?>"><?php echo esc_textarea($value); ?></textarea>
            <?php if ($description): ?>
                <p class="wc-invoice-description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render select field
     *
     * @param array $field Field configuration
     * @return void
     */
    private function renderSelect(array $field): void
    {
        $name = $this->getFieldName($field);
        $value = $this->getFieldValue($field);
        $options = $field['options'] ?? [];
        $description = $field['description'] ?? '';
        $label = $field['label'] ?? '';
        $id = $field['id'] ?? '';
        ?>
        <div class="wc-invoice-form-group">
            <?php if ($label): ?>
                <label class="wc-invoice-label">
                    <?php echo esc_html($label); ?>
                </label>
            <?php endif; ?>
            <select name="<?php echo esc_attr($name); ?>" 
                    class="wc-invoice-select" 
                    <?php if ($id): ?>id="<?php echo esc_attr($id); ?>"<?php endif; ?>>
                <?php foreach ($options as $option_value => $option_label): ?>
                    <option value="<?php echo esc_attr($option_value); ?>" <?php selected($value, $option_value); ?>>
                        <?php echo esc_html($option_label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($description): ?>
                <p class="wc-invoice-description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render color field
     *
     * @param array $field Field configuration
     * @return void
     */
    private function renderColor(array $field): void
    {
        $name = $this->getFieldName($field);
        $value = $this->getFieldValue($field);
        $id = $field['id'] ?? '';
        $description = $field['description'] ?? '';
        $label = $field['label'] ?? '';
        ?>
        <div class="wc-invoice-form-group">
            <?php if ($label): ?>
                <label class="wc-invoice-label">
                    <?php echo esc_html($label); ?>
                </label>
            <?php endif; ?>
            <div class="wc-invoice-color-picker-wrapper">
                <input type="color" 
                       name="<?php echo esc_attr($name); ?>" 
                       id="<?php echo esc_attr($id); ?>" 
                       value="<?php echo esc_attr($value); ?>" 
                       class="wc-invoice-color-picker" />
                <input type="text" 
                       id="<?php echo esc_attr($id); ?>_value" 
                       value="<?php echo esc_attr($value); ?>" 
                       class="wc-invoice-color-value" 
                       readonly />
            </div>
            <?php if ($description): ?>
                <p class="wc-invoice-description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render switch/checkbox field
     *
     * @param array $field Field configuration
     * @return void
     */
    private function renderSwitch(array $field): void
    {
        $name = $this->getFieldName($field);
        $value = $this->getFieldValue($field);
        $label = $field['label'] ?? '';
        $description = $field['description'] ?? '';
        $default = $field['default'] ?? false;
        $checked = $value !== null ? (bool) $value : $default;
        ?>
        <div class="wc-invoice-field-item">
            <div class="wc-invoice-field-info">
                <?php if ($label): ?>
                    <span class="wc-invoice-field-label"><?php echo esc_html($label); ?></span>
                <?php endif; ?>
                <?php if ($description): ?>
                    <span class="wc-invoice-field-description"><?php echo esc_html($description); ?></span>
                <?php endif; ?>
            </div>
            <label class="wc-invoice-switch">
                <input type="checkbox" 
                       name="<?php echo esc_attr($name); ?>" 
                       value="1" 
                       <?php checked($checked, true); ?> />
                <span class="wc-invoice-switch-slider"></span>
            </label>
        </div>
        <?php
    }

    /**
     * Render media/upload field
     *
     * @param array $field Field configuration
     * @return void
     */
    private function renderMedia(array $field): void
    {
        $name = $this->getFieldName($field);
        $value = $this->getFieldValue($field);
        $id = $field['id'] ?? '';
        $label = $field['label'] ?? '';
        $description = $field['description'] ?? '';
        $button_text = $field['button_text'] ?? __('Upload', 'wc-invoice');
        $remove_text = $field['remove_text'] ?? __('Remove', 'wc-invoice');
        $preview_class = $field['preview_class'] ?? '';
        $upload_class = $field['upload_class'] ?? '';
        $remove_class = $field['remove_class'] ?? '';
        $icon = $field['icon'] ?? '📷';
        
        $media_id = absint($value);
        $media_url = $media_id ? wp_get_attachment_image_url($media_id, 'full') : '';
        ?>
        <div class="wc-invoice-form-group">
            <?php if ($label): ?>
                <label class="wc-invoice-label">
                    <?php echo esc_html($label); ?>
                </label>
            <?php endif; ?>
            <div class="wc-invoice-<?php echo esc_attr($id); ?>-upload">
                <?php if ($media_url): ?>
                    <div class="wc-invoice-<?php echo esc_attr($id); ?>-preview <?php echo esc_attr($preview_class); ?>" style="display: flex;">
                        <img src="<?php echo esc_url($media_url); ?>" 
                             alt="<?php echo esc_attr($label); ?>" 
                             style="max-width: 200px; max-height: 100px; margin-bottom: 10px;" />
                        <button type="button" 
                                class="wc-invoice-btn wc-invoice-btn-secondary <?php echo esc_attr($remove_class); ?>" 
                                style="margin-left: 10px;">
                            <?php echo esc_html($remove_text); ?>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="wc-invoice-<?php echo esc_attr($id); ?>-preview <?php echo esc_attr($preview_class); ?>" style="display: none;"></div>
                <?php endif; ?>
                <input type="hidden" 
                       name="<?php echo esc_attr($name); ?>" 
                       id="<?php echo esc_attr($id); ?>" 
                       value="<?php echo esc_attr($media_id); ?>" />
                <button type="button" 
                        class="wc-invoice-btn wc-invoice-btn-secondary <?php echo esc_attr($upload_class); ?>">
                    <span class="wc-invoice-btn-icon"><?php echo esc_html($icon); ?></span>
                    <?php echo esc_html($button_text); ?>
                </button>
            </div>
            <?php if ($description): ?>
                <p class="wc-invoice-description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render group field (for fields list)
     *
     * @param array $field Field configuration
     * @return void
     */
    private function renderGroup(array $field): void
    {
        $label = $field['label'] ?? '';
        $description = $field['description'] ?? '';
        $fields = $field['fields'] ?? [];
        ?>
        <div class="wc-invoice-form-group">
            <?php if ($label): ?>
                <label class="wc-invoice-label">
                    <?php echo esc_html($label); ?>
                </label>
            <?php endif; ?>
            <?php if ($description): ?>
                <p class="wc-invoice-description" style="margin-bottom: 15px;"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
            
            <div class="wc-invoice-fields-list">
                <?php foreach ($fields as $sub_field): ?>
                    <?php $this->render($sub_field); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render custom field (allows custom callback)
     *
     * @param array $field Field configuration
     * @return void
     */
    private function renderCustom(array $field): void
    {
        $callback = $field['callback'] ?? null;
        
        if ($callback && is_callable($callback)) {
            call_user_func($callback, $field, $this->options, $this->option_name);
        }
    }
}

