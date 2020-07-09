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

namespace  Magetrend\GiftCard\Plugin\Tax\Sales\Total\Quote;

use Magento\Customer\Api\Data\AddressInterfaceFactory as CustomerAddressFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory as CustomerAddressRegionFactory;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Tax\Api\Data\QuoteDetailsInterfaceFactory;
use Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;

class CommonTaxCollector
{
    public function __construct(
        \Magetrend\GiftCard\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Add gift card shipping discount amount for tax calculation object
     * @param $subject
     * @param $parent
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param QuoteAddress\Total $total
     * @param $useBaseCurrency
     * @return mixed
     */
    public function aroundGetShippingDataObject(
        $subject,
        $parent,
        ShippingAssignmentInterface $shippingAssignment,
        QuoteAddress\Total $total,
        $useBaseCurrency
    ) {
        $itemDetails = $parent($shippingAssignment, $total, $useBaseCurrency);
        if ($itemDetails == null) {
            return $itemDetails;
        }

        if ($total->getShippingGiftcardAmount() < 0) {
            $storeId = $shippingAssignment->getShipping()->getAddress()->getQuote()->getStore()->getId();
            if ($this->helper->getTaxCalculationType($storeId) == \Magetrend\GiftCard\Model\Config\Source\Tax::BEFORE) {
                if ($useBaseCurrency) {
                    $discountAmount = $total->getShippingGiftcardAmount();
                } else {
                    $discountAmount = $total->getBaseShippingGiftcardAmount();
                }
                $itemDetails->setDiscountAmount($itemDetails->getDiscountAmount() - $discountAmount);
            }
        }
        return $itemDetails;
    }

    /**
     * Add gift card discount amount for tax calculation item object
     * @param $subject
     * @param $parent
     * @param \Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory $itemDataObjectFactory
     * @param AbstractItem $item
     * @param $priceIncludesTax
     * @param $useBaseCurrency
     * @param null $parentCode
     * @return mixed
     */
    public function aroundMapItem(
        $subject,
        $parent,
        \Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory $itemDataObjectFactory,
        AbstractItem $item,
        $priceIncludesTax,
        $useBaseCurrency,
        $parentCode = null
    ) {
        $itemDetails = $parent($itemDataObjectFactory, $item, $priceIncludesTax, $useBaseCurrency, $parentCode);
        if ($item->getGiftcardAmount() < 0) {
            $storeId = $item->getStoreId();
            if ($this->helper->getTaxCalculationType($storeId) == \Magetrend\GiftCard\Model\Config\Source\Tax::BEFORE) {
                if ($useBaseCurrency) {
                    $discountAmount = $item->getBaseGiftcardAmount();
                } else {
                    $discountAmount = $item->getGiftcardAmount();
                }
                $itemDetails->setDiscountAmount($itemDetails->getDiscountAmount() - $discountAmount);
            }
        }
        return $itemDetails;
    }
}
