<?php

namespace Patrickfuchshofer\Giftvoucher\Controller\Adminhtml\EditItem;

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

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $category = $objectManager->get('Magento\Catalog\Model\CategoryFactory');
        $CategoryLinkRepository = $objectManager->get('\Magento\Catalog\Model\CategoryLinkRepository');
        $helper = $objectManager->get('\Patrickfuchshofer\Giftvoucher\Helper\Func');
        $categoryId = $helper->create_Giftvoucher_category();

        $cate_ids = [$categoryId];
        if (isset($post['data']['parent'])) {
            $cate_ids = array_merge($cate_ids, $post['data']['parent']);
            $cate_ids = array_values(array_unique($cate_ids));
        }

        $product = $product = $objectManager->create('Magento\Catalog\Model\Product')->load($post['entity_id']);
        //$product->setCategoryIds($cate_ids);
        $product->setStatus($post['status'] ? 1 : 0); // Status on product enabled/ disabled 1/0
        $product->setName($post['name'] ? $post['name'] : 'Gift Voucher'); // Name of Product
        $product->setDescription($post['description'] ? $post['description'] : '');
        $product->setPrice($post['price'] ? $post['price'] : 0); // price of product
        $product->save();

       
        $galleryEntries = $product->getMediaGalleryEntries();
        $imageProcessor = $objectManager->create('\Magento\Catalog\Model\Product\Gallery\Processor');

        for ($i = 1; $i <= 3; $i++) {
            //Get image
            $image = array_filter($galleryEntries, function ($tmp) use ($i) {
                return in_array('image_style_' . $i, $tmp->getTypes());
            });
            $image = end($image);

            if ($image) {
                //Exists image matches image_style_1
                if (!isset($post['image' . $i])) {
                    //Not have upload file
                    //Delete current image
                    $imageProcessor->removeImage($product, $image->getFile());
                } else {
                    //Have upload file
                    if (isset($post['image' . $i][0]['path'])) {
                        //Delete current file
                        $imageProcessor->removeImage($product, $image->getFile());

                        //Upload new file
                        $imagePath = $post['image' . $i][0]['path'] . $post['image' . $i][0]['file'];
                        $result = $product->addImageToMediaGallery($imagePath, array(
                            'image', 'small_image', 'thumbnail', 'image_style_' . $i
                        ), false, false);
                        $result->getTypes([
                            'image_style_' . $i
                        ]);
                        $result->save();
                    }
                }
            } else {
                //Not have image matches image_style_1
                if (isset($post['image' . $i][0]['path'])) {
                    //Have upload file
                    //Upload image and add image to product media gallery
                    $imagePath = $post['image' . $i][0]['path'] . $post['image' . $i][0]['file'];
                    $result = $product->addImageToMediaGallery($imagePath, array(
                        'image', 'small_image', 'thumbnail', 'image_style_' . $i
                    ), false, false);
                    $result->getTypes([
                        'image_style_' . $i
                    ]);
                    $result->save();
                }
            }
        }

        $product->save();

        $diff = array_diff($product->getCategoryIds(), $cate_ids);
        $CategoryLinkRepository = $objectManager->get('\Magento\Catalog\Model\CategoryLinkRepository');
        foreach($diff as $id){
            if($id != $categoryId){
                $CategoryLinkRepository->deleteByIds($id,$product->getSku());
            }
        }

        $CategoryLinkRepository = $objectManager->get('\Magento\Catalog\Api\CategoryLinkManagementInterface');
        $CategoryLinkRepository->assignProductToCategories($product->getSku(), $cate_ids);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('giftvoucher/allgiftitems/index');
        $this->messageManager->addSuccess(__('You updated the gift item.'));
        return $resultRedirect;
    }
}
