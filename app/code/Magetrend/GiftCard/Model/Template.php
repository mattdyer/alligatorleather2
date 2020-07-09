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

namespace Magetrend\GiftCard\Model;

class Template extends \Magento\Framework\Model\AbstractModel
{
    public $helper;

    public $mediaConfig;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\GiftCard\Helper\Data $helper,
        \Magetrend\GiftCard\Model\Template\Media\Config $config,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->mediaConfig = $config;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\GiftCard\Model\ResourceModel\Template');
    }

    public function createTemplate($designCode, $storeId, $name)
    {
        $defaultConfig = [];
        $designDefaults = $this->helper->getDesign($designCode);
        foreach ($designDefaults['options'] as $key => $option) {
            $defaultConfig[$key] = $option['value'];
        }

        $this->setData(array_merge([
            'name' => $name,
            'design' => $designCode,
            'store_id' => $storeId
        ], $defaultConfig));

        $this->save();
        return $this;
    }

    public function getImagePath($key)
    {
        return $this->mediaConfig->getFullPath($this->getData($key));
    }

    /**
     * Returns default value from config
     * @param $key
     * @return string
     */
    public function getDesignDefault($key)
    {
        $designDefaults = $this->helper->getDesign($this->getDesign());
        if (!isset($designDefaults['options'][$key])) {
            return '';
        }

        return $designDefaults['options'][$key]['value'];
    }
}
