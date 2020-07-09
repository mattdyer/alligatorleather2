<?php

namespace Patrickfuchshofer\Giftvoucher\Block\Adminhtml\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class CountCategoryProducts extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        return $row->getProductCount() . '';
    }
}
