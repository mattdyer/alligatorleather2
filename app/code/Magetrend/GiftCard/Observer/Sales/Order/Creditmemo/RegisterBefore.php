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

namespace Magetrend\GiftCard\Observer\Sales\Order\Creditmemo;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class RegisterBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    protected $helper;

    /**
     * RegisterBefore constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magetrend\GiftCard\Helper\Data $helper
    ) {
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * Update credit memo before registration
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $creditMemoPostData = $this->request->getPost('creditmemo');
        if (isset($creditMemoPostData['giftcard_amount']) && is_numeric($creditMemoPostData['giftcard_amount'])) {
            $giftCardAmount = $creditMemoPostData['giftcard_amount'];
            $creditMemo = $observer->getCreditmemo();
            $order = $creditMemo->getOrder();
            $baseGiftCardAmount = $this->helper->currencyConvert(
                $giftCardAmount,
                $order->getOrderCurrencyCode(),
                $order->getBaseCurrencyCode()
            );

            $creditMemo->setGiftcardAmount($giftCardAmount);
            $creditMemo->setBaseGiftcardAmount($baseGiftCardAmount);
        }
        return $this;
    }
}
