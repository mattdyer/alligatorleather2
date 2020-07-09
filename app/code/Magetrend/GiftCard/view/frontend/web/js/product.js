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

define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    'priceBox',
    'jquery/ui',
    'jquery/jquery.parsequery',
    'mage/mage'
], function ($, _, mageTemplate) {
    'use strict';

    $.widget('mage.giftCardProduct', {
        options: {
            giftCardValueSelector: '#attribute_gc_set_id',
            sendToFriendSelector: '#attribute_gc_send_to_friend',
            sendToFriendFieldSelector: '.send-to-friend-form-field',
            priceHolderSelector: '.product-info-main .price-box',
            giftCardConfig: {}

        },

        _create: function () {
            this._setupChangeEvents();
            this._updateSendToFriendVisibility();
            this._reloadPrice();
        },

        _setupChangeEvents: function () {
             $(this.options.giftCardValueSelector).on('change', this, this._configure);
             $(this.options.sendToFriendSelector).on('click', this, this._sendToFriendConfigure);
        },

        _configure: function (event) {
            event.data._reloadPrice();
        },

        _sendToFriendConfigure: function(event) {
            event.data._updateSendToFriendVisibility();
        },

        _reloadPrice: function () {

            $(this.options.priceHolderSelector).trigger('updatePrice', this._getPrice());
        },

        _getPrice: function() {
            var giftCardSetId = $(this.options.giftCardValueSelector).val();
            var options = this.options.giftCardConfig.giftCardSets[giftCardSetId];
            var config = {
                prices : {
                    finalPrice: {
                        amount: options.prices.finalPrice.amount,
                        adjustments: {}
                    },
                    oldPrice: {
                        amount: options.prices.oldPrice.amount,
                        adjustments: {}
                    },
                    basePrice: {
                        amount: options.prices.basePrice.amount,
                        adjustments: {}
                    }
                }
            };
            return config;
        },

        _updateSendToFriendVisibility: function() {
            var sendToFriendFormField = $(this.options.sendToFriendFieldSelector);
            if ($(this.options.sendToFriendSelector).is(':checked')) {
                sendToFriendFormField.show();
                $('.send-to-friend-form-field').each(function () {
                    $(this).addClass('required');
                });
                var dataForm = $('#giftcard-send--form');
                dataForm.mage('validation', {});
            } else {
                sendToFriendFormField.hide();
            }
        }
    });

    return $.mage.giftCardProduct;
});
