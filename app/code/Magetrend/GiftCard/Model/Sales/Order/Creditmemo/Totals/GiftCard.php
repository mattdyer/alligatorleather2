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

namespace Magetrend\GiftCard\Model\Sales\Order\Creditmemo\Totals;

class GiftCard extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * Gift Card Discount calculation object
     *
     * @var \Magetrend\GiftCard\Model\Sales\TotalsCalculator
     */
    public $calculator;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrency;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magetrend\GiftCard\Model\Sales\TotalsCalculator  $totalsCalculator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magetrend\GiftCard\Model\Sales\TotalsCalculator $totalsCalculator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->setCode('giftcard');
        $this->calculator = $totalsCalculator;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
    }

    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditMemo)
    {

        $creditMemo->setGiftcardAmount(0);
        $creditMemo->setBaseGiftcardAmount(0);
        $creditMemo->setGiftcardShippingAmount(0);
        $creditMemo->setBaseGiftcardShippingAmount(0);

        $order = $creditMemo->getOrder();

        if ($order->getGiftcardAmount() == 0) {
            return $this;
        }

        $totalDiscountAmount = 0;
        $baseTotalDiscountAmount = 0;

        /**
         * Calculate how much shipping discount should be applied
         * basing on how much shipping should be refunded.
         */
        $baseShippingAmount = (float)$creditMemo->getBaseShippingAmount();

        if ($baseShippingAmount) {
            $baseTotalDiscountAmount = min($order->getBaseGiftcardShippingInvoiced(), $order->getBaseShippingAmount());
            $totalDiscountAmount = min($order->getGiftcardShippingInvoiced(), $order->getShippingAmount());

            $creditMemo->setGiftcardShippingAmount($totalDiscountAmount);
            $creditMemo->setBaseGiftcardShippingAmount($baseTotalDiscountAmount);
        }

        /** @var $item \Magento\Sales\Model\Order\Invoice\Item */
        foreach ($creditMemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();

            if ($orderItem->isDummy()) {
                continue;
            }

            $orderItemDiscount = (double)$orderItem->getGiftcardInvoiced();
            $baseOrderItemDiscount = (double)$orderItem->getBaseGiftcardInvoiced();
            $orderItemQty = $orderItem->getQtyInvoiced();

            if ($orderItemDiscount && $orderItemQty) {
                $discount = $orderItemDiscount - $orderItem->getGiftcardRefunded();
                $baseDiscount = $baseOrderItemDiscount - $orderItem->getBaseGiftcardRefunded();
                if (!$item->isLast()) {
                    $availableQty = $orderItemQty - $orderItem->getQtyRefunded();
                    $discount = $creditMemo->roundPrice($discount / $availableQty * $item->getQty(), 'regular', true);
                    $baseDiscount = $creditMemo->roundPrice(
                        $baseDiscount / $availableQty * $item->getQty(),
                        'base',
                        true
                    );
                }

                $item->setGiftcardAmount($discount);
                $item->setBaseGiftcardAmount($baseDiscount);

                $totalDiscountAmount += $discount;
                $baseTotalDiscountAmount += $baseDiscount;
            }
        }

        $creditMemo->setGiftcardAmount(-$totalDiscountAmount);
        $creditMemo->setBaseGiftcardAmount(-$baseTotalDiscountAmount);

        //$creditMemo->setGrandTotal($creditMemo->getGrandTotal() + $order->getData('discount_tax_compensation_invoiced') - $order->getData('shipping_discount_tax_compensation_amount'));
        //$creditMemo->setBaseGrandTotal($creditMemo->getBaseGrandTotal() + $order->getData('base_discount_tax_compensation_invoiced') - $order->getData('base_shipping_discount_tax_compensation_amnt'));

        $creditMemo->setGrandTotal($creditMemo->getGrandTotal() + $totalDiscountAmount);
        $creditMemo->setBaseGrandTotal($creditMemo->getBaseGrandTotal() + $baseTotalDiscountAmount);

        $creditMemo->setGiftcardRefunded($totalDiscountAmount);
        $creditMemo->setBaseGiftcardRefunded($baseTotalDiscountAmount);
        return $this;
    }
}
