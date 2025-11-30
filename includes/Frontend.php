<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * Frontend class for displaying invoices
 */
class Frontend
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
        add_action('init', [$this, 'addRewriteRules']);
        add_action('template_redirect', [$this, 'handleInvoiceView']);
        add_filter('query_vars', [$this, 'addQueryVars']);
    }

    /**
     * Add rewrite rules
     *
     * @return void
     */
    public function addRewriteRules(): void
    {
        add_rewrite_endpoint('wc-invoice', EP_ROOT);
        add_rewrite_rule('^wc-invoice/([0-9]+)/?$', 'index.php?wc-invoice=$matches[1]', 'top');
    }

    /**
     * Add query vars
     *
     * @param array $vars
     * @return array
     */
    public function addQueryVars(array $vars): array
    {
        $vars[] = 'wc-invoice';
        return $vars;
    }

    /**
     * Handle invoice view
     *
     * @return void
     */
    public function handleInvoiceView(): void
    {
        $invoice_id = get_query_var('wc-invoice');

        if (empty($invoice_id)) {
            return;
        }

        $invoice_id = absint($invoice_id);
        $invoice = Database::instance()->getInvoiceById($invoice_id);

        if (!$invoice) {
            wp_die(__('Invoice not found.', 'wc-invoice'), __('Invoice Not Found', 'wc-invoice'), ['response' => 404]);
        }

        $order = wc_get_order($invoice->order_id);

        if (!$order) {
            wp_die(__('Order not found.', 'wc-invoice'), __('Order Not Found', 'wc-invoice'), ['response' => 404]);
        }

        // Check permissions - allow if user can manage orders or is the order owner
        $can_view = false;

        if (current_user_can('manage_woocommerce') || current_user_can('edit_shop_orders')) {
            $can_view = true;
        } elseif (is_user_logged_in() && $order->get_customer_id() == get_current_user_id()) {
            $can_view = true;
        }

        // Allow filtering permissions
        $can_view = apply_filters('wc_invoice_can_view', $can_view, $invoice_id, $order);

        if (!$can_view) {
            wp_die(__('You do not have permission to view this invoice.', 'wc-invoice'), __('Access Denied', 'wc-invoice'), ['response' => 403]);
        }

        // Display invoice
        $this->displayInvoice($invoice, $order);
        exit;
    }

    /**
     * Display invoice
     *
     * @param object $invoice
     * @param \WC_Order $order
     * @return void
     */
    private function displayInvoice(object $invoice, \WC_Order $order): void
    {
        // Get invoice HTML
        $html = Generator::instance()->getInvoiceHTML($invoice->id);

        // Apply filters
        $html = apply_filters('wc_invoice_display_html', $html, $invoice->id, $order);

        // Output invoice
        echo $html;
    }

    /**
     * Get invoice URL
     *
     * @param int $invoice_id
     * @return string
     */
    public static function getInvoiceUrl(int $invoice_id): string
    {
        return home_url('/wc-invoice/' . $invoice_id . '/');
    }
}

