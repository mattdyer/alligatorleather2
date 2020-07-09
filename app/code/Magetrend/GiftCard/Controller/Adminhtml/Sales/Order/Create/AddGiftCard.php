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

namespace Magetrend\GiftCard\Controller\Adminhtml\Sales\Order\Create;

class AddGiftCard extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
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

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magetrend\GiftCard\Model\Quote $giftCardQuote,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->giftCardQuote = $giftCardQuote;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context, $productHelper, $escaper, $resultPageFactory, $resultForwardFactory);
    }

    public function execute()
    {
        $response = [
            'errorMsg' => '',
            'successMsg' => '',
            'couponCode' => ''
        ];

        if ($this->getRequest()->isPost() && $this->getRequest()->getParam('gift_card_code')) {
            $giftCardCode = (string)$this->getRequest()->getPost('gift_card_code');
            $quote = $this->_getQuote();
            try {
                if (!$this->giftCardQuote->validateGiftCardCode($giftCardCode, $quote->getId())) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Bad gift card code'));
                }
                $this->giftCardQuote->addGiftCardToQuote($giftCardCode, $quote->getId());
                $this->messageManager->addSuccess(__('Gift card has been added to cart'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError(__('%1', $e->getMessage()));
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We cannot apply the gift card code.'));
            }
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData([
            'success' => true
        ]);
    }
}