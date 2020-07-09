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

namespace Magetrend\GiftCard\Block\Cart;

use \Magento\Customer\Model\Session;

class Form extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @var \Magetrend\GiftCard\Model\Quote
     */
    public $giftCardQuote;

    /**
     * Form constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magetrend\GiftCard\Model\Quote $giftCardQuote
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magetrend\GiftCard\Model\Quote $giftCardQuote,
        array $data = []
    ) {
        $this->giftCardQuote = $giftCardQuote;
        parent::__construct($context, $customerSession, $checkoutSession, $data);
    }

    /**
     * Returns config data in json for javascript
     *
     * @return string
     */
    public function getJsonConfig()
    {
        return json_encode($this->getJsConfig());
    }

    /**
     * Returns javascript configuration in array
     *
     * @return array
     */
    public function getJsConfig()
    {
        return [
            'translate' => [
                'badGiftCardCode' => __('Bad gift card code'),
                'applyGiftCard' => __('Apply Gift Card'),
                'applying' => __('Applying...'),
            ]
        ];
    }

    /**
     * Returns applied gift card collection on cart
     *
     * @return \Magetrend\GiftCard\Model\ResourceModel\GiftCard\Collection|null
     */
    public function getCollection()
    {
        return $this->giftCardQuote->getGiftCardCollection();
    }

    /**
     * Do not show gift card form if gift card product is added to cart
     *
     * @return bool
     */
    public function showForm()
    {
        $items = $this->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            if ($item->getProductType() == \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                return false;
            };
        }
        return true;
    }
}
