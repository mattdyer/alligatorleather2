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

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magetrend\GiftCard\Model\Product\Type\GiftCard;
use Magento\Catalog\Model\Product;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $attributes = [
            'price', 'tax_class_id'
        ];
        foreach ($attributes as $attributeCode) {
            $relatedProductTypes = explode(
                ',',
                $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode, 'apply_to')
            );

            if (!in_array(GiftCard::TYPE_CODE, $relatedProductTypes)) {
                $relatedProductTypes[] = GiftCard::TYPE_CODE;
                $eavSetup->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attributeCode,
                    'apply_to',
                    implode(',', $relatedProductTypes)
                );
            }
        }

        /**
         * Add attributes to the eav/attribute
         */

        if (!$eavSetup->getAttribute(Product::ENTITY, 'gc_type')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gc_type',
                [
                    'type' => 'varchar',
                    'label' => 'Gift Card Type',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 10,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'used_in_product_listing' => false,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => true,
                    'user_defined' => 1,
                    'is_user_defined' => 1,
                    'group' => 'gc_options',
                    'visible' => 0,
                ]
            );
        }

        if (!$eavSetup->getAttribute(Product::ENTITY, 'gc_send_to_friend')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gc_send_to_friend',
                $this->getYesNoAttribute('Send to Friend', 'Magento\Config\Model\Config\Source\Yesno')
            );
        }

        if (!$eavSetup->getAttribute(Product::ENTITY, 'gc_send_by_post')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gc_send_by_post',
                $this->getYesNoAttribute('Send Printed Gift Card by Post', 'Magento\Config\Model\Config\Source\Yesno')
            );
        }

        if (!$eavSetup->getAttribute(Product::ENTITY, 'gc_field_sender_name')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gc_field_sender_name',
                $this->getYesNoAttribute('Sender Name')
            );
        }

        if (!$eavSetup->getAttribute(Product::ENTITY, 'gc_field_recipient_name')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gc_field_recipient_name',
                $this->getYesNoAttribute('Recipient Name')
            );
        }
        if (!$eavSetup->getAttribute(Product::ENTITY, 'gc_field_recipient_email')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gc_field_recipient_email',
                $this->getYesNoAttribute('Recipient Email')
            );
        }
        if (!$eavSetup->getAttribute(Product::ENTITY, 'gc_field_message')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gc_field_message',
                $this->getYesNoAttribute('Message to Friend')
            );
        }
    }

    public function getYesNoAttribute($label, $sourceModel = '')
    {
        return [
            'type' => 'int',
            'length' => 1,
            'label' => $label,
            'input' => 'text',
            'required' => false,
            'sort_order' => 10,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'used_in_product_listing' => false,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => true,
            'user_defined' => 1,
            'is_user_defined' => 1,
            'group' => 'gc_options',
            'visible' => 0,
            'source_model' => $sourceModel
        ];
    }
}