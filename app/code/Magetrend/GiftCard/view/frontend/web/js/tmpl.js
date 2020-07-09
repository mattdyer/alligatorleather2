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

define(['jquery'], function ($) {
        'use strict';

    var product = function() {

        var config = {};
        var init = function(options) {
            config = {

            };
            $.extend(config, options );
        };


        return {
            init: init
        };

    };

    $.widget('mage.gcProduct', {
        options: {},
        _create: function() {
            product.init(this.options);
        }
    });

    return $.mage.gcProduct;
});
