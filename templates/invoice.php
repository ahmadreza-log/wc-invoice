<?php
/**
 * Invoice template
 *
 * @var object $invoice
 * @var \WC_Order $order
 */

defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php esc_html_e('Invoice', 'wc-invoice'); ?> - <?php echo esc_html($invoice->invoice_number); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .invoice-header {
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .invoice-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-details, .order-details {
            width: 48%;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th,
        .invoice-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .invoice-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .invoice-total {
            text-align: right;
            margin-top: 20px;
        }
        .invoice-total table {
            margin-left: auto;
            width: 300px;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1><?php esc_html_e('Invoice', 'wc-invoice'); ?></h1>
    </div>

    <div class="invoice-info">
        <div class="invoice-details">
            <h3><?php esc_html_e('Invoice Details', 'wc-invoice'); ?></h3>
            <p><strong><?php esc_html_e('Invoice Number:', 'wc-invoice'); ?></strong> <?php echo esc_html($invoice->invoice_number); ?></p>
            <p><strong><?php esc_html_e('Invoice Date:', 'wc-invoice'); ?></strong> <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($invoice->invoice_date))); ?></p>
        </div>

        <div class="order-details">
            <h3><?php esc_html_e('Order Details', 'wc-invoice'); ?></h3>
            <p><strong><?php esc_html_e('Order Number:', 'wc-invoice'); ?></strong> #<?php echo esc_html($order->get_order_number()); ?></p>
            <p><strong><?php esc_html_e('Order Date:', 'wc-invoice'); ?></strong> <?php echo esc_html($order->get_date_created()->date_i18n(get_option('date_format'))); ?></p>
        </div>
    </div>

    <div class="invoice-items">
        <table class="invoice-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('Product', 'wc-invoice'); ?></th>
                    <th><?php esc_html_e('Quantity', 'wc-invoice'); ?></th>
                    <th><?php esc_html_e('Price', 'wc-invoice'); ?></th>
                    <th><?php esc_html_e('Total', 'wc-invoice'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order->get_items() as $item): ?>
                    <tr>
                        <td><?php echo esc_html($item->get_name()); ?></td>
                        <td><?php echo esc_html($item->get_quantity()); ?></td>
                        <td><?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?></td>
                        <td><?php echo wp_kses_post($order->get_formatted_line_subtotal($item, true)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="invoice-total">
        <table>
            <tr>
                <td><strong><?php esc_html_e('Subtotal:', 'wc-invoice'); ?></strong></td>
                <td><?php echo wp_kses_post($order->get_subtotal_to_display()); ?></td>
            </tr>
            <?php if ($order->get_total_tax() > 0): ?>
            <tr>
                <td><strong><?php esc_html_e('Tax:', 'wc-invoice'); ?></strong></td>
                <td><?php echo wp_kses_post($order->get_total_tax()); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td><strong><?php esc_html_e('Total:', 'wc-invoice'); ?></strong></td>
                <td><strong><?php echo wp_kses_post($order->get_formatted_order_total()); ?></strong></td>
            </tr>
        </table>
    </div>
</body>
</html>

