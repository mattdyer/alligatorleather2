<?php
namespace Madcomputing\ColorImages\Block;

class ImageGetter extends \Magento\Catalog\Block\Product\Gallery
{

    //protected $_registry;


    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory $optionCollection,
        array $data = []
    ) {
        //$this->_registry = $registry;
        //$this->optionCollection = $optionCollection;
        parent::__construct($context, $registry, $data);
    }


    /*public function getCurrentProduct()
    {   
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Framework\Registry')->registry('current_product');

        return $product;
    }*/

    public function getProductCustomOption($product)
    {
        try {

            // $product = $this->getCurrentProduct();

            /*try {
                $product = $this->productRepository->get($sku);
            } catch (\Exception $exception) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Such product doesn\'t exist'));
            }*/
            $productOption = $this->optionCollection->create()->getProductOptions($product->getEntityId(),$product->getStoreId(),false);
            $optionData = [];
            foreach($productOption as $option) {
                $optionId = $option->getId();
                $optionValues = $product->getOptionById($optionId);
                if ($optionValues === null) {
                    throw \Magento\Framework\Exception\NoSuchEntityException::singleField('optionId', $optionId);
                }
                foreach($optionValues->getValues() as $values) {
                    $optionData[] = $values->getData();
                }
            }
            return $optionData;
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Such product doesn\'t exist'));
        }
        return $product;
    }

    public function getTestString(){
        return "hello test";
    }

}