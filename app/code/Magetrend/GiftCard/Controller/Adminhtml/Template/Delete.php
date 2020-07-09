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

namespace Magetrend\GiftCard\Controller\Adminhtml\Template;

class Delete extends \Magetrend\GiftCard\Controller\Adminhtml\Template
{
    public function execute()
    {
        $request = $this->getRequest();
        $templateId = $request->getParam('id');
        try {
            $model = $this->_objectManager->create('Magetrend\GiftCard\Model\Template')
                ->load($templateId);
            $model->setIsDeleted(1)
                ->save();
            return $this->_jsonResponse(['success' => 1]);
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->_error($e->getMessage());
        }
    }
}
