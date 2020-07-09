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
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magetrend_GiftCard/summary/giftcard'
            },

            totals: quote.getTotals(),

            isDisplayed: function() {
                return this.isCalculated() && this.getPureValue() != 0;
            },

            getPureValue: function() {
                if (!this.isCalculated()) {
                    return 0;
                }
                var price = totals.getSegment('giftcard').value;
              //  alert(console.log(price));
                return price;
            },

            isCalculated: function() {
                return this.totals() && null != totals.getSegment('giftcard');
            },

            getValue: function() {
                return this.getFormattedPrice(this.getPureValue());
            }
        });
    }
);
