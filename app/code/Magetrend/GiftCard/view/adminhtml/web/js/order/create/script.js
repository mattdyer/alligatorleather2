/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/GiftCard
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-gift-card
 */

require([
    'jquery',
    "Magento_Sales/order/create/form"
], function(jQuery){
    var giftCard,
        config;

    window.GiftCardForm = Class.create();
    GiftCardForm.prototype = {
        config: {},

        initialize: function(config){
            this.config = config;
        },

        add: function(giftCardCode){
            new Ajax.Request(this.config.action.add, {
                parameters: {
                    gift_card_code: giftCardCode
                },

                onSuccess: function(response) {
                    if (response.status == 200) {
                        order.loadArea(['items', 'shipping_method', 'totals', 'billing_method'], true, {reset_shipping: 0});
                        order.orderItemChanged = false;
                    }
                }
            });
        },

        remove: function(giftCardId){
            new Ajax.Request(this.config.action.remove, {
                parameters: {
                    gift_card_id: giftCardId
                },

                onSuccess: function(response) {
                    if (response.status == 200) {
                        order.loadArea(['items', 'shipping_method', 'totals', 'billing_method'], true, {reset_shipping: 0});
                        order.orderItemChanged = false;
                    }
                }
            });
        }
    };

    config = jQuery('[data-giftcard-config]').data('giftcard-config');
    giftCard = new GiftCardForm(config);
    window.giftCard = giftCard;
});