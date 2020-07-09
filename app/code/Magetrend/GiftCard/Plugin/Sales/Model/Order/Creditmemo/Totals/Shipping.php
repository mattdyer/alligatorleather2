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

namespace  Magetrend\GiftCard\Plugin\Sales\Model\Order\Creditmemo\Totals;

class Shipping
{

    /**
     * Add compensated tax to shipping
     * @param $subject
     * @param $collect
     * @param \Magento\Sales\Model\Order\Creditmemo $creditMemo
     * @return mixed
     */
    public function aroundCollect($subject, $collect, \Magento\Sales\Model\Order\Creditmemo $creditMemo)
    {
        $order = $creditMemo->getOrder();
        if ($order->getGiftcardShippingInvoiced() != 0 ) {
            $order->setShippingTaxAmount($order->getShippingTaxAmount() + $order->getData('shipping_discount_tax_compensation_amount'));
            $order->setBaseShippingTaxAmount($order->getBaseShippingTaxAmount() + $order->getData('base_shipping_discount_tax_compensation_amnt'));
        }

        $result = $collect($creditMemo);

        if ($order->getGiftcardShippingInvoiced() != 0 ) {
            $order->setShippingTaxAmount($order->getShippingTaxAmount() - $order->getData('shipping_discount_tax_compensation_amount'));
            $order->setBaseShippingTaxAmount($order->getBaseShippingTaxAmount() - $order->getData('base_shipping_discount_tax_compensation_amnt'));
        }

        return $result;
    }
}
