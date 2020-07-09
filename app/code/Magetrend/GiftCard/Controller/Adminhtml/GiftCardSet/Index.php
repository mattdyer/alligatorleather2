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

namespace Magetrend\GiftCard\Controller\Adminhtml\GiftCardSet;

class Index extends \Magetrend\GiftCard\Controller\Adminhtml\GiftCardSet
{
    /**
     * Gift Card Set Page
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();

        $this->_setActiveMenu('Magetrend_GiftCard::giftcardset_index');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Gift Card Sets'));

        $this->_addBreadcrumb(__('Gift Card'), __('Gift Card'));
        $this->_addBreadcrumb(__('Sets'), __('Sets'));

        $this->_view->renderLayout();
    }
}
