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

class PlaceAfter implements ObserverInterface
{
    /**
     * @var \Magetrend\GiftCard\Model\GiftCardSetFactory
     */
    public $giftCardSetFactory;

    /**
     * @var \Magetrend\GiftCard\Model\GiftCard\Management
     */
    public $giftCardManagement;

    /**
     * PlaceAfter constructor.
     * @param \Magetrend\GiftCard\Model\GiftCardSetFactory $giftCardSetFactory
     * @param \Magetrend\GiftCard\Model\GiftCard\Management $management
     */
    public function __construct(
        \Magetrend\GiftCard\Model\GiftCardSetFactory $giftCardSetFactory,
        \Magetrend\GiftCard\Model\GiftCard\Management $management
    ) {
        $this->giftCardSetFactory = $giftCardSetFactory;
        $this->giftCardManagement = $management;
    }

    /**
     * After order place observer
     * @param Observer $observer
     * @return $this
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $this->processItems($observer);

        /**
         * Activate gift cards if invoice already exist.
         */
        $order = $observer->getOrder();
        if ($order->hasInvoices()) {
            $isEmailSent = $order->getEmailSent();
            $invoiceCollection = $order->getInvoiceCollection();
            foreach ($invoiceCollection as $invoice) {
                $items = $invoice->getAllItems();
                foreach ($items as $item) {
                    $orderItem = $item->getOrderItem();
                    if ($orderItem->getProductType() == \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                        $customerEmail = $invoice->getBillingAddress()->getEmail();
                        $this->giftCardManagement->activateGiftCard($orderItem, $customerEmail);
                    }
                }
            }
        }
    }

    /**
     * Actions related on gift card item
     * @param $observer
     * @return $this
     */
    public function processItems($observer)
    {
        $order = $observer->getOrder();
        $items = $order->getAllItems();
        foreach ($items as $item) {
            if ($item->getProductType() == \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                $this->prepareGiftCard($item);
            }
        }
        return $this;
    }

    /**
     * Prepare gift card item
     * @param $item
     * @return string
     * @throws \Exception
     */
    public function prepareGiftCard($item)
    {
        $productOptions = $item->getProductOptions();

        $giftCardOptions = $productOptions['info_buyRequest']['gift_card_attribute'];
        $giftCardSetId = $giftCardOptions['gc_set_id'];
        $isVirtual = true;
        if (isset($giftCardOptions['gc_send_by_post']) && $giftCardOptions['gc_send_by_post'] == 1) {
            $isVirtual = false;
        }

        $giftCardSet = $this->giftCardSetFactory->create()
            ->load($giftCardSetId);

        if (!$isVirtual || $item->getProduct()->getData('gc_use_code_generator') == 0) {
            if (!$giftCardSet->makeReservation($item->getData('qty_ordered'), $item->getData('quote_item_id'))) {
                throw new \Exception(__('There is not enough gifts cards'));
            }
        } else {
            $giftCardSet->createGiftCard($item->getData('quote_item_id'), $item->getData('qty_ordered'));
        }
    }
}
