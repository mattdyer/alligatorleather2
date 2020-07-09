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
 * Interface for gift card history data object
 * @api
 */
interface HistoryInterface
{
    const RELATED_OBJECT_ORDER = 'order';

    const RELATED_OBJECT_INVOICE = 'invoice';

    const RELATED_OBJECT_CREDITMEMO = 'creditmemo';

    const GIFT_CARD_ID = 'gift_card_id';

    const GIFT_CARD_STATUS = 'gift_card_status';

    const RELATED_OBJECT = 'related_object';

    const RELATED_ID = 'related_id';

    const AMOUNT = 'amount';

    const BALANCE = 'balance';

    const CURRENCY = 'currency';

    const MESSAGE = 'message';

    const MESSAGE_PARAMS = 'message_params';

    const CREATED_AT = 'created_at';

    /**
     * @param $giftCardId
     * @return HistoryInterface
     */
    public function setGiftCardId($giftCardId);

    /**
     * @param $giftCardStatus
     * @return HistoryInterface
     */
    public function setGiftCardStatus($giftCardStatus);

    /**
     * @param $relatedObject
     * @return HistoryInterface
     */
    public function setRelatedObject($relatedObject);

    /**
     * @param $relatedObject
     * @return HistoryInterface
     */
    public function setRelatedId($relatedId);

    /**
     * @param $amount
     * @return HistoryInterface
     */
    public function setAmount($amount);

    /**
     * @param $balnace
     * @return HistoryInterface
     */
    public function setBalance($balnace);

    /**
     * @param $currency
     * @return HistoryInterface
     */
    public function setCurrency($currency);

    /**
     * @param $message
     * @return HistoryInterface
     */
    public function setMessage($message);

    /**
     * @param $messageParams
     * @return HistoryInterface
     */
    public function setMessageParams($messageParams);

    public function getGiftCardId();

    public function getGiftCardStatus();

    public function getRelatedObject();

    public function getRelatedId();

    public function getAmount();

    public function getBalance();

    public function getCurrency();

    public function getMessage();

    public function getMessageParams();

}
