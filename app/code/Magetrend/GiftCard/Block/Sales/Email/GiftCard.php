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
namespace Magetrend\GiftCard\Block\Sales\Email;

class GiftCard extends \Magento\Framework\View\Element\Template
{

    public $giftCardOrder;

    public $collection = null;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magetrend\GiftCard\Model\Order $giftCardOrder,
        array $data = []
    ) {
        $this->giftCardOrder = $giftCardOrder;
        parent::__construct($context, $data);
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
