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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @codingStandardsIgnoreFile
 */

class InstallSchema implements InstallSchemaInterface
{
    private $__templateColumns =  [
        'entity_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1,
        ],
        'store_id' => [
            'type' => Table::TYPE_SMALLINT,
            'length' => null,
            'index' => true,
            'options' => ['unsigned' => true, 'nullable' => true],
        ],
        'is_deleted' => [
            'type' => Table::TYPE_SMALLINT,
            'length'=> 1,
            'options' => ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        ],
        'name' => ['type' => Table::TYPE_TEXT,'length'=> 255],
        'design' => ['type' => Table::TYPE_TEXT, 'length'=> 20] ,
        'text_1' => ['type' => Table::TYPE_TEXT],
        'text_2' => ['type' => Table::TYPE_TEXT],
        'text_3' => ['type' => Table::TYPE_TEXT],
        'text_4' => ['type' => Table::TYPE_TEXT],
        'text_5' => ['type' => Table::TYPE_TEXT],
        'text_6' => ['type' => Table::TYPE_TEXT],
        'text_7' => ['type' => Table::TYPE_TEXT],
        'text_8' => ['type' => Table::TYPE_TEXT],
        'text_9' => ['type' => Table::TYPE_TEXT],
        'color_1' => ['type' => Table::TYPE_TEXT, 'length'=> 7],
        'color_2' => ['type' => Table::TYPE_TEXT, 'length'=> 7],
        'color_3' => ['type' => Table::TYPE_TEXT, 'length'=> 7],
        'color_4' => ['type' => Table::TYPE_TEXT, 'length'=> 7],
        'color_5' => ['type' => Table::TYPE_TEXT, 'length'=> 7],
        'color_6' => ['type' => Table::TYPE_TEXT, 'length'=> 7],
        'color_7' => ['type' => Table::TYPE_TEXT, 'length'=> 7],
        'color_8' => ['type' => Table::TYPE_TEXT, 'length'=> 7],
        'color_9' => ['type' => Table::TYPE_TEXT, 'length'=> 7],
        'color_10' => ['type' => Table::TYPE_TEXT, 'length'=> 7],
        'image_1' => ['type' => Table::TYPE_TEXT, 'length'=> 255],
        'image_2' => ['type' => Table::TYPE_TEXT, 'length'=> 255],
        'size_1' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
        'size_2' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
        'size_3' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
        'size_4' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
        'size_5' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
        'size_6' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
        'size_7' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
        'size_8' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
        'size_9' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
        'size_10' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],

    ];

    private $__giftCardSetColumns =  [
        'entity_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],
        'store_ids' => [
            'type' => Table::TYPE_TEXT,
            'options' => ['nullable' => true],
        ],
        'name' => ['type' => Table::TYPE_TEXT,'length'=> 255],
        'template_id' => [
            'type' => Table::TYPE_INTEGER,
            'length' => 10,
            'index' => true,
            'options' => ['unsigned' => true, 'nullable' => false],

        ] ,
        'currency' => ['type' => Table::TYPE_TEXT, 'length'=> 4],
        'value' => ['type' => Table::TYPE_DECIMAL, 'length'=> '12,4'],
        'life_time' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
        'code_length' => ['type' => Table::TYPE_INTEGER, 'length'=> 3],
        'code_format'  => ['type' => Table::TYPE_TEXT, 'length'=> 50],
        'code_prefix' => ['type' => Table::TYPE_TEXT, 'length'=> 50],
        'code_suffix' => ['type' => Table::TYPE_TEXT, 'length'=> 50],
        'code_dash'=> ['type' => Table::TYPE_SMALLINT, 'length'=> 2],
    ];

    private $__giftCardColumns =  [
        'entity_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],
        'status' => ['type' => Table::TYPE_TEXT, 'length'=> 50],
        'store_ids' => [
            'type' => Table::TYPE_TEXT,
            'options' => ['nullable' => true],
        ],
        'code' => ['type' => Table::TYPE_TEXT, 'length'=> 50],
        'gift_card_set_id' => [
            'type' => Table::TYPE_INTEGER,
            'length' => 10,
            'options' => ['unsigned' => true, 'nullable' => true],
        ],
        'template_id' => [
            'type' => Table::TYPE_INTEGER,
            'length' => 10,
            'options' => ['unsigned' => true, 'nullable' => true]
        ],
        'currency' => ['type' => Table::TYPE_TEXT, 'length'=> 4],
        'value' => ['type' => Table::TYPE_DECIMAL, 'length'=> '12,4'],
        'balance' => ['type' => Table::TYPE_DECIMAL, 'length'=> '12,4'],
        'life_time' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
        'expire_date' => [
            'type' => Table::TYPE_DATETIME,
            'options' => ['nullable' => true],
        ],
        'created_at' => [
            'type' => Table::TYPE_TIMESTAMP,
            'options' => ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
        ],
        'order_id' => [
            'type' => Table::TYPE_INTEGER,
            'length' => 10,
            'index' => true,
            'options' => ['unsigned' => true, 'nullable' => false],
        ],
        'order_item_id' => [
            'type' => Table::TYPE_INTEGER,
            'length' => 10,
            'index' => true,
            'options' => ['unsigned' => true, 'nullable' => false],
        ],
        'quote_item_id' => [
            'type' => Table::TYPE_INTEGER,
            'length' => 10,
            'index' => true,
            'options' => ['unsigned' => true, 'nullable' => false],
        ],
    ];

    private $__productGiftCardSet =  [
        'entity_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],

        'product_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
            'index' => true,
            'options' => ['unsigned' => true, 'nullable' => false],

        ],

        'gift_card_set_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
            'index' => true,
            'options' => ['unsigned' => true, 'nullable' => false],

        ],
        'price' => [
            'type' => Table::TYPE_DECIMAL,
            'length'=> '12,2',
            'options' => ['default' => '0.0000', 'nullable' => false],
        ],
        'position' => ['type' => Table::TYPE_INTEGER, 'length'=> 10]
    ];

    private $__option = [
        'entity_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],
        'code' => ['type' => Table::TYPE_TEXT, 'length'=> 50],
        'label' => ['type' => Table::TYPE_TEXT, 'length'=> 100],
        'input_type' => ['type' => Table::TYPE_TEXT, 'length'=> 50],
        'options' => ['type' => Table::TYPE_TEXT],
        'is_required' => ['type' => Table::TYPE_INTEGER, 'length'=> 50],
        'validator' => ['type' => Table::TYPE_TEXT],
        'sort_order' => ['type' => Table::TYPE_INTEGER, 'length'=> 5],
    ];

    private $__quote = [
        'entity_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],
        'quote_id' => ['type' => Table::TYPE_INTEGER, 'length'=> 10],
        'gift_card_id' => ['type' => Table::TYPE_INTEGER, 'length'=> 10],
        'base_discount_amount' => ['type' => Table::TYPE_DECIMAL, 'length'=> '12,4'],
        'discount_amount' => ['type' => Table::TYPE_DECIMAL, 'length'=> '12,4'],
        'base_refund_amount' => ['type' => Table::TYPE_DECIMAL, 'length'=> '12,4'],
        'refund_amount' => ['type' => Table::TYPE_DECIMAL, 'length'=> '12,4'],
    ];

    private $__order = [
        'entity_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],
        'order_id' => ['type' => Table::TYPE_INTEGER, 'length'=> 10],
        'gift_card_id' => ['type' => Table::TYPE_INTEGER, 'length'=> 10],
        'base_discount_amount' => ['type' => Table::TYPE_DECIMAL, 'length'=> '12,4'],
        'discount_amount' => ['type' => Table::TYPE_DECIMAL, 'length'=> '12,4'],
        'base_refund_amount' => ['type' => Table::TYPE_DECIMAL, 'length'=> '12,4'],
        'refund_amount' => ['type' => Table::TYPE_DECIMAL, 'length'=> '12,4'],
    ];

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $_io;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * @var \Magetrend\GiftCard\Model\Template\Media\Config
     */
    protected $_mediaConfig;

    /**
     * @param \Magento\Framework\Filesystem\Io\File  $io
     * @param \Magento\Framework\App\Filesystem\DirectoryList  $directoryList
     */
    public function __construct(
        File $io,
        DirectoryList $directoryList,
        \Magetrend\GiftCard\Model\Template\Media\Config $mediaConfig
    ) {
        $this->_io = $io;
        $this->_directoryList = $directoryList;
        $this->_mediaConfig = $mediaConfig;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $this->createTable(
            $installer,
            $installer->getTable('mt_giftcard_template'),
            $this->__templateColumns
        );

        $this->createTable(
            $installer,
            $installer->getTable('mt_giftcard_set'),
            $this->__giftCardSetColumns
        );

        $this->createTable(
            $installer,
            $installer->getTable('mt_giftcard'),
            $this->__giftCardColumns
        );

        $this->createTable(
            $installer,
            $installer->getTable('mt_giftcard_set_product'),
            $this->__productGiftCardSet
        );

        $this->createTable(
            $installer,
            $installer->getTable('mt_giftcard_option'),
            $this->__option
        );

        $this->createTable(
            $installer,
            $installer->getTable('mt_giftcard_order'),
            $this->__order
        );

        $this->createTable(
            $installer,
            $installer->getTable('mt_giftcard_quote'),
            $this->__quote
        );

        $db = $installer->getConnection();

        $giftCardAmountOptions = [
            'TYPE'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'LENGTH'    => '12,4',
            'COMMENT'   => 'Gift Card Discount Amount',
            'DEFAULT' => '0.0000'
        ];

        $baseGiftCardAmountOptions = [
            'TYPE'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'LENGTH'    => '12,4',
            'COMMENT'   => 'Base Gift Card Discount Amount',
            'DEFAULT' => '0.0000'
        ];

        $db->addColumn($installer->getTable('quote_address'), 'giftcard_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('quote_address'), 'base_giftcard_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('quote_address'), 'giftcard_shipping_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('quote_address'), 'base_giftcard_shipping_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('quote_address_item'), 'giftcard_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('quote_address_item'), 'base_giftcard_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('quote_item'), 'giftcard_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('quote_item'), 'base_giftcard_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_order'), 'giftcard_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_order'), 'base_giftcard_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_order'), 'giftcard_invoiced', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_order'), 'base_giftcard_invoiced', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_order'), 'giftcard_refunded', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_order'), 'base_giftcard_refunded', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_order'), 'giftcard_canceled', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_order'), 'base_giftcard_canceled', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_order'), 'giftcard_shipping_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_order'), 'base_giftcard_shipping_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_order'), 'giftcard_shipping_invoiced', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_order'), 'base_giftcard_shipping_invoiced', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_order'), 'giftcard_shipping_refunded', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_order'), 'base_giftcard_shipping_refunded', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_order'), 'giftcard_shipping_canceled', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_order'), 'base_giftcard_shipping_canceled', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_order_item'), 'giftcard_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_order_item'), 'base_giftcard_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_order_item'), 'giftcard_invoiced', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_order_item'), 'base_giftcard_invoiced', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_order_item'), 'giftcard_refunded', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_order_item'), 'base_giftcard_refunded', $baseGiftCardAmountOptions);


        $db->addColumn($installer->getTable('sales_invoice'), 'giftcard_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_invoice'), 'base_giftcard_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_invoice'), 'giftcard_shipping_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_invoice'), 'base_giftcard_shipping_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_invoice_item'), 'giftcard_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_invoice_item'), 'base_giftcard_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_creditmemo'), 'giftcard_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_creditmemo'), 'base_giftcard_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_creditmemo'), 'giftcard_shipping_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_creditmemo'), 'base_giftcard_shipping_amount', $baseGiftCardAmountOptions);

        $db->addColumn($installer->getTable('sales_creditmemo_item'), 'giftcard_amount', $giftCardAmountOptions);
        $db->addColumn($installer->getTable('sales_creditmemo_item'), 'base_giftcard_amount', $baseGiftCardAmountOptions);


        $this->createDirectories();

        $installer->endSetup();
    }

    public function createTable($installer, $tableName, $columns)
    {
        $db = $installer->getConnection();
        $table = $db->newTable($tableName);

        foreach ($columns as $name => $info) {
            $options = [];
            if (isset($info['options'])) {
                $options = $info['options'];
            }

            if (isset($info['primary']) && $info['primary'] == 1) {
                $options = ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true];
            }

            if (!isset($info['length'])) {
                $info['length'] = '';
            }
            $table->addColumn(
                $name,
                $info['type'],
                $info['length'],
                $options,
                $name
            );

            if (isset($info['index'])) {
                $table->addIndex(
                    $installer->getIdxName($tableName, [$name]),
                    [$name]
                );
            }

            if (isset($info['foreign_key'])) {
                $table->addForeignKey(
                    $installer->getFkName($tableName, $name, $info['foreign_key'][0], $info['foreign_key'][1]),
                    $name,
                    $installer->getTable($info['foreign_key'][0]),
                    $info['foreign_key'][1],
                    Table::ACTION_NO_ACTION
                );
            }
        }

        $db->createTable($table);
    }

    /**
     * Create the directories
     */
    protected function createDirectories()
    {
        $this->_io->mkdir($this->_directoryList->getPath('media').'/'.$this->_mediaConfig->getBaseMediaPath(), 0775);
        $this->_io->mkdir($this->_directoryList->getPath('media').'/'.$this->_mediaConfig->getBaseTmpMediaPath(), 0775);
        $this->_io->mkdir($this->_directoryList->getPath('var').'/'.\Magetrend\GiftCard\Model\GiftCard::GIFT_CARD_DIR_JPG, 0775);
        $this->_io->mkdir($this->_directoryList->getPath('var').'/'.\Magetrend\GiftCard\Model\GiftCard::GIFT_CARD_DIR_PDF, 0775);
        $this->_io->mkdir($this->_directoryList->getPath('var').'/'.\Magetrend\GiftCard\Model\GiftCard::GIFT_CARD_DIR_ZIP, 0775);
    }
}
