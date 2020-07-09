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

namespace  Magetrend\GiftCard\Plugin\Quote\Address;

use Magento\Quote\Model\Quote\Address;

class ToOrder
{
    /**
     * Add quote address data to order
     * @param $subject
     * @param $parent
     * @param Address $address
     * @param array $data
     * @return mixed
     */
    public function aroundConvert($subject, $parent, Address $address, $data = [])
    {
        $order = $parent($address, $data);
        if ($address->getGiftcardAmount() != 0) {
            $order->setGiftcardAmount($address->getGiftcardAmount());
            $order->setBaseGiftcardAmount($address->getBaseGiftcardAmount());
        }

        if ($address->getGiftcardShippingAmount() != 0) {
            $order->setGiftcardShippingAmount($address->getGiftcardShippingAmount());
            $order->setBaseGiftcardShippingAmount($address->getBaseGiftcardShippingAmount());
        }

        return $order;
    }
}
