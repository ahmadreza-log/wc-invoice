# 📝 Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.0.2] - 2024-01-01

### Added
- ✅ HPOS (High-Performance Order Storage) compatibility
- 🔄 Support for both HPOS and legacy custom post type order storage
- 🎯 Automatic detection of order storage type
- 📦 HPOS compatibility declaration

### Changed
- 🔧 Refactored order column rendering for HPOS compatibility
- 🔧 Refactored meta box rendering for HPOS compatibility
- 📝 Improved code structure with shared methods

### Fixed
- 🐛 Fixed compatibility issues with WooCommerce HPOS feature
- 🔧 Fixed order column display in HPOS mode
- 🔧 Fixed meta box display in HPOS mode

## [0.0.1] - 2024-01-01

### Added
- ✨ Automatic invoice generation for WooCommerce orders
- 📊 Invoice management in admin panel
- 📥 Invoice download capability
- 🎨 Multiple invoice templates (Default, Modern, Minimal)
- 🔢 Customizable invoice number prefix
- 🌍 Full internationalization support
- ⚡ PSR-4 autoloading without Composer dependency
- 🔒 Security best practices implementation
- 📱 Responsive invoice templates
- 🎯 WooCommerce order integration
- 📋 Invoice meta box in order edit page
- 🔔 Admin notices system
- 💾 Database table for invoice storage
- 🎨 Admin and frontend CSS/JS assets

### Changed
- 🔄 Refactored class names to single-word format
- 📝 Updated documentation

### Fixed
- 🐛 Fixed invoice number generation
- 🔧 Fixed database table creation

### Security
- 🔒 Added nonce verification for AJAX requests
- 🛡️ Implemented proper capability checks

---

## Version History

- **0.0.1** - Initial development release

---

## Types of Changes

- **Added** for new features
- **Changed** for changes in existing functionality
- **Deprecated** for soon-to-be removed features
- **Removed** for now removed features
- **Fixed** for any bug fixes
- **Security** for vulnerability fixes

