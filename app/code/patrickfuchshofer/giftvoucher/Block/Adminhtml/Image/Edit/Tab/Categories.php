<?php

namespace Patrickfuchshofer\Giftvoucher\Block\Adminhtml\Image\Edit\Tab;


class Categories extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;



    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('categorysGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

    }


    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
        $category = $objectManager->get('Magento\Catalog\Model\CategoryFactory');
        $helper = $objectManager->get('\Patrickfuchshofer\Giftvoucher\Helper\Func');
        $categoryId = $helper->create_Giftvoucher_category();



        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addFieldToFilter('parent_id', $categoryId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        // $this->addColumn(
        //     'in_category',
        //     [
        //         'header_css_class' => 'a-center',
        //         'type' => 'checkbox',
        //         'name' => 'in_category',
        //         'align' => 'center',
        //         'index' => 'entity_id',
        //         'filter' => false,
        //     ]
        // );

    

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
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
            'created_at',
            [
                'header' => __('Date'),
                'type' => 'date',
                'width' => '50px',
                'index' => 'created_at'
            ]
        );

        $this->addColumn(
            'count',
            [
                'header' => __('Count'),
                'width' => '50px',
                'filter' => false,
                'sortable' => false,
                'type' => 'text',
                'renderer' => \Patrickfuchshofer\Giftvoucher\Block\Adminhtml\Widget\Grid\Column\Renderer\CountCategoryProducts::class,
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
                            'base' => '*/editcategory/index',
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
        return $this->getUrl('*/editcategory/index/id/'.$row->getId(), ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    
}
