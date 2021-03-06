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

namespace Magetrend\GiftCard\Pricing\Price;

class GiftCardPriceResolver implements \Magetrend\GiftCard\Pricing\Price\PriceResolverInterface
{
    protected $_collectionFactory;
    /**
     * GiftCardPriceResolver constructor.
     * @param \Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Price on listing,
     * @param \Magento\Framework\Pricing\SaleableInterface|\Magento\Catalog\Model\Product $product
     * @return float
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resolvePrice(\Magento\Framework\Pricing\SaleableInterface $product)
    {
        $collection = $this->_collectionFactory->create()
            ->addProductFilter($product->getId())
            ->setPageSize(1)
            ->sortByPosition();

        if ($collection->getSize() == 0) {
            return $product->getData('price');
        }

        $price = $collection->getFirstItem()->getPrice();
        return (float)$price;
    }
}
