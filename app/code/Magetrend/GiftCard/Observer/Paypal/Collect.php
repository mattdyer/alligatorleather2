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

namespace Magetrend\GiftCard\Observer\Paypal;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class Collect implements ObserverInterface
{
    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $moduleHelper;

    /**
     * Collect constructor.
     * @param \Magetrend\GiftCard\Helper\Data $moduleHelper
     */
    public function __construct(
        \Magetrend\GiftCard\Helper\Data $moduleHelper
    ) {
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Assign quote address data to order
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->moduleHelper->isActive()) {
            return;
        }

        /** @var \Magento\Payment\Model\Cart $cart */
        $cart = $observer->getEvent()->getCart();
        $salesEntity = $cart->getSalesModel();
        $totalGiftCard = 0;
        foreach ($salesEntity->getAllItems() as $item) {
            $originalItem = $item->getOriginalItem();
            $giftCardDiscount = $originalItem->getData('giftcard_amount');
            $totalGiftCard += $giftCardDiscount;
        }

        if ($totalGiftCard != 0) {
            $cart->addCustomItem(__('Gift Card Discount'), 1, $totalGiftCard);
        }
    }
}
