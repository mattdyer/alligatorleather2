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

namespace Magetrend\GiftCard\Controller\Adminhtml\GiftCard;

class Save extends \Magetrend\GiftCard\Controller\Adminhtml\GiftCard
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    public $dateFilter;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magetrend\GiftCard\Helper\Data $gcHelper
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magetrend\GiftCard\Helper\Data $gcHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
    ) {
        $this->dateFilter = $dateFilter;
        parent::__construct($context, $coreRegistry, $gcHelper, $resultPageFactory, $resultJsonFactory);
    }

    /**
     * Save Gift Card Set Save Action
     * @return $this
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $filterRules = [];
        if (isset($data['giftcard']['expire_date']) && !empty($data['giftcard']['expire_date'])) {
            $filterRules = ['expire_date' => $this->dateFilter];
        }

        $inputFilter = new \Zend_Filter_Input($filterRules, [], $data['giftcard']);
        $data = $inputFilter->getUnescaped();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Magetrend\GiftCard\Model\GiftCard');

            $id = $this->getRequest()->getParam('entity_id');
            if ($id) {
                $model->load($id);
            }
            if (isset($data['store_ids'])) {
                $data['store_ids'] = $this->gcHelper->convertStoreIdsToString($data['store_ids']);
            }

            $model->addData($data);
            try {
                $model->save();

                $this->messageManager->addSuccess(__('Information has been saved successful'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving..'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
