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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCard;

class Import extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize import block
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magetrend_GiftCard';
        $this->_controller = 'adminhtml_giftCard';

        parent::_construct();

        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
        $filePath = $this->coreRegistry->registry('giftcard_file_path');
        if (!empty($filePath)) {
            $this->buttonList->add(
                'save',
                [
                    'label' => __('Confirm and Finish Import'),
                    'class' => 'save primary',
                    'onclick' => 'document.getElementById("finish_form").submit();'
                ],
                -100
            );
        }
    }

    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('giftcard_giftcardset')->getId()) {
            return __(
                "Edit GiftCardSet '%1'",
                $this->escapeHtml($this->coreRegistry->registry('giftcard_giftcardset')->getTitle())
            );
        } else {
            return __('New GiftCardSet');
        }
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    public function _getSaveAndContinueUrl()
    {
        return $this->getUrl('giftcard/*/upload', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }
}
