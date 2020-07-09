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

namespace Magetrend\GiftCard\Block\Sales\Order\Item\Renderer;

class GiftCardRenderer extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer
{
    public function getItemOptions()
    {
        $orderItem = $this->getOrderItem();
        $giftCardInstance = $orderItem->getProduct()->getTypeInstance();
        return array_merge(
            $giftCardInstance->getItemOptionList($orderItem),
            parent::getItemOptions()
        );
    }
}
