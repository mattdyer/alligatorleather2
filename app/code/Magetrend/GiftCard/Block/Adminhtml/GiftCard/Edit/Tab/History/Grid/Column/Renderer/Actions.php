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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\History\Grid\Column\Renderer;

use Magetrend\GiftCard\Api\Data\HistoryInterface;

class Actions extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $relatedObject = $row->getRelatedObject();
        $actions = [];

        if ($row->getRelatedObject() == HistoryInterface::RELATED_OBJECT_ORDER) {
            $actions[] = [
                'url' => $this->getUrl('sales/order/view', ['order_id' => $row->getRelatedId()]),
                'caption' => __('View Order'),
            ];
        }

        $this->getColumn()->setActions($actions);
        return parent::render($row);
    }
}
