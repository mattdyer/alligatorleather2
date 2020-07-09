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

namespace Magetrend\GiftCard\Block\Adminhtml\Product\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;

class GiftCardOption extends Generic implements TabInterface
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var \Magetrend\GiftCard\Model\Config\Source\GiftCardType
     */
    public $giftCardType;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    public $yesno;

    /**
     * GiftCardOption constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magetrend\GiftCard\Model\Config\Source\GiftCardType $giftCardType
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magetrend\GiftCard\Model\Config\Source\GiftCardType $giftCardType,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = []
    ) {
        $this->giftCardType = $giftCardType;
        $this->coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        $this->yesno = $yesno;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Check is readonly block
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Assign Gift Cards Set');
    }

    /**
     * Get tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Information');
    }

    /**
     * Check if tab can be displayed
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->getProduct();
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Gift Card Options')]);

       $fieldset->addField(
           'gc_type',
           'select',
           [
               'name' => 'gc_type',
               'label' =>  __('Gift Card Type'),
               'title' =>  __('Status'),
               'value' => '',
               'required' => true,
               'options' => $this->giftCardType->toArray(),
           ]
       );

       $fieldset->addField(
           'gc_use_code_generator',
           'select',
           [
               'name' => 'gc_use_code_generator',
               'label' =>  __('Use Code Generator'),
               'title' =>  __('Use Code Generator'),
               'value' => '',
               'required' => true,
               'options' => $this->yesno->toArray(),
           ]
       );

        $fieldset->addField(
           'gc_send_to_friend',
           'select',
           [
               'name' => 'gc_send_to_friend',
               'label' =>  __('Send to Friend'),
               'title' =>  __('Send to Friend'),
               'value' => '',
               'required' => true,
               'options' => $this->yesno->toArray(),
           ]
       );

       $fieldset->addField(
           'gc_send_by_post',
           'select',
           [
               'name' => 'gc_send_by_post',
               'label' =>  __('Allow Refuse Real Gift Card'),
               'title' =>  __('Allow Refuse Real Gift Card'),
               'value' => '',
               'required' => true,
               'options' => $this->yesno->toArray(),
           ]
       );

       $fieldset->addField(
           'gc_field_sender_name',
           'select',
           [
               'name' => 'gc_field_sender_name',
               'label' =>  __('Show Field: Sender Name'),
               'title' =>  __('Show Field: Sender Name'),
               'value' => '',
               'required' => true,
               'options' => $this->yesno->toArray(),
           ]
       );

       $fieldset->addField(
           'gc_field_recipient_name',
           'select',
           [
               'name' => 'gc_field_recipient_name',
               'label' =>  __('Show Field: Recipient Name'),
               'title' =>  __('Show Field: Recipient Name'),
               'value' => '',
               'required' => true,
               'options' => $this->yesno->toArray(),
           ]
       );

       $fieldset->addField(
           'gc_field_recipient_email',
           'select',
           [
               'name' => 'gc_field_recipient_email',
               'label' =>  __('Show Field: Recipient Email'),
               'title' =>  __('Show Field: Recipient Email'),
               'value' => '',
               'required' => true,
               'options' => $this->yesno->toArray(),
           ]
       );

       $fieldset->addField(
           'gc_field_message',
           'select',
           [
               'name' => 'gc_field_message',
               'label' =>  __('Show Field: Message to Friend'),
               'title' =>  __('Show Field: Message to Friend'),
               'value' => '',
               'required' => true,
               'options' => $this->yesno->toArray(),
           ]
       );


        if ($model->getId()) {
            $data = $model->getData();

            $form->setValues($data);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
