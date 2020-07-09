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

namespace Magetrend\GiftCard\Controller\Adminhtml\GiftCardSet;

class Generate extends \Magetrend\GiftCard\Controller\Adminhtml\GiftCardSet
{
    /**
     * Generate Coupons action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');
            return;
        }
        $result = [];
        $model = $this->_initGiftCardSet();

        if (!$model->getId()) {
            $result['error'] = __('Object is not defined');
        } else {
            try {
                $qty = $this->getRequest()->getParam('qty');
                $generator = $model->getMassGenerator();
                $generator->generate($qty);
                $generated = $generator->getGeneratedCount();
                $this->messageManager->addSuccess(__('%1 gift card(s) have been generated.', $generated));
                $this->_view->getLayout()->initMessages();
                $result['messages'] = $this->_view->getLayout()->getMessagesBlock()->getGroupedHtml();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $result['error'] = $e->getMessage();
            } catch (\Exception $e) {
                $result['error'] = __(
                    'Something went wrong while generating gift card. Please review the log and try again.'
                ).$e->getMessage();
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
}
