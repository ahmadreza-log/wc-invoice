<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * Invoice generator class
 */
class Generator
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
        add_action('wc_invoice_generate', [$this, 'generate'], 10, 1);
    }

    /**
     * Generate invoice for order
     *
     * @param int $order_id
     * @return int|false Invoice ID or false on failure
     */
    public function generate(int $order_id)
    {
        $order = wc_get_order($order_id);

        if (!$order) {
            return false;
        }

        // Check if invoice already exists
        $existing_invoice = Database::instance()->getInvoiceByOrderId($order_id);
        if ($existing_invoice) {
            return $existing_invoice->id;
        }

        // Generate invoice number
        $invoice_number = $this->generateInvoiceNumber();

        // Create invoice record
        $invoice_id = Database::instance()->createInvoice([
            'order_id' => $order_id,
            'invoice_number' => $invoice_number,
            'invoice_date' => current_time('mysql'),
            'status' => 'completed',
        ]);

        if ($invoice_id) {
            do_action('wc_invoice_generated', $invoice_id, $order_id);
        }

        return $invoice_id;
    }

    /**
     * Generate unique invoice number
     *
     * @return string
     */
    private function generateInvoiceNumber(): string
    {
        $options = get_option('wc_invoice_settings', []);
        $prefix = $options['invoice_prefix'] ?? 'INV-';
        $number = $this->getNextInvoiceNumber();

        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get next invoice number
     *
     * @return int
     */
    private function getNextInvoiceNumber(): int
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_invoices';

        $last_number = $wpdb->get_var(
            "SELECT COUNT(*) FROM $table_name"
        );

        return (int) $last_number + 1;
    }

    /**
     * Get invoice HTML
     *
     * @param int $invoice_id
     * @return string
     */
    public function getInvoiceHTML(int $invoice_id): string
    {
        $invoice = Database::instance()->getInvoiceById($invoice_id);
        
        if (!$invoice) {
            return '';
        }

        $order = wc_get_order($invoice->order_id);
        
        if (!$order) {
            return '';
        }

        ob_start();
        include WC_INVOICE_DIR . 'templates/invoice.php';
        return ob_get_clean();
    }

    /**
     * Generate PDF
     *
     * @param int $invoice_id
     * @return string|false PDF file path or false on failure
     */
    public function generatePDF(int $invoice_id)
    {
        // PDF generation will be implemented later
        // This can use libraries like TCPDF, mPDF, or DomPDF
        return false;
    }
}

