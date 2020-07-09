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

namespace Magetrend\GiftCard\Observer\Sales\Order;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SubmitAfter implements ObserverInterface
{
    /**
     * @var \Magetrend\GiftCard\Model\GiftCardSetFactory
     */
    public $giftCardFactory;

    public $quote;

    /**
     * SubmitAfter constructor.
     * @param \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory
     * @param \Magetrend\GiftCard\Model\Quote $quote
     */
    public function __construct(
        \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magetrend\GiftCard\Model\Quote $quote
    ) {
        $this->giftCardFactory = $giftCardFactory;
        $this->quote = $quote;
    }

    /**
     * Observer controller
     * @param Observer $observer
     * @return $this
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $this->processItems($observer);
        $this->processDiscount($observer);
    }

    /**
     * Change quote_item_id to order_item_id and save order_id in gift card object
     * @param Observer $observer
     * @return $this
     * @throws \Exception
     */
    public function processItems(Observer $observer)
    {
        $order = $observer->getOrder();
        $items = $order->getAllItems();
        foreach ($items as $item) {
            if ($item->getProductType() == \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                $giftCard = $this->giftCardFactory->create()
                    ->load($item->getQuoteItemId(), 'quote_item_id');
                if ($giftCard->getId()) {
                    $giftCard->setOrderItemId($item->getId())
                        ->setOrderId($order->getId())
                        ->save();
                }
            }
        }
        return $this;
    }

    /**
     * Discount amount from gift card and change status
     *
     * @param $observer
     */
    protected function processDiscount($observer)
    {
        /**
         * @var \Magento\Sales\Model\Order $order
         */
        $order = $observer->getOrder();
        $this->quote->discountGiftCardBalance($order);
    }

}
