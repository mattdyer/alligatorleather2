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

namespace  Magetrend\GiftCard\Plugin\Sales\Order;

class Payment
{
    /**
     * @var \Magetrend\GiftCard\Model\GiftCardSetProductFactory
     */
    public $giftCardSetProductFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    public $request;

    /**
     * @var \Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory
     */
    public $collectionFactory;

    public $giftCardSetFactory;

    /**
     * Save constructor.
     * @param \Magetrend\GiftCard\Model\GiftCardSetProductFactory $giftCardSetProductFactory
     * @param \Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     */
    public function __construct(
        \Magetrend\GiftCard\Model\GiftCardSetProductFactory $giftCardSetProductFactory,
        \Magetrend\GiftCard\Model\GiftCardSetFactory $giftCardSetFactory,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $requestInterface
    ) {
        $this->_giftCardSetProductFactory = $giftCardSetProductFactory;
        $this->_request = $requestInterface;
        $this->_collectionFactory = $collectionFactory;
        $this->_giftCardSetFactory = $giftCardSetFactory;
    }
}
