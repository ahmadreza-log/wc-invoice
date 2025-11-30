# 🔌 WC Invoice Extensions

WC Invoice supports extensions (addons) that can be created as separate WordPress plugins. This allows you to extend the functionality of WC Invoice without modifying the core plugin.

## 📦 How It Works

Extensions are **separate WordPress plugins** that register themselves with WC Invoice using the extension API. This is similar to how Elementor, WooCommerce, and other major WordPress plugins handle extensions.

## 🚀 Creating an Extension

### Step 1: Create a WordPress Plugin

Create a new WordPress plugin with the standard plugin header:

```php
<?php
/**
 * Plugin Name: My WC Invoice Extension
 * Plugin URI: https://example.com/my-extension
 * Description: Adds custom functionality to WC Invoice
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * Text Domain: my-wc-invoice-extension
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * WC Invoice Requires: 0.0.10
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Register extension with WC Invoice
add_action('wc_invoice_register_addons', function() {
    if (!class_exists('WC_Invoice\Addon_Manager')) {
        return;
    }
    
    WC_Invoice\Addon_Manager::instance()->registerAddon(
        'my-extension-slug', // Unique slug
        __FILE__, // Main plugin file
        [
            'name' => 'My WC Invoice Extension',
            'version' => '1.0.0',
            'description' => 'Adds custom functionality to WC Invoice',
            'author' => 'Your Name',
            'author_uri' => 'https://example.com',
            'requires' => '0.0.10', // Minimum WC Invoice version required
        ]
    );
});

// Initialize extension when WC Invoice addon is loaded
add_action('wc_invoice_addon_loaded', function($slug, $data) {
    if ($slug === 'my-extension-slug') {
        // Initialize your extension here
        new My_WC_Invoice_Extension();
    }
}, 10, 2);

// Your extension class
class My_WC_Invoice_Extension {
    public function __construct() {
        $this->hooks();
    }
    
    private function hooks() {
        // Add your hooks here
        add_filter('wc_invoice_html', [$this, 'modifyInvoiceHTML'], 10, 2);
        add_action('wc_invoice_generated', [$this, 'onInvoiceGenerated'], 10, 2);
    }
    
    public function modifyInvoiceHTML($html, $invoice_id) {
        // Modify invoice HTML
        return $html;
    }
    
    public function onInvoiceGenerated($invoice_id, $order_id) {
        // Do something when invoice is generated
    }
}
```

### Step 2: Install as WordPress Plugin

1. Upload your plugin to `/wp-content/plugins/`
2. Activate it from WordPress admin → Plugins
3. Go to WooCommerce → Invoice Settings → Extensions
4. Activate your extension

## 🎣 Available Hooks

### Actions

- `wc_invoice_register_addons` - Register your extension
- `wc_invoice_addon_loaded` - Fired when extension is loaded
- `wc_invoice_addon_activated` - Fired when extension is activated
- `wc_invoice_addon_deactivated` - Fired when extension is deactivated
- `wc_invoice_generate` - Fired when invoice generation is requested
- `wc_invoice_generated` - Fired after invoice is generated

### Filters

- `wc_invoice_registered_addons` - Filter registered addons
- `wc_invoice_settings_fields` - Modify settings fields
- `wc_invoice_html` - Modify invoice HTML output
- `wc_invoice_number` - Modify invoice number
- `wc_invoice_template_path` - Modify template path

## 🔧 Extension API

Use `WC_Invoice\Addon_API` class for interacting with WC Invoice:

```php
use WC_Invoice\Addon_API;

// Get invoice
$invoice = Addon_API::getInvoice($invoice_id);

// Generate invoice
$invoice_id = Addon_API::generateInvoice($order_id);

// Get settings
$prefix = Addon_API::getSetting('invoice_prefix', 'INV-');

// Update settings
Addon_API::updateSetting('custom_field', 'value');

// Add notice
Addon_API::addNotice('Message', 'success');
```

## ✅ Best Practices

1. **Check if WC Invoice is active** before registering
2. **Use unique slug** for your extension
3. **Specify minimum version** requirement
4. **Use proper namespacing** for your classes
5. **Follow WordPress coding standards**
6. **Add proper error handling**
7. **Include uninstall cleanup** if needed

## 🔒 Security

- Always check `ABSPATH` constant
- Sanitize all user inputs
- Use nonces for forms
- Validate all data
- Escape all outputs

## 📝 Example Extensions

### Custom Invoice Fields

```php
add_filter('wc_invoice_settings_fields', function($fields) {
    $fields['custom_field'] = [
        'type' => 'text',
        'label' => 'Custom Field',
        'default' => '',
    ];
    return $fields;
});
```

### Modify Invoice HTML

```php
add_filter('wc_invoice_html', function($html, $invoice_id) {
    $custom_content = '<div class="custom-content">Custom content</div>';
    return $html . $custom_content;
}, 10, 2);
```

### Custom Actions

```php
add_action('wc_invoice_generated', function($invoice_id, $order_id) {
    // Send custom email
    // Update external system
    // Log to custom database
}, 10, 2);
```

## 🎯 Benefits

- ✅ **Separate plugins** - No need to modify core
- ✅ **Easy distribution** - Can be distributed via WordPress.org or your own site
- ✅ **Version control** - Each extension has its own version
- ✅ **Independent updates** - Update extensions separately
- ✅ **Better organization** - Keep functionality modular

