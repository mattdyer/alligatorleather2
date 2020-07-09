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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCardSet\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;

class Code extends Generic implements TabInterface
{
    /**
     * @var \Magetrend\GiftCard\Model\Config\Source\Format
     */
    public $format;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magetrend\GiftCard\Model\Config\Source\Format $format,
        array $data = []
    ) {
        $this->format = $format;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare Form
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('giftcard_giftcardset');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Gift Card Code Settings')]);

        $fieldset->addField(
            'code_length',
            'text',
            [
                'name' => 'code_length',
                'label' => __('Code Length'),
                'title' => __('Code Length'),
                'required' => false,
                'disabled' => false,
                'value' => 8
            ]
        );
        $fieldset->addField(
            'code_dash',
            'text',
            [
                'name' => 'code_dash',
                'label' => __('Dash'),
                'title' => __('Dash'),
                'note' => __('Add Dash Every Time after X Symbols'),
                'required' => false,
                'disabled' => false,
                'value' => 4
            ]
        );
        $fieldset->addField(
            'code_format',
            'select',
            [
                'name' => 'code_format',
                'label' =>  __('Code Format'),
                'title' =>  __('Code Format'),
                'value' => 'alphanum',
                'options' => $this->format->toArray(),
            ]
        );

        $fieldset->addField(
            'code_prefix',
            'text',
            [
                'name' => 'code_prefix',
                'label' => __('Code Prefix'),
                'title' => __('Code Prefix'),
                'required' => false,
                'disabled' => false,
                'value' => ''
            ]
        );

        $fieldset->addField(
            'code_suffix',
            'text',
            [
                'name' => 'code_suffix',
                'label' => __('Code Suffix'),
                'title' => __('Code Suffix'),
                'required' => false,
                'disabled' => false,
            ]
        );

        if ($model->getId()) {
            $form->setValues($model->getData());
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Popup Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Popup Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
