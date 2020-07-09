<?php

namespace Patrickfuchshofer\Giftvoucher\Controller\GiftItem;

class PdfPreview extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $_get = $this->getRequest()->getParams();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $moduleReader = $objectManager->get('\Magento\Framework\Module\Dir\Reader');
        $viewDir = $moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            'Patrickfuchshofer_Giftvoucher'
        );
        $moduleDir = $viewDir . '/..';
        $helper = $objectManager->get('\Patrickfuchshofer\Giftvoucher\Helper\Func');



        $watermark = __('This is a preview voucher.', 'gift-voucher');
        if ($helper->sanitize_text_field($_get['action']) == 'preview') {
            $watermark = __('This is a preview voucher.', 'gift-voucher');
        }

        $catid = $helper->sanitize_text_field(base64_decode($_get['catid']));
        $itemid = $helper->sanitize_text_field(base64_decode($_get['itemid']));
        $buyingfor = $helper->sanitize_text_field(base64_decode($_get['buyingfor']));
        $for = $helper->sanitize_text_field(base64_decode($_get['yourname']));
        $from = $helper->sanitize_text_field(base64_decode($_get['recipientname']));
        $value = $helper->sanitize_text_field(base64_decode($_get['totalprice']));
        $message = $helper->sanitize_textarea_field(base64_decode($_get['recipientmessage']));
        $code = '################';

        $wpdb = new \Patrickfuchshofer\Giftvoucher\Model\Libs\Wpdb();


        $setting_table     = $wpdb->prefix . 'giftvouchers_setting';
        $setting_options = $wpdb->get_row("SELECT * FROM $setting_table WHERE id = 1");
        $voucher_bgcolor = $helper->wpgv_hex2rgb($setting_options->voucher_bgcolor);
        $voucher_color = $helper->wpgv_hex2rgb($setting_options->voucher_color);
        $currency = ($setting_options->currency_position == 'Left') ? $setting_options->currency . ' ' . $value : $value . ' ' . $setting_options->currency;

        $wpgv_hide_expiry = $helper->get_option('wpgv_hide_expiry') ? $helper->get_option('wpgv_hide_expiry') : 'yes';
        $wpgv_expiry_date_format = $helper->get_option('wpgv_expiry_date_format') ? $helper->get_option('wpgv_expiry_date_format') : 'd.m.Y';

        if ($wpgv_hide_expiry == 'no') {
            $expiry = __('No Expiry', 'gift-voucher');
        } else {
            $expiry = ($setting_options->voucher_expiry_type == 'days') ? date($wpgv_expiry_date_format, strtotime('+' . $setting_options->voucher_expiry . ' days', time())) . PHP_EOL : $setting_options->voucher_expiry;
        }

        $formtype = 'item';
        $preview = true;

        if ($setting_options->is_style_choose_enable) {
            $voucher_style = $helper->sanitize_text_field(base64_decode($_get['style']));
            $style_image = $helper->get_post_meta($itemid, 'style' . ($voucher_style + 1) . '_image', true);
            $image_attributes = $helper->get_attached_file($style_image);
            $image = ($image_attributes) ? $image_attributes : $helper->get_option('wpgv_demoimageurl');
        } else {
            $voucher_style = $setting_options->voucher_style;
            $style_image = $helper->get_post_meta($itemid, 'style1_image', true);
            $image_attributes = $helper->get_attached_file($style_image);
            $image = ($image_attributes) ? $image_attributes : $helper->get_option('wpgv_demoimageurl');
        }
        $template_options = null;
        switch ($voucher_style) {
            case 0:
                $pdf = $helper->pdf_style1($image, $voucher_bgcolor, $formtype, $voucher_color, $itemid, $template_options, $buyingfor, $for, $from, $currency, $message, $expiry, $code, $setting_options, $preview, $watermark);
                break;
            case 1:
                $pdf = $helper->pdf_style2($image, $voucher_bgcolor, $formtype, $voucher_color, $itemid, $template_options, $buyingfor, $for, $from, $currency, $message, $expiry, $code, $setting_options, $preview, $watermark);
                break;
            case 2:
                $pdf = $helper->pdf_style3($image, $voucher_bgcolor, $formtype, $voucher_color, $itemid, $template_options, $buyingfor, $for, $from, $currency, $message, $expiry, $code, $setting_options, $preview, $watermark);
                break;
            default:
                $pdf = $helper->pdf_style1($image, $voucher_bgcolor, $formtype, $voucher_color, $itemid, $template_options, $buyingfor, $for, $from, $currency, $message, $expiry, $code, $setting_options, $preview, $watermark);
                break;
        }
        ob_clean();


        return $this->getResponse()->setBody($pdf->Output());

    }
}
