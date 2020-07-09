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

namespace Magetrend\GiftCard\Api;

/**
 * Interface for gift card in quote
 * @api
 */
interface GiftCardManagementInterface
{
    /**
     * Adds a gift card by code to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param string $giftCardCode The gift card code data.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified coupon could not be added.
     */
    public function add($cartId, $giftCardCode);

    /**
     * Removes a gift card by code from a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param string $giftCardCode The gift card code data.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified coupon could not be added.
     */
    public function remove($cartId, $giftCardCode);

    /**
     * Get a gift cards list by quote id.
     *
     * @param int $cartId The cart ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified coupon could not be added.
     */
    public function get($cartId);
}
