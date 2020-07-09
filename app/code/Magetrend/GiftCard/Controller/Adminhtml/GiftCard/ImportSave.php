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

class ImportSave extends \Magetrend\GiftCard\Controller\Adminhtml\GiftCard
{
    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magetrend_GiftCard::giftcard');
        return $resultPage;
    }

    /**
     * Edit Blog Post
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $params = $this->getRequest()->getParams();
            $formData = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData();

            if (!isset($formData['file_path']) || empty($formData['file_path'])) {

            }
            $filePath = $formData['file_path'];
            $import = $this->_objectManager->get('Magetrend\GiftCard\Model\GiftCard\Import');
            $import->importData($params, $filePath);

            $totalImported = $import->getSize();

            $this->messageManager->addSuccess(__(
                '%itemCount gift card item(s) was successful imported.',
                [
                    'itemCount' => $totalImported
                ]
            ));

            $this->_getSession()->setFormData();
            return $resultRedirect->setPath('*/*/index');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while uploading file..'));
        }

        return $resultRedirect->setPath('*/*/import');
    }
}
