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

use Magetrend\GiftCard\Helper\Data;

class Template extends \Magento\Backend\App\Action
{

    public $resultJsonFactory = null;

    public $coreRegistry = null;

    public $_storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
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
        return $this->_authorization->isAllowed('Magetrend_GiftCard::template');
    }

    protected function _jsonResponse($data)
    {
        return $this->resultJsonFactory->create()->setData($data);
    }

    protected function _initTemplate($idFieldName = 'id')
    {
        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = $this->_objectManager->create('Magetrend\GiftCard\Model\Template');
        if ($id) {
            $model->load($id);
        }
        if (!$this->coreRegistry->registry('current_giftcard_template')) {
            $this->coreRegistry->register('current_giftcard_template', $model);
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
