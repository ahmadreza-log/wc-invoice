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

        // Tab Navigation
        $('.wc-invoice-nav-link').on('click', function(e) {
            e.preventDefault();
            const tab = $(this).data('tab');
            
            // Update active state
            $('.wc-invoice-nav-link').removeClass('active');
            $(this).addClass('active');
            
            // Show/hide tab content
            $('.wc-invoice-tab-content').hide();
            $('.wc-invoice-tab-content[data-tab="' + tab + '"]').fadeIn(300);
            
            // Update URL without reload
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);
        });

        // Handle browser back/forward
        $(window).on('popstate', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab') || 'general';
            
            $('.wc-invoice-nav-link').removeClass('active');
            $('.wc-invoice-nav-link[data-tab="' + tab + '"]').addClass('active');
            
            $('.wc-invoice-tab-content').hide();
            $('.wc-invoice-tab-content[data-tab="' + tab + '"]').fadeIn(300);
        });

        // Reset to defaults
        $('#wc-invoice-reset-settings').on('click', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to reset all settings to defaults? This action cannot be undone.')) {
                return;
            }
            
            // Reset form
            $('form.wc-invoice-settings-form')[0].reset();
            
            // Show success message
            const $notice = $('<div class="notice notice-success is-dismissible"><p>Settings have been reset to defaults. Click "Save Settings" to apply.</p></div>');
            $('.wc-invoice-settings-wrapper').prepend($notice);
            
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        });

        // Form validation
        $('.wc-invoice-settings-form').on('submit', function(e) {
            let isValid = true;
            
            $(this).find('input[required]').each(function() {
                if (!$(this).val().trim()) {
                    isValid = false;
                    $(this).css('border-color', '#e53e3e');
                } else {
                    $(this).css('border-color', '');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
        });

        // Smooth scroll to top on tab change
        $('.wc-invoice-nav-link').on('click', function() {
            $('html, body').animate({
                scrollTop: $('.wc-invoice-settings-wrapper').offset().top - 20
            }, 300);
        });
    });
})(jQuery);

