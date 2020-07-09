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

namespace Magetrend\GiftCard\Api\Data;

/**
 * Interface for gift card data object
 * @api
 */
interface GiftCardInterface
{
    const STATUS = 'status';

    const BALANCE = 'balance';

    const EXPIRE_DATE = 'expire_date';

    const CURRENCY = 'currency';

    /**
     * @param string|null $currencyCode
     * @return double
     */
    public function getBalance($currencyCode = null);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getExpireDate();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param $status
     * @return \Magetrend\GiftCard\Api\Data\GiftCardInterface
     */
    public function setStatus($status);

    /**
     * @param float $balance
     * @param string|null $currencyCode
     * @return \Magetrend\GiftCard\Api\Data\GiftCardInterface
     */
    public function setBalance($balance, $currencyCode = null);

}
