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

namespace Magetrend\GiftCard\Observer\Sales\Order\Invoice;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class Pay implements ObserverInterface
{
    /**
     * @var \Magetrend\GiftCard\Model\GiftCard\Management
     */
    public $giftCardManagement;

    /**
     * Pay constructor.
     *
     * @param \Magetrend\GiftCard\Model\GiftCard\Management $management
     */
    public function __construct(
        \Magetrend\GiftCard\Model\GiftCard\Management $management
    ) {
        $this->giftCardManagement = $management;
    }

    /**
     * After Invoice pay observer
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $invoice = $observer->getInvoice();
        $items = $invoice->getAllItems();
        foreach ($items as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->getProductType() == \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                $customerEmail = $invoice->getBillingAddress()->getEmail();
                $this->giftCardManagement->activateGiftCard($orderItem, $customerEmail);
            }
        }
        return $this;
    }

}
