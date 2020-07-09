<?php

namespace Patrickfuchshofer\Giftvoucher\Model\ResourceModel;

/**
 * Contact Resource Model
 *
 * @author      Pierre FAY
 */
class Template extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('giftvouchers_template', 'id');
    }
}
