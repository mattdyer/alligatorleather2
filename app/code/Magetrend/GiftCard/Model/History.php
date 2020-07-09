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

namespace Magetrend\GiftCard\Model;

use Magetrend\GiftCard\Api\Data\HistoryInterface;

class History extends \Magento\Framework\Model\AbstractModel implements HistoryInterface
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magetrend\GiftCard\Model\ResourceModel\History');
    }

    public function setGiftCardId($giftCardId)
    {
        $this->setData(self::GIFT_CARD_ID, $giftCardId);
        return $this;
    }

    public function setGiftCardStatus($giftCardStatus)
    {
        $this->setData(self::GIFT_CARD_STATUS, $giftCardStatus);
        return $this;
    }

    public function setRelatedObject($relatedObject)
    {
        $this->setData(self::RELATED_OBJECT, $relatedObject);
        return $this;
    }

    public function setRelatedId($relatedId)
    {
        $this->setData(self::RELATED_ID, $relatedId);
        return $this;
    }

    public function setAmount($amount)
    {
        $this->setData(self::AMOUNT, $amount);
        return $this;
    }

    public function setBalance($balnace)
    {
        $this->setData(self::BALANCE, $balnace);
        return $this;
    }

    public function setCurrency($currency)
    {
        $this->setData(self::CURRENCY, $currency);
        return $this;
    }

    public function setMessage($message)
    {
        $this->setData(self::MESSAGE, $message);
        return $this;
    }

    public function setMessageParams($messageParams)
    {
        $this->setData(self::MESSAGE_PARAMS, $messageParams);
        return $this;
    }

    public function getGiftCardId()
    {
        return $this->getData(self::GIFT_CARD_ID);
    }

    public function getGiftCardStatus()
    {
        return $this->getData(self::GIFT_CARD_STATUS);
    }

    public function getRelatedObject()
    {
        return $this->getData(self::RELATED_OBJECT);
    }

    public function getRelatedId()
    {
        return $this->getData(self::RELATED_ID);
    }

    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    public function getMessageParams()
    {
        return $this->getData(self::MESSAGE_PARAMS);
    }

}
