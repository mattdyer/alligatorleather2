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

/**
 * {{block class="Magetrend\GiftCard\Block\Balance" }}
 */

namespace Magetrend\GiftCard\Block;

class Balance extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'Magetrend_GiftCard::balance.phtml';

    public $moduleHelper;

    public $reCaptcha;

    public $remoteAddress;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magetrend\GiftCard\Helper\Data $moduleHelper,
        \Magetrend\GiftCard\Helper\ReCaptcha $reCaptcha,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        array $data = []
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->reCaptcha = $reCaptcha;
        $this->remoteAddress = $remoteAddress;
        parent::__construct($context, $data);
    }

    public function getFormAction()
    {
        return $this->_urlBuilder->getUrl('giftcard/balance/check');
    }

    public function getFormId()
    {
        if ($this->getData('block_id') == '') {
            return 'gift_card_balance_form';
        }

        return $this->getData('block_id');
    }

    public function getJsonConfig()
    {
        return json_encode([
            'storeId' => $this->getStoreId(),
            'ip' => (string)$this->remoteAddress->getRemoteAddress(),
            'formId' => $this->getFormId(),
            'validateCaptcha' => $this->showCaptcha()?true:false
        ]);
    }

    /**
     * Returns module helper
     * @return \Magetrend\GiftCard\Helper\Data
     */
    public function getHelper()
    {
        return $this->moduleHelper;
    }

    /**
     * Returns recaptcha helper
     * @return \Magetrend\GiftCard\Helper\ReCaptcha
     */
    public function getReCaptcha()
    {
        return $this->reCaptcha;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function showCaptcha()
    {
        return $this->reCaptcha->showRecaptchaOnBalanceForm($this->getStoreId());
    }
}
