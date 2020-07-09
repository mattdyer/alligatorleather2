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

use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Price\AbstractPrice;

/**
 * Class RegularPrice
 */
class RegularPrice extends AbstractPrice implements GiftCardRegularPriceInterface
{
    /**
     * Price type
     */
    const PRICE_CODE = 'regular_price';

    /**
     * @var \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public $maxRegularAmount;

    /**
     * @var \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public $minRegularAmount;

    /**
     * @var array
     */
    public $values = [];

    /** @var PriceResolverInterface */
    public $priceResolver;

    public $collectionFactory;

    /**
     * @param \Magento\Framework\Pricing\SaleableInterface $saleableItem
     * @param float $quantity
     * @param \Magento\Framework\Pricing\Adjustment\CalculatorInterface $calculator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param PriceResolverInterface $priceResolver
     */
    public function __construct(
        \Magento\Framework\Pricing\SaleableInterface $saleableItem,
        $quantity,
        \Magento\Framework\Pricing\Adjustment\CalculatorInterface $calculator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        PriceResolverInterface $priceResolver,
        \Magetrend\GiftCard\Model\ResourceModel\GiftCardSetProduct\CollectionFactory $collectionFactory
    ) {
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency);
        $this->priceResolver = $priceResolver;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (!isset($this->values[$this->product->getId()])) {
            $this->values[$this->product->getId()] = $this->getMinValue($this->product);
        }

        return $this->values[$this->product->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getMinRegularAmount();
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxRegularAmount()
    {
        if (null === $this->maxRegularAmount) {
            $this->maxRegularAmount = $this->doGetMaxRegularAmount();
            $this->maxRegularAmount = $this->doGetMaxRegularAmount() ?: false;
        }
        return $this->maxRegularAmount;
    }

    /**
     * Get max regular amount. Template method
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function doGetMaxRegularAmount()
    {
        $maxAmount = null;
        foreach ($this->getUsedProducts() as $product) {
            $childPriceAmount = $product->getPriceInfo()->getPrice(self::PRICE_CODE)->getAmount();
            if (!$maxAmount || ($childPriceAmount->getValue() > $maxAmount->getValue())) {
                $maxAmount = $childPriceAmount;
            }
        }
        return $maxAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinRegularAmount()
    {
        return parent::getAmount();
        if (null === $this->minRegularAmount) {
            $this->minRegularAmount = $this->doGetMinRegularAmount() ?: parent::getAmount();
        }
        return $this->minRegularAmount;
    }

    /**
     * Get min regular amount. Template method
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function doGetMinRegularAmount()
    {
        $minAmount = null;
        foreach ($this->getUsedProducts() as $product) {
            $childPriceAmount = $product->getPriceInfo()->getPrice(self::PRICE_CODE)->getAmount();
            if (!$minAmount || ($childPriceAmount->getValue() < $minAmount->getValue())) {
                $minAmount = $childPriceAmount;
            }
        }
        return $minAmount;
    }

    public function getMinValue(\Magento\Framework\Pricing\SaleableInterface $product)
    {
        $collection = $this->collectionFactory->create()
            ->addProductFilter($product->getId())
            ->joinSets('value')
            ->setPageSize(1)
            ->sortByPosition();

        if ($collection->getSize() == 0) {
            return $product->getData('price');
        }

        $price = $collection->getFirstItem()->getValue();
        return (float)$price;
    }
}
