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

namespace Magetrend\GiftCard\Block\Product\View\Type;

class GiftCard extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $gcHelper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    public $jsonEncode;

    /**
     * @var null
     */
    private $assignedGiftCardSets = null;

    /**
     * GiftCard constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magetrend\GiftCard\Helper\Data $gcHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magetrend\GiftCard\Helper\Data $gcHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->gcHelper = $gcHelper;
        $this->jsonEncode = $jsonEncoder;
        parent::__construct($context, $arrayUtils, $data);
    }

    /**
     * Get allowed attributes
     *
     * @return array
     */
    public function getAssignedGiftCardSets()
    {
        if ($this->assignedGiftCardSets == null) {
            $this->assignedGiftCardSets = $this->getProduct()->getTypeInstance()->getAssignedGiftCardSets(
                $this->getProduct()
            );
        }
        return $this->assignedGiftCardSets;
    }

    /**
     * Returns module helper
     *
     * @return \Magetrend\GiftCard\Helper\Data
     */
    public function getHelper()
    {
        return $this->gcHelper;
    }

    /**
     * Returns product configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $currentProduct = $this->getProduct();
        $regularPrice = $currentProduct->getPriceInfo()->getPrice('regular_price');
        $finalPrice = $currentProduct->getPriceInfo()->getPrice('final_price');
        $giftCardSetsConfig = [];
        $giftCardSetsCollection = $this->getAssignedGiftCardSets();
        if ($giftCardSetsCollection->getSize() > 0) {
            foreach ($giftCardSetsCollection as $giftCardSet) {
                $data = $giftCardSet->getData();
                $data['prices'] = [
                    'oldPrice' => [
                        'amount' => $this->registerJsPrice(($data['value'] - $regularPrice->getAmount()->getValue())),
                    ],
                    'basePrice' => [
                        'amount' => $this->registerJsPrice(
                            ($data['price'] - $finalPrice->getAmount()->getBaseAmount())
                        ),
                    ],
                    'finalPrice' => [
                        'amount' => $this->registerJsPrice($data['price'] - $finalPrice->getAmount()->getValue()),
                    ]
                ];

                $giftCardSetsConfig[$giftCardSet->getGiftCardSetId()] = $data;
            }
        }
        $config = [
            'giftCardSets' => $giftCardSetsConfig,
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->registerJsPrice(
                        $regularPrice->getAmount()->getValue()
                    ),
                ],
                'basePrice' => [
                    'amount' => $this->registerJsPrice(
                        $finalPrice->getAmount()->getBaseAmount()
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->registerJsPrice(
                        $finalPrice->getAmount()->getValue()
                    ),
                ],
            ],
        ];

        return $config;
    }

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        return $this->jsonEncode->encode($this->getConfig());
    }

    /**
     * Returns current store
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getCurrentStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    public function registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }

    /**
     * Returns additional values for js config, con be overridden by descendants
     *
     * @return array
     */
    public function _getAdditionalConfig()
    {
        return [];
    }

    /**
     * Is allowed to refuse plastic gift card and get only virtual
     *
     * @param $product
     * @return bool
     */
    public function isAllowedToRefuse($product)
    {
        $gcType = $product->getGcType();
        if ($gcType == \Magetrend\GiftCard\Model\GiftCard::TYPE_REAL) {
            return false;
        }

        if ($gcType == \Magetrend\GiftCard\Model\GiftCard::TYPE_VIRTUAL) {
            return false;
        }

        if ($gcType == \Magetrend\GiftCard\Model\GiftCard::TYPE_VIRTUAL_REAL && !$product->getData('gc_send_by_post')) {
             return false;
        }

        return true;
    }

    /**
     * Returns send by post hidden checkbox value
     *
     * @param $product
     * @return int
     */
    public function getSendByPostHiddenValue($product)
    {
        $gcType = $product->getGcType();
        if ($gcType == \Magetrend\GiftCard\Model\GiftCard::TYPE_VIRTUAL) {
            return 0;
        }

        if ($gcType == \Magetrend\GiftCard\Model\GiftCard::TYPE_REAL) {
            return 1;
        }

        if ($gcType == \Magetrend\GiftCard\Model\GiftCard::TYPE_VIRTUAL_REAL) {
            if ($product->getData('gc_send_by_post') == 0) {
                return 1;
            }
        }
        return 0;
    }
}
