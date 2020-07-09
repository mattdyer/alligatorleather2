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

namespace Magetrend\GiftCard\Block\Adminhtml\Sales\Order\View\Items\Column\Name;

class GiftCard extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    /**
     * Add gift card item options
     * @return array
     */
    public function getOrderOptions()
    {
        $result = parent::getOrderOptions();
        $item = $this->getItem();
        $result = array_merge($item->getProduct()->getTypeInstance()->getItemOptionList($item), $result);
        return $result;
    }
}
