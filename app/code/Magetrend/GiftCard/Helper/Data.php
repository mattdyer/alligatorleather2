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

namespace Magetrend\GiftCard\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends AbstractHelper
{
    const XML_PATH_EMAIL_SEND_TO_FRIEND_TEMPLATE_ID = 'giftcard/email/to_friend';

    const XML_PATH_EMAIL_SEND_TO_CUSTOMER_TEMPLATE_ID = 'giftcard/email/to_customer';

    const XML_PATH_EMAIL_GIFT_CARD_SENDER_NAME = 'giftcard/email/default_sender_name';

    const XML_PATH_EMAIL_GIFT_CARD_SENDER_EMAIL = 'giftcard/email/default_sender_email';

    const XML_PATH_EMAIL_GIFT_CARD_PDF = 'giftcard/email/pdf';

    const XML_PATH_TAX_TYPE = 'giftcard/tax/type';

    const XML_PATH_TAX_PRICE_TAX = 'giftcard/tax/price_tax';

    const XML_PATH_CATALOG_PRICE_VIEW = 'giftcard/catalog/price_view';

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrencyInterface;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    public $readFactory;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    public $ioFile;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    public $currencyFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    public $timeZone;

    /**
     * @var \Magento\Framework\Filesystem
     */
    public $fileSystem;

    /**
     * @var \Magetrend\GiftCard\Model\Template\Media\Config
     */
    public $mediaConfig;

    public $productMetadata;

