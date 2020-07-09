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

use Magetrend\GiftCard\Api\HistoryManagementInterface;

class Order extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var ResourceModel\Order\CollectionFactory
     */
    public $giftCardOrderCollectionFactory;

    /**
     * @var ResourceModel\GiftCard\CollectionFactory
     */
    public $giftCardCollectionFactory;

    /**
     * @var ResourceModel\GiftCard\Collection | null
     */
    private $giftCardCollection = null;

    public $historyManagement;

    /**
     * Order constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Order\CollectionFactory $collectionFactory
     * @param ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\GiftCard\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory,
        \Magetrend\GiftCard\Api\HistoryManagementInterface $historyManagement,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->giftCardOrderCollectionFactory = $collectionFactory;
        $this->giftCardCollectionFactory = $giftCardCollectionFactory;
        $this->historyManagement = $historyManagement;
        return parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\GiftCard\Model\ResourceModel\Order');
    }

    /**
     * @param $creditMemo \Magento\Sales\Model\Order\Creditmemo
     * @return bool
     */
    public function returnCreditToGiftCard($creditMemo)
    {
        $amount = $creditMemo->getGiftcardAmount();
        if ($amount == 0) {
            return false;
        }
        $orderId = $creditMemo->getOrderId();
        $giftCardCollection = $this->getGiftCardCollection($orderId);

        if ($giftCardCollection->getSize() == 0) {
            return false;
        }

        $currency = $creditMemo->getOrderCurrencyCode();
        foreach ($giftCardCollection as $giftCard) {
            $baseBalance = $giftCard->getBalance();
            $value = $giftCard->getValue($currency);
            $balance = $giftCard->getBalance($currency);
            $availableAmount = $value - $balance;
            $refundAmount = min($availableAmount, $amount);

            $giftCard->setBalance($balance+$refundAmount, $currency)
                ->setStatus(\Magetrend\GiftCard\Model\GiftCard::STATUS_ACTIVE);

            $this->historyManagement->record(
                $giftCard,
                ($giftCard->getBalance() - $baseBalance),
                'Refund. Order #%1',
                $creditMemo->getOrder(),
                [$creditMemo->getOrder()->getIncrementId()]
            );

            $amount = $amount - $refundAmount;
            if ($amount == 0) {
                break;
            }
        }
        $giftCardCollection->walk('save');
    }

    /**
     * Returns used gift cards in order
     * @param $orderId
     * @return mixed
     */
    public function getCollectionByOrderId($orderId)
    {
        $collection = $this->giftCardOrderCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderId);
        return $collection;
    }

    /**
     * Returns gift card collection used in order
     * @param null $orderId
     * @return null
     */
    public function getGiftCardCollection($orderId = null)
    {
        if ($this->giftCardCollection == null) {
            $giftCardIds = $this->getCollectionByOrderId($orderId)
                ->addFieldToSelect('gift_card_id')
                ->getData();

            $this->giftCardCollection = $this->giftCardCollectionFactory->create()
                ->addFieldToFilter('entity_id', ['in' => $giftCardIds]);
        }
        return $this->giftCardCollection;
    }
}
