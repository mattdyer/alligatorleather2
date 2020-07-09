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

namespace Magetrend\GiftCard\Plugin\Sales\Block\Adminhtml\Order\View\Items\Renderer;

class DefaultRenderer
{

    /**
     * Add gift card amount column before totals
     * @param $subject
     * @param $columns
     * @return array
     */
    public function afterGetColumns($subject, $columns)
    {
        if (count($columns) == 0) {
            return $columns;
        }
        $sortedColumns = [];
        foreach ($columns as $key => $column) {
            if ($key == 'total') {
                $sortedColumns['giftcard'] = 'col-giftcard';

            }
            $sortedColumns[$key] = $column;
        }
        return $sortedColumns;
    }
}
