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

namespace Magetrend\GiftCard\Model\GiftCard;

/**
 * Actions related on gift cards manegement
 *
 * @package Magetrend\GiftCard\Model\GiftCard
 */
class Management
{
    /**
     * @var \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory
     */
    public $giftCardCollectionFactory;

    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $gcHelper;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    public $orderRepository;

    /**
     * Management constructor.
     * @param \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory
     * @param \Magetrend\GiftCard\Helper\Data $gcHelper
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory,
        \Magetrend\GiftCard\Helper\Data $gcHelper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->giftCardCollectionFactory = $giftCardCollectionFactory;
        $this->gcHelper = $gcHelper;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Activate ordered gift cards
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @param String $customerEmail
     * @return bool
     */
    public function activateGiftCard($item, $customerEmail)
    {
        $orderItemId = $item->getId();
        $buyRequest = $item->getBuyRequest();
        $giftCardOptions = $buyRequest->getData('gift_card_attribute');
        $giftCardType = $item->getProduct()->getGcType();
        $storeId = $item->getStoreId();

        if (!isset($giftCardOptions['gc_send_by_post'])) {
            $giftCardOptions['gc_send_by_post'] = 0;
        }

        $sendByPost = false;
        if ($giftCardType == \Magetrend\GiftCard\Model\GiftCard::TYPE_REAL
            || ($giftCardType == \Magetrend\GiftCard\Model\GiftCard::TYPE_VIRTUAL_REAL
                && $giftCardOptions['gc_send_by_post'] == 1)
        ) {
            $sendByPost = true;
        }

        $giftCardCollection = $this->giftCardCollectionFactory->create()
            ->addFieldToFilter('quote_item_id', $item->getQuoteItemId())
            ->addFieldToFilter('status', \Magetrend\GiftCard\Model\GiftCard::STATUS_WAITING_FOR_PAYMENT);

        if ($giftCardCollection->getSize() == 0) {
            return false;
        }

        foreach ($giftCardCollection as $giftCard) {
            $giftCard->setStatus(\Magetrend\GiftCard\Model\GiftCard::STATUS_ACTIVE)
                ->setExpireDate($giftCard->getValidTo());
            /**
             * Send virtual gift card to friend
             */
            $emailData  = array_merge($giftCardOptions, [
                'send_by_post' => $sendByPost,
                'order' => $item->getOrder(),
                'gift_card' => $giftCard
            ]);

            if (isset($giftCardOptions['gc_send_to_friend'])
                && $giftCardOptions['gc_send_to_friend'] == 1
                && $giftCardOptions['gc_send_by_post'] != 1) {
                $giftCard->sendGiftCard(
                    $this->gcHelper->getSendToFriendTemplateId($storeId),
                    $giftCardOptions['gc_field_recipient_email'],
                    $this->gcHelper->getGiftCardSenderName($storeId),
                    $this->gcHelper->getGiftCardSenderEmail($storeId),
                    $storeId,
                    $emailData
                );
            }

            /**
             * Send gift card to customer
             */

            if (!isset($giftCardOptions['gc_send_to_friend']) || $giftCardOptions['gc_send_to_friend'] == 0) {
                $giftCard->sendGiftCard(
                    $this->gcHelper->getSendToCustomerTemplateId($storeId),
                    $customerEmail,
                    $this->gcHelper->getGiftCardSenderName($storeId),
                    $this->gcHelper->getGiftCardSenderEmail($storeId),
                    $storeId,
                    $emailData
                );
            }
        }

        $giftCardCollection->walk('save');
        return true;
    }

    public function sendGiftCardEmailByOrder($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() != \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                continue;
            }

            $this->sendGiftCardEmailByOrderItem($item);
        }
    }

    public function sendGiftCardEmailByOrderItem($item)
    {
        $orderItemId = $item->getId();
        $buyRequest = $item->getBuyRequest();
        $giftCardOptions = $buyRequest->getData('gift_card_attribute');
        $giftCardType = $item->getProduct()->getGcType();
        $storeId = $item->getStoreId();
        $order = $item->getOrder();
        $customerEmail = $order->getCustomerEmail();

        if (!isset($giftCardOptions['gc_send_by_post'])) {
            $giftCardOptions['gc_send_by_post'] = 0;
        }

        $sendByPost = false;
        if ($giftCardType == \Magetrend\GiftCard\Model\GiftCard::TYPE_REAL
            || ($giftCardType == \Magetrend\GiftCard\Model\GiftCard::TYPE_VIRTUAL_REAL
                && $giftCardOptions['gc_send_by_post'] == 1)
        ) {
            $sendByPost = true;
        }

        $giftCardCollection = $this->giftCardCollectionFactory->create()
            ->addFieldToFilter('quote_item_id', $item->getQuoteItemId());

        if ($giftCardCollection->getSize() == 0) {
            return false;
        }

        foreach ($giftCardCollection as $giftCard) {
            /**
             * Send virtual gift card to friend
             */
            $emailData = array_merge($giftCardOptions, [
                'send_by_post' => $sendByPost,
                'order' => $order,
                'gift_card' => $giftCard
            ]);

            $giftCard->sendGiftCard(
                $this->gcHelper->getSendToCustomerTemplateId($storeId),
                $customerEmail,
                $this->gcHelper->getGiftCardSenderName($storeId),
                $this->gcHelper->getGiftCardSenderEmail($storeId),
                $storeId,
                $emailData
            );
        }
    }
}
