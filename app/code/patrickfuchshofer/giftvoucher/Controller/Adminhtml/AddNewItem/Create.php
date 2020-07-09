<?php

namespace Patrickfuchshofer\Giftvoucher\Controller\Adminhtml\AddNewItem;

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
        $helper = $objectManager->get('\Patrickfuchshofer\Giftvoucher\Helper\Func');
        $categoryId = $helper->create_Giftvoucher_category();

        $cate_ids = [$categoryId];
        if(isset($post['data']['parent'])){
            $cate_ids = array_merge($cate_ids, $post['data']['parent']);
            $cate_ids = array_values(array_unique($cate_ids));
        }

   
        $uniqid = uniqid();
        $product = $objectManager->create('\Magento\Catalog\Model\Product');
        $product->setSku($uniqid); // Set your sku here
        $product->setName($post['title'] ? $post['title'] : 'Gift Voucher'); // Name of Product
        if (isset($post['description'])) {
            $product->setDescription($post['description']);
        }
        $product->setAttributeSetId(4); // Attribute set id
        $product->setStatus($post['status'] ? 1: 0); // Status on product enabled/ disabled 1/0
        $product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
        $product->setTaxClassId(0); // Tax class id
        $product->setTypeId('giftvoucher'); // type of product (simple/virtual/downloadable/configurable)
        $product->setPrice($post['price'] ? $post['price'] : 0); // price of product
        $product->setUrlKey($uniqid);
        $product->setWebsiteIds(array(1));
        //$product->setCategoryIds($cate_ids);
        $product->setStockData(
            array(
                'use_config_manage_stock' => 0,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 99999999
            )
        );
        $product->save();


        for ($i = 1; $i <= 3; $i++) {
            if (isset($post['image' . $i][0]['path'])) {
                $imagePath = $post['image' . $i][0]['path'] . $post['image' . $i][0]['file'];
                $result = $product->addImageToMediaGallery($imagePath, array(
                    'image', 'small_image', 'thumbnail', 'image_style_' . $i
                ), false, false);
                $result->setTypes([
                    'image_style_' . $i
                ]);
                $result->save();
            }
        }
        $product->save();


 
        $CategoryLinkRepository = $objectManager->get('\Magento\Catalog\Api\CategoryLinkManagementInterface');
        $CategoryLinkRepository->assignProductToCategories($product->getSku(), $cate_ids);
        

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('giftvoucher/allgiftitems/index');
        $this->messageManager->addSuccess(__('You created the gift item.'));
        return $resultRedirect;
    }
}
