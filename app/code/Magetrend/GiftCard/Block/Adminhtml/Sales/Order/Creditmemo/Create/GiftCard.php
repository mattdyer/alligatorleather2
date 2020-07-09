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

namespace Magetrend\GiftCard\Block\Adminhtml\Sales\Order\Creditmemo\Create;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class GiftCard extends \Magento\Backend\Block\Template
{
    /**
     * Source object
     *
     * @var \Magento\Framework\DataObject
     */
    public $source;

    /**
     * Tax config
     *
     * @var \Magento\Tax\Model\Config
     */
    public $taxConfig;

    /**
     * @var PriceCurrencyInterface
     */
    public $priceCurrency;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->taxConfig = $taxConfig;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * Initialize creditmemo agjustment totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->source = $parent->getSource();
        //@codingStandardsIgnoreLine
        $total = new \Magento\Framework\DataObject(['code' => 'giftcard', 'block_name' => $this->getNameInLayout()]);
        $parent->removeTotal('giftcard');
        $parent->addTotal($total);
        return $this;
    }

    /**
     * Get source object
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->source;
    }
}
