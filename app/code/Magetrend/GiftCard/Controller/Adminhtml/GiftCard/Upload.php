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

use Magento\Framework\App\Filesystem\DirectoryList;

class Upload extends \Magetrend\GiftCard\Controller\Adminhtml\GiftCard
{
    /**
     * @var \Magetrend\GiftCard\Model\GiftCard\Import
     */
    public $import;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magetrend\GiftCard\Helper\Data $gcHelper,
        \Magetrend\GiftCard\Model\GiftCard\Import $import,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->import = $import;
        parent::__construct($context, $coreRegistry, $gcHelper, $resultPageFactory, $resultJsonFactory);
    }

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

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'file']
            );

            $uploader->setAllowedExtensions(['csv']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $uploader->setAllowCreateFolders(true);
            $tmpFilePath = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::TMP)
                ->getAbsolutePath();

            $result = $uploader->save($tmpFilePath);
            if (!isset($result['path']) || !isset($result['file'])) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Unable to upload file'));
            }

            $filePath = $result['path'].$result['file'];
            $this->import->getCollectionFromFile($filePath);

            $this->messageManager->addSuccess(__(
                'The %file file was successful uploaded. Records found: %itemCount. Records ignored: %ignoredItem',
                [
                    'file' => $result['file'],
                    'itemCount' => $this->import->getItemCount(),
                    'ignoredItem' => $this->import->getIgnoredItemCount(),
                ]
            ));

            if ($this->import->getItemCount() - $this->import->getIgnoredItemCount() > 0) {
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData([
                    'file_path' => $filePath
                ]);
            } else {
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData([]);
            }

            return $resultRedirect->setPath('*/*/import');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while uploading file..'));
        }
        $this->_getSession()->setFormData();
        return $resultRedirect->setPath('*/*/import');
    }
}
