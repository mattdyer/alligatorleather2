<?php

namespace Patrickfuchshofer\Giftvoucher\Model\Product\Type;

class Giftvoucher extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    const TYPE_ID = 'giftvoucher';

    /**
     * {@inheritdoc}
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
        // method intentionally empty
    }
}
