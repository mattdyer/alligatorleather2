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
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Model\Product;

/**
 * Upgrade data
 *
 * @category MageTrend
 * @package  Magetend/GiftCard
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-gift-card
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    private $pageFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->pageFactory = $pageFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var EavSetup $eavSetup */

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gc_use_code_generator',
                $this->getYesNoAttribute('Use Code Generator', 'Magento\Config\Model\Config\Source\Yesno')
            );
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gc_use_code_generator',
                'used_in_product_listing',
                '1'
            );
        }

        if (version_compare($context->getVersion(), '2.0.4', '<')) {
            $this->upgradeData204();
        }

        $setup->endSetup();
    }

    public function upgradeData204()
    {
        $page = $this->pageFactory->create();
        $page->load('gift-card-balance', 'identifier');

        if (!$page->getId()) {
            $page->setTitle('Check your gift card balance')
                ->setIdentifier('gift-card-balance')
                ->setIsActive(true)
                ->setPageLayout('1column')
                ->setStores([0])
                ->setContent('{{block class="Magetrend\GiftCard\Block\Balance"}}')
                ->save();
        }
    }

    /**
     * Returns attribute yes-no config data
     *
     * @param $label
     * @param string $sourceModel
     * @return array
     */
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
            'used_in_product_listing' => true,
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
