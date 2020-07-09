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

namespace Magetrend\GiftCard\Block\Adminhtml\Template;

use Magento\Framework\App\Filesystem\DirectoryList;

class Mteditor extends \Magento\Backend\Block\Template
{

    /**
     * Media gift card background path
     */
    const MEDIA_IMAGE_DIR = 'mt/giftcard/background';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    public $systemStore;

    /**
     * @var \Magento\Config\Model\Config\Source\Locale
     */
    public $locale;

    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    public $localeResolver;

    /**
     * @var \Magento\Email\Model\Template\Config
     */
    public $emailConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $helper;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    public $readFactory;

    /**
     * Available backgrounds image formats
     *
     * @var array
     */
    public $availableImageFormat = ['jpg', 'jpeg', 'png', 'gif'];

    /**
     * Mteditor constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Email\Model\Template\Config $emailConfig
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Config\Model\Config\Source\Locale $locale
     * @param \Magento\Framework\Locale\Resolver $localeResolver
     * @param \Magetrend\GiftCard\Helper\Data $helper
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $read
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Email\Model\Template\Config $emailConfig,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Config\Model\Config\Source\Locale $locale,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magetrend\GiftCard\Helper\Data $helper,
        \Magento\Framework\Filesystem\Directory\ReadFactory $read,
        array $data
    ) {
        $this->systemStore = $systemStore;
        $this->locale = $locale;
        $this->localeResolver = $localeResolver;
        $this->emailConfig = $emailConfig;
        $this->coreRegistry = $coreRegistry;
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->readFactory = $read;
        parent::__construct($context, $data);
    }

    /**
     * Returns javascript configuration as array
     *
     * @return array
     */
    public function getConfig()
    {
        $template = $this->getGiftCardTemplate();
        $config = [
            'text' => $this->getOptions('text'),
            'size' => $this->getOptions('size'),
            'color' => $this->getOptions('color'),
            'image' => $this->getOptions('image'),
            'design' => $template->getDesign(),
            'name' => $template->getName(),
            'store_id' => $template->getStoreId(),
            'action' => $this->getActions(),
            'formKey' => $this->formKey->getFormKey(),
            'imageList' => $this->getImageList(),
            'template_id' => $this->getTemplateId(),
        ];
        return $config;
    }

    public function getOptions($type)
    {
        $design = $this->getDesignList();
        $options = [];
        $template = $this->getGiftCardTemplate();
        $currentDesign = $template->getDesign();
        foreach ($design as $item) {
            $designCode = $item['code'];
            foreach ($item['options'] as $key => $option) {
                if (substr_count($key, $type.'_') == 1) {
                    $value = $option['value'];
                    if ($currentDesign == $designCode) {
                        $value = $template->getData($key);

                        if ($type == 'color' && substr_count($value, '#') == 0) {
                            $value = '#'.$value;
                        }
                    }
                    $options[$designCode][$key] = [
                        'code' => $key,
                        'value' => $value,
                        'label' => $option['label']
                    ];
                }
            }
        }
        return $options;
    }

    /**
     * Returns already uploaded image list
     *
     * @return array
     */
    public function getImageList()
    {
        $list = [];
        $path = $this->_filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            self::MEDIA_IMAGE_DIR
        );
        $baseUrl = $this->getStore()
            ->getBaseUrl('media').self::MEDIA_IMAGE_DIR.'/';
        $readFactory = $this->readFactory->create($path);
        if (!$readFactory->isExist()) {
            return [];
        }
        $fileList = $readFactory->read();

        if (!empty($fileList)) {
            foreach ($fileList as $entry) {
                if ($entry != '.' && $entry!= '..') {
                    $extension = explode('.', $entry);
                    if (in_array(end($extension), $this->availableImageFormat)) {
                        $list[] = $baseUrl.$entry;
                    }
                }
            }
        }
        return $list;
    }

    public function getActions()
    {
        return [
            'back' => $this->getUrl("*/*/index/"),
            'createTemplateUrl' => $this->getUrl("*/*/create/"),
            'initTemplateUrl' => $this->getUrl("*/*/template/"),
            'uploadUrl' => $this->getUrl("*/*/upload/"),
            'saveUrl' => $this->getUrl("*/*/save/"),
            'saveInfo' => $this->getUrl("*/*/saveInfo/"),
            'deleteTemplateAjax' => $this->getUrl("*/*/delete/"),
            'preview' => $this->getUrl("*/*/preview/"),
        ];
    }

    public function getDesignList()
    {
        return $this->helper->getDesignList();
    }

    public function getJsonConfig()
    {
        return json_encode($this->getConfig());
    }

    public function getLocaleOptions()
    {
        return $this->locale->toOptionArray();
    }

    public function getCurrentLocale()
    {
        return $this->localeResolver->getLocale();
    }

    public function getStoreOptions()
    {
        return $this->systemStore->getStoreValuesForForm(false, true);
    }

    public function getTemplateId()
    {
        $template = $this->getGiftCardTemplate();
        if (!$template) {
            return 0;
        }
        return $template->getId();
    }

    public function getStore()
    {
        $template = $this->getGiftCardTemplate();
        if (!$template || !$template->getId()) {
            return $this->_storeManager->getStore();
        }

        return $this->_storeManager->getStore($template->getStoreId());
    }

    public function getGiftCardTemplate()
    {
        return $this->coreRegistry->registry('current_giftcard_template');
    }
}
