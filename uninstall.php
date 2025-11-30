<?php

/**
 * Uninstall script
 *
 * This file is executed when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Drop custom tables
$table_name = $wpdb->prefix . 'wc_invoices';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Delete options
delete_option('wc_invoice_settings');

// Clear any cached data
wp_cache_flush();

