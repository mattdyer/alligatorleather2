<?php

namespace Patrickfuchshofer\Giftvoucher\Controller\Adminhtml\AddNewCategory;

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
        $category = $objectManager->get('Magento\Catalog\Model\CategoryFactory');
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $rootPath  =  $directory->getRoot();
        $helper = $objectManager->get('\Patrickfuchshofer\Giftvoucher\Helper\Func');
        $categoryId = $helper->create_Giftvoucher_category();


        $category = $objectManager->create('\Magento\Catalog\Model\Category');

        $category->setName($post['title']);
        $category->setDescription($post['description']);
        $category->setParentId($categoryId);
        //$category->setIncludeInMenu(false);
        $category->setIsActive(true);
        $category->setDisplayMode('PRODUCTS');
        $category->setStoreId(0);
        $category->setLevel(3);
        $category->save();


       
        if (isset($post['image'][0]['name'])) {
            $imagePath = $post['image'][0]['path'] . $post['image'][0]['file'];
            $info = pathinfo($imagePath);
            $path = $rootPath . '/pub/media/catalog/category/';
            $new_name = uniqid() . '.' . $info['extension'];
            $new_file = $path . $new_name;
            if (!file_exists($path)) {
                mkdir($path, 0755,  true);
            }

            // echo $imagePath;
            // echo '<br/>';
            // echo $new_file;
            // exit();
            copy($imagePath, $new_file);
            //$category->setImage($new_name);

            $category->setImage($new_name, array(
                'image', 'small_image', 'thumbnail',
            ), true, false);
        }
        $category->setPath('1/2/' . $categoryId . '/' . $category->getId());
        $category->setStoreId(0);
        $category->save();

        $category->setUrlKey(uniqid());
        $category->setStoreId(0);
        $category->save();




        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('giftvoucher/itemcategory/index');
        $this->messageManager->addSuccess(__('You created the item category.'));
        return $resultRedirect;
    }
}
