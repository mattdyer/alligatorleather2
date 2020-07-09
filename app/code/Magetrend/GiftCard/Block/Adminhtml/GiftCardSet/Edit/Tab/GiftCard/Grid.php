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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCardSet\Edit\Tab\GiftCard;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * @var \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory
     */
    public $collectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Update grid configuration
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();
        $this->setId('couponCodesGrid');
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for grid
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _prepareCollection()
    {
        $giftCardSet = $this->coreRegistry->registry('giftcard_giftcardset');
        /**
         * @var \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $collection
         */
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('gift_card_set_id', $giftCardSet->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _prepareColumns()
    {
        $this->addColumn(
            'code',
            [
                'header' => __('Gift Card Code'),
                'index' => 'code',
            ]
        );

        $this->addColumn(
            'balance',
            [
                'header' => __('Balance'),
                'index' => 'balance',
                'type' => 'text',
                'renderer' =>
                    'Magetrend\GiftCard\Block\Adminhtml\GiftCard\Grid\Column\Renderer\Balance'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'text',
                'renderer' =>
                    'Magetrend\GiftCard\Block\Adminhtml\GiftCard\Grid\Column\Renderer\Status'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created'),
                'index' => 'created_at',
                'type' => 'datetime',
                'align' => 'center',
                'width' => '160'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('giftcard/giftcardset/giftCardGrid', ['_current' => true]);
    }
}
