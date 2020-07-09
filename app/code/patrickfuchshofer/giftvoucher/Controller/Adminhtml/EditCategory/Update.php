<?php

namespace Patrickfuchshofer\Giftvoucher\Controller\Adminhtml\EditCategory;

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
        
        error_reporting(E_ERROR | E_PARSE);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $rootPath  =  $directory->getRoot();

       
        $category = $objectManager->create('Magento\Catalog\Model\Category')->load($post['entity_id']);
        $category->setName($post['name']);
        $category->setDescription($post['description'] ? $post['description'] : '');
        $category->setStoreId(0);
        $category->save();
      
      

        if ($category->getImage()) {
            //Category have image
            if (!isset($post['image'])) {
                // $path = $rootPath . '/pub/media/catalog/category/' . $category->getImage();
                // unlink($path);
                $category->setImage(null);
                $category->setStoreId(0);
                $category->save();
            }else{
                if (isset($post['image'][0]['name'])) {
                    // $path = $rootPath . '/pub/media/catalog/category/' . $category->getImage();
                    // unlink($path);

                    $imagePath = $post['image'][0]['path'] . $post['image'][0]['file'];
                    $info = pathinfo($imagePath);
                    $path = $rootPath . '/pub/media/catalog/category/';
                    $new_name = uniqid(). '.' .$info['extension'];
                    $new_file = $path . $new_name;
                    if(!file_exists($path)){
                        mkdir($path, 0755,  true);
                    }
                    copy($imagePath, $new_file);
                    
                    $category->setImage($new_name, array(
                        'image', 'small_image', 'thumbnail',
                    ), true, false);
                    $category->setStoreId(0);
                    $category->save();
                    
                }
            }
        }else{
            if (isset($post['image'][0]['name'])) {
                $imagePath = $post['image'][0]['path'] . $post['image'][0]['file'];
                $info = pathinfo($imagePath);
                $path = $rootPath . '/pub/media/catalog/category/';
                $new_name = uniqid(). '.' .$info['extension'];
                $new_file = $path . $new_name;
                if(!file_exists($path)){
                    mkdir($path, 0755,  true);
                }
                copy($imagePath, $new_file);
                $category->setImage($new_name, array(
                    'image', 'small_image', 'thumbnail',
                ), true, false);
                $category->setStoreId(0);
                $category->save();
            }
        }
       
        
    

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('giftvoucher/itemcategory/index');
        $this->messageManager->addSuccess(__('You updated the item category.'));
        return $resultRedirect;
    }
}
