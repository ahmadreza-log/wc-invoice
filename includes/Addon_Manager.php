<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * Addon Manager class
 * 
 * Manages addons/extensions for WC Invoice plugin
 */
class Addon_Manager
{
    private static $instance = null;
    private array $addons = [];
    private array $active_addons = [];

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
        $this->loadAddons();
    }

    /**
     * Register hooks
     *
     * @return void
     */
    private function hooks(): void
    {
        add_action('admin_menu', [$this, 'addAddonsMenu'], 100);
        add_action('admin_init', [$this, 'handleAddonActivation']);
    }

    /**
     * Load all addons
     *
     * @return void
     */
    private function loadAddons(): void
    {
        // Allow plugins to register themselves as WC Invoice addons
        do_action('wc_invoice_register_addons');
        
        // Get registered addons from other plugins
        $registered_addons = apply_filters('wc_invoice_registered_addons', []);
        
        foreach ($registered_addons as $addon_data) {
            if (empty($addon_data['slug']) || empty($addon_data['file'])) {
                continue;
            }

            $addon_slug = $addon_data['slug'];
            $this->addons[$addon_slug] = $addon_data;

            // Load active addons
            if ($this->isAddonActive($addon_slug)) {
                $this->loadAddonFile($addon_data);
                $this->active_addons[$addon_slug] = $addon_data;
            }
        }
    }

    /**
     * Register an addon
     *
     * @param string $slug Addon slug (unique identifier)
     * @param string $file Main plugin file path
     * @param array $args Addon arguments
     * @return bool
     */
    public function registerAddon(string $slug, string $file, array $args = []): bool
    {
        if (empty($slug) || empty($file) || !file_exists($file)) {
            return false;
        }

        // Get plugin data from file header
        $plugin_data = get_file_data($file, [
            'Name' => 'Plugin Name',
            'PluginURI' => 'Plugin URI',
            'Version' => 'Version',
            'Description' => 'Description',
            'Author' => 'Author',
            'AuthorURI' => 'Author URI',
            'TextDomain' => 'Text Domain',
            'DomainPath' => 'Domain Path',
        ], 'plugin');

        // Merge with provided args
        $addon_data = array_merge([
            'slug' => $slug,
            'file' => $file,
            'name' => $plugin_data['Name'] ?? $args['name'] ?? '',
            'version' => $plugin_data['Version'] ?? $args['version'] ?? '1.0.0',
            'description' => $plugin_data['Description'] ?? $args['description'] ?? '',
            'author' => $plugin_data['Author'] ?? $args['author'] ?? '',
            'author_uri' => $plugin_data['AuthorURI'] ?? $args['author_uri'] ?? '',
            'plugin_uri' => $plugin_data['PluginURI'] ?? $args['plugin_uri'] ?? '',
            'requires' => $args['requires'] ?? '',
            'text_domain' => $plugin_data['TextDomain'] ?? $args['text_domain'] ?? '',
        ], $args);

        if (empty($addon_data['name'])) {
            return false;
        }

        $this->addons[$slug] = $addon_data;
        return true;
    }

    /**
     * Load addon file
     *
     * @param array $data Addon data
     * @return void
     */
    private function loadAddonFile(array $data): void
    {
        // Check requirements
        if (!empty($data['requires'])) {
            $required_version = $data['requires'];
            if (version_compare(WC_INVOICE_VERSION, $required_version, '<')) {
                add_action('admin_notices', function() use ($data, $required_version) {
                    printf(
                        '<div class="notice notice-error"><p><strong>%s</strong>: %s</p></div>',
                        esc_html($data['name']),
                        sprintf(
                            esc_html__('Requires WC Invoice version %s or higher.', 'wc-invoice'),
                            esc_html($required_version)
                        )
                    );
                });
                return;
            }
        }

        // Fire addon loaded action (the plugin file should already be loaded by WordPress)
        do_action('wc_invoice_addon_loaded', $data['slug'], $data);
    }

    /**
     * Check if addon is active
     *
     * @param string $slug Addon slug
     * @return bool
     */
    private function isAddonActive(string $slug): bool
    {
        $active_addons = get_option('wc_invoice_active_addons', []);
        return in_array($slug, $active_addons, true);
    }

    /**
     * Activate addon
     *
     * @param string $slug Addon slug
     * @return bool
     */
    public function activateAddon(string $slug): bool
    {
        if (!isset($this->addons[$slug])) {
            return false;
        }

        $active_addons = get_option('wc_invoice_active_addons', []);
        
        if (!in_array($slug, $active_addons, true)) {
            $active_addons[] = $slug;
            update_option('wc_invoice_active_addons', $active_addons);
        }

        // Fire activation hook
        do_action('wc_invoice_addon_activated', $slug, $this->addons[$slug]);

        return true;
    }

    /**
     * Deactivate addon
     *
     * @param string $slug Addon slug
     * @return bool
     */
    public function deactivateAddon(string $slug): bool
    {
        $active_addons = get_option('wc_invoice_active_addons', []);
        
        if (($key = array_search($slug, $active_addons, true)) !== false) {
            unset($active_addons[$key]);
            update_option('wc_invoice_active_addons', array_values($active_addons));
        }

        // Fire deactivation hook
        do_action('wc_invoice_addon_deactivated', $slug);

        return true;
    }

    /**
     * Get all addons
     *
     * @return array
     */
    public function getAllAddons(): array
    {
        return $this->addons;
    }

    /**
     * Get active addons
     *
     * @return array
     */
    public function getActiveAddons(): array
    {
        return $this->active_addons;
    }

    /**
     * Get addon by slug
     *
     * @param string $slug Addon slug
     * @return array|null
     */
    public function getAddon(string $slug): ?array
    {
        return $this->addons[$slug] ?? null;
    }

    /**
     * Add addons menu
     *
     * @return void
     */
    public function addAddonsMenu(): void
    {
        add_submenu_page(
            'woocommerce',
            __('WC Invoice Addons', 'wc-invoice'),
            __('Invoice Addons', 'wc-invoice'),
            'manage_options',
            'wc-invoice-addons',
            [$this, 'renderAddonsPage']
        );
    }

    /**
     * Handle addon activation/deactivation
     *
     * @return void
     */
    public function handleAddonActivation(): void
    {
        if (!isset($_GET['page']) || $_GET['page'] !== 'wc-invoice-addons') {
            return;
        }

        if (!isset($_GET['action']) || !isset($_GET['addon'])) {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'wc-invoice'));
        }

        check_admin_referer('wc_invoice_addon_action');

        $action = sanitize_text_field($_GET['action']);
        $addon_slug = sanitize_text_field($_GET['addon']);

        if ($action === 'activate') {
            $this->activateAddon($addon_slug);
            wp_redirect(add_query_arg(['addon_activated' => '1'], admin_url('admin.php?page=wc-invoice-addons')));
            exit;
        } elseif ($action === 'deactivate') {
            $this->deactivateAddon($addon_slug);
            wp_redirect(add_query_arg(['addon_deactivated' => '1'], admin_url('admin.php?page=wc-invoice-addons')));
            exit;
        }
    }

    /**
     * Render addons page
     *
     * @return void
     */
    public function renderAddonsPage(): void
    {
        if (isset($_GET['addon_activated'])) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Addon activated successfully.', 'wc-invoice') . '</p></div>';
        }
        if (isset($_GET['addon_deactivated'])) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Addon deactivated successfully.', 'wc-invoice') . '</p></div>';
        }

        $all_addons = $this->getAllAddons();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('WC Invoice Extensions', 'wc-invoice'); ?></h1>
            <p><?php esc_html_e('Extend WC Invoice functionality with third-party plugins. Install and activate extension plugins from the WordPress plugin directory or upload them manually.', 'wc-invoice'); ?></p>

            <?php if (empty($all_addons)): ?>
                <div class="notice notice-info">
                    <p><strong><?php esc_html_e('No extensions found.', 'wc-invoice'); ?></strong></p>
                    <p><?php esc_html_e('To create an extension for WC Invoice, create a separate WordPress plugin and register it using the WC Invoice extension API.', 'wc-invoice'); ?></p>
                    <p><?php esc_html_e('Example:', 'wc-invoice'); ?></p>
                    <pre style="background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>add_action('wc_invoice_register_addons', function() {
    WC_Invoice\Addon_Manager::instance()->registerAddon(
        'my-extension-slug',
        __FILE__,
        [
            'name' => 'My Extension',
            'version' => '1.0.0',
            'description' => 'Extension description',
            'author' => 'Your Name',
            'requires' => '0.0.10'
        ]
    );
});</code></pre>
                </div>
            <?php else: ?>
                <div class="wc-invoice-addons-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
                    <?php foreach ($all_addons as $slug => $addon): ?>
                        <?php $is_active = $this->isAddonActive($slug); ?>
                        <div class="wc-invoice-addon-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 20px; background: #fff;">
                            <h3><?php echo esc_html($addon['name']); ?></h3>
                            <p><?php echo esc_html($addon['description'] ?? ''); ?></p>
                            <p><strong><?php esc_html_e('Version:', 'wc-invoice'); ?></strong> <?php echo esc_html($addon['version'] ?? '1.0.0'); ?></p>
                            <?php if (!empty($addon['author'])): ?>
                                <p><strong><?php esc_html_e('Author:', 'wc-invoice'); ?></strong> 
                                    <?php if (!empty($addon['author_uri'])): ?>
                                        <a href="<?php echo esc_url($addon['author_uri']); ?>" target="_blank"><?php echo esc_html($addon['author']); ?></a>
                                    <?php else: ?>
                                        <?php echo esc_html($addon['author']); ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                            
                            <p style="margin-top: 15px;">
                                <?php if ($is_active): ?>
                                    <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['action' => 'deactivate', 'addon' => $slug]), 'wc_invoice_addon_action')); ?>" 
                                       class="button button-secondary">
                                        <?php esc_html_e('Deactivate', 'wc-invoice'); ?>
                                    </a>
                                    <span style="color: green; margin-left: 10px;">✓ <?php esc_html_e('Active', 'wc-invoice'); ?></span>
                                <?php else: ?>
                                    <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['action' => 'activate', 'addon' => $slug]), 'wc_invoice_addon_action')); ?>" 
                                       class="button button-primary">
                                        <?php esc_html_e('Activate', 'wc-invoice'); ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}

