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

namespace Magetrend\GiftCard\Observer\Sales\Order\Invoice;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class Register implements ObserverInterface
{
    /**
     * After Invoice pay observer
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Sales\Model\Order
         */
        $order = $observer->getOrder();
        $invoice = $observer->getInvoice();

        $order->setGiftcardInvoiced($order->getGiftcardInvoiced() + $invoice->getGiftcardAmount());
        $order->setBaseGiftcardInvoiced($order->getBaseGiftcardInvoiced() + $invoice->getBaseGiftcardAmount());

        $order->setGiftcardShippingInvoiced(
            $order->getGiftcardShippingInvoiced() + $invoice->getGiftcardShippingAmount()
        );
        $order->setBaseGiftcardShippingInvoiced(
            $order->getBaseGiftcardShippingInvoiced() + $invoice->getBaseGiftcardShippingAmount()
        );

        return $this;
    }
}
