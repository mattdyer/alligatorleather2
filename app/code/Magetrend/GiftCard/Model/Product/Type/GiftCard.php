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

namespace Magetrend\GiftCard\Model\Product\Type;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory as GiftCardSetProductCollection;
use Magento\Checkout\Model\Session;

class GiftCard extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    /**
     * Product Type Code
     */
    const TYPE_CODE = 'giftcard';

    /**
     * @var string
     */
    private $giftCardAttributes = '_cache_instance_giftcard_attributes';

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    public $cache;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    public $extensionAttributesJoinProcessora;

    /**
     * @var Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\Collection | null
     */
    private $assignedGiftCardSets = null;

    /**
     * @var \Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory
     */
    public $giftCardSetsProductCollectionFactory;

    /**
     * @var \Magetrend\GiftCard\Model\GiftCard\Attribute
     */
    public $giftCardAttribute;

    /**
     * @var \Magetrend\GiftCard\Model\GiftCardSetFactory
     */
    public $giftCardSetFactory;

    /**
     * @var \Magetrend\GiftCard\Model\GiftCardFactory
     */
    public $giftCardFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $gcHelper;

    /**
     * @var \Magetrend\GiftCard\Model\GiftCardSet
     */
    public $giftCardSet;

    /**
     * @var \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory
     */
    public $giftCardCollectionFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    public $session;

    /**
     * @var \Magetrend\GiftCard\Model\Quote
     */
    public $giftCardQuote;

    /**
     * @var array
     */
    public $sortOrder = [
        'gc_value' => 0,
        'gc_code' => 1,
        'gc_send_by_post' => 2,
        'gc_send_to_friend' => 3,
        'gc_field_sender_name' => 4,
        'gc_field_recipient_name' => 5,
        'gc_field_recipient_email' => 6,
        'gc_field_message' => 7
    ];

    public $yesNoFields = [
        'gc_send_to_friend' => 1,
        'gc_send_by_post' => 1
    ];

    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        GiftCardSetProductCollection $giftCardSetsProductCollectionFactory,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory,
        \Magetrend\GiftCard\Model\GiftCard\Attribute $giftCardAttribute,
        \Magetrend\GiftCard\Model\GiftCardSetFactory $giftCardSetFactory,
        \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magetrend\GiftCard\Model\Quote $giftCardQuote,
        \Magetrend\GiftCard\Model\GiftCardSet $giftCardSet,
        \Magetrend\GiftCard\Helper\Data $gcHelper,
        Session $session,
        \Magento\Framework\Cache\FrontendInterface $cache = null
    ) {
        $this->productFactory = $productFactory;
        $this->giftCardAttribute = $giftCardAttribute;
        $this->cache = $cache;
        $this->extensionAttributesJoinProcessora = $extensionAttributesJoinProcessor;
        $this->giftCardSetsProductCollectionFactory = $giftCardSetsProductCollectionFactory;
        $this->giftCardSetFactory = $giftCardSetFactory;
        $this->gcHelper = $gcHelper;
        $this->giftCardSet = $giftCardSet;
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardCollectionFactory = $giftCardCollectionFactory;
        $this->giftCardQuote = $giftCardQuote;
        $this->session = $session;
        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );
    }

    /**
     * Check is virtual product
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isVirtual($product)
    {
        $giftCardType = $this->getGiftCardType($product);
        if ($giftCardType == \Magetrend\GiftCard\Model\GiftCard::TYPE_VIRTUAL) {
            return true;
        }

        if ($giftCardType == \Magetrend\GiftCard\Model\GiftCard::TYPE_REAL) {
            return false;
        }

        if ($giftCardType == \Magetrend\GiftCard\Model\GiftCard::TYPE_VIRTUAL_REAL) {
            $buyRequestSerialized = $product->getCustomOption('info_buyRequest')->getValue();
            if (!empty($buyRequestSerialized)) {
                $buyRequest = $this->gcHelper->unserialize($buyRequestSerialized);
                $giftCardAttributes = $buyRequest['gift_card_attribute'];
                if (isset($giftCardAttributes['gc_send_by_post'])
                    && $giftCardAttributes['gc_send_by_post'] == 1) {
                    return false;
                }
            }
        }
        return true;
    }

    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {

    }

    /**
     * Returns product status for sale
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isSalable($product)
    {
        $assignedSets = $this->getAssignedGiftCardSets($product);
        if ($assignedSets->count() == 0) {
            return false;
        }
        return true;
    }

    public function hasOptions($product)
    {
        return true;
    }

    /**
     * Prepare product for card
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string $processMode
     * @return array|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {
        $this->validateBuyRequest($buyRequest, $product);
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);
        $result = $this->addGiftCardValueOption($result, $buyRequest, $product);
        if (is_string($result)) {
            return $result;
        }
        $result = $this->addGiftCardOtherOptions($result, $buyRequest, $product);
        $this->removeAddedGiftCardCodes();
        return $result;
    }

    /**
     * Assign gift card set automatically if there is available only one set
     * @param $buyRequest
     * @param $product
     * @return string
     */
    public function assignAutomatically($buyRequest, $product)
    {
        $assignedSets = $this->getAssignedGiftCardSets($product);
        if ($assignedSets->getSize() == 1) {
            $buyRequestData = $buyRequest->getData();
            $buyRequestData['gc_set_id'] = $assignedSets->getFirstItem()->getId();
            $buyRequest->setData($buyRequestData);
        }
    }

    /**
     * Add gift card value custom option
     * @param $result
     * @param $buyRequest
     * @param $product
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addGiftCardValueOption($result, $buyRequest, $product)
    {
        $productOptions = $buyRequest->getData('gift_card_attribute');
        if (!isset($productOptions['gc_set_id']) || !is_numeric($productOptions['gc_set_id'])) {
             return __('Please choose gift card value.')->render();
        }

        $giftCardSet = $this->giftCardSetFactory->create()
            ->load($productOptions['gc_set_id']);

        if (!$giftCardSet->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Ups... Something goes wrong.'));
        }

        //check is set available for this product
        $assignedSets = $this->getAssignedGiftCardSets($product)
            ->addFieldToFilter('gift_card_set_id', $giftCardSet->getId());

        if ($assignedSets->getSize() == 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can not add this gift card item right now.')
            );
        }

        $aData = $assignedSets->getData();
        $result = $this->addCustomOptionToResult($result, 'gift_card_set', serialize([
            'id' => $giftCardSet->getId(),
            'value' => $giftCardSet->getValue(),
            'price' => $aData[0]['price'],
            'currency' => $giftCardSet->getCurrency(),
        ]));

        return $result;
    }

    /**
     * Add other gift card custom options
     * @param $result
     * @param $buyRequest
     * @param $product
     * @return mixed
     */
    public function addGiftCardOtherOptions($result, $buyRequest, $product)
    {
        $attributeCollection = $this->giftCardAttribute->getCollection();
        $productOptions = $buyRequest->getData('gift_card_attribute');
        if ($attributeCollection) {
            foreach ($attributeCollection as $attribute) {
                if (isset($productOptions[$attribute->getAttributeCode()])
                    && !empty($productOptions[$attribute->getAttributeCode()])
                ) {
                    $result = $this->addCustomOptionToResult(
                        $result,
                        $attribute->getAttributeCode(),
                        $productOptions[$attribute->getAttributeCode()]
                    );
                }
            }
        }
        return $result;
    }

    /**
     * Add custom option to result
     * @param $result
     * @param $key
     * @param $value
     * @return mixed
     */
    public function addCustomOptionToResult($result, $key, $value)
    {
        foreach ($result as $product) {
            $product->addCustomOption($key, $value);
        }

        return $result;
    }

    /**
     * Check available quantity
     * @param \Magento\Framework\DataObject $buyRequest
     * @param $product
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateBuyRequest(\Magento\Framework\DataObject $buyRequest, $product)
    {
        /**
         * Shop owner are able to print gift card after order every time or are selling virtual gift cards
         * so we can generate it after order
         */
        if (!$this->gcHelper->isStockValidationEnabled()) {
            return true;
        }
        $gcOptions = $buyRequest->getData('gift_card_attribute');

        /**
         * No need to send printed gift card so we will be able to generate it
         */

        if (!$gcOptions || !isset($gcOptions['gc_send_by_post']) || $gcOptions['gc_send_by_post'] != 1) {
            return true;
        }

        $availableQty = $this->getAvailableQty($gcOptions['gc_set_id']);
        $itemQtyInCart = $this->getItemQtyInCart($gcOptions['gc_set_id']);
        if ($availableQty - $itemQtyInCart < $buyRequest->getQty()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Unable to add requested quantity of item.'));
        }
        return true;
    }

    /**
     * Returns qty of gift card which are printed and ready for sale
     * @param $giftCardSetId
     * @return mixed
     */
    public function getAvailableQty($giftCardSetId)
    {
        $availableQty = $this->giftCardCollectionFactory->create()
            ->addFieldToFilter('gift_card_set_id', $giftCardSetId)
            ->addFieldToFilter('status', \Magetrend\GiftCard\Model\GiftCard::STATUS_AVAILABLE)
            ->getSize();
        return $availableQty;
    }

    public function getItemQtyInCart($giftCardSetId)
    {

        $items = $this->session->getQuote()->getAllVisibleItems();
        $counter = 0;

        foreach ($items as $item) {
            if ($item->getProductType() != \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                continue;
            };
            $buyRequest = $item->getBuyRequest();
            $gcOptions = $buyRequest->getData('gift_card_attribute');
            if (isset($gcOptions['gc_send_by_post'])) {
                if ($gcOptions['gc_set_id'] == $giftCardSetId && $gcOptions['gc_send_by_post'] == 1) {
                    $counter = $counter + $buyRequest->getQty();
                }
            }
        }
        return $counter;
    }

    public function getGiftCardAttributes($product)
    {
        if (!$product->hasData($this->giftCardAttributes)) {
            $product->setData($this->giftCardAttributes, $this->getGiftCardAttributeCollection($product));
        }
        return $product->getData($this->giftCardAttributes);
    }

    public function getGiftCardAttributeCollection($product)
    {
        $list = [];
        $list['gc_type'] = $this->_eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'gc_type');
        return $list;
    }

    /**
     * Get MetadataPool instance
     * @return MetadataPool
     */
    private function getMetadataPool()
    {
        if (!$this->metadataPool) {
            $this->metadataPool = ObjectManager::getInstance()->get(MetadataPool::class);
        }
        return $this->metadataPool;
    }

    /**
     * @deprecated
     * @return \Magento\Framework\Cache\FrontendInterface
     */
    private function getCache()
    {
        if ($this->cache == null) {
            $this->cache = ObjectManager::getInstance()->get(\Magento\Framework\Cache\FrontendInterface::class);
        }
        return $this->cache;
    }

    public function getAssignedGiftCardSets($product)
    {
        if ($this->assignedGiftCardSets == null) {
             $productId = $product->getId();
             $collection = $this->giftCardSetsProductCollectionFactory->create()
                 ->addFieldToFilter('product_id', $productId)
                 ->joinSets([
                     'value', 'name', 'currency', 'gift_card_set_id' => 'entity_id'
                 ])
                 ->sortByPosition();

             $collection = $this->unsetNotAvailableSets($collection, $product);
             $this->assignedGiftCardSets = $collection;
        }

        return $this->assignedGiftCardSets;
    }

    /**
     * Returns gift card type
     * @param $product
     * @return mixed
     */
    public function getGiftCardType($product)
    {
        if ($product->hasData('gc_type')) {
            $giftCardType = $product->getData('gc_type');
        } else {
            $productObject = $this->productFactory->create()->load($product->getId());
            $giftCardType = $productObject->getGcType();
        }
        return $giftCardType;
    }

    /**
     * Remove gift card sets which is not available
     *
     * @param $collection
     * @param $product
     * @return mixed
     */
    public function unsetNotAvailableSets($collection, $product)
    {
        if ($collection->getSize() > 0 && $product->getData('gc_use_code_generator') == 0) {
            $setsIds = [];
            foreach ($collection as $item) {
                $setsIds[] = $item->getGiftCardSetId();
            }
            $giftCardCollection = $this->giftCardCollectionFactory->create()
                ->addFieldToSelect('gift_card_set_id')
                ->addFieldToFilter('gift_card_set_id', ['in' => $setsIds])
                ->addFieldToFilter('status', \Magetrend\GiftCard\Model\GiftCard::STATUS_AVAILABLE);
            $giftCardCollection->getSelect()->group('gift_card_set_id');

            $availableSets = [];
            if ($giftCardCollection->getSize() > 0) {
                foreach ($giftCardCollection as $giftCard) {
                    $availableSets[$giftCard->getGiftCardSetId()] = 1;
                }
            }

            foreach ($collection as $key => $item) {
                if (!isset($availableSets[$item->getGiftCardSetId()])) {
                    $collection->removeItemByKey($item->getId());
                }
            }
        }
        return $collection;
    }

    /**
     * Returns gift card item option list
     *
     * @param $item
     * @return array|bool
     */
    public function getItemOptionList($item)
    {
        $buyRequest = $item->getBuyRequest();
        $customOptionValues = $buyRequest->getGiftCardAttribute();
        if (count($customOptionValues) == 0) {
            return false;
        }
        $attributeList = $this->giftCardAttribute->getCollection();
        $customOptionValues = $item->getBuyRequest()->getData('gift_card_attribute');
        $customOption = $this->getAdditionalOptions($item);

        if ($attributeList->getSize() == 0) {
            return false;
        }

        foreach ($attributeList as $attribute) {
            if (isset($customOptionValues[$attribute->getAttributeCode()])
                && !empty($customOptionValues[$attribute->getAttributeCode()])) {
                $value = $customOptionValues[$attribute->getAttributeCode()];
                $option = [
                    'label' => __($attribute->getFrontendLabel()),
                    'value' => $value,
                    'print_value' => $customOptionValues[$attribute->getAttributeCode()],
                ];

                if (isset($this->yesNoFields[$attribute->getAttributeCode()])) {
                    switch ($option['value']) {
                        case 1:
                            $option['value'] = __('Yes');
                            $option['print_value'] = __('Yes');
                            break;
                        default:
                            $option['value'] = __('No');
                            $option['print_value'] = __('No');
                            break;
                    }
                }

                $option['code'] = $attribute->getAttributeCode();
                $customOption[] = $option;
            }
        }

        $customOption = $this->sortCustomOptions($customOption);
        return $customOption;
    }

    /**
     * Returns additional gift card option list
     * @param $item
     * @return array
     */
    public function getAdditionalOptions($item)
    {
        $customOption = [];
        $giftCardCollection = $this->getGiftCardCollection($item);
        if ($giftCardCollection->getSize() > 0) {
            $i = 1;
            foreach ($giftCardCollection as $key => $giftCard) {
                $code = $giftCard->getCode().' ('.__($giftCard->getStatus()).')';
                $customOption[] = [
                    'label' => $giftCardCollection->getSize() ==1?__('Gift Card Code'):__('Gift Card Code #').$i,
                    'value' => $code,
                    'print_value' => $code,
                    'code' => 'gc_code_'.$i
                ];
                $i++;
            }

            $giftCard = $giftCardCollection->getFirstItem();
            $giftCardValue = $giftCard->getFormattedValue(2);
            $customOption[] = [
                'label' => __('Gift Card Value'),
                'value' => $giftCardValue,
                'print_value' => $giftCardValue,
                'code' => 'gc_value'
            ];
        }
        return $customOption;
    }

    /**
     * Returns gift card object assigned to order item
     * @param $item
     * @return mixed
     */
    public function getGiftCardCollection($item)
    {
        $orderItem = $this->getOrderItem($item);
        $giftCardCollection = $this->giftCardCollectionFactory->create()
            ->addFieldToFilter('quote_item_id', $orderItem->getQuoteItemId());
        return $giftCardCollection;
    }

    /**
     * Returns order item
     * @param $item
     * @return mixed
     */
    public function getOrderItem($item)
    {
        if ($item instanceof \Magento\Sales\Model\Order\Item) {
            return $item;
        } else {
            return $item->getOrderItem();
        }
    }

    /**
     * Options sorter
     * @param $options
     * @return array
     */
    public function sortCustomOptions($options)
    {
        $optionArray = [];
        $sortedArray = [];
        foreach ($options as $option) {
            if (isset($option['code']) && isset($this->sortOrder[$option['code']])) {
                $sortedArray[$this->sortOrder[$option['code']]] = $option;
            }
        }
        ksort($sortedArray);
        foreach ($options as $option) {
            if (substr_count($option['code'], 'gc_code') == 1) {
                $optionArray[] = $option;
            }
        }
        foreach ($sortedArray as $option) {
            $optionArray[] = $option;
        }

        return $optionArray;
    }

    public function removeAddedGiftCardCodes()
    {
        $this->giftCardQuote->removeAllGiftCardFromQuote();
    }

    /**
     * Prepare selected options for gift card product
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @param  \Magento\Framework\DataObject $buyRequest
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function processBuyRequest($product, $buyRequest)
    {

        $gcAttribute = $buyRequest->getGiftCardAttribute();
        $gcAttribute = is_array($gcAttribute) ? $gcAttribute : [];

        $options = ['gift_card_attribute' => $gcAttribute];

        return $options;
    }

    public function getMinPrice()
    {
        return 0;
    }
}
