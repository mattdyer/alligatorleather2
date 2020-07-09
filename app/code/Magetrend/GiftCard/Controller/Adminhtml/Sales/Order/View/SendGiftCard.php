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

namespace Magetrend\GiftCard\Controller\Adminhtml\Sales\Order\View;

class SendGiftCard extends \Magento\Backend\App\Action
{
    public $giftCardManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magetrend\GiftCard\Model\GiftCard\Management $giftCardManager
    ) {
        $this->giftCardManager = $giftCardManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $orederId = $this->getRequest()->getParam('order_id');
        if ($orederId && is_numeric($orederId)) {
            try {
                $this->giftCardManager->sendGiftCardEmailByOrder($orederId);

                $this->messageManager->addSuccess(__('Gift cards has been send to customer'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError(__('%1', $e->getMessage()));
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Ops... Something went wrong. Unable to send gift cards.'));
            }
        }

        return $this->resultRedirectFactory->create()
            ->setPath('sales/order/view', ['order_id' => $orederId]);
    }

    /**
     * Check if user has enough privileges
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::actions');
    }
}