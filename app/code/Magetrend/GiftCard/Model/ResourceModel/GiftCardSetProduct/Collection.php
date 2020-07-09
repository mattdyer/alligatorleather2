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

namespace Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init(
            'Magetrend\GiftCard\Model\GiftCardSetProduct',
            'Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct'
        );
    }

    /**
     * Join gift card sets fields
     * @param array $columns
     * @return $this
     */
    public function joinSets($columns = ['*'])
    {
        $this->getSelect()->join(
            ['gift_card_set_table' => $this->getTable('mt_giftcard_set')],
            'gift_card_set_table.entity_id = main_table.gift_card_set_id',
            $columns
        );
        return $this;
    }

    /**
     * Add store filter
     * @param $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->addFieldToFilter('store_id', $storeId);
        return $this;
    }

    /**
     * Sort collection by position
     * @param string $sortDirection
     * @return $this
     */
    public function sortByPosition($sortDirection = 'ASC')
    {
        $this->setOrder('position', $sortDirection);
        return $this;
    }

    /**
     * Product filter
     * @param $productId
     * @return $this
     */
    public function addProductFilter($productId)
    {
        $this->addFieldToFilter('product_id', $productId);
        return $this;
    }
}
