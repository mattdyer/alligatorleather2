<?php

namespace Patrickfuchshofer\Giftvoucher\Controller\Adminhtml\EditTemplate;

class Update extends \Magento\Backend\App\Action
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
        $template = $objectManager->create('Patrickfuchshofer\Giftvoucher\Model\Template')->load($post['id']);
        $template->setTitle($post['title']);
        $template->setActive($post['active'] ? 1 : 0);

        $image_style = (array) json_decode($template->getData('image_style'), true);
        for ($i = 1; $i <= 3; $i++) {
            $key = $i - 1;

            if ($image_style[$key]) {
                if (!isset($post['image' . $i])) {
                    $image_style[$key] = null;
                } else {
                    if (isset($post['image' . $i][0]['path'])) {
                        $imagePath = $post['image' . $i][0]['path'] . $post['image' . $i][0]['file'];
                        $info = pathinfo($imagePath);
                        $path = $helper->wp_upload_dir()['basedir'] . '/giftvoucher/template/';
                        $new_name = uniqid() . '.' . $info['extension'];
                        $new_file = $path . $new_name;
                        if (!file_exists($path)) {
                            mkdir($path, 0755,  true);
                        }
                        copy($imagePath, $new_file);
                        $image_style[$key] = $new_name;
                    }
                }
            } else {
                if (isset($post['image' . $i][0]['path'])) {
                    $imagePath = $post['image' . $i][0]['path'] . $post['image' . $i][0]['file'];
                    $info = pathinfo($imagePath);
                    $path = $helper->wp_upload_dir()['basedir'] . '/giftvoucher/template/';
                    $new_name = uniqid() . '.' . $info['extension'];
                    $new_file = $path . $new_name;
                    if (!file_exists($path)) {
                        mkdir($path, 0755,  true);
                    }
                    copy($imagePath, $new_file);
                    $image_style[$key] = $new_name;
                }
            }
        }

        if (count(array_filter($image_style))) {
            $template->setData('image_style', json_encode($image_style));
        } else {
            $template->setData('image_style', null);
        }
        $template->save();


        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('giftvoucher/vouchertemplate/index');
        $this->messageManager->addSuccess(__('You updated the voucher template.'));
        return $resultRedirect;
    }
}
