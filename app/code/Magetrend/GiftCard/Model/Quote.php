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

namespace Magetrend\GiftCard\Model;

use Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModel;
use Magento\Checkout\Model\Session;
use Magetrend\GiftCard\Api\HistoryManagementInterface;

class Quote extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var GiftCardFactory
     */
    public $giftCardFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManagerInterface;

    /**
     * @var ResourceModel\Quote\CollectionFactory
     */
    public $giftCardQuoteCollectionFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    public $session;

    /**
     * @var QuoteFactory
     */
    public $quoteFactory;

    /**
     * @var OrderFactory
     */
    public $orderFactory;

    /**
     * @var ResourceModel\GiftCard\CollectionFactory
     */
    public $giftCardCollectionFactory;

    /**
     * @var \Magetrend\GiftCard\Model\ResourceModel\GiftCard\Collection | null
     */
    private $giftCardCollection = null;

    public $historyManagement;

    /**
     * Quote constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param GiftCardFactory $giftCardFactory
     * @param \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magetrend\GiftCard\Model\ResourceModel\Quote\CollectionFactory $giftCardQuoteCollectionFactory
     * @param \Magento\Checkout\Model\Session $session
     * @param QuoteFactory $quoteFactory
     * @param OrderFactory $orderFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magetrend\GiftCard\Model\ResourceModel\Quote\CollectionFactory $giftCardQuoteCollectionFactory,
        Session $session,
        \Magetrend\GiftCard\Model\QuoteFactory $quoteFactory,
        \Magetrend\GiftCard\Model\OrderFactory $orderFactory,
        \Magetrend\GiftCard\Api\HistoryManagementInterface $historyManagement,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->giftCardFactory= $giftCardFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->giftCardQuoteCollectionFactory = $giftCardQuoteCollectionFactory;
        $this->session = $session;
        $this->quoteFactory = $quoteFactory;
        $this->orderFactory = $orderFactory;
        $this->giftCardCollectionFactory = $giftCardCollectionFactory;
        $this->historyManagement = $historyManagement;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\GiftCard\Model\ResourceModel\Quote');
    }

    /**
     * Returns applied gift cards for quote collection
     * @param null $quoteId
     * @return mixed
     */
    public function getCollection($quoteId = null)
    {
        if ($quoteId == null) {
            $quoteId = $this->session->getQuote()->getId();
        }
        $collection = $this->giftCardQuoteCollectionFactory->create()
            ->addFieldToFilter('quote_id', $quoteId);
        return $collection;
    }

    /**
     * Returns applied gift cards for quote collection
     * @param null $quoteId
     * @return ResourceModel/GiftCard/Collection|null
     */
    public function getGiftCardCollection($quoteId = null)
    {
        if ($this->giftCardCollection == null) {
            $giftCardIds = $this->getCollection($quoteId)
                ->addFieldToSelect('gift_card_id')
                ->getData();

            $this->giftCardCollection = $this->giftCardCollectionFactory->create()
                ->addFieldToFilter('entity_id', ['in' => $giftCardIds]);
        }
        return $this->giftCardCollection;
    }

    /**
     * Validate added gift card code
     * @param $giftCardCode
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateGiftCardCode($giftCardCode, $quoteId = null)
    {
        if (empty($giftCardCode)
            || strlen($giftCardCode) < \Magetrend\GiftCard\Model\GiftCard::MIN_GIFT_CARD_CODE_LENGTH) {
            return false;
        }

        /**
         * Check gift card code
         */
        $giftCard = $this->giftCardFactory->create()->load($giftCardCode, 'code');
        if (!$giftCard || !$giftCard->getId()) {
            return false;
        }

        /**
         * Check if is not expired
         */

        $expireDate = $giftCard->getExpireDate();
        if (!empty($expireDate)) {
            $expireDate = str_replace('00:00:00', '23:59:59', $expireDate);
            if (strtotime($expireDate) <= time()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Gift card is expired.'));
            }
        }

        /**
         * Check gift card balance
         */
        if ($giftCard->getBalance() == 0) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Not enough balance in gift card.'));
        }

        /**
         * Check gift card status
         */
        if ($giftCard->getStatus() != \Magetrend\GiftCard\Model\GiftCard::STATUS_ACTIVE) {
            return false;
        }

        /**
         * Check is available on store
         */
        $storeId = $this->storeManagerInterface->getStore()->getId();
        if (!$giftCard->isAvailableOnStore($storeId)) {
            return false;
        }

        /**
         * Check is it already added
         */
        $giftCardCollection = $this->getCollection($quoteId)
            ->addFieldToFilter('gift_card_id', $giftCard->getId());

        if ($giftCardCollection->getSize() > 0) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Gift card is already applied for cart.'));
        }

        return true;
    }

    /**
     * Add gift card code to quote
     * @param $giftCardCode
     * @param $quoteId
     * @return bool
     */
    public function addGiftCardToQuote($giftCardCode, $quoteId = null)
    {
        if ($quoteId == null) {
            $quoteId = $this->session->getQuote()->getId();
        }
        $giftCard = $this->giftCardFactory->create()->load($giftCardCode, 'code');
        if (!$giftCard || !$giftCard->getId()) {
            return false;
        }
        $this->quoteFactory->create()
            ->setData([
                'quote_id' => $quoteId,
                'gift_card_id' => $giftCard->getId(),
                'base_discount_amount' => $giftCard->getData('balance'),
                'discount_amount' => $giftCard->getBalance(),
                'base_refund_amount' => 0,
                'refund_amount' => 0,
            ])
            ->save();
        return true;
    }

    /**
     * Remove gift card from quote
     * @param $giftCardId
     * @param null $quoteId
     * @return bool
     */
    public function removeGiftCardFromQuote($giftCardId, $quoteId = null)
    {
        if (!is_numeric($giftCardId)) {
            return false;
        }

        $this->getCollection($quoteId)
            ->addFieldToFilter('gift_card_id', $giftCardId)
            ->walk('delete');

        return true;
    }

    /**
     * Remove all gift card codes from quote
     * @param null $quoteId
     */
    public function removeAllGiftCardFromQuote($quoteId = null)
    {
        $this->getCollection($quoteId)
            ->walk('delete');
    }

    /**
     * Returns total balance of applied gift cards
     * @param $currencyCode
     * @return int
     */
    public function getTotalBalance($currencyCode)
    {
        $giftCardCollection = $this->getGiftCardCollection();
        if ($giftCardCollection->getSize() == 0) {
            return 0;
        }
        $balance = 0;
        foreach ($giftCardCollection as $giftCard) {
            $balance += $giftCard->getBalance($currencyCode);
        }
        return $balance;
    }

    /**
     * Is added some gift card items in cart
     */
    public function isGiftCardProductItemInCart()
    {
        $items = $this->session->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            if ($item->getProductType() == \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                return true;
            };
        }
        return false;
    }

    /**
     * @param $order \Magento\Sales\Model\Order
     * @return bool
     */
    public function discountGiftCardBalance($order)
    {
        $orderId = $order->getId();
        $quoteId = $order->getQuoteId();
        $amount = $order->getGiftcardAmount();
        $currency = $order->getOrderCurrencyCode();

        if ($amount == 0) {
            return false;
        }

        $giftCardCollection = $this->getGiftCardCollection($quoteId);
        if ($giftCardCollection->getSize() == 0) {
            return false;
        }
        $amount = $amount*-1;

        /**
         * @var $giftCard \Magetrend\GiftCard\Model\GiftCard
         */
        foreach ($giftCardCollection as $giftCard) {
            $balance = $giftCard->getBalance($currency);
            $baseBalance = $giftCard->getBalance();
            $discountAmount = min($amount, $balance);

            $giftCard->setBalance(max(0, $balance-$discountAmount), $currency);
            if ($giftCard->getBalance() == 0) {
                $giftCard->setStatus(\Magetrend\GiftCard\Model\GiftCard::STATUS_INACTIVE);
            }
            $this->orderFactory->create()
                ->setData([
                    'order_id' => $orderId,
                    'gift_card_id' => $giftCard->getId(),
                    'discount_amount' => $discountAmount,
                    'base_discount_amount' => $baseBalance - $giftCard->getBalance(),
                ])->save();

            $this->historyManagement->record(
                $giftCard,
                ($giftCard->getBalance() - $baseBalance),
                'Paid for order #%1',
                $order,
                [$order->getIncrementId()]
            );

            $amount -= $discountAmount;
            if ($amount == 0) {
                break;
            }
        }

        $giftCardCollection->walk('save');
        return true;
    }
}
