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

namespace Magetrend\GiftCard\Model\ResourceModel\Product\Indexer\Price;

/**
 * Gift Card Product Price Indexer Resource model
 */
class GiftCard extends \Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\DefaultPrice
{
    /**
     * @param null|int|array $entityIds
     */
    protected function reindex($entityIds = null)
    {
        if ($this->hasEntity() || !empty($entityIds)) {
            $this->prepareFinalPriceDataForType($entityIds, $this->getTypeId());
            $this->_applyCustomOption();
            $this->applyGiftCardOption($entityIds);
            $this->_movePriceDataToIndexTable($entityIds);
        }

        return $this;
    }

    private function applyGiftCardOption($entityIds = null)
    {
        $temporaryOptionsTableName = 'catalog_product_index_price_giftcard_opt_temp';
        $this->getConnection()->createTemporaryTableLike(
            $temporaryOptionsTableName,
            $this->getTable('catalog_product_index_price_giftcard_opt_tmp'),
            true
        );

        $this->fillTemporaryOptionsTable($temporaryOptionsTableName, $entityIds);
        $this->updateTemporaryTable($temporaryOptionsTableName);
        $this->getConnection()->delete($temporaryOptionsTableName);

        return $this;
    }

    private function fillTemporaryOptionsTable(string $temporaryOptionsTableName, $entityIds = null)
    {
        $metadata = $this->metadataPool->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
        $linkField = $metadata->getLinkField();

        $select = $this->getConnection()->select()->from(
            ['i' => $this->_getDefaultFinalPriceTable()],
            []
        )->join(
            ['s' => $this->getTable('mt_giftcard_set_product')],
            's.product_id = i.entity_id',
            []
        )->columns(
            [
                'i.entity_id',
                'customer_group_id',
                'website_id',
                'MIN(s.price)',
                'MAX(s.price)',
                'i.tier_price',
            ]
        )->group(
            ['i.entity_id', 'customer_group_id', 'website_id']
        );

        if ($entityIds !== null) {
            $select->where('i.entity_id IN (?)', $entityIds);
        }

        $query = $select->insertFromSelect($temporaryOptionsTableName);
        $this->getConnection()->query($query);
    }

    private function updateTemporaryTable(string $temporaryOptionsTableName)
    {
        $finalPriceTable = $this->_getDefaultFinalPriceTable();
        $table = ['i' => $finalPriceTable];
        $selectForCrossUpdate = $this->getConnection()->select()->join(
            ['io' => $temporaryOptionsTableName],
            'i.entity_id = io.entity_id AND i.customer_group_id = io.customer_group_id AND ' .
            'i.website_id = io.website_id',
            []
        );
        $selectForCrossUpdate->columns(
            [
                'min_price' => 'io.min_price',
                'max_price' => 'io.max_price',
            ]
        );

        $query = $selectForCrossUpdate->crossUpdateFromSelect($table);
        $this->getConnection()->query($query);
    }
}
