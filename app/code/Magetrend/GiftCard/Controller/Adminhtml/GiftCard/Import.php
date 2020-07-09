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

class Import extends \Magetrend\GiftCard\Controller\Adminhtml\GiftCard
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

        $formData = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData();

        if (isset($formData['file_path'])) {
            $this->_session->setData('giftcard_file_path', $formData['file_path']);
            $this->coreRegistry->register('giftcard_file_path', $formData['file_path']);
        }

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            __('Import'),
            __('Import')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Gift Card'));
        $resultPage->getConfig()->getTitle()
            ->prepend(__('Gift Card Import'));

        return $resultPage;
    }
}
