<?php

namespace Patrickfuchshofer\Giftvoucher\Block\Adminhtml\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class TemplateImage extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('\Patrickfuchshofer\Giftvoucher\Helper\Func');



        $image_style = (array) json_decode($row->getData('image_style'), true);
        $html = '';
        foreach ($image_style as $image_name) {
            if ($image_name) {
                $image_url = $helper->get_site_url() . '/pub/media/giftvoucher/template/' . $image_name;
                $html .= '<img src="' . $image_url . '" height="60" /><br/>';
            }
        }
        return $html;
    }
}
