<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * PHP version 5.3 or later
 *
 * @category MageTrend
 * @package  Magetend/GiftCard
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-gift-card
 */

namespace Magetrend\GiftCard\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory;

/**
 * Class Customertab
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftCard extends AbstractModifier
{
    const DATA_SCOPE = '';
    const DATA_SCOPE_ASSIGN_GIFT_CARD_SET = 'assigngiftcardset';
    const DATA_SCOPE_SETTINGS = 'gift_card_product_settings';
    const GROUP_ASSIGN_GIFT_CARD_SET = 'assigngiftcardset';

    /**
     * @var LocatorInterface
     */
    public $locator;

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * @var ProductLinkRepositoryInterface
     */
    public $productLinkRepository;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var ImageHelper
     */
    public $imageHelper;

    /**
     * @var Status
     */
    public $status;

    /**
     * @var AttributeSetRepositoryInterface
     */
    public $attributeSetRepository;

    /**
     * @var string
     */
    public $scopeName;

    /**
     * @var string
     */
    public $scopePrefix;

    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $helper;

    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \Magetrend\GiftCard\Model\Config\Source\GiftCardType
     */
    public $giftCardType;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param ProductLinkRepositoryInterface $productLinkRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ImageHelper $imageHelper
     * @param Status $status
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param string $scopeName
     * @param string $scopePrefix
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ProductLinkRepositoryInterface $productLinkRepository,
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        Status $status,
        AttributeSetRepositoryInterface $attributeSetRepository,
        CollectionFactory $collectionFactory,
        \Magetrend\GiftCard\Helper\Data $helper,
        \Magetrend\GiftCard\Model\Config\Source\GiftCardType $giftCardType,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        $scopeName = '',
        $scopePrefix = ''
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->productLinkRepository = $productLinkRepository;
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        $this->status = $status;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->scopeName = $scopeName;
        $this->scopePrefix = $scopePrefix;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
        $this->giftCardType = $giftCardType;
        $this->productFactory = $productFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_ASSIGN_GIFT_CARD_SET => [
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_SETTINGS => $this->getSettingsFieldSet(),
                        $this->scopePrefix . static::DATA_SCOPE_ASSIGN_GIFT_CARD_SET => $this->getAssignSetFieldSet(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Gift Card Options'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' => 1000
                            ],
                        ],

                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * Prepares config for the Related products fieldset
     *
     * @return array
     */
    public function getAssignSetFieldSet()
    {
        $content = __(
            'Relation between product and gift cards'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Assign Gift Cards Sets'),
                    $this->scopePrefix . static::DATA_SCOPE_ASSIGN_GIFT_CARD_SET
                ),
                'modal' => $this->getGenericModal(
                    __('Assign Gift Cards Sets'),
                    $this->scopePrefix . static::DATA_SCOPE_ASSIGN_GIFT_CARD_SET
                ),
                static::DATA_SCOPE_ASSIGN_GIFT_CARD_SET => $this->getGrid(
                    $this->scopePrefix . static::DATA_SCOPE_ASSIGN_GIFT_CARD_SET
                ),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Gift Cards Sets'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 10,
                    ],
                ],
            ]
        ];
    }

    public function getSettingsFieldSet()
    {
        $giftCardTypeField =  [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Gift Card Type'),
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'formElement' => \Magento\Ui\Component\Form\Element\Select::NAME,
                        'dataType' => \Magento\Ui\Component\Form\Element\DataType\Text::NAME,
                        'dataScope' => 'gc_type',

                        'sortOrder' => 1,
                        'required' => true,
                        'options' => $this->giftCardType->toOptionArray()
                    ],
                ],
            ],
        ];

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Fieldset::NAME,
                        'componentType' => Fieldset::NAME,
                        'sortOrder' => 1,
                        'additionalClasses' => 'admin__field-wide',
                        'dataScope' => 'data.product',
                    ],
                ],
            ],
            'children' => [
                'gift_card_type' => $giftCardTypeField,
                'gc_use_code_generator' => $this->getYesNoField('Use Code Generator', 'gc_use_code_generator', 2),
                'send_to_friend' => $this->getYesNoField('Send to Friends', 'gc_send_to_friend', 3),
                'allow_to_refuse' => $this->getYesNoField('Allow Refuse Real Gift Card', 'gc_send_by_post', 4),
                'gc_field_sender_name' => $this->getYesNoField(
                    'Show Field: Sender Name',
                    'gc_field_sender_name',
                    5
                ),
                'gc_field_recipient_name' => $this->getYesNoField(
                    'Show Field: Recipient Name',
                    'gc_field_recipient_name',
                    6
                ),
                'field_recipient_email' => $this->getYesNoField(
                    'Show Field: Recipient Email',
                    'gc_field_recipient_email',
                    7
                ),
                'field_message' => $this->getYesNoField(
                    'Show Field: Message to Friend',
                    'gc_field_message',
                    8
                ),
            ],
        ];
    }

    /**
     * Returns yes/no field data
     *
     * @param $label
     * @param $name
     * @param $sort
     * @return array
     */
    public function getYesNoField($label, $name, $sort)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __($label),
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'formElement' => \Magento\Ui\Component\Form\Element\Select::NAME,
                        'dataType' => \Magento\Ui\Component\Form\Element\DataType\Number::NAME,
                        'dataScope' => $name,
                        'options' => [
                            ['value' => 0,  'label' => __('No')],
                            ['value' => 1,  'label' => __('Yes')]
                        ],
                        'sortOrder' => $sort
                    ],
                ],
            ],
        ];
    }

    /**
     * Modify data
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();

        if (!$productId) {
            return $data;
        }

        $data[$productId]['links'][self::DATA_SCOPE_ASSIGN_GIFT_CARD_SET] = $this->getLinksData($productId);
        $productObject = $this->productFactory->create()->load($productId);
        $data[$productId]['product']['gc_type'] = $productObject->getData('gc_type');
        $data[$productId]['product']['gc_use_code_generator'] = $productObject->getData('gc_use_code_generator');
        $data[$productId]['product']['gc_send_to_friend'] = $productObject->getData('gc_send_to_friend');
        $data[$productId]['product']['gc_field_recipient_name'] = $productObject->getData('gc_field_recipient_name');
        $data[$productId]['product']['gc_send_by_post'] = $productObject->getData('gc_send_by_post');
        $data[$productId]['product']['gc_field_sender_name'] = $productObject->getData('gc_field_sender_name');
        $data[$productId]['product']['gc_field_recipient_email'] = $productObject->getData('gc_field_recipient_email');
        $data[$productId]['product']['gc_field_message'] = $productObject->getData('gc_field_message');

        return $data;
    }

    public function getLinksData($productId)
    {
        $data = [];
        $collection = $this->collectionFactory->create()
            ->joinSets([
                'name' => 'name',
                'value' => 'value',
                'currency' => 'currency',
                'life_time' => 'life_time',
                'id' => 'entity_id'
            ])
            ->addFieldToSelect([
                'price' => 'price',
                'position' => 'position'
            ])
            ->addFieldToFilter('product_id', $productId);

        if ($collection->getSize() > 0) {
            foreach ($collection as $item) {
                $item->setData('price', number_format($item->getPrice(), 2));
                $data[] = $this->prepareDataObject($item);
            }
        }

        return $data;
    }

    /**
     * Prepare data object
     *
     * @param $dataObject
     * @return mixed
     */
    public function prepareDataObject($dataObject)
    {
        $dataObject->setLifeTime($this->helper->formatDays($dataObject->getLifeTime()));
        $dataObject->setValue($this->helper->formatPrice($dataObject->getValue(), $dataObject->getCurrency()));
        return $dataObject->getData();
    }

    /**
     * Prepare data column
     *
     * @param ProductInterface $linkedProduct
     * @param ProductLinkInterface $linkItem
     * @return array
     */
    public function fillData(ProductInterface $linkedProduct, ProductLinkInterface $linkItem)
    {

        return [
            'id' => $linkedProduct->getId(),
            'thumbnail' => $this->imageHelper->init($linkedProduct, 'product_listing_thumbnail')->getUrl(),
            'name' => $linkedProduct->getName(),
            'status' => $this->status->getOptionText($linkedProduct->getStatus()),
            'attribute_set' => $this->attributeSetRepository
                ->get($linkedProduct->getAttributeSetId())
                ->getAttributeSetName(),
            'sku' => $linkItem->getLinkedProductSku(),
            'price' => $linkedProduct->getPrice(),
            'position' => $linkItem->getPosition(),
        ];
    }

    /**
     * Retrieve all data scopes
     *
     * @return array
     */
    public function getDataScopes()
    {
        return [
            static::DATA_SCOPE_ASSIGN_GIFT_CARD_SET,
        ];
    }

    /**
     * Retrieve button set
     *
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     * @return array
     */
    public function getButtonSet(Phrase $content, Phrase $buttonTitle, $scope)
    {
        $modalTarget = $this->scopeName . '.' . static::GROUP_ASSIGN_GIFT_CARD_SET . '.' . $scope . '.modal';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.' . $scope . '_product_listing',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],

                ],
            ],
        ];
    }

    /**
     * Prepares config for modal slide-out panel
     *
     * @param Phrase $title
     * @param string $scope
     * @return array
     */
    public function getGenericModal(Phrase $title, $scope)
    {
        $listingTarget = $scope . '_product_listing';

        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Assign'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $listingTarget,
                                'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.' . $listingTarget . '.product_columns.ids',
                                'ns' => $listingTarget,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id',
                                    'storeId' => '${ $.provider }:data.product.current_store_id',
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id',
                                    'storeId' => '${ $.externalProvider }:params.current_store_id',
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $modal;
    }

    /**
     * Retrieve grid
     *
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getGrid($scope)
    {
        $dataProvider = $scope . '_product_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'entity_id',
                            'name' => 'name',
                            'value' => 'value',
                            'life_time' => 'life_time',

                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }'
                        ],
                        'sortOrder' => 2,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->fillMeta(),
                ],
            ],
        ];
    }

    /**
     * Retrieve meta column
     *
     * @return array
     */
    public function fillMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            'name' => $this->getTextColumn('name', false, __('Name'), 20),
            'value' => $this->getTextColumn('value', false, __('Initial Value'), 25),
            'life_time' => $this->getTextColumn('life_time', false, __('Life Time'), 30),
            'price' => $this->getPriceColumn('price', true, __('Price'), 60),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
            'position' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Number::NAME,
                            'formElement' => Input::NAME,
                            'componentType' => Field::NAME,
                            'dataScope' => 'position',
                            'sortOrder' => 80,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    public function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }

    /**
     * Retrieve price column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    public function getPriceColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/form/element/input',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }
}
