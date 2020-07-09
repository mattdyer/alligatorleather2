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

namespace Magetrend\GiftCard\Block\Cart\Item\Renderer;

use Magento\Catalog\Model\Config\Source\Product\Thumbnail as ThumbnailSource;
use Magento\Checkout\Block\Cart\Item\Renderer;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Checkout\Block\Cart\Item\Renderer\Actions;
use Magento\Checkout\Model\Session;

/**
 * Shopping cart item render block for gift card products.
 */
class GiftCard extends Renderer implements IdentityInterface
{
    /**
     * Path in config to the setting which defines if parent or child product should be used to generate a thumbnail.
     */
    const CONFIG_THUMBNAIL_SOURCE = 'checkout/cart/configurable_product_image';

    public $giftCardAttribute;

    public $giftCardSetFactory;

    private $giftCardSetPrice = [];

    public $sortOrder = [
        'gc_value' => 0,
        'gc_send_by_post' => 1,
        'gc_send_to_friend' => 2,
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
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        Session $checkoutSession,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Element\Message\InterpretationStrategyInterface $messageInterpretationStrategy,
        \Magetrend\GiftCard\Model\GiftCard\Attribute $giftCardAttribute,
        \Magetrend\GiftCard\Model\GiftCardSetFactory $giftCardSetFactory,
        array $data = []
    ) {
        $this->giftCardAttribute = $giftCardAttribute;
        $this->giftCardSetFactory = $giftCardSetFactory;
        parent::__construct(
            $context,
            $productConfig,
            $checkoutSession,
            $imageBuilder,
            $urlHelper,
            $messageManager,
            $priceCurrency,
            $moduleManager,
            $messageInterpretationStrategy,
            $data
        );
    }

    /**
     * Get list of all options for product
     *
     * @return array
     */
    public function getOptionList()
    {
        $item = $this->getItem();
        $attributeList = $this->giftCardAttribute->getCollection();
        $customOption = [];
        $customOptionValues = $item->getBuyRequest()->getData('gift_card_attribute');
        $giftCardValue = $this->getGiftCardSetValue($customOptionValues['gc_set_id']);

        $customOption[] = [
            'label' => __('Gift Card Value'),
            'value' => $giftCardValue,
            'print_value' => $giftCardValue,
            'code' => 'gc_value'
        ];

        if ($attributeList->getSize() > 0) {
            foreach ($attributeList as $attribute) {
                if (!$item->getOptionByCode($attribute->getAttributeCode())) {
                    continue;
                }
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
        $options = array_merge($customOption, $this->_productConfig->getOptions($item));
        return $options;
    }

    public function getGiftCardSetValue($giftCardSetId)
    {
        if (!isset($this->giftCardSetPrice[$giftCardSetId])) {
            $giftCardSet = $this->giftCardSetFactory->create()
                ->load($giftCardSetId);
            $this->giftCardSetPrice[$giftCardSetId] = $giftCardSet->getFormattedValue();
        }
        return $this->giftCardSetPrice[$giftCardSetId];
    }

    public function sortCustomOptions($options)
    {
        $sortedArray = [];
        foreach ($options as $option) {
            if (isset($option['code']) && isset($this->sortOrder[$option['code']])) {
                $sortedArray[$this->sortOrder[$option['code']]] = $option;
            }
        }
        ksort($sortedArray);
        return $sortedArray;
    }
}
