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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCard\Import\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;

class Defaults extends Generic implements TabInterface
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
     * @var \Magetrend\GiftCard\Model\Config\Source\Status
     */
    public $statusOption;

    /**
     * @var \Magetrend\GiftCard\Model\Config\Source\GiftCardSets
     */
    public $giftCardSetsOption;

    /**
     * Defaults constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magetrend\GiftCard\Model\Config\Source\Currency $currency
     * @param \Magetrend\GiftCard\Model\Config\Source\Template $template
     * @param \Magetrend\GiftCard\Model\Config\Source\GiftCardSets $giftCardSets
     * @param \Magetrend\GiftCard\Model\Config\Source\Status $status
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magetrend\GiftCard\Model\Config\Source\Currency $currency,
        \Magetrend\GiftCard\Model\Config\Source\Template $template,
        \Magetrend\GiftCard\Model\Config\Source\GiftCardSets $giftCardSets,
        \Magetrend\GiftCard\Model\Config\Source\Status $status,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->currency = $currency;
        $this->templateOption = $template;
        $this->statusOption = $status;
        $this->giftCardSetsOption = $giftCardSets;
        parent::__construct($context, $registry, $formFactory, $data);
    }

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
                    'id' => 'finish_form',
                    'action' => $this->getUrl('*/*/importSave'),
                    'method' => 'post',
                ]
            ]
        );
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Gift Card Information')]);

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' =>  __('Status'),
                'title' =>  __('Status'),
                'value' => '',
                'required' => true,
                'options' => $this->statusOption->toArray(),
            ]
        );

        $fieldset->addField(
            'gift_card_set_id',
            'select',
            [
                'name' => 'gift_card_set_id',
                'label' =>  __('Gift Card Set'),
                'title' =>  __('Gift Card Set'),
                'value' => '',
                'required' => false,
                'options' => $this->giftCardSetsOption->toArray(true),
            ]
        );

        $fieldset->addField(
            'template_id',
            'select',
            [
                'name' => 'template_id',
                'label' =>  __('Gift Card Template'),
                'title' =>  __('Gift Card Template'),
                'value' => '',
                'required' => false,
                'options' => $this->templateOption->toArray(true),
            ]
        );

        $fieldset->addField(
            'currency',
            'select',
            [
                'name' => 'currency',
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
                'name' => 'value',
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
                'name' => 'balance',
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
                'name' => 'store_ids[]',
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
                'name' => 'life_time',
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
                'name' => 'expire_date',
                'label' =>  __('Expire Date'),
                'title' =>  __('Expire Date'),
                'note' => __('Gift Card will not be available to redeem after this date'),
                'required' => false,
                'disabled' => false,
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
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
