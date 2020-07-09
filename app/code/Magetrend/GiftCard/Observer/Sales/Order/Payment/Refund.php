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

namespace Magetrend\GiftCard\Observer\Sales\Order\Payment;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class Refund implements ObserverInterface
{
    public $giftCardFactory;

    public $order;

    public function __construct(
        \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magetrend\GiftCard\Model\Order $order
    ) {
        $this->order = $order;
        $this->giftCardFactory = $giftCardFactory;
    }

    /**
     * Actions after refund
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $creditMemo = $observer->getCreditmemo();
        $this->returnCreditToGiftCard($creditMemo);
        $this->disableGiftCards($creditMemo);
        $this->setGiftCardAmounts($creditMemo);

        return $this;
    }

    /**
     * Set refunded amount in order object
     * @param \Magento\Sales\Model\Order\Creditmemo $creditMemo
     */
    protected function setGiftCardAmounts($creditMemo)
    {
        $order = $creditMemo->getOrder();
        if ($order->getGiftcardAmount() == 0) {
            return;
        }

        $order->setGiftcardRefunded($creditMemo->getGiftcardAmount());
        $order->setBaseGiftcardRefunded($creditMemo->getBaseGiftcardAmount());
    }

    /**
     * Set gift card status inactive if there is gift card items
     * @param \Magento\Sales\Model\Order\Creditmemo $creditMemo
     */
    protected function disableGiftCards($creditMemo)
    {
        $items = $creditMemo->getAllItems();
        if (count($items) == 0) {
            return;
        }

        foreach ($items as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->getProductType() != \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                continue;
            }

            $giftCard = $this->giftCardFactory->create()
                ->load($orderItem->getId(), 'order_item_id');
            if (!$giftCard->getId()) {
                continue;
            }
            $giftCard->setStatus(\Magetrend\GiftCard\Model\GiftCard::STATUS_INACTIVE)
                ->save();
        }
    }

    public function returnCreditToGiftCard($creditMemo)
    {
        $this->order->returnCreditToGiftCard($creditMemo);
    }
}
