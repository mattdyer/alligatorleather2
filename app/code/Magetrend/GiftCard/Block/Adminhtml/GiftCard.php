<?php
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

namespace Magetrend\GiftCard\Block\Adminhtml;

class GiftCard extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Update widget configuration
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_controller = 'giftcard_index';
        $this->_addButtonLabel = __('New Gift Card');
        $this->_headerText = __('Manage Gift Cards');
        parent::_construct();
    }
}
