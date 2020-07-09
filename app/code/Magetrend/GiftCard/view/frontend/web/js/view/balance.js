/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * PHP version 5.3 or later
 *
 * @category MageTrend
 * @package  Magetend/GiftCard
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-gift-card
 */

define(
    ['jquery', 'mage/validation', 'mage/translate'],
    function ($, validation, tr) {
        'use strict';

        return {
            dataForm: null,

            submitButtonText: '',

            options : {
                formId: 'gift_card_balance_form',
                validateCaptcha: false,
                storeId: 0,
                ip: '127.0.0.1'
            },

            init: function(config) {
                this.options = config;

                this.dataForm = $('#'+this.options.formId);
                this.dataForm.mage('validation', {});

                this.resetForm();
                this.setupEvents();
            },

            setupEvents: function () {
                $('#'+this.options.formId).on('submit', this, this.onSubmit);
            },

            onSubmit: function (event) {
                event.preventDefault();
                event.data.submitForm();
            },

            submitForm: function () {
                var self = this;
                
                if (!this.isValid()) {
                    return;
                }

                var actionUrl = this.dataForm.attr('action');
                var giftCardCode = this.dataForm.find('input[name="gift_card_code"]').val();
                var captchaResponse = '';
                if(this.options.validateCaptcha) {
                    captchaResponse = grecaptcha.getResponse();
                }

                this.showLoading();
                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        gift_card_code : giftCardCode,
                        captcha_response: captchaResponse,
                        store_id: this.options.storeId,
                        ip: this.options.ip,
                        isAjax: true,
                    },

                    success: function(response) {
                        self.hideLoading();
                        if (self.options.validateCaptcha) {
                            grecaptcha.reset();
                        }

                        if (response.error) {
                            self.showErrorMsg(response.error);
                            return
                        } else if (response.success == 1) {
                            self.showGiftCardDetails(response.giftcard);
                        }
                    }
                });
            },

            isValid: function () {
                this.resetForm();
                if (!this.dataForm.validation('isValid')) {
                    return false;
                }

                if (this.options.validateCaptcha) {
                    var response = grecaptcha.getResponse();
                    if(response.length == 0) {
                        var field =  this.dataForm.find('.re_captcha_error');
                        setTimeout(function () {
                            field.css('display', 'block');
                        }, 2);

                        return false;
                    }
                }

                return true;
            },

            resetForm: function () {
                this.dataForm.find('.re_captcha_error').hide();
                this.dataForm.find('.gift-card-balance-error').hide();
                this.dataForm.find('.gift-card-balance-success').hide();
            },

            showErrorMsg: function (message) {
                this.resetForm();
                var container = this.dataForm.find('.actions-toolbar');
                this.dataForm.find('.gift-card-balance-error')
                    .text(message)
                    .css({
                        'height': container.css('height'),
                        'line-height': container.css('height')
                    })
                    .show();
            },

            showGiftCardDetails: function (giftCard) {
                this.resetForm();
                this.dataForm.find('.gift-card-status').text(giftCard.status);
                this.dataForm.find('.gift-card-amount').text(giftCard.balance);

                if (giftCard.has_expiration_date) {
                    this.dataForm.find('.gift-card-valid-to').text(giftCard.valid_to);
                    this.dataForm.find('.gift-card-valid-to-container').show();
                } else {
                    this.dataForm.find('.gift-card-valid-to-container').hide();
                }

                this.dataForm.find('.gift-card-balance-success').show();
            },

            hideLoading: function () {
                var button = this.dataForm.find('.action.submit span');
                button.text(this.submitButtonText);
            },
            
            showLoading: function () {
                var button = this.dataForm.find('.action.submit span');
                this.submitButtonText = button.text();
                button.text(tr('Please wait...'));
            }
        }
    }
);