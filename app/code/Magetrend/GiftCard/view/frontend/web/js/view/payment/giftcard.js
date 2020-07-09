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
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magetrend_GiftCard/js/action/add-giftcard',
        'Magetrend_GiftCard/js/action/remove-giftcard',
        'mage/storage',
        'Magetrend_GiftCard/js/model/checkout/resource-url-manager'
    ],
    function ($, ko, Component, quote, addGiftCard, removeGiftCard, storage, urlManager) {
        'use strict';

        var giftCardCode = ko.observable(null),
            hasAddedGiftCard = ko.observable(false),
            reloadList = ko.observable(true);

        var giftCardList = ko.observableArray([]);

        return Component.extend({
            defaults: {
                template: 'Magetrend_GiftCard/payment/giftcard'
            },
            giftCardCode: giftCardCode,

            /**
             * need to reload list
             */
            reloadList: reloadList,

            canShowForm: false,

            /**
             * is there some added gift cards
             */
            hasAddedGiftCard: hasAddedGiftCard,


            /**
             * Show or hide gift card list
             * @returns {*}
             */
            isVisibleList: function () {
                this.getGiftCardList();
                return hasAddedGiftCard();
            },

            /**
             * Coupon code application procedure
             */
            apply: function() {
                addGiftCard(giftCardCode(), reloadList);
            },

            /**
             * Returns gift card list
             */
            getGiftCardList: function () {
                if (reloadList()) {
                    reloadList(false);
                    hasAddedGiftCard(false);
                    var url = urlManager.getGiftCardUrl(quote.getQuoteId());
                    storage.get(
                        url
                    ).done(
                        function (response) {
                            if (response) {
                                var resp = JSON.parse(response);
                                if (resp.isEnabled != '1') {
                                    $('.opc-payment-additional.giftcard-code').hide();
                                }

                                var giftCards = resp.listData;
                                if (giftCards.length == 0){
                                    hasAddedGiftCard(false);
                                } else {
                                    hasAddedGiftCard(true);
                                }
                                giftCardList(giftCards);
                            }
                        }
                    );
                }
                return giftCardList;
            },

            /**
             * Remove gift card
             */
            remove: function(giftCard) {
                removeGiftCard(giftCard.code, reloadList);
            },

            /**
             * Coupon form validation
             *
             * @returns {Boolean}
             */
            validate: function () {
                var form = '#gift_card_code';
                return $(form).validation() && $(form).validation('isValid');
            }
        });
    }
);
