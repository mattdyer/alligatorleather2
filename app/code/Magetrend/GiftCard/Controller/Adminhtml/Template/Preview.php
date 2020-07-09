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

namespace Magetrend\GiftCard\Controller\Adminhtml\Template;

class Preview extends \Magetrend\GiftCard\Controller\Adminhtml\Template
{

    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $gcHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magetrend\GiftCard\Helper\Data $gcHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->gcHelper = $gcHelper;
        parent::__construct($context, $coreRegistry, $resultJsonFactory, $storeManager);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $imageWidth = $params['image_width'];
        $designCode = $params['design'];
        $templateId = $params['template_id'];
        $patch = $this->gcHelper->getPreviewImagePath();
        $url = $this->gcHelper->getPreviewImageUrl().'?'.time();
        unset($params['image_width']);
        try {
            $previewData = $this->_objectManager->get('Magetrend\GiftCard\Helper\Data')->preparePreviewData($params);
            $template = $this->_objectManager->create('Magetrend\GiftCard\Model\Template')->load($templateId);
            $giftCard = $this->_objectManager->create('Magetrend\GiftCard\Model\GiftCard')->setData([
                'formated_value' => '150$',
                'life_time' => '365',
                'created_at' => date('Y-m-d H:i:s'),
                'expire_date' => date('Y-m-d H:i:s', strtotime('+365days')),
                'currency' => $this->_storeManager->getStore($template->getStoreId())->getCurrentCurrency()->getCode(),
                'code' => '9685-AAAA-5884',
                'value' => 50,
                'balance' => 50,
            ]);

            $template->setData($previewData);
            $draw = $this->_objectManager->create('Magetrend\GiftCard\Model\GiftCard\Draw');
            $draw->setTemplate($template);
            $draw->setGiftCard($giftCard);
            $draw->draw($params);
            $draw->resize($imageWidth);
            $draw->save($patch);

            return $this->_jsonResponse([
                'success' => 1,
                'image' => $url
            ]);
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->_error($e->getMessage());
        }
    }
}
