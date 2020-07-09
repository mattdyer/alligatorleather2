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

class SendGiftCard extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var \Magento\Framework\Registry
     */
    public $registry = null;

    /**
     * PrintPdf constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $order = $this->registry->registry('current_order');
        $orderId = $order->getId();
        $url = $this->getUrl('giftcard/sales_order_view/sendGiftCard/', ['order_id' => $orderId]);

        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() != \Magetrend\GiftCard\Model\Product\Type\GiftCard::TYPE_CODE) {
                continue;
            }

            $this->addButton(
                'send_gift_card',
                [
                    'label'   => 'Send Gift Card',
                    'class'   => 'print',
                    'onclick' => 'setLocation(\'' . $url. '\')'
                ]
            );

            break;
        }

        return parent::_construct();
    }
}
