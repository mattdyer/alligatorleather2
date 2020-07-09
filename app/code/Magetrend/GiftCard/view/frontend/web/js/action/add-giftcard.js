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
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magetrend_GiftCard/js/model/checkout/resource-url-manager',
        'Magento_Checkout/js/model/error-processor',
        'Magetrend_GiftCard/js/model/payment/giftcard-messages',
        'mage/storage',
        'mage/translate',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (
        ko, $, quote, urlManager, errorProcessor, messageContainer, storage, $t, getPaymentInformationAction, totals,
        fullScreenLoader
    ) {
        'use strict';

        return function (couponCode, reloadList) {
            var quoteId = quote.getQuoteId(),
                url = urlManager.getApplyGiftCardUrl(couponCode, quoteId),
                message = $t('Your gift card was successfully applied.');

            fullScreenLoader.startLoader();
            return storage.put(
                url,
                {},
                false
            ).done(
                function (response) {
                    if (response) {
                        var deferred = $.Deferred();

                        reloadList(true);
                        totals.isLoading(true);
                        getPaymentInformationAction(deferred);
                        $.when(deferred).done(function () {
                            fullScreenLoader.stopLoader();
                            totals.isLoading(false);
                        });
                        messageContainer.addSuccessMessage({
                            'message': message
                        });
                    }
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                    totals.isLoading(false);
                    errorProcessor.process(response, messageContainer);
                }
            );
        };
    }
);
