<?php

namespace Patrickfuchshofer\Giftvoucher\Controller\GiftItemPage;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $helper = $objectManager->get('\Patrickfuchshofer\Giftvoucher\Helper\Func');


        if (!$helper->get_current_user_id()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
        return $this->_pageFactory->create();
    }
}
