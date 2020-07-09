<?php

namespace Patrickfuchshofer\Giftvoucher\Model;

/**
 * Contact Model
 *
 * @author      Pierre FAY
 */
class Template extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Patrickfuchshofer\GIftVoucher\Model\ResourceModel\Template::class);
    }
}
