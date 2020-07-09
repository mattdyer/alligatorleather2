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

namespace Magetrend\GiftCard\Observer\Sales\Service\Quote;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SubmitBefore implements ObserverInterface
{
    /**
     * Assign quote address data to order
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Sales\Model\Order
         */
        $order = $observer->getOrder();
        /**
         * @var \Magento\Quote\Model\Quote
         */
        $quote = $observer->getQuote();
        $addresses = $quote->getAllAddresses();
        foreach ($addresses as $address) {
            if ($address->getGiftcardAmount() != 0) {
                $order->setGiftcardAmount($address->getGiftcardAmount());
                $order->setBaseGiftcardAmount($address->getBaseGiftcardAmount());
            }

            if ($address->getGiftcardShippingAmount() != 0) {
                $order->setGiftcardShippingAmount($address->getGiftcardShippingAmount());
                $order->setBaseGiftcardShippingAmount($address->getBaseGiftcardShippingAmount());
            }
        }
    }
}
