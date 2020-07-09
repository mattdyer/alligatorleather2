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

class GiftCardSet extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Updated widghet configuration
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_controller = 'giftcardset_index';
        $this->_addButtonLabel = __('Create New Set');
        $this->_headerText = __('Manage Sets');
        parent::_construct();
    }
}
