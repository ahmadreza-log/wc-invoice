/**
 * Admin JavaScript for WC Invoice
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle invoice generation button
        $('.wc-invoice-generate-btn').on('click', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const orderId = $button.data('order-id');
            
            if (!orderId) {
                alert('Invalid order ID');
                return;
            }
            
            $button.prop('disabled', true).text('Generating...');
            
            $.ajax({
                url: wcInvoice.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wc_invoice_generate',
                    order_id: orderId,
                    nonce: wcInvoice.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || 'Error generating invoice');
                        $button.prop('disabled', false).text('Generate Invoice');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                    $button.prop('disabled', false).text('Generate Invoice');
                }
            });
        });
    });
})(jQuery);

