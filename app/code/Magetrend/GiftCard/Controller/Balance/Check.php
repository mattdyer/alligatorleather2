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

namespace Magetrend\GiftCard\Controller\Balance;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Check extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;

    public $giftCardFactory;

    public $reCaptcha;

    /**
     * Check constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magetrend\GiftCard\Model\GiftCard\Balance $balance
     * @param \Magetrend\GiftCard\Helper\ReCaptcha $reCaptcha
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magetrend\GiftCard\Helper\ReCaptcha $reCaptcha
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->giftCardFactory = $giftCardFactory;
        $this->reCaptcha = $reCaptcha;
        parent::__construct($context);
    }

    /**
     * Add gift card code action
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return string
     */
    public function execute()
    {
        $response = [
            'error' => '',
            'success' => 0,
            'giftcard' => ''
        ];

        $request = $this->getRequest();
        if ($request->getPost('isAjax') != 'true') {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }

        if ($request->isPost() && $request->getParam('gift_card_code')) {
            $giftCardCode = (string)$request->getPost('gift_card_code');

            try {
                if ($this->reCaptcha->showRecaptchaOnBalanceForm($request->getPost('store_id'))) {
                    $this->reCaptcha->validate(
                        $request->getPost('captcha_response'),
                        $request->getPost('ip'),
                        $request->getPost('store_id')
                    );
                }

                if (empty($giftCardCode)) {
                    throw new LocalizedException(__('Bad gift card code'));
                }

                /**
                 * @var \Magetrend\GiftCard\Model\GiftCard $giftCard
                 */
                $giftCard = $this->giftCardFactory->create()
                    ->load($giftCardCode, 'code');

                if (!$giftCard->getId()) {
                    throw new LocalizedException(__('Bad gift card code'));
                }

                $validTo = !empty($giftCard->getFormattedExpireDate())?
                    $giftCard->getFormattedExpireDate():__('None');

                $response['success'] = 1;
                $response['giftcard'] = [
                    'balance' => $giftCard->getFormattedBalance($giftCard->getCurrency()),
                    'valid_to' => $validTo,
                    'has_expiration_date' => !empty($giftCard->getFormattedExpireDate()),
                    'status' => (string)__(ucfirst($giftCard->getStatus()))
                ];
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response['error'] = __('%1', $e->getMessage());
            } catch (\Exception $e) {
                $response['error'] = __('Ops... Something goes wrong :(');
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
