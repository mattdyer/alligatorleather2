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

namespace Magetrend\GiftCard\Plugin\Payment;

use Magento\Store\Model\ScopeInterface;;

class Cashondelivery
{
    /**
     * @var  \Magento\Checkout\Model\Cart
     */
    public $cart;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->cart = $cart;
        $this->scopeConfig = $scopeConfig;
    }

    public function afterIsAvailable($parentModel, $result)
    {
        if (!$result) {
            return $result;
        }

        if (!$this->scopeConfig->getValue('giftcard/payment/cod', ScopeInterface::SCOPE_STORE)) {
            return $result;
        }

        if ($this->cart->getItemsCount() == 0) {
            return $result;
        }

        $items = $this->cart->getItems();
        foreach ($items as $item) {
            if ($item->getProductType() == \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                return false;
            }
        }

        return $result;
    }
}
