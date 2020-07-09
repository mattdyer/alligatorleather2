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
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class ReCaptcha extends AbstractHelper
{
    const XML_PATH_RECAPTCHA_SITE_KEY = 'giftcard/recaptcha/site_key';

    const XML_PATH_RECAPTCHA_SECRET_KEY = 'giftcard/recaptcha/secret_key';

    const XML_PATH_RECAPTCHA_SHOW_BALANCE_FORM = 'giftcard/recaptcha/show_balance_form';

    public function validate($captcha, $ip, $storeId)
    {
        $request = 'https://www.google.com/recaptcha/api/siteverify?secret='
            .$this->getRecaptchaSecretKey($storeId)
            .'&response='.$captcha
            .'&remoteip='.$ip;
        $response = json_decode(file_get_contents($request), true );

        if (!isset($response['success']) || empty($response['success']) || $response['success'] != 1) {
            throw new LocalizedException(__('Captcha is not valid'));
        }

        return true;
    }

    public function showRecaptchaOnBalanceForm($storeId = 0)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECAPTCHA_SHOW_BALANCE_FORM,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getRecaptchaSiteKey($storeId = 0)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECAPTCHA_SITE_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getRecaptchaSecretKey($storeId = 0)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECAPTCHA_SECRET_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
