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

namespace Magetrend\GiftCard\Model\Checkout\Cart;

use Magetrend\GiftCard\Api\GiftCardManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestGiftCardManagement implements \Magetrend\GiftCard\Api\GuestGiftCardManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CouponManagementInterface
     */
    private $giftCardManagement;

    /**
     * Constructs a coupon read service object.
     *
     * @param GiftCardManagementInterface $giftCardManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        GiftCardManagementInterface $giftCardManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->giftCardManagement = $giftCardManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function add($cartId, $giftCardCode)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->giftCardManagement->add($quoteIdMask->getQuoteId(), $giftCardCode);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId, $giftCardCode)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->giftCardManagement->remove($quoteIdMask->getQuoteId(), $giftCardCode);
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->giftCardManagement->get($quoteIdMask->getQuoteId());
    }
}
