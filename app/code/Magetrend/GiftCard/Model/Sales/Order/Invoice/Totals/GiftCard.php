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

namespace Magetrend\GiftCard\Model\Sales\Order\Invoice\Totals;

class GiftCard extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
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

    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {

        $invoice->setGiftcardAmount(0);
        $invoice->setBaseGiftcardAmount(0);
        $invoice->setGiftcardShippingAmount(0);
        $invoice->setBaseGiftcardShippingAmount(0);

        $totalDiscountAmount = 0;
        $baseTotalDiscountAmount = 0;

        /**
         * Checking if shipping discount was added in previous invoices.
         * So basically if we have invoice with positive discount and it
         * was not canceled we don't add shipping discount to this one.
         */
        $addShippingDiscount = true;
        foreach ($invoice->getOrder()->getInvoiceCollection() as $previousInvoice) {
            if ($previousInvoice->getGiftcardShippingAmount()) {
                $addShippingDiscount = false;
            }
        }

        if ($addShippingDiscount) {
            $totalDiscountAmount = $totalDiscountAmount + $invoice->getOrder()->getGiftcardShippingAmount();
            $baseTotalDiscountAmount = $baseTotalDiscountAmount + $invoice->getOrder()->getBaseGiftcardShippingAmount();
            $invoice->setGiftcardShippingAmount($totalDiscountAmount);
            $invoice->setBaseGiftcardShippingAmount($baseTotalDiscountAmount);
        }

        /** @var $item \Magento\Sales\Model\Order\Invoice\Item */
        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }

            $orderItemDiscount = (double)$orderItem->getGiftcardAmount();
            $baseOrderItemDiscount = (double)$orderItem->getBaseGiftcardAmount();
            $orderItemQty = $orderItem->getQtyOrdered();

            if ($orderItemDiscount && $orderItemQty) {
                /**
                 * Resolve rounding problems
                 */
                $discount = $orderItemDiscount - $orderItem->getGiftcardInvoiced();
                $baseDiscount = $baseOrderItemDiscount - $orderItem->getBaseGiftcardInvoiced();
                if (!$item->isLast()) {
                    $activeQty = $orderItemQty - $orderItem->getQtyInvoiced();
                    $discount = $invoice->roundPrice($discount / $activeQty * $item->getQty(), 'regular', true);
                    $baseDiscount = $invoice->roundPrice($baseDiscount / $activeQty * $item->getQty(), 'base', true);
                }

                $item->setGiftcardAmount($discount);
                $item->setBaseGiftcardAmount($baseDiscount);

                $totalDiscountAmount += $discount;
                $baseTotalDiscountAmount += $baseDiscount;
            }
        }

        $invoice->setGiftcardAmount($totalDiscountAmount);
        $invoice->setBaseGiftcardAmount($baseTotalDiscountAmount);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $totalDiscountAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseTotalDiscountAmount);

        return $this;
    }
}
