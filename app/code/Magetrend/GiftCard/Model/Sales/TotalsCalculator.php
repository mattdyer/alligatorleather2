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

namespace Magetrend\GiftCard\Model\Sales;

use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\ScopeInterface;
use Magetrend\GiftCard\Model\Config\Source\Tax;

class TotalsCalculator extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var int
     */
    private $itemDiscountAmount = 0;

    /**
     * @var int
     */
    private $baseItemDiscountAmount = 0;

    /**
     * @var int
     */
    private $shippingDiscountAmount = 0;

    /**
     * @var int
     */
    private $baseShippingDiscountAmount = 0;

    /**
     * @var \Magetrend\GiftCard\Model\Quote
     */
    public $giftCardQuote;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    public $catalogData;

    /**
     * @var \Magento\Framework\DataObject
     */
    private $amount;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrency;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Magento\GiftCard\Helper\Data
     */
    public $moduleHelper;

    /**
     * @var \Magento\Tax\Model\Config
     */
    public $taxConfig;

    /**
     * TotalsCalculator constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magetrend\GiftCard\Model\Quote $giftCardQuote
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\GiftCard\Model\Quote $giftCardQuote,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magetrend\GiftCard\Helper\Data $moduleHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->giftCardQuote = $giftCardQuote;
        $this->catalogData = $catalogData;
        $this->priceCurrency = $priceCurrency;
        $this->scopeConfig = $scopeConfigInterface;
        $this->moduleHelper = $moduleHelper;
        $this->taxConfig = $taxConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Is added some gift cards to quote
     * @return bool
     */
    public function isAllowedToProcess($quoteId)
    {
        if ($this->giftCardQuote->getGiftCardCollection($quoteId)->getSize() == 0) {
            return false;
        }
        return true;
    }

    public function reset($items, $address)
    {
        $address->setBaseGiftcardShippingAmount(0);
        $address->setGiftcardShippingAmount(0);
        $address->setBaseGiftcardAmount(0);
        $address->setGiftcardAmount(0);
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $child->setGiftcardAmount(0);
                    $child->setBaseGiftcardAmount(0);
                }
            } else {
                $item->setGiftcardAmount(0);
                $item->setBaseGiftcardAmount(0);
            }
        }
    }

    /**
     * Calculate gift card discount amounts
     * @param $items
     * @param Address $address
     */
    public function initTotals($items, Address $address)
    {
        $this->shippingDiscountAmount = 0;
        $this->baseShippingDiscountAmount = 0;
        $this->itemDiscountAmount = 0;
        $this->baseItemDiscountAmount = 0;

        $totalGiftCardBalance = $this->giftCardQuote->getTotalBalance($address->getQuote()->getQuoteCurrencyCode());
        $baseTotalGiftCardBalance = $this->giftCardQuote->getTotalBalance($address->getQuote()->getBaseCurrencyCode());

        $itemPrice = 0;
        $baseItemPrice = 0;
        $itemDiscountAmount = 0;
        $baseItemDiscountAmount = 0;
        $storeId = 0;

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            $qty = $item->getTotalQty();
            $itemPrice += $this->getItemDiscountCalculationPrice($item) * $qty;
            $baseItemPrice += $this->getItemBaseDiscountCalculationPrice($item) * $qty;
            $itemDiscountAmount += $item->getDiscountAmount();
            $baseItemDiscountAmount += $item->getBaseDiscountAmount();
            $storeId = $item->getStoreId();
        }

        $shippingPrice = $this->getShippingDiscountCalculationPrice($address, $storeId);
        $baseShippingPrice = $this->getBaseShippingDiscountCalculationPrice($address, $storeId);

        $this->amount = new \Magento\Framework\DataObject([
            'total_gift_card_balance' => $totalGiftCardBalance,
            'base_total_gift_card_balance' => $baseTotalGiftCardBalance,
            'item_price_total' => $itemPrice,
            'base_item_price_total' => $baseItemPrice,
            'discount_total' => $itemDiscountAmount,
            'base_discount_total' => $baseItemDiscountAmount,
            'shipping_price' => $shippingPrice,
            'base_shipping_price' => $baseShippingPrice,
            'discount_shipping' => $address->getShippingDiscountAmount(),
            'base_discount_shipping' => $address->getBaseShippingDiscountAmount(),
        ]);

        $totalCalculationAmount = $this->amount->getItemPriceTotal()+$this->amount->getShippingPrice();
        $baseTotalCalculationAmount = $this->amount->getBaseItemPriceTotal()+$this->amount->getBaseShippingPrice();

        $this->amount->setData('total_calculation_amount', $totalCalculationAmount);
        $this->amount->setData('base_total_calculation_amount', $baseTotalCalculationAmount);

        $balanceForItems = min($this->amount->getItemPriceTotal(), $this->amount->getTotalGiftCardBalance());
        $baseBalanceForItem = min($this->amount->getBaseItemPriceTotal(), $this->amount->getBaseTotalGiftCardBalance());

        $this->amount->setData('balance_for_item', $balanceForItems);
        $this->amount->setData('base_balance_for_item', $baseBalanceForItem);

        $this->amount->setData('balance_for_shipping', $this->amount->getTotalGiftCardBalance() - $balanceForItems);
        $this->amount->setData(
            'base_balance_for_shipping',
            $this->amount->getBaseTotalGiftCardBalance() - $baseBalanceForItem
        );

        $discountRateForItem = 0;
        $baseDiscountRateForItem = 0;
        if ($this->amount->getItemPriceTotal() != 0) {
            $discountRateForItem = $this->amount->getBalanceForItem() / $this->amount->getItemPriceTotal();
            $baseDiscountRateForItem = $this->amount->getBaseBalanceForItem() / $this->amount->getBaseItemPriceTotal();
        }

        $this->amount->setData('discount_rate', $discountRateForItem);
        $this->amount->setData('base_discount_rate', $baseDiscountRateForItem);
    }

    /**
     * Calculate item gift card discount amount
     * @param $item
     * @return $this|bool
     */
    public function process($item)
    {
        if ($this->amount->getBaseBalanceForItem() <= 0) {
            return false;
        }

        $qty = $item->getTotalQty();
        $itemPrice = $this->getItemDiscountCalculationPrice($item) * $qty;
        $baseItemPrice = $this->getItemBaseDiscountCalculationPrice($item) * $qty;
        if ($baseItemPrice < 0) {
            return false;
        }

        $discountAmount = min(
            $itemPrice,
            $this->priceCurrency->round($itemPrice * $this->amount->getDiscountRate()),
            $this->amount->getBalanceForItem()
        );
        $baseDiscountAmount = min(
            $itemPrice,
            $this->priceCurrency->round($itemPrice * $this->amount->getBaseDiscountRate()),
            $this->amount->getBaseBalanceForItem()
        );

        $this->amount->setBalanceForItem($this->amount->getBalanceForItem() - $discountAmount);
        $this->amount->setBaseBalanceForItem($this->amount->getBaseBalanceForItem() - $baseDiscountAmount);

        $item->setGiftcardAmount($discountAmount<0?$discountAmount:-$discountAmount); //negative
        $item->setBaseGiftcardAmount($baseDiscountAmount<0?$baseDiscountAmount:-$baseDiscountAmount); //negative

        return $this;
    }

    /**
     * Calculate gift card discount amount for shipping
     * @param $address
     * @param $total
     * @return $this|bool
     */
    public function processShippingAmount($address, $total)
    {
        if ($this->amount->getBaseBalanceForShipping() <= 0) {
            return false;
        }

        $shippingAmount = $this->amount->getShippingPrice();
        $baseShippingAmount = $this->amount->getBaseShippingPrice();

        if ($baseShippingAmount <= 0) {
            return $this;
        }

        $discountAmount = min(
            $shippingAmount,
            $this->amount->getBalanceForShipping()
        );
        $baseDiscountAmount = min(
            $baseShippingAmount,
            $this->amount->getBaseBalanceForShipping()
        );

        $address->setGiftcardShippingAmount($discountAmount<0?$discountAmount:-$discountAmount); //negative
        $address->setBaseGiftcardShippingAmount($baseDiscountAmount<0?$baseDiscountAmount:-$baseDiscountAmount); //negative

        $total->setShippingGiftcardAmount($discountAmount<0?$discountAmount:-$discountAmount); //negative
        $total->setBaseShippingGiftcardAmount($baseDiscountAmount<0?$baseDiscountAmount:-$baseDiscountAmount); //negative

        return $this;
    }

    /**
     * Return item price without tax and discount
     *
     * @param  $item
     * @return float
     */
    public function getItemDiscountCalculationPrice($item)
    {
        if ($this->moduleHelper->applyOnPriceIncludeTax($item->getStoreId())) {
            $itemPrice = $item->getPriceInclTax();
        } else {
            $itemPrice = $item->getPrice();
        }

        if ($this->moduleHelper->getTaxCalculationType($item->getStoreId()) == Tax::AFTER
            && !$this->moduleHelper->applyOnPriceIncludeTax($item->getStoreId())) {
            $itemPrice += $item->getTaxAmount() / $item->getTotalQty();
        }

        $itemDiscount = $item->getDiscountAmount() / $item->getTotalQty();
        return max(0, $itemPrice - $itemDiscount);
    }

    /**
     * Return base item price without tax and discount
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    public function getItemBaseDiscountCalculationPrice($item)
    {

        if ($this->moduleHelper->applyOnPriceIncludeTax($item->getStoreId())) {
            $itemPrice = $item->getBasePriceInclTax();
        } else {
            $itemPrice = $item->getBasePrice();
        }

        if ($this->moduleHelper->getTaxCalculationType($item->getStoreId()) == Tax::AFTER
            && !$this->moduleHelper->applyOnPriceIncludeTax($item->getStoreId())) {
            $itemPrice += $item->getBaseTaxAmount() / $item->getTotalQty();
        }

        $itemDiscount = $item->getBaseDiscountAmount() / $item->getTotalQty();
        return max(0, $itemPrice - $itemDiscount);
    }

    /**
     * Returns shipping amount with tax minus discount amount
     * @param Address $address
     * @return mixed
     */
    public function getShippingDiscountCalculationPrice(Address $address, $storeId = 0)
    {
        $shippingPrice = $address->getShippingAmount();
        if ($this->taxConfig->getShippingTaxClass($storeId) != 0
            && $this->moduleHelper->getTaxCalculationType($storeId) == Tax::AFTER) {
            $shippingPrice += $address->getShippingTaxAmount();
        }

        return max(0, $shippingPrice - $address->getShippingDiscountAmount());
    }

    /**
     * Returns base shipping amount with tax minus base discount amount
     * @param $address
     * @return mixed
     */
    public function getBaseShippingDiscountCalculationPrice($address, $storeId = 0)
    {
        $shippingPrice = $address->getBaseShippingAmount();
        if ($this->taxConfig->getShippingTaxClass($storeId) != 0
            && $this->moduleHelper->getTaxCalculationType($storeId) == Tax::AFTER) {
            $shippingPrice += $address->getBaseShippingTaxAmount();
        }

        return max(0, $shippingPrice - $address->getBaseShippingDiscountAmount());
    }

    /**
     * Calculate base item discount amount
     * @param $amount
     */
    public function addBaseItemDiscountAmount($amount)
    {
        $this->baseItemDiscountAmount +=$amount;
    }

    /**
     * Calculate item discount amount
     * @param $amount
     */
    public function addItemDiscountAmount($amount)
    {
        $this->itemDiscountAmount += $amount;
    }

    /**
     * Calculate base shipping discount amount
     * @param $amount
     */
    public function addBaseShippingDiscountAmount($amount)
    {
        $this->baseShippingDiscountAmount +=$amount;
    }

    /**
     * Calculate shipping discount amount
     * @param $amount
     */
    public function addShippingDiscountAmount($amount)
    {
        $this->shippingDiscountAmount += $amount;
    }

    /**
     * Returns shipping discount amount
     * @return int
     */
    public function getShippingDiscountAmount()
    {
        return $this->shippingDiscountAmount;
    }

    /**
     * Returns base shipping discount amount
     * @return int
     */
    public function getBaseShippingDiscountAmount()
    {
        return $this->baseShippingDiscountAmount;
    }

    /**
     * Returns items discount amount
     * @return int
     */
    public function getItemDiscountAmount()
    {
        return $this->itemDiscountAmount;
    }

    /**
     * Returns items base discount amount
     * @return int
     */
    public function getBaseItemDiscountAmount()
    {
        return $this->baseItemDiscountAmount;
    }
}
