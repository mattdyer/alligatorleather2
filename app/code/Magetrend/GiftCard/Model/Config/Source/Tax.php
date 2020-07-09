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

class Tax implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     * @return array
     */

    const AFTER = 'after';

    const BEFORE = 'before';

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
     * @return array
     */
    public function toArray()
    {
        return [
            self::BEFORE => __('Before Tax'),
            self::AFTER => __('After Tax'),
        ];
    }
}