    public $jsonHelper;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $timezone
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magetrend\GiftCard\Model\Template\Media\Config $mediaConfig
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone $timezone,
        \Magento\Framework\Filesystem $filesystem,
        \Magetrend\GiftCard\Model\Template\Media\Config $mediaConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->readFactory = $readFactory;
        $this->ioFile = $ioFile;
        $this->fileSystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->timeZone = $timezone;
        $this->mediaConfig = $mediaConfig;
        $this->productMetadata = $productMetadata;
        $this->jsonHelper = $jsonHelper;
        return parent::__construct($context);
    }

    /**
     * Returns extension status
     *
     * @param null $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return true;
    }

    /**
     * Returns gift card designs list
     *
     * @return array
     */
    public function getDesignList()
    {
        $list = [];
        $designs = $this->scopeConfig->getValue('magetrend/giftcard/design');
        foreach ($designs as $key => $design) {
            $list[] = array_merge($design, ['code' => $key,]);
        }
        return $list;
    }

    /**
     * Returns gift card design
     *
     * @param $key
     * @return array
     */
    public function getDesign($key)
    {
        $designs = $this->scopeConfig->getValue('magetrend/giftcard/design');
        return array_merge($designs[$key], ['code' => $key,]);
    }

    /**
     * Prepare gift card for preview
     *
     * @param $params
     * @return array
     */
    public function preparePreviewData($params)
    {
        $templateData = [];
        foreach ($params as $type => $values) {
            if (!is_array($values)) {
                continue;
            }
            foreach ($values as $key => $item) {
                if ($type == 'image' && substr_count($item['value'], 'Magetrend_GiftCard::') == 0) {
                    $imageName = explode('/', $item['value']);
                    $templateData[$key] = end($imageName);
                } else {
                    $templateData[$key] = $item['value'];
                }
            }
        }

        $templateData['design'] = $params['design'];
        return $templateData;
    }

    /**
     * Convert collor type
     *
     * @param $hex
     * @return array
     */
    public function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = [$r, $g, $b];
        return $rgb;
    }

    /**
     * Returns gift card status backend label
     * @param $statusCode
     * @return \Magento\Framework\Phrase
     */
    public function getStatusBackendLabel($statusCode)
    {
        switch ($statusCode) {
            case \Magetrend\GiftCard\Model\GiftCard::STATUS_NEW:
                return __('New');
        }

        return $statusCode;
    }

    /**
     * Returns gift card status frontend label
     * @param $statusCode
     * @return \Magento\Framework\Phrase
     */
    public function getStatusFrontEndLabel($statusCode)
    {
        switch ($statusCode) {
            case \Magetrend\GiftCard\Model\GiftCard::STATUS_NEW:
                return __('New');
        }

        return $statusCode;
    }

    /**
     * Add "days" label for integer
     * @param $dayCount
     * @return string
     */
    public function formatDays($dayCount)
    {
        if ($dayCount == 0) {
            return '-';
        } elseif ($dayCount == 1) {
            return $dayCount.__('day');
        }
        return $dayCount.__('days');
    }

    /**
     * Add currency code to price
     * @param $price
     * @param $currencyCode
     * @return mixed
     */
    public function formatPrice($price, $currencyCode)
    {
        $formattedPrice = $this->priceCurrencyInterface->format(
            $price,
            false,
            \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
            0,
            $currencyCode
        );
        return $formattedPrice;
    }

    /**
     * Enable/disable gift card product stock
     *
     * @return bool
     */
    public function isStockValidationEnabled()
    {
        return true;
    }

    /**
     * Returns send gift card to friend email template id
     * @param null $storeId
     * @return mixed
     */
    public function getSendToFriendTemplateId($storeId = null)
    {
        $templateId = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SEND_TO_FRIEND_TEMPLATE_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $templateId;
    }

    /**
     * Returns send gift card to friend email template id
     * @param null $storeId
     * @return mixed
     */
    public function getSendToCustomerTemplateId($storeId = null)
    {
        $templateId = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SEND_TO_CUSTOMER_TEMPLATE_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $templateId;
    }

    /**
     * Returns send gift card email from name
     * @param null $storeId
     * @return mixed
     */
    public function getGiftCardSenderName($storeId = null)
    {
        $senderName = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_GIFT_CARD_SENDER_NAME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $senderName;
    }

    /**
     * Returns send gift card email from email
     * @param null $storeId
     * @return mixed
     */
    public function getGiftCardSenderEmail($storeId = null)
    {
        $senderEmail = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_GIFT_CARD_SENDER_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $senderEmail;
    }

    /**
     * Returns send gift card email from email
     * @param null $storeId
     * @return mixed
     */
    public function canAttachPdf($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_GIFT_CARD_PDF,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) == 1;
    }

    public function prepareEmailData($data)
    {
        return [
            'message' => isset($data['gc_field_message'])?$data['gc_field_message']:'',
            'recipientName' => isset($data['gc_field_recipient_name'])?$data['gc_field_recipient_name']:'',
            'senderName' => isset($data['gc_field_sender_name'])?$data['gc_field_sender_name']:'',
            'sendByPost' => isset($data['gc_send_by_post'])?$data['gc_send_by_post']:0,
            'order' => $data['order'],
            'giftCard' => $data['gift_card'],
        ];
    }

    public function createDirIfNotExist($dir, $withFileName = true)
    {
        if ($withFileName) {
            $dir = explode('/', $dir);
            unset($dir[count($dir) - 1]);
            $dir = implode('/', $dir);
        }

        if (!$this->readFactory->create($dir)->isExist()) {
            $this->ioFile->mkdir($dir, 0775, true);
        }
    }

    public function getTaxCalculationType($storeId)
    {
        $taxType = $this->scopeConfig->getValue(
            self::XML_PATH_TAX_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $taxType;
    }

    public function getPriceView($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATALOG_PRICE_VIEW,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function applyOnPriceIncludeTax($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TAX_PRICE_TAX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) == 1;
    }

    public function currencyConvert($amount, $fromCurrency = null, $toCurrency = null)
    {
        if (!$fromCurrency) {
            $fromCurrency = $this->storeManager->getStore()->getBaseCurrency();
        }

        if (!$toCurrency) {
            $toCurrency = $this->storeManager->getStore()->getCurrentCurrency();
        }

        if (is_string($fromCurrency)) {
            $rateToBase = $this->currencyFactory->create()
                ->load($fromCurrency)
                ->getAnyRate($this->storeManager->getStore()->getBaseCurrency()->getCode());
        } elseif ($fromCurrency instanceof \Magento\Directory\Model\Currency) {
            $rateToBase = $fromCurrency->getAnyRate($this->storeManager->getStore()->getBaseCurrency()->getCode());
        }

        $rateFromBase = $this->storeManager->getStore()->getBaseCurrency()->getRate($toCurrency);
        if ($rateToBase && $rateFromBase) {
            $amount = $amount * $rateToBase * $rateFromBase;
        }

        return $amount;
    }

    /**
     * @param Y-m-d H:i:s string $date
     * @param $storeId
     * @param int $type
     * @return string
     */
    public function formatDate($date, $storeId, $type = \IntlDateFormatter::MEDIUM)
    {
        $localeCode = $this->scopeConfig
            ->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        return $this->timeZone->formatDateTime($date, $type, \IntlDateFormatter::NONE, $localeCode);
    }

    /**
     * Convert store ids array to string for store in database
     * @param array $storeIds
     * @return string
     */
    public function convertStoreIdsToString($storeIds = [])
    {
        return implode(",", $storeIds);
    }

    /**
     * Convert a sting from database to store ids array
     * @param string $storeIds
     * @return array
     */
    public function convertStoreIdsToArray($storeIds = "")
    {
        return explode(",", $storeIds);
    }

    /**
     * Returns all active store list
     * @return array
     */
    public function getAllStoresIdList()
    {
        $storeIds = [];
        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            if ($store->getId() == 0 || !$store->isActive()) {
                continue;
            }
            $storeIds[] = $store->getId();
        }
        return $storeIds;
    }

    public function getStoreFrontendUrl($storeId = 0)
    {
        return $this->scopeConfig->getValue('web/unsecure/base_url', 'store', $storeId);
    }

    public function getPreviewImagePath()
    {
        $path = $this->fileSystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            $this->mediaConfig->getBaseTmpMediaPath().'/tmp-preview.jpg'
        );

        $this->createDirIfNotExist($path);
        return $path;
    }

    public function getPreviewImageUrl()
    {
        $path = $this->storeManager->getStore(0)
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).''
                .$this->mediaConfig->getBaseTmpMediaPath().'/tmp-preview.jpg';

        return $path;
    }

    /**
     * Returns is magento 2.0 or later version
     * @return mixed
     */
    public function isM20()
    {
        return version_compare($this->productMetadata->getVersion(), '2.1.0', '<');
    }

    public function isSerialized($value)
    {
        if ((@unserialize($value)) === false) {
            return false;
        }
        return true;
    }

    public function unserialize($string)
    {
        if ($this->isSerialized($string)) {
            return unserialize($string);
        }

        return $this->jsonHelper->jsonDecode($string);
    }


}
