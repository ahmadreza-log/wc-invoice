# 📦 WC Invoice Addons

This directory is for WC Invoice addons/extensions. Addons extend the functionality of the main plugin.

## 📁 Directory Structure

```
addons/
├── your-addon-name/
│   ├── addon.php (required)
│   ├── index.php (required)
│   └── ... (other files)
```

## 📝 Creating an Addon

### 1. Create Addon Directory

Create a new directory in `addons/` with your addon name (use lowercase, hyphens for spaces).

### 2. Create addon.php

Create `addon.php` file with the following header:

```php
<?php
/**
 * Addon Name: Your Addon Name
 * Addon URI: https://yourwebsite.com
 * Description: Description of your addon
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * Requires: 0.0.6
 * Text Domain: wc-invoice-your-addon
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

// Your addon code here
```

### 3. Addon Class Structure

```php
class WC_Invoice_Your_Addon
{
    public function __construct()
    {
        $this->hooks();
    }

    private function hooks(): void
    {
        // Register your hooks here
    }
}

// Initialize when addon is loaded
add_action('wc_invoice_addon_loaded', function($slug, $data) {
    if ($slug === 'your-addon-name') {
        new WC_Invoice_Your_Addon();
    }
}, 10, 2);
```

## 🎣 Available Hooks

### Actions

- `wc_invoice_addon_loaded` - Fired when addon is loaded
- `wc_invoice_addon_activated` - Fired when addon is activated
- `wc_invoice_addon_deactivated` - Fired when addon is deactivated
- `wc_invoice_generate` - Fired when invoice generation is requested
- `wc_invoice_generated` - Fired after invoice is generated

### Filters

- `wc_invoice_settings_fields` - Modify settings fields
- `wc_invoice_html` - Modify invoice HTML output
- `wc_invoice_number` - Modify invoice number
- `wc_invoice_template_path` - Modify template path

## 🔧 Addon API

Use `WC_Invoice\Addon_API` class for interacting with the plugin:

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

## 📋 Example Addon

See `example-addon/` directory for a complete example.

## ✅ Requirements

- Addon must have `addon.php` file
- Addon must have proper header information
- Addon directory must have `index.php` for security
- Minimum required version can be specified in header

## 🚀 Best Practices

1. Use unique class names (prefix with your addon name)
2. Use proper namespacing if needed
3. Check plugin version compatibility
4. Use provided API methods
5. Follow WordPress coding standards
6. Add proper error handling
7. Include uninstall cleanup if needed

## 🔒 Security

- Always check `ABSPATH` constant
- Sanitize all user inputs
- Use nonces for forms
- Validate all data
- Escape all outputs

