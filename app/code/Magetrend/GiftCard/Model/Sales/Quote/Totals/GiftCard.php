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

namespace Magetrend\GiftCard\Model\Sales\Quote\Totals;

class GiftCard extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
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
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $moduleHelper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magetrend\GiftCard\Model\Sales\TotalsCalculator  $totalsCalculator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magetrend\GiftCard\Model\Sales\TotalsCalculator $totalsCalculator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magetrend\GiftCard\Helper\Data $moduleHelper
    ) {
        $this->setCode('giftcard');
        $this->calculator = $totalsCalculator;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Collect address discount amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $shippingAssignment->getItems();

        if (!count($items)) {
            return $this;
        }

        $this->calculator->reset($items, $address);

        if (!$this->calculator->isAllowedToProcess($quote->getId())) {
            return $this;
        }

        $this->calculator->initTotals($items, $address);
       //** @var \Magento\Quote\Model\Quote\Item $item
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $this->calculator->process($item);
                $this->distributeDiscount($item);
                foreach ($item->getChildren() as $child) {
                    $this->aggregateItemDiscount($child, $total);
                }
            } else {
                $this->calculator->process($item);
                $this->aggregateItemDiscount($item, $total);
            }
        }

        /**
         * Process shipping amount discount
         */
        if ($address->getShippingAmount() > 0) {
            $this->calculator->processShippingAmount($address, $total);
            $total->addTotalAmount($this->getCode(), $address->getGiftcardShippingAmount());
            $total->addBaseTotalAmount($this->getCode(), $address->getBaseGiftcardShippingAmount());
        }

        $giftCardTotalAmount = $total->getTotalAmount($this->getCode());
        $baseGiftCardTotalAmount = $total->getBaseTotalAmount($this->getCode());
        $giftCardTotalAmount = $giftCardTotalAmount<0?$giftCardTotalAmount:-$giftCardTotalAmount;
        $baseGiftCardTotalAmount = $baseGiftCardTotalAmount<0?$baseGiftCardTotalAmount:-$baseGiftCardTotalAmount;

        $address->setGiftcardAmount($giftCardTotalAmount); //negative
        $address->setBaseGiftcardAmount($baseGiftCardTotalAmount); //negative

        $total->setSubtotalWithDiscount($total->getSubtotal() + $giftCardTotalAmount);
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $baseGiftCardTotalAmount);
        return $this;
    }

    /**
     * Aggregate item discount information to total data and related properties
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    public function aggregateItemDiscount(
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $total->addTotalAmount($this->getCode(), $item->getGiftcardAmount());
        $total->addBaseTotalAmount($this->getCode(), $item->getBaseGiftcardAmount());
        return $this;
    }

    /**
     * Distribute discount at parent item to children items
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return $this
     */
    public function distributeDiscount(\Magento\Quote\Model\Quote\Item\AbstractItem $item)
    {
        $parentBaseRowTotal = $item->getBaseRowTotal();
        $keys = [
            'giftcard_amount',
            'base_giftcard_amount',
            'giftcard_shipping_amount',
            'base_giftcard_shipping_amount'

        ];
        $roundingDelta = [];
        foreach ($keys as $key) {
            //Initialize the rounding delta to a tiny number to avoid floating point precision problem
            $roundingDelta[$key] = 0.0000001;
        }
        foreach ($item->getChildren() as $child) {
            $ratio = $child->getBaseRowTotal() / $parentBaseRowTotal;
            foreach ($keys as $key) {
                if (!$item->hasData($key)) {
                    continue;
                }
                $value = $item->getData($key) * $ratio;
                $roundedValue = $this->priceCurrency->round($value + $roundingDelta[$key]);
                $roundingDelta[$key] += $value - $roundedValue;
                $child->setData($key, $roundedValue);
            }
        }

        foreach ($keys as $key) {
            $item->setData($key, 0);
        }
        return $this;
    }

    /**
     * Add gift card discount total information to address
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;
        $amount = $total->getData('giftcard_amount');

        if ($amount != 0) {
            if ($amount > 0) {
                $amount *=-1;
            }
            $result = [
                'code' => 'giftcard',
                'title' => __('Gift Card Discount'),
                'value' => $amount
            ];
        }
        return $result;
    }
}
