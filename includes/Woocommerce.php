<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * WooCommerce integration class
 */
class Woocommerce
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
        // Add invoice button to order actions
        add_filter('woocommerce_order_actions', [$this, 'addOrderActions'], 10, 1);
        add_action('woocommerce_order_action_generate_invoice', [$this, 'handleGenerateInvoiceAction']);

        // Check if HPOS (High-Performance Order Storage) is enabled
        if ($this->isHPOSEnabled()) {
            // HPOS compatible hooks
            add_filter('woocommerce_shop_order_list_table_columns', [$this, 'addInvoiceColumn'], 20);
            add_action('woocommerce_shop_order_list_table_column_values', [$this, 'renderInvoiceColumnHPOS'], 10, 2);
            add_action('woocommerce_admin_order_data_after_order_details', [$this, 'renderInvoiceMetaBoxHPOS'], 10, 1);
        } else {
            // Legacy custom post type hooks
            add_filter('manage_edit-shop_order_columns', [$this, 'addInvoiceColumn'], 20);
            add_action('manage_shop_order_posts_custom_column', [$this, 'renderInvoiceColumn'], 10, 2);
            add_action('add_meta_boxes', [$this, 'addInvoiceMetaBox']);
        }
    }

    /**
     * Check if HPOS (High-Performance Order Storage) is enabled
     *
     * @return bool
     */
    private function isHPOSEnabled(): bool
    {
        if (!class_exists('\Automattic\WooCommerce\Utilities\OrderUtil')) {
            return false;
        }

        return \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
    }

    /**
     * Add order actions
     *
     * @param array $actions
     * @return array
     */
    public function addOrderActions(array $actions): array
    {
        $actions['generate_invoice'] = __('Generate Invoice', 'wc-invoice');
        return $actions;
    }

    /**
     * Handle generate invoice action
     *
     * @param \WC_Order $order
     * @return void
     */
    public function handleGenerateInvoiceAction(\WC_Order $order): void
    {
        // Invoice generation will be handled by Generator
        do_action('wc_invoice_generate', $order->get_id());
    }

    /**
     * Add invoice column to orders list
     *
     * @param array $columns
     * @return array
     */
    public function addInvoiceColumn(array $columns): array
    {
        $columns['wc_invoice'] = __('Invoice', 'wc-invoice');
        return $columns;
    }

    /**
     * Render invoice column (Legacy - Custom Post Type)
     *
     * @param string $column
     * @param int $post_id
     * @return void
     */
    public function renderInvoiceColumn(string $column, int $post_id): void
    {
        if ($column !== 'wc_invoice') {
            return;
        }

        $this->renderInvoiceColumnContent($post_id);
    }

    /**
     * Render invoice column (HPOS compatible)
     *
     * @param string $column
     * @param \WC_Order $order
     * @return void
     */
    public function renderInvoiceColumnHPOS(string $column, \WC_Order $order): void
    {
        if ($column !== 'wc_invoice') {
            return;
        }

        $this->renderInvoiceColumnContent($order->get_id());
    }

    /**
     * Render invoice column content (shared method)
     *
     * @param int $order_id
     * @return void
     */
    private function renderInvoiceColumnContent(int $order_id): void
    {
        $invoice = Database::instance()->getInvoiceByOrderId($order_id);

        if ($invoice) {
            echo '<a href="#" class="button button-small">' . esc_html__('View Invoice', 'wc-invoice') . '</a>';
        } else {
            echo '<span class="na">—</span>';
        }
    }

    /**
     * Add invoice meta box (Legacy - Custom Post Type)
     *
     * @return void
     */
    public function addInvoiceMetaBox(): void
    {
        add_meta_box(
            'wc-invoice-meta-box',
            __('Invoice Information', 'wc-invoice'),
            [$this, 'renderInvoiceMetaBox'],
            'shop_order',
            'side',
            'default'
        );
    }

    /**
     * Render invoice meta box (Legacy - Custom Post Type)
     *
     * @param \WP_Post $post
     * @return void
     */
    public function renderInvoiceMetaBox(\WP_Post $post): void
    {
        $order_id = $post->ID;
        $this->renderInvoiceMetaBoxContent($order_id);
    }

    /**
     * Render invoice meta box (HPOS compatible)
     *
     * @param \WC_Order $order
     * @return void
     */
    public function renderInvoiceMetaBoxHPOS(\WC_Order $order): void
    {
        $order_id = $order->get_id();
        $this->renderInvoiceMetaBoxContent($order_id);
    }

    /**
     * Render invoice meta box content (shared method)
     *
     * @param int $order_id
     * @return void
     */
    private function renderInvoiceMetaBoxContent(int $order_id): void
    {
        $invoice = Database::instance()->getInvoiceByOrderId($order_id);

        if ($invoice) {
            ?>
            <div class="wc-invoice-meta-box">
                <p>
                    <strong><?php esc_html_e('Invoice Number:', 'wc-invoice'); ?></strong><br>
                    <?php echo esc_html($invoice->invoice_number); ?>
                </p>
                <p>
                    <strong><?php esc_html_e('Invoice Date:', 'wc-invoice'); ?></strong><br>
                    <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($invoice->invoice_date))); ?>
                </p>
                <p>
                    <a href="#" class="button button-primary"><?php esc_html_e('Download Invoice', 'wc-invoice'); ?></a>
                </p>
            </div>
            <?php
        } else {
            ?>
            <div class="wc-invoice-meta-box">
                <p><?php esc_html_e('No invoice generated yet.', 'wc-invoice'); ?></p>
                <p>
                    <button type="button" class="button button-primary wc-invoice-generate-btn" data-order-id="<?php echo esc_attr($order_id); ?>">
                        <?php esc_html_e('Generate Invoice', 'wc-invoice'); ?>
                    </button>
                </p>
            </div>
            <?php
        }
    }
}

