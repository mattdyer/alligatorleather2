<?php

namespace Magetrend\GiftCard\Api;

use Magetrend\GiftCard\Api\Data\GiftCardInterface;

interface GiftCardRepositoryInterface
{
    /**
     * Get gift card by code
     *
     * @param string $code
     * @return \Magetrend\GiftCard\Api\Data\GiftCardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode($code);

    /**
     * Save gift card.
     *
     * @param \Magetrend\GiftCard\Api\Data\GiftCardInterface $giftCard
     * @return \Magetrend\GiftCard\Api\Data\GiftCardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(GiftCardInterface $giftCard);

    /**
     * Update gift card.
     *
     * @param string $code
     * @param \Magetrend\GiftCard\Api\Data\GiftCardInterface $giftCard
     * @return \Magetrend\GiftCard\Api\Data\GiftCardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update($code, $giftCard);
}