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

namespace  Magetrend\GiftCard\Controller\Adminhtml;

class GiftCardSet extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory|null
     */
    public $resultJsonFactory = null;

    /**
     * @var \Magento\Framework\Registry|null
     */
    public $coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $gcHelper;

    /**
     * GiftCardSet constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magetrend\GiftCard\Helper\Data $gcHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magetrend\GiftCard\Helper\Data $gcHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->gcHelper = $gcHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Templates / Gift Card'));
        $this->_view->renderLayout();
    }

    /**
     * Check if user has enough privileges
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magetrend_GiftCard::giftcardset');
    }

    protected function _jsonResponse($data)
    {
        return $this->resultJsonFactory->create()->setData($data);
    }

    protected function _initGiftCardSet($idFieldName = 'id')
    {
        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = $this->_objectManager->create('Magetrend\GiftCard\Model\GiftCardSet');
        if ($id) {
            $model->load($id);
        }
        if (!$this->coreRegistry->registry('giftcard_giftcardset')) {
            $this->coreRegistry->register('giftcard_giftcardset', $model);
        }

        return $model;
    }

    protected function _error($message)
    {
        return $this->resultJsonFactory->create()->setData([
            'error' => $message
        ]);
    }
}
