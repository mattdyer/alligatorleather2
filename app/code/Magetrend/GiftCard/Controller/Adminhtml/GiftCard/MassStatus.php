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
use Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

class MassStatus extends \Magetrend\GiftCard\Controller\Adminhtml\GiftCard\AbstractMassAction
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\Registry $coreRegistry,
        \Magetrend\GiftCard\Helper\Data $gcHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        return parent::__construct($context, $filter, $coreRegistry, $gcHelper, $resultPageFactory, $resultJsonFactory);
    }

    protected function massAction(AbstractCollection $collection)
    {
        $status = $this->getRequest()->getParam('status');
        foreach ($collection as $item) {
            $item->setStatus($status);
        }
        $collection->walk('save');

        $this->messageManager->addSuccess(__('A status of %1 record(s) have been changed.', $collection->getSize()));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('giftcard/giftcard/index');
    }
}
