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

class GiftCardSet extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $gcHelper;

    /**
     * @var GiftCard\MassGenerator
     */
    public $massGenerator;

    /**
     * @var ResourceModel\GiftCard\CollectionFactory
     */
    public $giftCardCollectionFactory;

    /**
     * @var GiftCardFactory
     */
    public $giftCardFactory;

    /**
     * GiftCardSet constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magetrend\GiftCard\Helper\Data $helper
     * @param GiftCard\MassGenerator $massGenerator
     * @param ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory
     * @param GiftCardFactory $giftCardFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\GiftCard\Helper\Data $helper,
        \Magetrend\GiftCard\Model\GiftCard\MassGenerator $massGenerator,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory,
        \Magetrend\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->gcHelper = $helper;
        $this->massGenerator = $massGenerator;
        $this->giftCardCollectionFactory = $giftCardCollectionFactory;
        $this->giftCardFactory = $giftCardFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\GiftCard\Model\ResourceModel\GiftCardSet');
    }

    /**
     * Returns gift card generator object
     *
     * @return GiftCard\MassGenerator
     */
    public function getMassGenerator()
    {
        $this->massGenerator->setGiftCardSet($this);
        return $this->massGenerator;
    }

    /**
     * Returns formated gift card value
     *
     * @return mixed
     */
    public function getFormattedValue()
    {
        return $this->gcHelper->formatPrice($this->getValue(), $this->getCurrency());
    }

    /**
     * Makes gift card reservation
     *
     * @param $qty
     * @param $quoteItemId
     * @return bool
     */
    public function makeReservation($qty, $quoteItemId)
    {
        $availableGiftCard = $this->getAvailableGiftCardCollection($qty);
        if ($qty > $availableGiftCard->getSize()) {
            return false;
        }
        foreach ($availableGiftCard as $giftCard) {
            $giftCard->setStatus(\Magetrend\GiftCard\Model\GiftCard::STATUS_WAITING_FOR_PAYMENT)
                ->setQuoteItemId($quoteItemId);
        }
        $availableGiftCard->walk('save');
        return true;
    }

    /**
     * Generates virtual gift card
     *
     * @param $quoteItemId
     * @param int $qty
     * @return bool
     */
    public function createGiftCard($quoteItemId, $qty = 1)
    {
        $this->massGenerator->setGiftCardSet($this);
        $this->massGenerator->generate($qty, [
            'quote_item_id' => $quoteItemId,
            'status' => \Magetrend\GiftCard\Model\GiftCard::STATUS_WAITING_FOR_PAYMENT
        ]);
        return true;
    }

    public function getAvailableGiftCardCollection($qty = 0)
    {
        $collection = $this->giftCardCollectionFactory->create()
            ->addFieldToFilter('gift_card_set_id', $this->getId())
            ->addFieldToFilter('status', \Magetrend\GiftCard\Model\GiftCard::STATUS_AVAILABLE);
        if ($qty > 0) {
            $collection
                ->setPageSize($qty)
                ->setCurPage(1);
        }
        return $collection;
    }
}
