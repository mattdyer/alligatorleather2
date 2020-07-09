<?php

namespace Patrickfuchshofer\Giftvoucher\Block\Adminhtml\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class ProductCategories extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        
        $cateIds = $row->getCategoryIds();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cate = $objectManager->create('Magento\Catalog\Model\Category');
        $arr = [];
        foreach ($cateIds as $id) {
            $category = $cate->load($id);
            $arr[] = $category->getName();
        }
        return implode(', ', $arr);
    }
}
