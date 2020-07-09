<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Patrickfuchshofer\Giftvoucher\Model\Category;

use \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

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
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }


        $collection = $this->collection;

        $items = $collection
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->getItems();

        $id = $this->request->getParam('id');
        foreach ($items as $item) {
            $array = $item->getData();
            if ($item->getId() == $id) {
                $category = $objectManager->create('Magento\Catalog\Model\Category')->load($id);
                if($category->getImageUrl()){
                    $array['image'] = [
                        0 => [
                            'url' => $category->getImageUrl()
                        ]
                    ];

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
