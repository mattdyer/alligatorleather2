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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCard;

class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Prepare grid coulumns
     *
     * @return mixed
     */
    public function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
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
        return $this->getUrl('giftcard/*/grid', ['_current' => true]);
    }

    /**
     * Returns row url
     *
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'giftcard/*/edit',
            ['id' => $row->getId()]
        );
    }
}
