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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCard\Import\Tab\Upload;

use \Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{
    /**
     * Prepare form
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/upload'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('File  Upload')]);

        $fieldset->addField(
            'data_file',
            'file',
            [
                'name' => 'file',
                'label' =>  __('File'),
                'title' =>  __('File'),
                'value' => '',
                'required' => true,
                'note' => __(
                    '.csv file example you can download here: <a href="'
                    .$this->_assetRepo->getUrl('Magetrend_GiftCard::csv/import_example.csv')
                    .'">import_example.csv</a>'
                ),
            ]
        );

        $fieldset->addField(
            'upload',
            'submit',
            [
                'name' => 'upoad_button',
                'label' => '',
                'title' => '',
                'required' => false,
                'value' => __('Upload File'),
                'class' => 'action-default scalable action-primary',
                'style' => 'width: auto'

            ]
        );
        $form->setUseContainer(true);
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
        return __('File Upload');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('File Upload');
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
