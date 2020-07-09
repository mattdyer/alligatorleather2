<?php

namespace Magetrend\GiftCard\Api;

use Magento\Sales\Api\Data\OrderInterface;
use Magetrend\GiftCard\Api\Data\HistoryInterface;

interface HistoryManagementInterface
{
    /**
     * @param $giftCard
     * @param $amount
     * @param string $notes
     * @param null $relatedObject
     * @param [] $noteParams
     * @return HistoryInterface
     */
    public function record($giftCard, $amount, $note = '', $relatedObject = null, $noteParams = []);
}