<?php

namespace WC_Invoice;

defined('ABSPATH') || exit;

/**
 * Database handler class
 */
class Database
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
    private function __construct() {}

    /**
     * Create database tables
     *
     * @return void
     */
    public function createTables(): void
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'wc_invoices';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id bigint(20) UNSIGNED NOT NULL,
            invoice_number varchar(100) NOT NULL,
            invoice_date datetime NOT NULL,
            status varchar(20) DEFAULT 'pending',
            pdf_path varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY invoice_number (invoice_number),
            KEY order_id (order_id),
            KEY status (status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Get invoice by order ID
     *
     * @param int $order_id
     * @return object|null
     */
    public function getInvoiceByOrderId(int $order_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_invoices';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE order_id = %d",
                $order_id
            )
        );
    }

    /**
     * Get invoice by ID
     *
     * @param int $invoice_id
     * @return object|null
     */
    public function getInvoiceById(int $invoice_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_invoices';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $invoice_id
            )
        );
    }

    /**
     * Get invoice by invoice number
     *
     * @param string $invoice_number
     * @return object|null
     */
    public function getInvoiceByNumber(string $invoice_number)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_invoices';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE invoice_number = %s",
                $invoice_number
            )
        );
    }

    /**
     * Create invoice
     *
     * @param array $data
     * @return int|false Invoice ID or false on failure
     */
    public function createInvoice(array $data)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_invoices';

        $defaults = [
            'order_id' => 0,
            'invoice_number' => '',
            'invoice_date' => current_time('mysql'),
            'status' => 'pending',
            'pdf_path' => null,
        ];

        $data = wp_parse_args($data, $defaults);

        $result = $wpdb->insert($table_name, $data);

        if ($result) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * Update invoice
     *
     * @param int $invoice_id
     * @param array $data
     * @return bool
     */
    public function updateInvoice(int $invoice_id, array $data): bool
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_invoices';

        return (bool) $wpdb->update(
            $table_name,
            $data,
            ['id' => $invoice_id]
        );
    }

    /**
     * Delete invoice
     *
     * @param int $invoice_id
     * @return bool
     */
    public function deleteInvoice(int $invoice_id): bool
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_invoices';

        return (bool) $wpdb->delete($table_name, ['id' => $invoice_id]);
    }
}

