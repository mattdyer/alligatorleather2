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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;

class General extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    public $systemStore;

    /**
     * @var \Magetrend\GiftCard\Model\Config\Source\Currency
     */
    public $currency;

    /**
     * @var \Magetrend\GiftCard\Model\Config\Source\Template
     */
    public $templateOption;

    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $gcHelper;

    /**
     * @var \Magetrend\GiftCard\Model\Config\Source\Status
     */
    public $statusOption;

    /**
     * General constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magetrend\GiftCard\Model\Config\Source\Currency $currency
     * @param \Magetrend\GiftCard\Model\Config\Source\Template $template
     * @param \Magetrend\GiftCard\Model\Config\Source\Status $status
     * @param \Magetrend\GiftCard\Helper\Data $gcHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magetrend\GiftCard\Model\Config\Source\Currency $currency,
        \Magetrend\GiftCard\Model\Config\Source\Template $template,
        \Magetrend\GiftCard\Model\Config\Source\Status $status,
        \Magetrend\GiftCard\Helper\Data $gcHelper,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->currency = $currency;
        $this->templateOption = $template;
        $this->statusOption = $status;
        $this->gcHelper = $gcHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    //@codingStandardsIgnoreLine
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('giftcard_giftcard');
        $isElementDisabled = false;
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);

        if ($model->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                ['name' => 'giftcard[entity_id]', 'value' => '']
            );
        }

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'giftcard[status]',
                'label' =>  __('Status'),
                'title' =>  __('Status'),
                'value' => '',
                'required' => true,
                'options' => $this->statusOption->toArray(),
            ]
        );

        $fieldset->addField(
            'code',
            'text',
            [
                'name' => 'giftcard[code]',
                'label' => __('Gift Card Code'),
                'title' => __('Gift Card Code'),
                'required' => true,
                'disabled' => false
            ]
        );

        $fieldset->addField(
            'template_id',
            'select',
            [
                'name' => 'giftcard[template_id]',
                'label' =>  __('Gift Card Template'),
                'title' =>  __('Gift Card Template'),
                'value' => '',
                'required' => true,
                'options' => $this->templateOption->toArray(),
            ]
        );

        $fieldset->addField(
            'currency',
            'select',
            [
                'name' => 'giftcard[currency]',
                'label' =>  __('Currency'),
                'title' =>  __('Currency'),
                'value' => '',
                'required' => true,
                'options' => $this->currency->toArray(),
            ]
        );

        $fieldset->addField(
            'value',
            'text',
            [
                'name' => 'giftcard[value]',
                'label' =>  __('Initial Value'),
                'title' =>  __('Initial Value'),
                'value' => '0',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'balance',
            'text',
            [
                'name' => 'giftcard[balance]',
                'label' =>  __('Gift Card Balance'),
                'title' =>  __('Gift Card Balance'),
                'value' => '0',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'store_ids',
            'multiselect',
            [
                'name' => 'giftcard[store_ids][]',
                'label' => __('Available on Stores'),
                'title' => __('Available on Stores'),
                'required' => true,
                'values' => $this->systemStore->getStoreValuesForForm(false, true),
                'value' => 0,
            ]
        );

        $fieldset->addField(
            'life_time',
            'text',
            [
                'name' => 'giftcard[life_time]',
                'label' =>  __('Gift Card Life Time'),
                'title' =>  __('Gift Card Life Time'),
                'value' => '365',
                'note' => __(
                    'How many days gift card will be active after purchase.
                    0 - no expiration period. Default: 365 (1 year)'
                ),
            ]
        );

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
        $fieldset->addField(
            'expire_date',
            'date',
            [
                'name' => 'giftcard[expire_date]',
                'label' =>  __('Expire Date'),
                'title' =>  __('Expire Date'),
                'note' => __('Gift Card will not be available to redeem after this date'),
                'required' => false,
                'disabled' => false,
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
            ]
        );

        if ($model->getId()) {
            $data = $model->getData();
            $data['value'] = number_format($data['value'], 2);
            $data['balance'] = number_format($data['balance'], 2);
            $data['store_ids'] = $this->gcHelper->convertStoreIdsToArray($data['store_ids']);
            $form->setValues($data);
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
        return __('General Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General Settings');
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
