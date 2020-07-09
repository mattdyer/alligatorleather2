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

class Create extends \Magetrend\GiftCard\Controller\Adminhtml\Template
{
    /**
     * Process action
     *
     * @return $this
     */
    public function execute()
    {
        $request = $this->getRequest();
        $design = $request->getParam('design');
        $name = $request->getParam('name');
        $storeId = $request->getParam('store_id');

        try {
            $template = $this->_objectManager->create('Magetrend\GiftCard\Model\Template')
                ->createTemplate($design, $storeId, $name);

            return $this->_jsonResponse([
                'success' => 1,
                'redirectTo' => $this->getUrl("*/*/mteditor/", ['id' => $template->getId()])
            ]);
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->_error($e->getMessage());
        }
    }
}
