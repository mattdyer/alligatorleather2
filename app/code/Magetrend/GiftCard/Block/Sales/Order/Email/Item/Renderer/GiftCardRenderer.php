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

namespace Magetrend\GiftCard\Block\Sales\Order\Email\Item\Renderer;

class GiftCardRenderer extends \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder
{
    public function getItemOptions()
    {
        $item = $this->getItem();
        $giftCardInstance = $item->getProduct()->getTypeInstance();
        return array_merge(
            $giftCardInstance->getItemOptionList($item),
            parent::getItemOptions()
        );
    }
}
