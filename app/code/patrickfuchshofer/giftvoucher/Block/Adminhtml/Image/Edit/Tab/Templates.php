<?php

namespace Patrickfuchshofer\Giftvoucher\Block\Adminhtml\Image\Edit\Tab;


class Templates extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */



    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('templatesGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }


    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $contactModel = $objectManager->create('Patrickfuchshofer\Giftvoucher\Model\Template');
        $collection = $contactModel->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        // $this->addColumn(
        //     'in_template',
        //     [
        //         'header_css_class' => 'a-center',
        //         'type' => 'checkbox',
        //         'name' => 'in_template',
        //         'align' => 'center',
        //         'index' => 'entity_id',
        //         'filter' => false,
        //     ]
        // );



        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'image',
            [
                'header' => __('Image'),
                'width' => '50px',
                'filter' => false,
                'sortable' => false,
                'type' => 'text',
                'renderer' => \Patrickfuchshofer\Giftvoucher\Block\Adminhtml\Widget\Grid\Column\Renderer\TemplateImage::class,
            ]
        );

        // $this->addColumn(
        //     'categories',
        //     [
        //         'header' => __('Item Categories'),
        //         'width' => '50px',
        //         'filter' => false,
        //         'sortable' => false,
        //     ]
        // );

        $this->addColumn(
            'active',
            [
                'header' => __('Status'),
                'width' => '50px',
                'index' => 'active',
                'filter' => false,
                'renderer' => \Patrickfuchshofer\Giftvoucher\Block\Adminhtml\Widget\Grid\Column\Renderer\TemplateStatus::class,
            ]
        );

      

        $this->addColumn(
            'templateadd_time',
            [
                'header' => __('Order Date'),
                'type' => 'date',
                'width' => '50px',
                'index' => 'templateadd_time'
            ]
        );


        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/edittemplate/index',
                        ],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/edittemplate/index/id/' . $row->getId(), ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
