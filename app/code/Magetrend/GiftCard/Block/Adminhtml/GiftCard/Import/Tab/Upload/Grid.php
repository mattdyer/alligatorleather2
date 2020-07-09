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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCard\Import\Tab\Upload;

use Magetrend\GiftCard\Model\GiftCard;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @var GiftCard\Import
     */
    public $import;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param GiftCard\Import $import
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magetrend\GiftCard\Model\GiftCard\Import $import,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->import = $import;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Update block configuration
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();
        $this->setId('couponCodesGrid');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
    }

    /**
     * Prepare collection for grid
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _prepareCollection()
    {
        $filePath = $this->_backendSession->getData('giftcard_file_path');
        if (empty($filePath)) {
            return parent::_prepareCollection();
        }

        $collection = $this->import->getCollectionFromFile($filePath);
        $this->setCollection($collection);
    }

    /**
     * Define grid columns
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _prepareColumns()
    {
        $filePath = $this->_backendSession->getData('giftcard_file_path');
        $columnList = $this->import->getColumnList($filePath);
        foreach ($columnList as $column) {
            $columnData = [
                'header' => __('Column: '.$column),
                'index' => $column,
                'filter'    => false,
                'sortable'  => false
            ];

            $this->addColumn('grid_'.$column, $columnData);
        }
        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('giftcard/giftcard/importGrid', ['_current' => true]);
    }
}
