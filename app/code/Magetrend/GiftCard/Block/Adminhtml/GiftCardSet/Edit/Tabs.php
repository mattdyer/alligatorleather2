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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCardSet\Edit;

use \Magento\Backend\Model\Auth\Session;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * Tabs constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param Session $authSession
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        Session $authSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Update tab configuration
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftcardset_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Gift Card Set Information'));
    }

    /**
     * Add tabs blocks
     *
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function _beforeToHtml()
    {
        $this->addTab(
            'general_section',
            [
                'label' => __('General Settings'),
                'title' => __('General Settings'),
                'active' => true,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\GiftCard\Block\Adminhtml\GiftCardSet\Edit\Tab\General'
                )->toHtml()
            ]
        );

        $this->addTab(
            'code_section',
            [
                'label' => __('Code Settings'),
                'title' => __('Code Settings'),
                'active' => false,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\GiftCard\Block\Adminhtml\GiftCardSet\Edit\Tab\Code'
                )->toHtml()
            ]
        );

        $model = $this->coreRegistry->registry('giftcard_giftcardset');
        if ($model->getId()) {
            $this->addTab(
                'gift_card_section',
                [
                    'label' => __('Gift Card Generator'),
                    'title' => __('Gift Card Generator'),
                    'active' => false,
                    'content' => $this->getLayout()->createBlock(
                        'Magetrend\GiftCard\Block\Adminhtml\GiftCardSet\Edit\Tab\GiftCard'
                    )->toHtml()
                ]
            );
        }

        return parent::_beforeToHtml();
    }
}
