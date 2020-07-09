<?php

namespace Magetrend\GiftCard\Api;

use Magetrend\GiftCard\Api\Data\HistoryInterface;

interface HistoryRepositoryInterface
{
    /**
     * Create or update a data
     */
    public function save(HistoryInterface $record);
}