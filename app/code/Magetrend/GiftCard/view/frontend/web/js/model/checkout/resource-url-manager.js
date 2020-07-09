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
        'Magento_Checkout/js/model/resource-url-manager'
    ],
    function(urlBuilder) {
        "use strict";
        return {
            getApplyGiftCardUrl: function (giftCardCode, quoteId) {
                var params = (urlBuilder.getCheckoutMethod() == 'guest') ? {quoteId: quoteId} : {};
                var urls = {
                    'guest': '/guest-carts/' + quoteId + '/gift-card/' + giftCardCode,
                    'customer': '/carts/mine/gift-card/' + giftCardCode
                };
                return urlBuilder.getUrl(urls, params);
            },

            getRemoveGiftCardUrl: function (giftCardCode, quoteId) {
                var params = (urlBuilder.getCheckoutMethod() == 'guest') ? {quoteId: quoteId} : {};
                var urls = {
                    'guest': '/guest-carts/' + quoteId + '/gift-card/' + giftCardCode,
                    'customer': '/carts/mine/gift-card/' + giftCardCode
                };
                return urlBuilder.getUrl(urls, params);
            },

            getGiftCardUrl: function (quoteId) {
                var params = (urlBuilder.getCheckoutMethod() == 'guest') ? {quoteId: quoteId} : {};
                var urls = {
                    'guest': '/guest-carts/' + quoteId + '/gift-card',
                    'customer': '/carts/mine/gift-card/'
                };
                return urlBuilder.getUrl(urls, params);
            }
        }
    }
);

