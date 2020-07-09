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

class Attribute
{
    private $eavAttributeList = [
        'gc_type',
        'gc_use_code_generator',
        'gc_send_to_friend',
        'gc_send_by_post',
        'gc_field_sender_name',
        'gc_field_recipient_name',
        'gc_field_recipient_email',
        'gc_field_message'
    ];

    private $collection = null;

    public $eavCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
    ) {
        $this->eavCollectionFactory = $collectionFactory;
    }

    public function getCollection()
    {
        if ($this->collection == null) {
            $this->collection = $this->eavCollectionFactory->create()
                ->addFieldToFilter('attribute_code', ['in' => $this->eavAttributeList]);
        }
        return $this->collection;
    }
}
