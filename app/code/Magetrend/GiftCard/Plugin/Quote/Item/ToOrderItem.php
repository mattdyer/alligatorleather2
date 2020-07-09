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

namespace  Magetrend\GiftCard\Plugin\Quote\Item;

class ToOrderItem
{
    /**
     * Add gift card discount to order item
     * @param $subject
     * @param $parent
     * @param Item|AddressItem $item
     * @param array $data
     * @return OrderItemInterface
     */
    public function aroundConvert($subject, $parent, $item, $data = [])
    {
        $orderItem = $parent($item, $data);
        if ($item->getGiftcardAmount() != 0) {
            $orderItem['giftcard_amount'] = $item->getGiftcardAmount();
            $orderItem['base_giftcard_amount'] = $item->getBaseGiftcardAmount();
        }
        return $orderItem;
    }
}
