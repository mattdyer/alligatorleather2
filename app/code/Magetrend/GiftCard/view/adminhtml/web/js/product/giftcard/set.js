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

require([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal'
], function($){
    $('.gift-card-set-action').click(function(){
        console.log('trigger vor');
        var form = $('[data-role=giftcard-set-selector]');
        form.modal('openModal');
    });

});