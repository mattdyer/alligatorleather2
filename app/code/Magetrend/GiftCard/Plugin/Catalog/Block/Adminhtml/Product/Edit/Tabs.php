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

namespace  Magetrend\GiftCard\Plugin\Catalog\Block\Adminhtml\Product\Edit;

class Tabs
{
    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $helper;

    public $layout;

    public $registry;

    /**
     * Tabs constructor.
     * @param \Magetrend\GiftCard\Helper\Data $helper
     */
    public function __construct(\Magetrend\GiftCard\Helper\Data $helper,
                                \Magento\Framework\View\LayoutInterface $layout,
                                \Magento\Framework\Registry $registry
    ) {
        $this->layout = $layout;
        $this->helper = $helper;
        $this->registry = $registry;
    }

    /**
     * Add tab
     * @param $subject
     */
    public function beforeToHtml($subject)
    {
       if ($this->helper->isM20()) {
           $currentProduct = $this->registry->registry('current_product');
            if (!$currentProduct
                || $currentProduct->getTypeId() != \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                return;
            }

           $subject->addTab(
               'gift_card_options',
               [
                   'label' => __('Gift Card Options'),
                   'content' =>
                       $this->layout->createBlock(
                           'Magetrend\GiftCard\Block\Adminhtml\Product\Edit\Tab\GiftCardOption'
                       )->toHtml(),
                   'group_code' => 'advanced'
               ]
           );

           $subject->addTab(
               'gift_card_series',
               [
                   'label' => __('Assign Gift Card Sets'),
                   'url' => $subject->getUrl('giftcard/catalog_product/sets', ['_current' => true]),
                   'class' => 'ajax',
                   'group_code' => 'advanced'
               ]
           );
       }
    }
}
