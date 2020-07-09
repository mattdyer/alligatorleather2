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

namespace Magetrend\GiftCard\Controller\Cart;

use Magento\Checkout\Model\Session;

class Remove extends \Magento\Checkout\Controller\Cart
{

    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    public $quoteRepository;

    /**
     * @var \Magetrend\GiftCard\Model\Quote
     */
    public $giftCardQuote;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;

    /**
     * Remove constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magetrend\GiftCard\Model\Quote $giftCardQuote
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magetrend\GiftCard\Model\Quote $giftCardQuote,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->giftCardQuote = $giftCardQuote;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart);
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
            'errorMsg' => '',
            'successMsg' => '',
            'couponCode' => ''
        ];

        if (is_numeric($this->getRequest()->getParam('id'))) {
            $giftCardId = (string)$this->getRequest()->getParam('id');
            $cartQuote = $this->cart->getQuote();
            try {
                $this->giftCardQuote->removeGiftCardFromQuote($giftCardId);
                $this->quoteRepository->save($cartQuote);
                $response['successMsg'] = __('Gift card has been removed from cart');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response['errorMsg'] = __('%1', $e->getMessage());
            } catch (\Exception $e) {
                $response['errorMsg'] = __('Ops... Something goes wrong');
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }

        if ($this->getRequest()->getParam('isAjax') == 'true') {
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData($response);
        } else {
            if (isset($response['successMsg']) && !empty($response['successMsg'])) {
                $this->messageManager->addSuccess($response['successMsg']);
            } elseif (isset($response['errorMsg']) && !empty($response['errorMsg'])) {
                $this->messageManager->addError($response['errorMsg']);
            }
            return $this->_goBack();
        }
    }
}
