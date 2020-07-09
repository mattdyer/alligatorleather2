<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Patrickfuchshofer\Giftvoucher\Controller\GiftItemPage;

class Ajax extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();


        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $moduleReader = $objectManager->get('\Magento\Framework\Module\Dir\Reader');
        $helper = $objectManager->get('\Patrickfuchshofer\Giftvoucher\Helper\Func');
        $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();

        $viewDir = $moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            'Patrickfuchshofer_Giftvoucher'
        );

        $moduleDir = $viewDir . '/..';

        if ($post['action'] == 'wpgv_doajax_get_itemcat_image') {

            $category = $objectManager->create('Magento\Catalog\Model\Category')->load($post['catid']);
            $object = $objectManager->get('\Magento\Framework\View\Asset\Repository');
            if ($category->getImage()) {
                $image = $category->getImageUrl();
            } else {
                $image = $object->getUrl("Patrickfuchshofer_Giftvoucher::img/demo.png");
            }
            $result = $this->resultJsonFactory->create();

            $data = [
                'image' => $image
            ];

            return $result->setData($data);
        } else if ($post['action'] == 'wpgv_doajax_get_item_data') {
            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($post['itemid']);
            $galleryEntries = $product->getMediaGalleryEntries();
            $object = $objectManager->get('\Magento\Framework\View\Asset\Repository');
            $images = [];
            for ($i = 1; $i <= 3; $i++) {
                $image = array_filter($galleryEntries, function ($tmp) use ($i) {
                    return in_array('image_style_' . $i, $tmp->getTypes());
                });
                $image = end($image);

                if ($image) {
                    $images[] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $image->getFile();
                } else {
                    $images[] = $object->getUrl("Patrickfuchshofer_Giftvoucher::img/demo.png");
                }
            }

            $result = $this->resultJsonFactory->create();

            $data = [
                'description' => $product->getDescription(),
                'images' => $images,
                'price' => $product->getPrice(),
                'special_price' => null,
                'title' => $product->getName(),
            ];

            return $result->setData($data);
        } else if ($post['action'] == 'wpgv_doajax_item_pdf_save_func') {


            $catid = $helper->sanitize_text_field(base64_decode($post['catid']));
            $itemid = $helper->sanitize_text_field(base64_decode($post['itemid']));
            $buyingfor = $helper->sanitize_text_field(base64_decode($post['buyingfor']));
            $for = $helper->sanitize_text_field(base64_decode($post['yourname']));
            $from = isset($post['recipientname']) ? $helper->sanitize_text_field(base64_decode($post['recipientname'])) : '';
            $value = $helper->sanitize_text_field(base64_decode($post['totalprice']));
            $message = $helper->sanitize_textarea_field(base64_decode($post['recipientmessage']));
            $code = $post['couponcode'];
            $shipping = $helper->sanitize_text_field(base64_decode($post['shipping']));
            $shipping_email = isset($post['shipping_email']) ? $helper->sanitize_email(base64_decode($post['shipping_email'])) : '';
            $firstname = isset($post['firstname']) ? $helper->sanitize_text_field(base64_decode($post['firstname'])) : '';
            $lastname = isset($post['lastname']) ? $helper->sanitize_text_field(base64_decode($post['lastname'])) : '';
            $receipt_email = isset($post['receipt_email']) ? $helper->sanitize_email(base64_decode($post['receipt_email'])) : '';
            $address = isset($post['address']) ? $helper->sanitize_text_field(base64_decode($post['address'])) : '';
            $pincode = isset($post['pincode']) ? $helper->sanitize_text_field(base64_decode($post['pincode'])) : '';
            $shipping_method = isset($post['shipping_method']) ? base64_decode($post['shipping_method']) : '';
            $paymentmethod = $helper->sanitize_text_field(base64_decode($post['paymentmethod']));

            $wpdb = new \Patrickfuchshofer\Giftvoucher\Model\Libs\Wpdb();
            $voucher_table     = $wpdb->prefix . 'giftvouchers_list';
            $setting_table     = $wpdb->prefix . 'giftvouchers_setting';
            $setting_options = $wpdb->get_row("SELECT * FROM $setting_table WHERE id = 1");

            //$image = $helper->get_attached_file(get_post_thumbnail_id($itemid)) ? $helper->get_attached_file(get_post_thumbnail_id($itemid)) : $helper->get_option('wpgv_demoimageurl');
            $voucher_bgcolor = $helper->wpgv_hex2rgb($setting_options->voucher_bgcolor);
            $voucher_color = $helper->wpgv_hex2rgb($setting_options->voucher_color);

            $currency = ($setting_options->currency_position == 'Left') ? $setting_options->currency . ' ' . $value : $value . ' ' . $setting_options->currency;


            $wpgv_hide_expiry = $helper->get_option('wpgv_hide_expiry') ? $helper->get_option('wpgv_hide_expiry') : 'yes';

            $wpgv_customer_receipt = $helper->get_option('wpgv_customer_receipt') ? $helper->get_option('wpgv_customer_receipt') : 0;
            $wpgv_expiry_date_format = $helper->get_option('wpgv_expiry_date_format') ? $helper->get_option('wpgv_expiry_date_format') : 'd.m.Y';
            $wpgv_enable_pdf_saving = $helper->get_option('wpgv_enable_pdf_saving') ? $helper->get_option('wpgv_enable_pdf_saving') : 0;

            if ($wpgv_hide_expiry == 'no') {
                $expiry = __('No Expiry', 'gift-voucher');
            } else {
                $expiry = ($setting_options->voucher_expiry_type == 'days') ? date($wpgv_expiry_date_format, strtotime('+' . $setting_options->voucher_expiry . ' days', time())) . PHP_EOL : $setting_options->voucher_expiry;
            }

            $upload = $helper->wp_upload_dir();
            $upload_dir = $upload['basedir'];
            $curr_time = time();
            if (!file_exists($upload_dir . '/voucherpdfuploads/')) {
                mkdir($upload_dir . '/voucherpdfuploads/', 0755, true);
            }
            $upload_dir = $upload_dir . '/voucherpdfuploads/' . $curr_time . $post['couponcode'] . '.pdf';
            $upload_url = $curr_time . $post['couponcode'];

            $formtype = 'item';
            $preview = false;

            if ($setting_options->is_style_choose_enable) {
                $voucher_style = $helper->sanitize_text_field(base64_decode($post['style']));
                $style_image = $helper->get_post_meta($itemid, 'style' . ($voucher_style + 1) . '_image', true);
                $image_attributes = $helper->get_attached_file($style_image);
                $image = ($image_attributes) ? $image_attributes : $helper->get_option('wpgv_demoimageurl');
            } else {
                $voucher_style = $setting_options->voucher_style;
                $style_image = $helper->get_post_meta($itemid, 'style1_image', true);
                $image_attributes = $helper->get_attached_file($style_image);
                $image = ($image_attributes) ? $image_attributes : $helper->get_option('wpgv_demoimageurl');
            }

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

            if ($wpgv_enable_pdf_saving) {
                $pdf->Output($upload_dir, 'F');
            } else {
                $pdf->Output('F', $upload_dir);
            }

            $result = $wpdb->insert(
                $voucher_table,
                array(
                    'order_type'        => 'items',
                    'itemcat_id'         => $catid,
                    'item_id'             => $itemid,
                    'buying_for'        => $buyingfor,
                    'from_name'         => $for,
                    'to_name'             => $from,
                    'amount'            => $value,
                    'message'            => $message,
                    'shipping_type'        => $shipping,
                    'shipping_email'    => $shipping_email,
                    'firstname'            => $firstname,
                    'lastname'            => $lastname,
                    'email'                => $receipt_email,
                    'address'            => $address,
                    'postcode'            => $pincode,
                    'shipping_method'    => $shipping_method,
                    'pay_method'        => $paymentmethod,
                    'expiry'            => $expiry,
                    'couponcode'        => $code,
                    'voucherpdf_link'    => $upload_url,
                    'voucheradd_time'    => $helper->current_time('mysql'),
                    'payment_status'    => 'Not Pay'
                )
            );



            $lastid = $wpdb->insert_id;
            //\WPGV_Gift_Voucher_Activity::record($lastid, 'create', '', 'Voucher ordered by ' . $for . ', Message: ' . $message);

            //Customer Receipt
            if ($wpgv_customer_receipt) {
                $email = $receipt_email;
                $upload_dir = $upload['basedir'];
                $receiptupload_dir = $upload_dir . '/voucherpdfuploads/' . $curr_time . $post['couponcode'] . '-receipt.pdf';

                $receipt = $helper->pdf_receipt($setting_options, $lastid, $for, $buyingfor, $from, $email, $currency, $code, $expiry, $paymentmethod);

                if ($wpgv_enable_pdf_saving) {
                    $receipt->Output($receiptupload_dir, 'F');
                } else {
                    $receipt->Output('F', $receiptupload_dir);
                }
            }

            $preshipping_methods = explode(',', $setting_options->shipping_method);
            foreach ($preshipping_methods as $method) {
                $preshipping_method = explode(':', $method);
                if (trim($preshipping_method[1]) == $shipping_method) {
                    $value += trim($preshipping_method[0]);
                    break;
                }
            }
            $currency = ($setting_options->currency_position == 'Left') ? $setting_options->currency . ' ' . $value : $value . ' ' . $setting_options->currency;

            $return_url = $helper->get_site_url() . 'giftvoucher/giftitem/paymentsuccessful/?voucheritem=' . $lastid;
            $cancel_url = $helper->get_site_url() . 'giftvoucher/giftitem/paymentcancel/?voucheritem=' . $lastid;
            $notify_url = $helper->get_site_url() . 'giftvoucher/giftitem/paymentsuccessful/?voucheritem=' . $lastid;



            if ($paymentmethod == 'Paypal') {

                $paypal_email = $setting_options->paypal_email;

                $querystring = '';
                if ($setting_options->test_mode) {
                    $querystring .= 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_xclick';
                } else {
                    $querystring .= 'https://www.paypal.com/cgi-bin/webscr?cmd=_xclick';
                }
                $querystring .= "&business=" . urlencode($paypal_email) . "&";
                $querystring .= "item_name=" . urlencode($helper->get_the_title($itemid) . ' Voucher') . "&";
                $querystring .= "item_number=" . urlencode($lastid) . "&";
                $querystring .= "amount=" . urlencode($value) . "";
                $querystring .= "&amp;currency_code=$setting_options->currency_code&";
                if ($shipping == 'shipping_as_post') {
                    $querystring .= "first_name=" . urlencode($firstname) . "&";
                    $querystring .= "last_name=" . urlencode($lastname) . "&";
                    $querystring .= "email=" . urlencode($receipt_email) . "&";
                } elseif ($shipping == 'shipping_as_email') {
                    $querystring .= "first_name=" . urlencode($for) . "&";
                    $querystring .= "email=" . urlencode($receipt_email) . "&";
                }
                $querystring .= "custom=" . urlencode($lastid) . "&";
                $querystring .= "return=" . urlencode(stripslashes($return_url)) . "&";
                $querystring .= "cancel_return=" . urlencode(stripslashes($cancel_url)) . "&";
                // $querystring .= "notify_url=".urlencode($notify_url);

                //echo $querystring;

                return $this->getResponse()->setBody($querystring);

            } elseif ($paymentmethod == 'Sofort') {

                $Sofortueberweisung = new Sofortueberweisung($setting_options->sofort_configure_key);

                $Sofortueberweisung->setAmount($value);
                $Sofortueberweisung->setCurrencyCode($setting_options->currency_code);

                $Sofortueberweisung->setReason($setting_options->reason_for_payment, $lastid);
                $Sofortueberweisung->setSuccessUrl($return_url, true);
                $Sofortueberweisung->setAbortUrl($cancel_url);
                $Sofortueberweisung->setNotificationUrl($notify_url);

                $Sofortueberweisung->sendRequest();

                if ($Sofortueberweisung->isError()) {
                    //SOFORT-API didn't accept the data
                    
                    return $this->getResponse()->setBody( $Sofortueberweisung->getError());
                } else {
                    //buyer must be redirected to $paymentUrl else payment cannot be successfully completed!
                    $paymentUrl = $Sofortueberweisung->getPaymentUrl();
                    return $this->getResponse()->setBody( $paymentUrl);
                }
            } elseif ($paymentmethod == 'Stripe') {
                $stripesuccesspageurl = $helper->get_option('wpgv_stripesuccesspage');
                $stripeemail = ($receipt_email) ? $receipt_email : $shipping_email;
                $tmp = '<div class="wpgvmodaloverlay"><div class="wpgvmodalcontent"><h4>' . $helper->get_the_title($itemid) . '</h4><span class="wpgv-payment-errors"></span><form action="' . get_page_link($stripesuccesspageurl) . '" method="POST" id="stripePaymentForm">
                            <input type="hidden" name="orderid" value="' . $lastid . '">
                            <div class="payeremail">
                                <input type="email" name="email" placeholder="Email Address" class="wpgv-email" required value="' . $stripeemail . '">
                            </div>
                            <div class="paymentinfo">
                                <input type="text" name="card_num" placeholder="Card Number" size="20" autocomplete="off" class="wpgv-card-number" required>
                                <input type="text" name="cvc" placeholder="CVV" size="3" autocomplete="off" class="wpgv-card-cvc" required>
                                <input type="text" name="exp_month" placeholder="MM" size="2" class="wpgv-card-expiry-month" autocomplete="off" required>
                                <input type="text" name="exp_year" placeholder="YY" size="2" class="wpgv-card-expiry-year" autocomplete="off" required>
                            </div>
                            <button type="submit" id="wpgvpayBtn">Pay ' . $currency . '</button>
                            <div class="wpgv-cancel"><a href="' . $cancel_url . '">Cancel Payment</a></div></form></div></div>
                            <script type="text/javascript">
                                Stripe.setPublishableKey(\'' . $setting_options->stripe_publishable_key . '\');
            
                                function stripeResponseHandler(status, response) {
                                    if (response.error) {
                                        jQuery("#wpgvpayBtn").removeAttr("disabled");
                                        jQuery(".wpgv-payment-errors").html(response.error.message);
                                    } else {
                                        var form$ = jQuery("#stripePaymentForm");
                                        //get token id
                                        var token = response["id"];
                                        //insert the token into the form
                                        form$.append("<input type=\'hidden\' name=\'stripeToken\' value=\'" + token + "\' />");
                                        //submit form to the server
                                        form$.get(0).submit();
                                    }
                                }
                                jQuery(document).ready(function($) {
                                    //on form submit
                                    $("#stripePaymentForm").submit(function(event) {
                                        //disable the submit button to prevent repeated clicks
                                        $("#wpgvpayBtn").attr("disabled", "disabled");
                    
                                        //create single-use token to charge the user
                                        Stripe.createToken({
                                            number: $(".wpgv-card-number").val(),
                                            cvc: $(".wpgv-card-cvc").val(),
                                            exp_month: $(".wpgv-card-expiry-month").val(),
                                            exp_year: $(".wpgv-card-expiry-year").val()
                                        }, stripeResponseHandler);
                    
                                        //submit from callback
                                        return false;
                                    });
                                });
                            </script>';
                return $this->getResponse()->setBody( $tmp);
            } elseif ($paymentmethod == 'Per Invoice') {
                return $this->getResponse()->setBody( $notify_url . '&per_invoice=1');
            }
        }
    }
}
