<?php

namespace Patrickfuchshofer\Giftvoucher\Model\ResourceModel\Template;


/**
 * Contact Resource Model Collection
 *
 * @author      Pierre FAY
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \Patrickfuchshofer\Giftvoucher\Model\Template::class,
            \Patrickfuchshofer\Giftvoucher\Model\ResourceModel\Template::class
        );
    }
}
