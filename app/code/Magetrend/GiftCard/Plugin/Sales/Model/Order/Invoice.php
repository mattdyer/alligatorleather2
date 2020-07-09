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

namespace  Magetrend\GiftCard\Plugin\Sales\Model\Order;

class Invoice
{

    public function beforePay($subject)
    {
        //echo $subject->getOrder()->getBaseTotalDue();exit;
        //$subject->getOrder()->setBaseTotalPaid($subject->getOrder()->getTotalPaid() + $subject->getOrder()->getBaseTotalDue());
    }
}
