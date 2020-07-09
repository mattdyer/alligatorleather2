<?php

namespace Magetrend\GiftCard\Model;

use Magetrend\GiftCard\Api\Data\GiftCardInterface;
use Magetrend\GiftCard\Api\GiftCardRepositoryInterface;

class GiftCardRepository implements GiftCardRepositoryInterface
{
    public $giftCardFactory;

    public $giftCardResource;

    private $codeRegistry = [];

    private $idRegistry = [];

    public function __construct(
        \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCard $giftCardResource
    ) {
        $this->giftCardResource = $giftCardResource;
        $this->giftCardFactory = $giftCardFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getByCode($code)
    {
        if (isset($this->codeRegistry[$code])) {
            return $this->codeRegistry[$code];
        }

        /**
         * @var GiftCardInterface $giftCard
         */
        $giftCard = $this->giftCardFactory->create()
            ->load($code, 'code');

        if (!$giftCard->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Gift card does not exist: "%1" ', $code)
            );
        }

        $this->codeRegistry[$code] = $giftCard;
        $this->idRegistry[$giftCard->getId()] = $giftCard;

        return $giftCard;
    }

    /**
     * {@inheritdoc}
     */
    public function save(GiftCardInterface $giftCard)
    {
        $this->giftCardResource->save($giftCard);
        return $giftCard;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, $giftCard)
    {
        $giftCardModel = $this->getByCode($code);
        $giftCardModel->addData($giftCard->getData());
        $this->save($giftCardModel);
        return $giftCardModel;
    }
}