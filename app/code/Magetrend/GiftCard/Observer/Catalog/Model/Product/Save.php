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

namespace Magetrend\GiftCard\Observer\Catalog\Model\Product;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class Save implements ObserverInterface
{

    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $helper;

    public $request;

    /**
     * Save constructor.
     * @param \Magetrend\GiftCard\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     */
    public function __construct(
        \Magetrend\GiftCard\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $requestInterface
    ) {
        $this->helper = $helper;
        $this->request = $requestInterface;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (!$this->helper->isM20() || !$this->request->isPost()) {
            return;
        }

        $product = $observer->getProduct();

        $fields = [
            'gc_type',
            'gc_use_code_generator',
            'gc_send_to_friend',
            'gc_send_by_post',
            'gc_field_sender_name',
            'gc_field_recipient_name',
            'gc_field_recipient_email',
            'gc_field_message'
        ];

        foreach ($fields as $field) {
            $value = $this->request->getPost($field, false);
            if ($value === false) {
                continue;
            }

            $product->setData($field, $value);
        }
    }
}