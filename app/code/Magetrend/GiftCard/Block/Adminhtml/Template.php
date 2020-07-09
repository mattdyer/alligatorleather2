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

class Template extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @inheritdoc
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_controller = 'template_index';
        $this->_headerText = __('Manage Template');
        parent::_construct();
    }

    /**
     * Change button url
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _prepareLayout()
    {
        $this->removeButton('add');
        $this->addButton('add', [
            'id' => 'add_new_blog_post',
            'label' => __('Create New Template'),
            'class' => 'add primary',
            'onclick' => "setLocation('" . $this->getUrl('giftcard/*/mteditor') . "')"
        ]);
        return parent::_prepareLayout();
    }
}
