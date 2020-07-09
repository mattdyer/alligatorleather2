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

namespace Magetrend\GiftCard\Model\Product\Type\GiftCard;

class Price extends \Magento\Catalog\Model\Product\Type\Price
{
    public function getPrice($product)
    {
        if ($product->getCustomOption('gift_card_set')) {
            $options = unserialize($product->getCustomOption('gift_card_set')->getValue());
            return $options['price'];
        }
        return 0;
    }
}
