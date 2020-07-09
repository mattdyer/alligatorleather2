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

namespace Magetrend\GiftCard\Model\GiftCard;

class MassGenerator
{
    /**
     * How many times try to generate code
     */
    const MAX_GENERATE_ATTEMPTS = 10;

    /**
     * Gift card code delimiter
     */
    const DELIMITER = '-';

    /**
     * @var \Magetrend\GiftCard\Model\GiftCardSet|null
     */
    private $giftCardSet = null;

    /**
     * @var int
     */
    private $generatedCount = 0;

    /**
     * @var \Magetrend\GiftCard\Model\GiftCardFactory
     */
    public $giftCardFactory;

    /**
     * @var string
     */
    private $format = 'alphanum';

    /**
     * @var int
     */
    private $length = 8;

    /**
     * @var int
     */
    private $dash = 4;

    /**
     * @var string
     */
    private $suffix = '';

    /**
     * @var string
     */
    private $prefix = '';

    /**
     * MassGenerator constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->giftCardFactory = $giftCardFactory;
    }

    /**
     * Assign gift card set for gift card generator
     * @param \Magetrend\GiftCard\Model\GiftCardSet $giftCardSet
     */
    public function setGiftCardSet(\Magetrend\GiftCard\Model\GiftCardSet $giftCardSet)
    {
        $this->format = $giftCardSet->getCodeFormat();
        $this->length = $giftCardSet->getCodeLength();
        $this->dash = $giftCardSet->getCodeDash();
        $this->suffix = $giftCardSet->getCodeSuffix();
        $this->prefix = $giftCardSet->getCodePrefix();
        $this->giftCardSet = $giftCardSet;
    }

    /**
     * Returns assigned gift card set
     * @return \Magetrend\GiftCard\Model\GiftCardSet|null
     */
    public function getGiftCardSet()
    {
        return $this->giftCardSet;
    }

    /**
     * Generate gift cards
     * @param $size
     * @param $customData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generate($size, $customData = [])
    {
        $this->generatedCount = 0;
        $giftCardSet = $this->getGiftCardSet();
        $maxAttempts = self::MAX_GENERATE_ATTEMPTS;
        $code = '';

        for ($i = 0; $i < $size; $i++) {
            $attempt = 0;
            $giftCard = $this->giftCardFactory->create();

            while ($attempt < $maxAttempts) {
                $code = $this->generateCode();
                $giftCard->load($code, 'code');
                if (!$giftCard->getId()) {
                    break;
                }
                $attempt++;
                if ($attempt >= $maxAttempts) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Unable to create requested Gift Card Qty. Please increase code length and try again.')
                    );
                }
            }

            $giftCard->setCode($code);
            $giftCard->setStatus(\Magetrend\GiftCard\Model\GiftCard::STATUS_NEW);

            if ($giftCardSet->getId()) {
                $giftCard->setGiftCardSetId($giftCardSet->getId());
                $giftCard->setBalance($giftCardSet->getValue());
                $giftCard->setValue($giftCardSet->getValue());
                $giftCard->setLifeTime($giftCardSet->getLifeTime());
                $giftCard->setCurrency($giftCardSet->getCurrency());
                $giftCard->setTemplateId($giftCardSet->getTemplateId());
                $giftCard->setStoreIds($giftCardSet->getStoreIds());
            }

            if (count($customData) > 0) {
                foreach ($customData as $key => $value) {
                    $giftCard->setData($key, $value);
                }
            }
            $giftCard->save();
            $this->generatedCount++;
        }
    }

    /**
     * Generate gift card code
     * @return string
     */
    public function generateCode()
    {
        $format  = $this->getFormat();
        $length  = max(1, (int) $this->getLength());
        $split   = max(0, (int) $this->getDash());
        $suffix  = $this->getSuffix();
        $prefix  = $this->getPrefix();

        $splitChar = self::DELIMITER;
        $charset = $this->getCharset($format);

        $code = '';
        $charsetSize = count($charset);
        for ($i=0; $i < $length; $i++) {
            $char = $charset[mt_rand(0, $charsetSize - 1)];
            if ($split > 0 && ($i % $split) == 0 && $i != 0) {
                $char = $splitChar . $char;
            }
            $code .= $char;
        }

        $code = $prefix . $code . $suffix;
        return $code;
    }

    /**
     * Returns gift card code format
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Returns gift card code length
     * @return string
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Returns dash interval
     * @return integer
     */
    public function getDash()
    {
        return $this->dash;
    }

    /**
     * Returns gift card code suffix
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * Returns gift card code prefix
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Returns available character list
     * @param $format
     * @return array
     */
    public function getCharset($format)
    {
        $chars = '';
        $ab = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '01234567890';

        switch ($format) {
            case 'alphanum':
                $chars = $ab.$num;
                break;
            case 'alpha':
                $chars = $ab;
                break;
            case 'num':
                $chars = $num;
                break;
        }
        return str_split($chars);
    }

    /**
     * Returns generated gift card qty
     * @return int
     */
    public function getGeneratedCount()
    {
        return $this->generatedCount;
    }
}
