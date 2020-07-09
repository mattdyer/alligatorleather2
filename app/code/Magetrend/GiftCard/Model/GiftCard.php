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

namespace Magetrend\GiftCard\Model;

use Magento\Directory\Model\Currency;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magetrend\GiftCard\Api\Data\GiftCardInterface;
use Magetrend\GiftCard\Api\HistoryManagementInterface;

class GiftCard extends \Magento\Framework\Model\AbstractModel implements GiftCardInterface
{
    const STATUS_NEW = 'new';

    const STATUS_AVAILABLE = 'available';

    const STATUS_WAITING_FOR_PAYMENT = 'waiting_for_payment';

    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const TYPE_REAL = 'real';

    const TYPE_VIRTUAL = 'virtual';

    const TYPE_VIRTUAL_REAL = 'virtual-real';

    const GIFT_CARD_DIR_PDF = 'mt/giftcard/pdf';

    const GIFT_CARD_DIR_JPG = 'mt/giftcard/jpg';

    const GIFT_CARD_DIR_ZIP = 'mt/giftcard/zip';

    const MIN_GIFT_CARD_CODE_LENGTH = 4;

    public $gcHelper;

    public $transportBuilder;

    public $fileSystem;

    public $readFactory;

    public $draw;

    public $pdf;

    public $templateFactory;

    public $priceCurrencyInterface;

    public $storeManagerInterface;

    public $priceHelper;

    private $template = null;

    public $currencyFactory;

    public $orderItemFactory;

    private $giftCardSet = null;

    public $giftCardSetFactory;

    public $file;

    public $historyManagement;

