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

namespace Magetrend\GiftCard\Controller\Adminhtml\Mteditor\Template;

class Template extends \Magetrend\GiftCard\Controller\Adminhtml\Template
{
    public $emailConfig = null;

    public function execute()
    {
        $template = $this->_initTemplate('id');
        $templateId = $this->getRequest()->getParam('template');
        try {
            $template->setForcedArea($templateId);
            $template->loadDefault($templateId);

            $this->getResponse()->clearHeaders();
            $this->getResponse()->setHttpResponseCode(200);

            return $this->resultJsonFactory->create()->setData([
                'template' => $template->getData(),
                'newFormKey' =>  $this->_objectManager->get('Magento\Framework\Data\Form\FormKey')->getFormKey(),
            ]);
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
    }
}
