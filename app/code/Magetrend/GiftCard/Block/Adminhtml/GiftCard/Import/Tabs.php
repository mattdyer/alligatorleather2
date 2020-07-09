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

namespace Magetrend\GiftCard\Block\Adminhtml\GiftCard\Import;

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
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftcardset_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Import Data'));
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
            'upload_section',
            [
                'label' => __('File Upload'),
                'title' => __('File Upload'),
                'active' => true,
                'content' => $this->getLayout()->createBlock(
                    'Magetrend\GiftCard\Block\Adminhtml\GiftCard\Import\Tab\Upload'
                )->toHtml()
            ]
        );

        $filePath = $this->coreRegistry->registry('giftcard_file_path');
        if (!empty($filePath)) {
            $this->addTab(
                'form_section',
                [
                    'label' => __('Gift Card Information'),
                    'title' => __('Gift Card Information'),
                    'active' => false,
                    'content' => $this->getLayout()->createBlock(
                        'Magetrend\GiftCard\Block\Adminhtml\GiftCard\Import\Tab\Defaults'
                    )->toHtml()
                ]
            );
        }

        return parent::_beforeToHtml();
    }
}
