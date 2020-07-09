<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Patrickfuchshofer\Giftvoucher\Model;

use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var Collection
     */
    protected $collection;
    protected $request;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = [],
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
        $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
        $imageProcessor = $objectManager->create('\Magento\Catalog\Model\Product\Gallery\Processor');
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }


        $collection = $this->collection;

        $items = $collection
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('status')
            ->getItems();

        $id = $this->request->getParam('id');
        foreach ($items as $item) {
            $array = $item->getData();
            if ($item->getId() == $id) {
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($id);
                $galleryEntries = $product->getMediaGalleryEntries();
                $array['data']['parent'] = $item->getCategoryIds();

                for ($i = 1; $i <= 3; $i++) {
                    $image = array_filter($galleryEntries, function ($tmp) use ($i) {
                        return in_array('image_style_' . $i, $tmp->getTypes());
                    });
                    $image = end($image);

                    if ($image) {

                        $array['image' . $i][] = [
                            'url' => $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $image->getFile()
                        ];
                    }
                }
            }
            $this->loadedData[$item->getId()] = $array;
        }


        return $this->loadedData;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return null;
    }
}