    public $orderItemRepository;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\GiftCard\Helper\Data $gcHelper,
        \Magento\Framework\Filesystem $filesystem,
        \Magetrend\GiftCard\Model\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magetrend\GiftCard\Model\GiftCard\Draw $draw,
        \Magetrend\GiftCard\Model\TemplateFactory $templateFactory,
        \Magetrend\GiftCard\Model\GiftCard\Pdf $pdf,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Magetrend\GiftCard\Model\GiftCardSetFactory $giftCardSetFactory,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magetrend\GiftCard\Api\HistoryManagementInterface $historyManagement,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->gcHelper = $gcHelper;
        $this->transportBuilder = $transportBuilder;
        $this->fileSystem = $filesystem;
        $this->readFactory = $readFactory;
        $this->draw = $draw;
        $this->templateFactory = $templateFactory;
        $this->pdf = $pdf;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->priceHelper = $priceHelper;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->currencyFactory = $currencyFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->giftCardSetFactory = $giftCardSetFactory;
        $this->file = $file;
        $this->historyManagement = $historyManagement;
        $this->orderItemRepository = $orderItemRepository;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magetrend\GiftCard\Model\ResourceModel\GiftCard');
    }

    /**
     * @return \Magetrend\GiftCard\Model\GiftCardSet
     */
    public function getGiftCardSet()
    {
        if ($this->giftCardSet == null) {
            $this->giftCardSet = $this->giftCardSetFactory->create();
            if (is_numeric($this->getGiftCardSetId())) {
                $this->giftCardSet->load($this->getGiftCardSetId());
            }
        }
        return $this->giftCardSet;
    }

    /**
     * Send Gift Card via email
     * @param $templateId
     * @param $sendToEmail
     * @param $senderName
     * @param $senderEmail
     * @param $storeId
     * @param array $data
     * @return bool
     */
    public function sendGiftCard($templateId, $sendToEmail, $senderName, $senderEmail, $storeId, $data = [])
    {
        $message = $this->transportBuilder->createMessage()
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($this->gcHelper->prepareEmailData($data))
            ->setFrom([
                'name' => $senderName,
                'email' => $senderEmail
            ])
            ->addTo($sendToEmail)
            ->setReplyTo($senderEmail, $senderName);

        if ($this->isPrintable() && $this->gcHelper->canAttachPdf($storeId)) {
            $message->addPdf($this->getGiftCardPathPdf(), __('GiftCard').'-'.$this->getId().'.pdf');
        }

        $transport = $message->getTransport();
        $transport->sendMessage();
        return true;
    }

    /**
     * Returns gift card pdf format file name
     * @return string
     */
    public function getFileNamePdf()
    {
        return $this->getId().'_'.md5($this->getId().' '.$this->getCreatedAt()).'.pdf';
    }

    /**
     * Returns gift card jpg format file name
     * @return string
     */
    public function getFileNameJpg()
    {
        return $this->getId().'_'.md5($this->getId().' '.$this->getCreatedAt()).'.jpg';
    }

    /**
     * Returns gift card pdf file path
     */
    public function getGiftCardPathPdf()
    {
        $path = $this->fileSystem->getDirectoryRead(
            DirectoryList::VAR_DIR
        )->getAbsolutePath(
            self::GIFT_CARD_DIR_PDF.'/'.$this->getFileNamePdf()
        );

        $this->gcHelper->createDirIfNotExist($path);
        if ($this->file->isExists($path)) {
            $this->file->deleteFile($path);
        }

        $this->pdf->jpgToPdf($path, $this->getGiftCardPathJpg());
        return $path;
    }

    /**
     * Returns gift card jpg file path
     * @return string
     */
    public function getGiftCardPathJpg()
    {
        $path = $this->fileSystem->getDirectoryRead(
            DirectoryList::VAR_DIR
        )->getAbsolutePath(
            self::GIFT_CARD_DIR_JPG.'/'.$this->getFileNameJpg()
        );

        $this->gcHelper->createDirIfNotExist($path);
        if ($this->file->isExists($path)) {
            $this->file->deleteFile($path);
        }
        
        $this->draw->setGiftCard($this);
        $this->draw->setTemplate($this->getTemplate());
        $this->draw->draw();
        $this->draw->save($path);

        return $path;
    }

    /**
     * Returns template object
     * @return mixed
     */
    public function getTemplate()
    {
        if ($this->template == null) {
            $templateId = $this->getTemplateId();
            $this->template = $this->templateFactory->create()
                ->load($templateId);
        }
        return $this->template;
    }

    public function getBalance($currencyCode = null)
    {
        if ($currencyCode == null) {
            return $this->getData('balance');
        }
        $currency = $this->currencyFactory->create()->load($this->getCurrency());
        return $currency->convert($this->getData('balance'), $currencyCode);
    }

    public function getValue($currencyCode = null)
    {
        if ($currencyCode == null) {
            return $this->getData('value');
        }
        $currency = $this->currencyFactory->create()->load($this->getCurrency());
        return $currency->convert($this->getData('value'), $currencyCode);
    }

    /**
     * Set balance
     * @param $balance
     * @param null $currencyCode
     * @return $this
     */
    public function setBalance($balance, $currencyCode = null)
    {
        if ($currencyCode == null) {
            return $this->setData('balance', $balance);
        }
        $currency = $this->currencyFactory->create()->load($currencyCode);
        $balanceInBaseCurrency = $currency->convert($balance, $this->getCurrency());

        return $this->setData('balance', $balanceInBaseCurrency);
    }

    public function isAvailableOnStore($storeId)
    {
        $storeIdList = $this->getStoreIdList();
        if (count($storeIdList) == 0) {
            return true;
        }

        if (!in_array($storeId, $storeIdList) && !in_array('0', $storeIdList)) {
            return false;
        }
        return true;
    }

    public function getStoreIdList()
    {
        $storeIds = $this->getStoreIds();
        return $this->gcHelper->convertStoreIdsToArray($storeIds);
    }

    /**
     * Returns gift card balance with currency code
     * @param $currency
     * @return float|string
     */
    public function getFormattedBalance($currency = null)
    {
        if ($currency == null) {
            $currency = $this->storeManagerInterface->getStore()->getCurrentCurrency()->getCode();
        }
        $balance = $this->getBalance($currency);
        return $this->priceHelper->currency($balance, true, false);
    }

    public function getFormattedExpireDate()
    {
        if ($this->getExpireDate() == null) {
            return "";
        }

        $storeId = 0;
        if (is_numeric($this->getOrderItemId())) {
            $orderItem = $this->orderItemFactory->create()
                ->load($this->getOrderItemId());
            $storeId = $orderItem->getStoreId();
        }

        $validTo = $this->gcHelper->formatDate($this->getExpireDate(), $storeId);
        return $validTo;
    }

    public function getValidTo()
    {
        if (!is_numeric($this->getLifeTime())) {
            return null;
        }

        $validTo = date('Y-m-d h:i:s', strtotime('+'.$this->getLifeTime().' days', strtotime($this->getCreatedAt())));
        return $validTo;
    }

    /**
     * Returns list of store urls and names
     */
    public function getAvailableOnStores()
    {
        $storeIdsList = $this->getStoreIdList();
        if (in_array(0, $storeIdsList)) {
            $storeIdsList = $this->gcHelper->getAllStoresIdList();
        }
        $storeList = [];
        if (count($storeIdsList) > 0) {
            foreach ($storeIdsList as $storeId) {
                $store = $this->storeManagerInterface->getStore($storeId);
                $storeList[] = [
                    'name' => $store->getName(),
                    'url' => $this->gcHelper->getStoreFrontendUrl($storeId),
                ];
            }
        }
        return $storeList;
    }

    public function getFormattedValue($precision = 0)
    {
        $currency = $this->currencyFactory->create()->load($this->getCurrency());
        return $currency->formatPrecision($this->getData('value'), $precision, [], false);
    }

    public function isPrintable()
    {
        if (!$this->getTemplateId() || $this->getTemplateId() == 0) {
            return false;
        }

        return true;
    }

    public function afterSave()
    {
        if ($this->isObjectNew()) {
            $this->historyManagement->record($this, $this->getBalance(), 'Created');
        }

        return parent::afterSave();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpireDate()
    {
        return $this->getData(self::EXPIRE_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
        return $this;
    }

    /**
     * Returns cart item info
     * @return array|string
     */
    public function getAdditionalInfo($attributeCode = null)
    {
        if (!$this->getOrderItemId()) {
            return [];
        }
        try {
            $orderItem = $this->orderItemRepository->get($this->getOrderItemId());
        } catch (NoSuchEntityException $e) {
            return [];
        }

        $productOptions = $orderItem->getProductOptions();
        if (!isset($productOptions['info_buyRequest'])) {
            return [];
        }

        $buyRequest = $productOptions['info_buyRequest'];

        if (!isset($buyRequest['gift_card_attribute'])) {
            return [];
        }

        if (!$attributeCode) {
            return $buyRequest['gift_card_attribute'];
        }

        if (isset($buyRequest['gift_card_attribute'][$attributeCode])) {
            return $buyRequest['gift_card_attribute'][$attributeCode];
        }

        return '';
    }

    public function beforeDelete()
    {
        parent::beforeDelete();
        $this->historyManagement->deleteRecords($this->getId());
        return $this;
    }
}
