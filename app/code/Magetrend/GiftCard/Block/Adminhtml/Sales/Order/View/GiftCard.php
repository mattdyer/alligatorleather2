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
namespace Magetrend\GiftCard\Block\Adminhtml\Sales\Order\View;

class GiftCard extends \Magento\Backend\Block\Template
{
    public $registry;

    public $giftCardOrder;

    public $collection = null;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magetrend\GiftCard\Model\Order $giftCardOrder,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->giftCardOrder = $giftCardOrder;
        parent::__construct($context, $data);
    }

    public function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    public function getGiftCardCollection()
    {
        if ($this->collection !== null) {
            return $this->collection;
        }

        $this->collection = $this->giftCardOrder->getGiftCardCollection($this->getOrder()->getId());
        return $this->collection;
    }
}
