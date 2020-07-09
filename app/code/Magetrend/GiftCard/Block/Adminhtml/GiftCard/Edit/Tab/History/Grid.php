<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Affiliate
 * @author   Edvinas St. <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.magetrend.com/magento-2-affiliate
 */

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\History;

use Magetrend\GiftCard\Api\Data\HistoryInterface;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * @var \Magetrend\Affiliate\Model\ResourceModel\Record\Click\CollectionFactory
     */
    public $collectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magetrend\GiftCard\Model\ResourceModel\History\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftCardHistory');
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
        $giftCard = $this->coreRegistry->registry('giftcard_giftcard');
        if ($giftCard->getId()) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter(HistoryInterface::GIFT_CARD_ID, $giftCard->getId());
            $this->setCollection($collection);
        }

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
            'date',
            [
                'header' => __('Date'),
                'index' => 'created_at',
                'type' => 'datetime',
                'align' => 'center',
                'width' => '160',
                'filter' => false,
            ]
        );

        $this->addColumn(
            'amount',
            [
                'header' => __('Amount'),
                'index' => 'amount',
                'filter' => false,
                'renderer' =>
                    \Magetrend\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\History\Grid\Column\Renderer\Amount::class

            ]
        );

        $this->addColumn(
            'balance',
            [
                'header' => __('Balance'),
                'index' => 'balance',
                'filter' => false,
                'renderer' =>
                    \Magetrend\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\History\Grid\Column\Renderer\Balance::class
            ]
        );

        $this->addColumn(
            'gift_card_status',
            [
                'header' => __('Status'),
                'index' => 'gift_card_status',
                'filter' => false,
                'renderer' =>
                    \Magetrend\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\History\Grid\Column\Renderer\Translate::class
            ]
        );

        $this->addColumn(
            'message',
            [
                'header' => __('Notes'),
                'filter' => false,
                'index' => 'message',
                'renderer' =>
                    \Magetrend\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\History\Grid\Column\Renderer\Message::class
            ]
        );

        $this->addColumn(
            'actions',
            [
                'header' => __('Actions'),
                'filter' => false,
                'index' => 'actions',
                'renderer' =>
                    \Magetrend\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\History\Grid\Column\Renderer\Actions::class
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Returns grid url
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('giftcard/giftcard/historyGrid', ['_current' => true]);
    }
}
