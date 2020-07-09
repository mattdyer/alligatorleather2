<?php

namespace Magetrend\GiftCard\Model;

use Magetrend\GiftCard\Api\Data\HistoryInterface;
use Magetrend\GiftCard\Api\HistoryRepositoryInterface;

class HistoryRepository implements HistoryRepositoryInterface
{
    public $resource;

    public function __construct(
        \Magetrend\GiftCard\Model\ResourceModel\History $resource
    ) {
        $this->resource = $resource;
    }

    public function save(HistoryInterface $record)
    {
        $this->resource->save($record);
        return $record;
    }
}