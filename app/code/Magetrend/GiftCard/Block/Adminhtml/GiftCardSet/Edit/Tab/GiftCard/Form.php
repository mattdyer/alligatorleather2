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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCardSet\Edit\Tab\GiftCard;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare coupon codes generation parameters form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    //@codingStandardsIgnoreLine
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $model = $this->_coreRegistry->registry('giftcard_giftcardset');
        $setId = $model->getId();
        $form->setHtmlIdPrefix('giftcard_');

        $gridBlock = $this->getLayout()
            ->createBlock('Magetrend\GiftCard\Block\Adminhtml\GiftCardSet\Edit\Tab\GiftCard\Grid');
        $gridBlockJsObject = '';
        if ($gridBlock) {
            $gridBlockJsObject = $gridBlock->getJsObjectName();
        }

        $fieldset = $form->addFieldset('information_fieldset', ['legend' => __('Gift Card Generator')]);
        $fieldset->addClass('ignore-validate');
        $fieldset->addField('gift_card_set_id', 'hidden', ['name' => 'id', 'value' => $setId]);
        $fieldset->addField(
            'qty',
            'text',
            [
                'name' => 'qty',
                'label' => __('Gift Card Qty'),
                'title' => __('Gift Card Qty'),
                'required' => true,
                'class' => 'validate-digits validate-greater-than-zero'
            ]
        );

        $idPrefix = $form->getHtmlIdPrefix();
        $generateUrl = $this->getGenerateUrl();

        $fieldset->addField(
            'generate_button',
            'note',
            [
                'text' => $this->getButtonHtml(
                    __('Generate'),
                    "generateGiftCardCodes('{$idPrefix}' ,'{$generateUrl}', '{$gridBlockJsObject}')",
                    'generate'
                )
            ]
        );

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Retrieve URL to Generate Action
     *
     * @return string
     */
    public function getGenerateUrl()
    {
        return $this->getUrl('giftcard/*/generate');
    }
}
