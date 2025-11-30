# 📝 Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.0.9] - 2024-01-01

### Added
- ✍️ Signature upload field in General settings
- 📍 Address field with WooCommerce integration
- 📅 Date format selector (6 formats available)
- ✅ Fields visibility toggles (First Name, Last Name, Address, Email)
- 🎨 Checkbox styling for fields list
- 📝 Signature upload functionality

### Features
- 🔗 Address automatically populated from WooCommerce store settings
- ✏️ Address can be edited in plugin settings without affecting WooCommerce
- 📅 Multiple date format options with preview
- ✅ Toggle visibility of customer fields on invoices
- ✍️ Signature image upload and preview

## [0.0.8] - 2024-01-01

### Fixed
- 🐛 Fixed input styles not applying due to WordPress admin CSS override
- ✅ Added higher CSS specificity with !important rules
- 🎯 Added inline styles to ensure styles are applied
- 🔧 Improved CSS selector specificity
- 📱 Fixed backdrop-filter browser compatibility

### Changed
- 🔄 Enhanced CSS loading with inline styles
- ✅ Better style override protection

## [0.0.7] - 2024-01-01

### Changed
- 🎨 Enhanced color picker design with Glassmorphism
- ✨ Added color value display next to picker
- 💫 Smooth hover animations for color picker
- 🎯 Better visual feedback on color selection
- 📱 Improved color picker wrapper styling

### Added
- 📝 Color value text input (readonly, displays hex value)
- 🔄 Sync between color picker and value display
- ✅ Manual color value input support

## [0.0.6] - 2024-01-01

### Added
- 📦 Addon system for extending plugin functionality
- 🔌 Addon Manager class for managing addons
- 🎣 Addon API for addon developers
- 📁 Addon directory structure
- 📝 Example addon template
- 🎯 Addon activation/deactivation system
- 🔄 Addon loading and initialization
- 📋 Addons management page in admin

### Features
- ✅ Automatic addon discovery
- ✅ Version requirement checking
- ✅ Addon activation/deactivation
- ✅ Hook system for addons
- ✅ API methods for addon integration
- ✅ Example addon for reference

## [0.0.5] - 2024-01-01

### Added
- 📝 New settings structure with General and Theme tabs
- 🎨 Theme selection (Modern, Flat, Simple, Classic)
- 🎨 Primary color picker
- 🎨 Text color picker
- 📷 Logo upload functionality with preview
- 🔤 Font upload for TTF, WOFF, WOFF2, EOT, SVG formats
- 📝 Title field for invoice customization
- 🎯 Media uploader integration

### Changed
- 🔄 Restructured settings menu (General and Theme only)
- 🗑️ Removed old settings sections (Invoice, Display, Advanced)
- 📝 Simplified settings organization
- 🎨 Enhanced form fields with better styling

### Removed
- 🗑️ Old settings methods (renderGeneralSection, renderInvoicePrefixField, renderInvoiceTemplateField)
- 🗑️ Unused settings sections

## [0.0.4] - 2024-01-01

### Changed
- 🔄 Moved settings page to WooCommerce admin submenu
- 🗑️ Removed standalone dashboard page
- 📍 Settings now accessible from WooCommerce → Invoice Settings
- 🎯 Simplified menu structure (single settings page)

### Removed
- 🗑️ Dashboard page (no longer needed)

## [0.0.3] - 2024-01-01

### Added
- 🎨 Modern Glassmorphism design for settings panel
- 📱 Responsive sidebar navigation
- 🎯 Tab-based settings organization (General, Invoice, Display, Advanced)
- ✨ Beautiful gradient header with animations
- 🔘 Modern toggle switches
- 💫 Smooth animations and transitions
- 📝 Enhanced form fields with glassmorphism effect
- 🎨 Custom CSS textarea for advanced styling
- 🔄 Reset to defaults functionality
- ✅ Form validation

### Changed
- 🎨 Complete redesign of settings page UI
- 📱 Improved mobile responsiveness
- 🎯 Better user experience with tab navigation
- 💎 Enhanced visual design with backdrop filters

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

