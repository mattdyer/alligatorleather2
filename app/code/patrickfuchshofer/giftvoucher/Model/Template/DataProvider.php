<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Patrickfuchshofer\Giftvoucher\Model\Template;


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
        \Patrickfuchshofer\Giftvoucher\Model\ResourceModel\Template\Collection $collectionFactory,
        array $meta = [],
        array $data = [],
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
        $this->collection = $collectionFactory;
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
        $helper = $objectManager->get('\Patrickfuchshofer\Giftvoucher\Helper\Func');

        if (isset($this->loadedData)) {
            return $this->loadedData;
        }


        $collection = $this->collection;

        $items = $collection
            ->getItems();

        $id = $this->request->getParam('id');
        foreach ($items as $item) {
            $array = $item->getData();
            if ($array['image_style']) {
                $image_style = json_decode($array['image_style'], true);
                for ($i = 1; $i <= 3; $i++) {
                    if ($image_style[$i - 1]) {
                        $array['image' . $i] = [
                            0 => [
                                'url' => $helper->get_site_url() . 'pub/media/giftvoucher/template/' . $image_style[$i - 1]
                            ]
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
