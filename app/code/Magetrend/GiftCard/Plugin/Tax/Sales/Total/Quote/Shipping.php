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

class Shipping
{
    public function aroundCollect(
        $subject,
        $parent,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $results = $parent($quote, $shippingAssignment, $total);
        $quote->getShippingAddress()
            ->setShippingTaxAmount($total->getShippingTaxAmount());

        $quote->getShippingAddress()
            ->setBaseShippingTaxAmount($total->getBaseShippingTaxAmount());
        return $results;
    }
}
