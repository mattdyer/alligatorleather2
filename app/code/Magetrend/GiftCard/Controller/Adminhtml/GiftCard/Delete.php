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

namespace Magetrend\GiftCard\Controller\Adminhtml\GiftCard;

class Delete extends \Magetrend\GiftCard\Controller\Adminhtml\GiftCard
{
    
    public function execute()
    {
        $model = $this->_objectManager->create('Magetrend\GiftCard\Model\GiftCard');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This object no longer exists.'));
                $this->_redirect('*/*index');
                return;
            }
            try {
                $model->delete();
                $this->messageManager->addSuccess(__('The gift card was successful deleted.'));
                $this->_redirect('*/*/index');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete data right now. Please review log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                // save data in session
                $this->_objectManager->get(
                    'Magento\Backend\Model\Session'
                )->setFormData(
                    $this->getRequest()->getParams()
                );
                // redirect to edit form
                $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find an object to delete.'));
        $this->_redirect('*/*/index');
    }
}
