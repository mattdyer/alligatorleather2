<?php

namespace Patrickfuchshofer\Giftvoucher\Block\Adminhtml\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class TemplateStatus extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        return $row->getActive() ? 'Active' : 'Inactive';
    }
}
