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

namespace  Magetrend\GiftCard\Plugin\Sales\Model\Order\Invoice;

class Item
{
    /**
     * Copy invoice item data to order item
     * @param $subject
     * @param $item
     * @return mixed
     */
    public function afterRegister($subject, $item)
    {
        $orderItem = $item->getOrderItem();
        $orderItem->setGiftcardInvoiced($orderItem->getGiftcardInvoiced() + $item->getGiftcardAmount());
        $orderItem->setBaseGiftcardInvoiced($orderItem->getBaseGiftcardInvoiced() + $item->getBaseGiftcardAmount());
        return $item;
    }
}
