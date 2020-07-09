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

namespace Magetrend\GiftCard\Model\Config\Source;

class GiftCardSets implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magetrend\GiftCard\Model\ResourceModel\Template\CollectionFactory $collectionFactory
     */
    public $collectionFactory;

    /**
     * Template constructor.
     * @param \Magetrend\GiftCard\Model\ResourceModel\GiftCardSet\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magetrend\GiftCard\Model\ResourceModel\GiftCardSet\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];
        $options = $this->toArray();
        foreach ($options as $key => $label) {
            $data[] = [
                'value' => $key,
                'label' => $label
            ];
        }
        return $data;
    }

    /**
     * Get options in "key-value" format
     * @param bool $whiteOption
     * @return array
     */
    public function toArray($whiteOption = false)
    {
        $options = [];
        if ($whiteOption) {
            $options[0] = "";
        }

        $collection = $this->collectionFactory->create();

        foreach ($collection as $item) {
            $options[$item->getId()] = $item->getName();
        }
        return $options;
    }
}
