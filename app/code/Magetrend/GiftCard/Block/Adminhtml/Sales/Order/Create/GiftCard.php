<?php

namespace Magetrend\GiftCard\Block\Adminhtml\Sales\Order\Create;

class GiftCard extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    public $jsonHelper;

    public $giftCardQuote;

    private $giftCardCollection = null;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magetrend\GiftCard\Model\QuoteFactory $giftCardQuote,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->giftCardQuote = $giftCardQuote;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_giftcard_form');
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Gift Cards');
    }

    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-promo-quote';
    }

    public function getJsonConfig()
    {
        return $this->jsonHelper->jsonEncode($this->getConfig());
    }

    public function getConfig()
    {
        return [
            'action' => [
                'add' => $this->getUrl('giftcard/sales_order_create/addGiftCard'),
                'remove' => $this->getUrl('giftcard/sales_order_create/removeGiftCard'),
            ]
        ];
    }

    public function getGiftCardCollection()
    {
        if ($this->giftCardCollection === null) {
            $quoteId = $this->getQuote()->getId();
            $this->giftCardCollection = $this->giftCardQuote->create()->getGiftCardCollection($quoteId);
        }

        return $this->giftCardCollection;
    }
}
