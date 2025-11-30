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

        // Add invoice column to orders list
        add_filter('manage_edit-shop_order_columns', [$this, 'addInvoiceColumn'], 20);
        add_action('manage_shop_order_posts_custom_column', [$this, 'renderInvoiceColumn'], 10, 2);

        // Add invoice meta box to order edit page
        add_action('add_meta_boxes', [$this, 'addInvoiceMetaBox']);
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
     * Render invoice column
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

        $invoice = Database::instance()->getInvoiceByOrderId($post_id);

        if ($invoice) {
            echo '<a href="#" class="button button-small">' . esc_html__('View Invoice', 'wc-invoice') . '</a>';
        } else {
            echo '<span class="na">—</span>';
        }
    }

    /**
     * Add invoice meta box
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
     * Render invoice meta box
     *
     * @param \WP_Post $post
     * @return void
     */
    public function renderInvoiceMetaBox(\WP_Post $post): void
    {
        $order_id = $post->ID;
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

