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
namespace Magetrend\GiftCard\Block\Adminhtml\Sales\Order;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * Get totals source object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Create the gift cards totals summary
     *
     * @return $this
     */
    public function initTotals()
    {
        $giftCardDiscountAmount = $this->getSource()->getGiftcardAmount();
        $baseGiftCardDiscountAmount = $this->getSource()->getBaseGiftcardAmount();
        if ($giftCardDiscountAmount == 0) {
            return $this;
        }

        $parentBlockName = $this->getParentBlock()->getNameInLayout();
        if ($parentBlockName == 'creditmemo_totals') {
            $label = __('Refunded to Gift Card');
            if ($giftCardDiscountAmount < 0) {
                $giftCardDiscountAmount *=-1;
                $baseGiftCardDiscountAmount *=-1;
            }
        } else {
            $label = __('Gift Card Discount');
            if ($giftCardDiscountAmount > 0) {
                $giftCardDiscountAmount *=-1;
                $baseGiftCardDiscountAmount *=-1;
            }
        }

        //@codingStandardsIgnoreLine
        $total = new \Magento\Framework\DataObject(
            [
                'code' => $this->getNameInLayout(),
                'label' => $label,
                'value' => $giftCardDiscountAmount,
                'base_value' => $baseGiftCardDiscountAmount
            ]
        );

        if ($this->getBeforeCondition()) {
            $this->getParentBlock()->addTotalBefore($total, $this->getBeforeCondition());
        } else {
            $this->getParentBlock()->addTotal($total, $this->getAfterCondition());
        }

        if ($parentBlockName == 'order_totals' && $this->getSource()->getGiftcardRefunded() != 0) {
            //@codingStandardsIgnoreLine
            $totalRefunded = new \Magento\Framework\DataObject(
                [
                    'code' => $this->getNameInLayout().'_refunded',
                    'label' => __('Refunded to Gift Card'),
                    'value' => $this->getSource()->getGiftcardRefunded(),
                    'base_value' => $this->getSource()->getBaseGiftcardRefunded()
                ]
            );

            if ($this->getBeforeCondition()) {
                $this->getParentBlock()->addTotalBefore($totalRefunded, $this->getBeforeCondition());
            } else {
                $this->getParentBlock()->addTotal($totalRefunded, $this->getAfterCondition());
            }
        }
        return $this;
    }
}
