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

namespace Magetrend\GiftCard\Setup;


use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magetrend\GiftCard\Api\Data\HistoryInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $this->upgrade203($setup, $context);
        }

        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            $this->upgrade205($setup, $context);
        }

        if (version_compare($context->getVersion(), '2.0.6', '<')) {
            $this->upgrade206($setup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param $context
     */
    public function upgrade203($setup, $context)
    {
        $db = $setup->getConnection();
        $db->modifyColumn(
            $setup->getTable('mt_giftcard'),
            'value',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'comment' => 'Gift Card Value'
            ]
        );

        $db->modifyColumn(
            $setup->getTable('mt_giftcard'),
            'balance',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'comment' => 'Gift Card Balance'
            ]
        );


        $db->modifyColumn(
            $setup->getTable('mt_giftcard_set_product'),
            'price',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'comment' => 'Gift Card Price'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param $context
     */
    public function upgrade205($installer, $context)
    {
        /**
         * Create table 'mt_giftcard_history'
         */
        $tableName = $installer->getTable('mt_giftcard_history');
        $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                HistoryInterface::GIFT_CARD_ID,
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Gift Card Id'
            )->addColumn(
                HistoryInterface::GIFT_CARD_STATUS,
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['unsigned' => true, 'nullable' => true],
                'Gift Card Status'
            )->addColumn(
                HistoryInterface::RELATED_ID,
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Related Object Id'
            )->addColumn(
                HistoryInterface::RELATED_OBJECT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['unsigned' => true, 'nullable' => true],
                'Related Object Type'
            )->addColumn(
                HistoryInterface::AMOUNT,
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Gift Card Amount'
            )->addColumn(
                HistoryInterface::BALANCE,
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Gift Card Balance'
            )->addColumn(
                HistoryInterface::CURRENCY,
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '5',
                ['nullable' => false],
                'Currency'
            )->addColumn(
                HistoryInterface::MESSAGE,
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Message'
            )->addColumn(
                HistoryInterface::MESSAGE_PARAMS,
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Json Message Params'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName($tableName, [HistoryInterface::GIFT_CARD_ID]),
                [HistoryInterface::GIFT_CARD_ID]
            )->addForeignKey(
                $installer->getFkName($tableName, HistoryInterface::GIFT_CARD_ID, 'mt_giftcard', 'entity_id'),
                HistoryInterface::GIFT_CARD_ID,
                $installer->getTable('mt_giftcard'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->setComment('Gift Card Trasactions History');

        $installer->getConnection()->createTable($table);
    }


    /**
     * @param SchemaSetupInterface $installer
     * @param $context
     */
    public function upgrade206($installer, $context)
    {
        /**
         * Create table 'mt_giftcard_history'
         */
        $tableName = $installer->getTable('catalog_product_index_price_giftcard_opt_tmp');
        $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false, 'default' => '0', 'primary' => true],
                'Customer Group ID'
            )->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false, 'default' => '0', 'primary' => true],
                'Website ID'
            )->addColumn(
                'min_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Min Price'
            )->addColumn(
                'max_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Max Price'
            )->addColumn(
                'tier_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Tier Price'
            )->setComment('Temp Gift Card Price Index Table');

        $installer->getConnection()->createTable($table);
    }
}
