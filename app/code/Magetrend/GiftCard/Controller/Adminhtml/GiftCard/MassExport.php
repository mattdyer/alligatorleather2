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

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Response\Http\FileFactory;
use Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;
use Magento\Framework\Archive\Zip;
use Magento\Framework\App\Filesystem\DirectoryList;

class MassExport extends \Magetrend\GiftCard\Controller\Adminhtml\GiftCard\AbstractMassAction
{
    /**
     * @var FileFactory
     */
    public $fileFactory;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var Zip
     */
    public $zip;

    /**
     * @var DirectoryList
     */
    public $directoryList;

    /**
     * MassExport constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magetrend\GiftCard\Helper\Data $gcHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param CollectionFactory $collectionFactory
     * @param DateTime $dateTime
     * @param Zip $zip
     * @param FileFactory $fileFactory
     * @param DirectoryList $directoryList
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\Registry $coreRegistry,
        \Magetrend\GiftCard\Helper\Data $gcHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        CollectionFactory $collectionFactory,
        DateTime $dateTime,
        Zip $zip,
        FileFactory $fileFactory,
        DirectoryList $directoryList
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->zip = $zip;
        $this->collectionFactory = $collectionFactory;
        $this->directoryList = $directoryList;
        return parent::__construct($context, $filter, $coreRegistry, $gcHelper, $resultPageFactory, $resultJsonFactory);
    }

    /**
     * Export gift card for print in pdf or jpg format
     * @param AbstractCollection $collection
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        $format = $this->getRequest()->getParam('format');
        if ($collection->getSize() == 0) {
            $this->messageManager->addMessage(__('Please select an items for export.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('giftcard/giftcard/index');
        }
        $tmpFileName = '/'.\Magetrend\GiftCard\Model\GiftCard::GIFT_CARD_DIR_ZIP.'/'.time().'_'.$collection->getSize().'.zip';
        $tmpFilePath = $this->directoryList->getPath('var').$tmpFileName;
        $fileName = "";
        $this->gcHelper->createDirIfNotExist($tmpFilePath);

        $zip = new \ZipArchive();
        $zip->open($tmpFilePath, \ZipArchive::CREATE);
        foreach ($collection as $giftCard) {
            if (!$giftCard->isPrintable()) {
                continue;
            }
            $filePath = false;
            switch ($format) {
                case 'jpg':
                    $filePath = $giftCard->getGiftCardPathJpg();
                    $fileName = $giftCard->getFileNameJpg();
                    break;
                case 'pdf':
                    $filePath = $giftCard->getGiftCardPathPdf();
                    $fileName = $giftCard->getFileNamePdf();
                    break;
            }
            if (!$filePath) {
                continue;
            }

            $zip->addFile($filePath, $fileName);
        }
        $zip->close();

        return $this->fileFactory->create(
            sprintf('gift_cards_%s.zip', $this->dateTime->date('Y-m-d_H-i-s')),
            [
                'rm' => 1,
                'type' => 'filename',
                'value' => $tmpFileName,
            ],
            DirectoryList::VAR_DIR,
            'application/zip'
        );
    }
}