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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCard\Import\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;

class Upload extends \Magento\Backend\Block\Text\ListText implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Manage Coupon Codes');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Manage Coupon Codes');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return $this->_isEditing();
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return !$this->_isEditing();
    }

    /**
     * Check whether we edit existing rule or adding new one
     *
     * @return bool
     */
    public function _isEditing()
    {
        return false;
    }

    public function _beforeToHtml()
    {
        $this->setChild(
            'import_code_form',
            $this->getLayout()->createBlock('Magetrend\GiftCard\Block\Adminhtml\GiftCard\Import\Tab\Upload\Form')
        );

        $this->setChild(
            'import_code_grid',
            $this->getLayout()->createBlock('Magetrend\GiftCard\Block\Adminhtml\GiftCard\Import\Tab\Upload\Grid')
        );
        return parent::_beforeToHtml();
    }
}
