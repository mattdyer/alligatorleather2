<?php

namespace Patrickfuchshofer\Giftvoucher\Controller\Adminhtml\AddNewTemplate;

class Create extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;


    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory

    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Load the page defined in view/adminhtml/layout/exampleadminnewpage_helloworld_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
        $helper = $objectManager->get('\Patrickfuchshofer\Giftvoucher\Helper\Func');
        $template = create_object('Patrickfuchshofer\Giftvoucher\Model\Template');
        $template->setTitle($post['title']);
        $template->setActive($post['status'] ? 1 : 0);
        $template->save();

        $images = [];
        for ($i = 1; $i <= 3; $i++) {
            if (isset($post['image' . $i][0]['name'])) {
                $imagePath = $post['image' . $i][0]['path'] . $post['image' . $i][0]['file'];
                $info = pathinfo($imagePath);
                $path = $helper->wp_upload_dir()['basedir'] . '/giftvoucher/template/';
                $new_name = uniqid() . '.' . $info['extension'];
                $new_file = $path . $new_name;
                if (!file_exists($path)) {
                    mkdir($path, 0755,  true);
                }
                copy($imagePath, $new_file);

                $images[] = $new_name;
            } else {
                $images[] = null;
            }
        }

        if (count(array_filter($images))) {
            $template->setData('image_style', json_encode($images));
        }
        $template->save();

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('giftvoucher/vouchertemplate/index');
        $this->messageManager->addSuccess(__('You created the voucher template.'));
        return $resultRedirect;
    }
}
