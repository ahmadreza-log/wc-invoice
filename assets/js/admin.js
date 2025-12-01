/**
 * Admin JavaScript for WC Invoice
 */

(function ($) {
    'use strict';

    // i18n helper function
    const __ = wp.i18n ? wp.i18n.__ : function (text) { return text; };

    /**
     * Toast Notification System
     * Simple function to show toast notifications with Glassmorphism design
     * 
     * Usage:
     *   wcInvoiceToast('Settings saved!', 'success');
     *   wcInvoiceToast('Error occurred', 'error', { title: 'Error', duration: 5000 });
     *   wcInvoiceToast('Warning message', 'warning', { showProgress: false });
     *   wcInvoiceToast('Info message', 'info', { closeButton: false });
     * 
     * @param {string} message - Toast message
     * @param {string} type - Toast type: 'success', 'error', 'warning', 'info' (default: 'success')
     * @param {object} options - Optional settings
     *   - title: string - Toast title (optional)
     *   - duration: number - Auto-hide duration in ms (default: 4000, 0 = no auto-hide)
     *   - showProgress: boolean - Show progress bar (default: true)
     *   - closeButton: boolean - Show close button (default: true)
     * @returns {object} Toast instance with hide() method
     */
    window.wcInvoiceToast = function (message, type = 'success', options = {}) {
        const defaults = {
            title: '',
            duration: 10000,
            showProgress: true,
            closeButton: true
        };

        const settings = Object.assign({}, defaults, options);

        // Create toast container if it doesn't exist
        let $container = $('.wc-invoice-toast-container');
        if ($container.length === 0) {
            $container = $('<div class="wc-invoice-toast-container"></div>');
            $('body').append($container);
        }

        // Icons for different types - Better emojis
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        // Create toast element
        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        const $toast = $(`
            <div class="wc-invoice-toast ${type}" id="${toastId}">
                <div class="wc-invoice-toast-icon">${icons[type] || icons.success}</div>
                <div class="wc-invoice-toast-content">
                    ${settings.title ? `<div class="wc-invoice-toast-title">${settings.title}</div>` : ''}
                    <div class="wc-invoice-toast-message">${message}</div>
                </div>
                ${settings.closeButton ? '<button class="wc-invoice-toast-close" aria-label="Close">×</button>' : ''}
                ${settings.showProgress ? '<div class="wc-invoice-toast-progress" style="animation-duration: ' + settings.duration + 'ms;"></div>' : ''}
            </div>
        `);

        // Add to container
        $container.append($toast);

        // Trigger show animation
        setTimeout(() => {
            $toast.addClass('show');
        }, 10);

        // Close button handler
        $toast.find('.wc-invoice-toast-close').on('click', function () {
            hideToast($toast);
        });

        // Auto hide
        if (settings.duration > 0) {
            setTimeout(() => {
                hideToast($toast);
            }, settings.duration);
        }

        // Hide function
        function hideToast($toastElement) {
            $toastElement.removeClass('show').addClass('hide');
            setTimeout(() => {
                $toastElement.remove();
            }, 400);
        }

        return {
            hide: function () {
                hideToast($toast);
            }
        };
    };

    $(document).ready(function () {
        // Handle invoice generation button
        $('.wc-invoice-generate-btn').on('click', function (e) {
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
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || 'Error generating invoice');
                        $button.prop('disabled', false).text('Generate Invoice');
                    }
                },
                error: function () {
                    alert('An error occurred. Please try again.');
                    $button.prop('disabled', false).text('Generate Invoice');
                }
            });
        });

        // Tab Navigation
        $('.wc-invoice-nav-link').on('click', function (e) {
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
        $(window).on('popstate', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab') || 'general';

            $('.wc-invoice-nav-link').removeClass('active');
            $('.wc-invoice-nav-link[data-tab="' + tab + '"]').addClass('active');

            $('.wc-invoice-tab-content').hide();
            $('.wc-invoice-tab-content[data-tab="' + tab + '"]').fadeIn(300);
        });

        // Reset to defaults
        $('#wc-invoice-reset-settings').on('click', function (e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to reset all settings to defaults? This action cannot be undone.')) {
                return;
            }

            // Reset form
            $('form.wc-invoice-settings-form')[0].reset();

            // Show toast notification
            wcInvoiceToast(
                __('Settings have been reset to defaults. Click "Save Settings" to apply.', 'wc-invoice'),
                'info',
                {
                    title: __('Reset', 'wc-invoice'),
                    duration: 5000
                }
            );
        });

        // AJAX Form Submission
        $('.wc-invoice-settings-form').on('submit', function (e) {
            e.preventDefault();

            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.html();

            // Form validation
            let isValid = true;
            $form.find('input[required]').each(function () {
                if (!$(this).val().trim()) {
                    isValid = false;
                    $(this).css('border-color', '#e53e3e');
                } else {
                    $(this).css('border-color', '');
                }
            });

            if (!isValid) {
                wcInvoiceToast(
                    __('Please fill in all required fields.', 'wc-invoice'),
                    'error',
                    { duration: 3000 }
                );
                return false;
            }

            // Disable submit button
            $submitBtn.prop('disabled', true).html('<span class="wc-invoice-btn-icon">⏳</span> ' + __('Saving...', 'wc-invoice'));

            // Collect form data
            const formData = {};
            $form.find('input, select, textarea').each(function () {
                const $field = $(this);
                const name = $field.attr('name');

                if (!name || name.indexOf('wc_invoice_settings[') === -1) return;

                // Extract field name from wc_invoice_settings[field_name]
                const match = name.match(/\[([^\]]+)\]/);
                if (match) {
                    const fieldName = match[1];
                    const fieldType = $field.attr('type');

                    if (fieldType === 'checkbox') {
                        // Always include checkbox value, even if unchecked
                        formData[fieldName] = $field.is(':checked') ? '1' : '0';
                    } else if (fieldType === 'hidden') {
                        // Hidden fields (media IDs, etc.)
                        const val = $field.val();
                        // For media fields (_id), send '0' if empty, otherwise send the value
                        if (fieldName.indexOf('_id') !== -1) {
                            formData[fieldName] = (val === '' || val === null || val === undefined) ? '0' : String(val);
                        } else {
                            formData[fieldName] = val || '';
                        }
                    } else {
                        // Text, select, textarea, etc.
                        // Send empty string if empty (don't convert to '0')
                        formData[fieldName] = $field.val() || '';
                    }
                }
            });
            
            // Debug: Log form data (remove in production)
            // console.log('Form Data:', formData);

            // Send AJAX request
            $.ajax({
                url: wcInvoice.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wc_invoice_save_settings',
                    settings: formData,
                    nonce: wcInvoice.settingsNonce
                },
                success: function (response) {
                    if (response.success) {
                        wcInvoiceToast(
                            response.data.message || __('Settings saved successfully.', 'wc-invoice'),
                            'success',
                            {
                                title: __('Success', 'wc-invoice'),
                                duration: 3000
                            }
                        );

                        // Update URL to remove any error states
                        const url = new URL(window.location);
                        url.searchParams.delete('settings-updated');
                        window.history.replaceState({}, '', url);
                    } else {
                        wcInvoiceToast(
                            response.data.message || __('Failed to save settings.', 'wc-invoice'),
                            'error',
                            {
                                title: __('Error', 'wc-invoice'),
                                duration: 4000
                            }
                        );
                    }
                },
                error: function (xhr, status, error) {
                    wcInvoiceToast(
                        __('An error occurred while saving settings. Please try again.', 'wc-invoice'),
                        'error',
                        {
                            title: __('Error', 'wc-invoice'),
                            duration: 4000
                        }
                    );
                },
                complete: function () {
                    // Re-enable submit button
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });

            return false;
        });

        // Smooth scroll to top on tab change
        $('.wc-invoice-nav-link').on('click', function () {
            $('html, body').animate({
                scrollTop: $('.wc-invoice-settings-wrapper').offset().top - 20
            }, 300);
        });

        // Logo upload
        let logoFrame;
        $('.wc-invoice-upload-logo').on('click', function (e) {
            e.preventDefault();

            if (logoFrame) {
                logoFrame.open();
                return;
            }

            logoFrame = wp.media({
                title: 'Select Logo',
                button: {
                    text: 'Use this logo'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            logoFrame.on('select', function () {
                const attachment = logoFrame.state().get('selection').first().toJSON();
                $('#wc_invoice_logo_id').val(attachment.id);

                const preview = $('.wc-invoice-logo-preview');
                preview.find('img').attr('src', attachment.url);
                preview.show();
            });

            logoFrame.open();
        });

        // Remove logo
        $(document).on('click', '.wc-invoice-remove-logo', function (e) {
            e.preventDefault();
            $('#wc_invoice_logo_id').val('0');
            $('.wc-invoice-logo-preview').hide();
        });

        // Signature upload
        let signatureFrame;
        $('.wc-invoice-upload-signature').on('click', function (e) {
            e.preventDefault();

            if (signatureFrame) {
                signatureFrame.open();
                return;
            }

            signatureFrame = wp.media({
                title: 'Select Signature',
                button: {
                    text: 'Use this signature'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            signatureFrame.on('select', function () {
                const attachment = signatureFrame.state().get('selection').first().toJSON();
                $('#wc_invoice_signature_id').val(attachment.id);

                const preview = $('.wc-invoice-signature-preview');
                preview.find('img').attr('src', attachment.url);
                preview.show();
            });

            signatureFrame.css('display', 'flex');
        });

        // Remove signature
        $(document).on('click', '.wc-invoice-remove-signature', function (e) {
            e.preventDefault();
            $('#wc_invoice_signature_id').val('0');
            $('.wc-invoice-signature-preview').hide();
        });

        // Font upload
        let fontFrames = {};
        $('.wc-invoice-upload-font').on('click', function (e) {
            e.preventDefault();
            const format = $(this).data('format');
            const accept = $(this).data('accept');

            if (fontFrames[format]) {
                fontFrames[format].open();
                return;
            }

            fontFrames[format] = wp.media({
                title: 'Select ' + format.toUpperCase() + ' Font',
                button: {
                    text: 'Use this font'
                },
                multiple: false,
                library: {
                    type: 'application'
                }
            });

            fontFrames[format].on('select', function () {
                const attachment = fontFrames[format].state().get('selection').first().toJSON();
                $('#wc_invoice_font_' + format + '_id').val(attachment.id);

                const wrapper = $(this.el).closest('.wc-invoice-font-upload-wrapper');
                if (!wrapper.find('.wc-invoice-font-preview').length) {
                    wrapper.prepend('<div class="wc-invoice-font-preview"><span class="wc-invoice-font-name">' + attachment.filename + '</span><button type="button" class="wc-invoice-btn-remove-font" data-format="' + format + '">Remove</button></div>');
                } else {
                    wrapper.find('.wc-invoice-font-name').text(attachment.filename);
                }
            });

            fontFrames[format].open();
        });

        // Remove font
        $(document).on('click', '.wc-invoice-btn-remove-font', function (e) {
            e.preventDefault();
            const format = $(this).data('format');
            $('#wc_invoice_font_' + format + '_id').val('0');
            $(this).closest('.wc-invoice-font-preview').remove();
        });

        // Color picker sync
        $('#wc_invoice_primary_color').on('input change', function () {
            $('#wc_invoice_primary_color_value').val($(this).val());
        });

        $('#wc_invoice_text_color').on('input change', function () {
            $('#wc_invoice_text_color_value').val($(this).val());
        });

        // Allow manual color value input
        $('#wc_invoice_primary_color_value, #wc_invoice_text_color_value').on('input', function () {
            const value = $(this).val();
            const colorRegex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;

            if (colorRegex.test(value)) {
                if ($(this).attr('id') === 'wc_invoice_primary_color_value') {
                    $('#wc_invoice_primary_color').val(value);
                } else {
                    $('#wc_invoice_text_color').val(value);
                }
            }
        });
    });
})(jQuery);

