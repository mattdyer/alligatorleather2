<?php

namespace Magetrend\GiftCard\Model;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magetrend\GiftCard\Api\Data\HistoryInterface;
use Magetrend\GiftCard\Api\HistoryManagementInterface;
use Magetrend\GiftCard\Api\HistoryRepositoryInterface;

class HistoryManagement implements HistoryManagementInterface
{
    public $historyFactory;

    public $historyRepository;

    public $giftCardCollection;

    public $historyCollectionFactory;

    public function __construct(
        \Magetrend\GiftCard\Api\Data\HistoryInterfaceFactory $historyFactory,
        \Magetrend\GiftCard\Api\HistoryRepositoryInterface $historyRepository,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollection,
        \Magetrend\GiftCard\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
    ) {
        $this->historyFactory = $historyFactory;
        $this->historyRepository = $historyRepository;
        $this->giftCardCollection = $giftCardCollection;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    public function record($giftCard, $amount, $note = '', $relatedObject = null, $noteParams = [])
    {
        /**
         * @var HistoryInterface $record
         */
        $record = $this->historyFactory->create();
        $record
            ->setGiftCardId($giftCard->getId())
            ->setBalance($giftCard->getBalance())
            ->setCurrency($giftCard->getCurrency())
            ->setGiftCardStatus($giftCard->getStatus())
            ->setAmount($amount)
            ->setMessage($note)
            ->setMessageParams(json_encode($noteParams));

        if ($relatedObject != null) {
            $record->setRelatedObject($this->getRelatedObjectType($relatedObject))
                ->setRelatedId($relatedObject->getId());
        }

        $this->historyRepository->save($record);

        return $record;
    }

    /**
     * Returns related object type
     * @param $relatedObject
     * @return string
     */
    public function getRelatedObjectType($relatedObject)
    {
        if ($relatedObject instanceof OrderInterface) {
            return HistoryInterface::RELATED_OBJECT_ORDER;
        } elseif ($relatedObject instanceof InvoiceInterface) {
            return HistoryInterface::RELATED_OBJECT_INVOICE;
        } elseif ($relatedObject instanceof CreditmemoInterface) {
            return HistoryInterface::RELATED_OBJECT_CREDITMEMO;
        }

        return '';
    }

    public function deleteRecords($giftCardId)
    {
        $collection = $this->historyCollectionFactory->create()
            ->addFieldToFilter('gift_card_id', $giftCardId);

        if ($collection->getSize() == 0) {
            return;
        }
        $collection->walk('delete');
    }
}