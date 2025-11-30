# 🧾 WC Invoice

> Professional invoice generator plugin for WooCommerce with PDF and HTML invoice generation capabilities

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue.svg)](https://wordpress.org/)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-5.0%2B-purple.svg)](https://woocommerce.com/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net/)
[![Version](https://img.shields.io/badge/Version-0.0.1-blue.svg)](https://github.com/your-username/wc-invoice)

## 📋 Table of Contents

- [Features](#-features)
- [Installation](#-installation)
- [Usage](#-usage)
- [Requirements](#-requirements)
- [Project Structure](#-project-structure)
- [Development](#-development)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

- 🎯 **Automatic Invoice Generation** - Automatically generate invoices for WooCommerce orders
- 📊 **Invoice Management** - Manage all invoices from the admin panel
- 📥 **Download Capability** - Download invoices in multiple formats
- 🎨 **Customizable Templates** - Multiple invoice templates to choose from
- 🔢 **Custom Invoice Numbers** - Configure your own invoice number prefix
- 🌍 **Internationalization Ready** - Fully translatable with WordPress i18n
- ⚡ **PSR-4 Autoloading** - Modern code structure without Composer dependency
- 🔒 **Secure** - Built with WordPress security best practices

## 🚀 Installation

### Manual Installation

1. Download or clone this repository
2. Upload the `wc-invoice` folder to `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Make sure WooCommerce is installed and activated

### Via Git

```bash
cd wp-content/plugins/
git clone https://github.com/your-username/wc-invoice.git
```

## 📖 Usage

After activating the plugin:

1. Navigate to **WC Invoice** in the WordPress admin menu
2. Configure invoice settings (prefix, template, etc.)
3. Generate invoices from WooCommerce orders page
4. View and download invoices from the order edit page

### Generating an Invoice

1. Go to **WooCommerce → Orders**
2. Open an order
3. Click **Generate Invoice** button in the invoice meta box
4. Download or view the generated invoice

## 📋 Requirements

- **WordPress**: 5.8 or higher
- **PHP**: 7.4 or higher
- **WooCommerce**: 5.0 or higher
- **MySQL**: 5.6 or higher

## 📁 Project Structure

```
wc-invoice/
├── 📂 assets/
│   ├── 📂 css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── 📂 js/
│       └── admin.js
├── 📂 includes/
│   ├── Admin.php
│   ├── Ajax.php
│   ├── Database.php
│   ├── Enqueue.php
│   ├── Generator.php
│   ├── Notices.php
│   ├── Woocommerce.php
│   └── 📂 helpers/
│       └── functions.php
├── 📂 templates/
│   └── invoice.php
├── 📂 vendor/
│   └── Autoloader.php
├── 📂 languages/
├── wc-invoice.php
├── uninstall.php
└── README.md
```

## 🛠️ Development

This plugin uses **PSR-4 Autoloading** and does not require Composer.

### Namespace Structure

- `WC_Invoice` - Main classes
- `WC_Invoice\Admin` - Admin panel related classes
- `WC_Invoice\Helpers` - Helper functions
- `WC_Invoice\Integrations` - Integration with other plugins

### Setting Up Development Environment

1. Clone the repository
2. Navigate to the plugin directory
3. The plugin uses WordPress coding standards
4. All classes follow PSR-4 autoloading convention

### Code Style

- Follow WordPress Coding Standards
- Use PSR-4 autoloading
- All user-facing strings must be translatable
- Use type hints where possible (PHP 7.4+)

## 🌍 Internationalization

The plugin is fully internationalized and ready for translation. All user-facing strings use WordPress translation functions (`__()`, `_e()`, `esc_html__()`, etc.) with the text domain `wc-invoice`.

### Translating the Plugin

1. Use a translation tool like [Poedit](https://poedit.net/)
2. Scan the plugin files for translatable strings
3. Create translation files in `languages/` folder
4. Use format: `wc-invoice-{locale}.po` and `wc-invoice-{locale}.mo`

## 🤝 Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and version history.

## 🔒 Security

If you discover any security-related issues, please email security@example.com instead of using the issue tracker.

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 👤 Author

**Your Name**

- Website: [yourwebsite.com](https://yourwebsite.com)
- GitHub: [@your-username](https://github.com/your-username)

## 🙏 Acknowledgments

- Built for the WordPress and WooCommerce community
- Inspired by the need for simple, effective invoice generation

---

⭐ If you find this plugin useful, please consider giving it a star on GitHub!
