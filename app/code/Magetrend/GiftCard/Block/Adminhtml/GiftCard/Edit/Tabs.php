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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCard\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Update tabs configuration
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftcardset_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Gift Card Information'));
    }

    /**
     * Add tabs bloks
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _beforeToHtml()
    {
        $this->addTab(
            'general_section',
            [
                'label' => __('General Information'),
                'title' => __('General Information'),
                'active' => true,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\General'
                )->toHtml()
            ]
        );

        $this->addTab(
            'history_section',
            [
                'label' => __('Balance History'),
                'title' => __('Balance History'),
                'active' => false,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\History'
                )->toHtml()
            ]
        );

        return parent::_beforeToHtml();
    }
}
