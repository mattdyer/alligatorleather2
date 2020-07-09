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

namespace Magetrend\GiftCard\Plugin\Sales\Block\Adminhtml\Items\Column;

class DefaultColumn
{

    /**
     * Add gift card discount to total amount
     * @param $subject
     * @param $parent
     * @param $item
     * @return mixed
     */
    public function aroundGetTotalAmount($subject, $parent, $item)
    {
        $totalAmount = $parent($item);
        $totalAmount = max($totalAmount + $item->getGiftcardAmount(), 0);
        return $totalAmount;
    }

    /**
     * Add gift card discount to base total amount
     * @param $subject
     * @param $parent
     * @param $item
     * @return mixed
     */
    public function aroundGetBaseTotalAmount($subject, $parent, $item)
    {
        $baseTotalAmount = $parent($item);
        $baseTotalAmount = max($baseTotalAmount + $item->getBaseGiftcardAmount(), 0);
        return $baseTotalAmount;
    }
}
