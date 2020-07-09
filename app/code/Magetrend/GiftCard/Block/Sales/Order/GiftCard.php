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

namespace Magetrend\GiftCard\Block\Sales\Order;

use Magento\Sales\Model\Order;

class GiftCard extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Order
     */
    public $order;

    /**
     * @var \Magento\Framework\DataObject
     */
    public $source;

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get order object
     *
     * @return \Magento\Framework\DataObject
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Initialize all order totals relates with gift card
     *
     * @return \Magetrend\GiftCard\Block\Sales\Order\GiftCard
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->order = $parent->getOrder();
        $this->source = $parent->getSource();

        if ($this->order->getGiftcardAmount() != 0) {
            $this->addGiftCard();
        }
        return $this;
    }

    /**
     * Add tax total string
     *
     * @param string $after
     * @return \Magetrend\GiftCard\Block\Sales\Order\GiftCard
     */
    public function addGiftCard($after = 'tax')
    {
        //@codingStandardsIgnoreLine
        $taxTotal = new \Magento\Framework\DataObject(['code' => 'giftcard', 'block_name' => $this->getNameInLayout()]);
        $this->getParentBlock()->addTotal($taxTotal, $after);
        return $this;
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
