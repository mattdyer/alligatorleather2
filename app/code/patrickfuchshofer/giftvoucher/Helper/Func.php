<?php

namespace Patrickfuchshofer\Giftvoucher\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Func extends AbstractHelper
{

    function translations()
    {
        return [
            'select_template' => (string) __('Please select voucher template', 'gift-voucher'),
            'accept_terms' => (string) __('Please accept the terms and conditions', 'gift-voucher'),
            'finish' => (string) __('Finish', 'gift-voucher'),
            'next' => (string) __('Continue', 'gift-voucher'),
            'previous' => (string) __('Back', 'gift-voucher'),
            'submitted' => (string) __('Submitted!', 'gift-voucher'),
            'error_occur' => (string) __('Error occurred', 'gift-voucher'),
            'total_character' => (string) __('Total Characters', 'gift-voucher'),
            'via_post' => (string) __('Shipping via Post', 'gift-voucher'),
            'via_email' => (string) __('Shipping via Email', 'gift-voucher'),
            'checkemail' => (string) __('Please check email address.', 'gift-voucher'),
            'required' => (string) __('This field is required.', 'gift-voucher'),
            'remote' => (string) __('Please fix this field.', 'gift-voucher'),
            'maxlength' => (string) __('Please enter no more than {0} characters.', 'gift-voucher'),
            'email' => (string) __('Please enter a valid email address.', 'gift-voucher'),
            'max' => (string) __('Please enter a value less than or equal to {0}.', 'gift-voucher'),
            'min' => (string) __('Please enter a value greater than or equal to {0}.', 'gift-voucher')
        ];
    }


    function sanitize_text_field($var)
    {
        return $var;
    }
    function sanitize_textarea_field($var)
    {
        return $var;
    }
    function sanitize_email($var)
    {
        return $var;
    }

    function wpgv_hex2rgb($color)
    {
        if ($color[0] == '#')
            $color = substr($color, 1);

        if (strlen($color) == 6)
            list($r, $g, $b) = array(
                $color[0] . $color[1],
                $color[2] . $color[3],
                $color[4] . $color[5]
            );
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        else
            return false;

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return array($r, $g, $b);
    }

    function get_option($var)
    {
        if ($var == 'wpgv_demoimageurl') {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            return $objectManager->get('\Magento\Framework\View\Asset\Repository')->getUrl("Patrickfuchshofer_Giftvoucher::img/demo.png");
        }
        return null;
    }

    function get_assets_url($dir)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get('\Magento\Framework\View\Asset\Repository')->getUrl("Patrickfuchshofer_Giftvoucher::$dir");
    }

    function wpgv_em($word)
    {
        $word = html_entity_decode(strip_tags(stripslashes($word)), ENT_NOQUOTES, 'UTF-8');
        $enc = mb_detect_encoding($word, "windows-1252,ISO-8859-1,ISO-8859-2");
        $word = iconv('UTF-8', 'windows-1252', $word);
        return $word;
    }

    function get_product_by_id($productId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
    }

    function get_the_title($productId)
    {
        $product = $this->get_product_by_id($productId);
        if ($product) {
            return $product->getName();
        }
        return '';
    }


    function get_post_meta($productId, $key, $single = null)
    {
        $product = $this->get_product_by_id($productId);

        if (
            $key == 'style1_image'
            || $key == 'style2_image'
            || $key == 'style3_image'
        ) {
            $numberOfStyle = preg_replace('/\D/', '', $key);

            $galleryEntries = $product->getMediaGalleryEntries();

            $image = array_filter($galleryEntries, function ($tmp) use ($numberOfStyle) {
                return in_array('image_style_' . $numberOfStyle, $tmp->getTypes());
            });

            $image = end($image);

            return $image;
        }

        return $product->getData($key);
    }

    function get_attached_file($file)
    {

        if (!$file) {
            return null;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
        return $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $file->getFile();
    }


    function get_product_image_style_url($productId, $numberOfStyle, $showDemo = false)
    {
        $product = get_product_by_id($productId);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
        $galleryEntries = $product->getMediaGalleryEntries();

        $image = array_filter($galleryEntries, function ($tmp) use ($numberOfStyle) {
            return in_array('image_style_' . $numberOfStyle, $tmp->getTypes());
        });

        if ($image) {
            return $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $image->getFile();
        }

        if ($showDemo) {
            $objectManager->get('\Magento\Framework\View\Asset\Repository')->getUrl("Patrickfuchshofer_Giftvoucher::img/demo.png");
        }

        return '';
    }


    function get_setting_options()
    {
        //Get settings
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();

        //Select Data from table
        $sql = "SELECT * FROM giftvouchers_setting WHERE id = 1";
        $result = $connection->fetchAll($sql);
        return (object) end($result);
    }


    function get_module_dir()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reader = $objectManager->get('\Magento\Framework\Module\Dir\Reader');
        $moduleViewDir = $reader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            'Patrickfuchshofer_Giftvoucher'
        );

        return $moduleViewDir . '/..';
    }
    function get_site_url()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        return $storeManager->getStore()->getBaseUrl();
    }

    function home_url()
    {
        return $this->get_site_url();
    }

    function get_home_url()
    {
        return $this->home_url();
    }

    function wp_create_nonce($var)
    {
        return '';
    }

    function create_Giftvoucher_category()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
        $category = $objectManager->get('Magento\Catalog\Model\CategoryFactory');

        $collection = $category
            ->create()
            ->getCollection()
            ->addAttributeToFilter('name', 'Gift Voucher')
            ->setPageSize(1);

        if ($collection->getSize()) {
            $categoryId = $collection->getFirstItem()->getId();
        } else {
            $data = [
                'data' => [
                    "parent_id" => 2, //Default Category
                    'name' => 'Gift Voucher',
                    "is_active" => true,
                    "position" => 10,
                    //"include_in_menu" => false,
                ]
            ];
            $category = $objectManager->create('Magento\Catalog\Model\Category', $data);
            $repository = $objectManager->get(\Magento\Catalog\Api\CategoryRepositoryInterface::class);
            $result = $repository->save($category);
            $categoryId = $result->getId();
        }

        return $categoryId;
    }


    function get_categories()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $categoryId = $this->create_Giftvoucher_category();
        $category = $objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
        return $category->getChildrenCategories();
    }


    function wp_upload_dir()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');

        return [
            'basedir' => $directory->getRoot() . '/pub/media'
        ];
    }



    function current_time($type, $gmt = 0)
    {
        switch ($type) {
            case 'mysql':
                return ($gmt) ? gmdate('Y-m-d H:i:s') : gmdate('Y-m-d H:i:s', (time() + ($this->get_option('gmt_offset') * 60 * 60)));
            case 'timestamp':
                return ($gmt) ? time() : time() + ($this->get_option('gmt_offset') * 60 * 60);
            default:
                return ($gmt) ? gmdate($type) : gmdate($type, time() + ($this->get_option('gmt_offset') * 60 * 60));
        }
    }

    function wp_die($message)
    {
        throw new \Exception($message);
    }


    function get_current_user_id()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $tmp = $objectManager->get('\Magento\Customer\Model\Session');
        return $tmp->getCustomer()->getId();
    }

    function wpgv_mailvarstr($string, $setting_options, $voucher_options)
    {
        $vars = array(
            '{order_type}'        => ($voucher_options->order_type) ? $voucher_options->order_type : 'vouchers',
            '{company_name}'      => ($setting_options->company_name) ? $setting_options->company_name : '',
            '{website_url}'       => $this->get_site_url(),
            '{sender_email}'      => $setting_options->sender_email,
            '{sender_name}'       => $setting_options->sender_name,
            '{order_number}'      => $voucher_options->id,
            '{amount}'            => $voucher_options->amount,
            '{customer_name}'     => $voucher_options->from_name,
            '{recipient_name}'    => $voucher_options->to_name,
            '{customer_email}'    => ($voucher_options->email) ? $voucher_options->email : $voucher_options->shipping_email,
            '{customer_address}'  => $voucher_options->address,
            '{customer_postcode}' => $voucher_options->postcode,
            '{coupon_code}'       => $voucher_options->couponcode,
            '{payment_method}'    => $voucher_options->pay_method,
            '{payment_status}'    => $voucher_options->payment_status,
            '{pdf_link}'          => $this->get_home_url() . 'pub/media/voucherpdfuploads/' . $voucher_options->voucherpdf_link . '.pdf',
            '{receipt_link}'      => $this->get_home_url() . 'pub/media/voucherpdfuploads/' . $voucher_options->voucherpdf_link . '-receipt.pdf',
        );

        return strtr($string, $vars);
    }


    function wp_mail($to, $subject, $message, $headers = '', $attachments = array())
    {
        $mail = new \Zend_Mail();
        $mail->setBodyHtml($message);
        $mail->setFrom('giftvoucher@example.com');
        $mail->addTo($to);
        $mail->setSubject($subject);

        foreach ($attachments as $path) {
            $name = pathinfo($path)['filename'];
            $content = file_get_contents($path); // e.g. ("attachment/abc.pdf")
            $attachment = new \Zend_Mime_Part($content);
            $attachment->type = 'application/pdf';
            $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
            $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
            $attachment->filename =  $name . '.pdf'; // name of file
            $mail->addAttachment($attachment);
            unlink($path);
        }

        $mail->send();
        return true;
        // Compact the input, apply the filters, and extract them back out

        /**
         * Filters the wp_mail() arguments.
         *
         * @since 2.2.0
         *
         * @param array $args A compacted array of wp_mail() arguments, including the "to" email,
         *                    subject, message, headers, and attachments values.
         */
        $atts['to'] = $to;
        $atts['subject'] = $subject;
        $atts['message'] = $message;
        $atts['headers'] = $headers;
        $atts['attachments'] = $attachments;

        if (isset($atts['to'])) {
            $to = $atts['to'];
        }

        if (!is_array($to)) {
            $to = explode(',', $to);
        }

        if (isset($atts['subject'])) {
            $subject = $atts['subject'];
        }

        if (isset($atts['message'])) {
            $message = $atts['message'];
        }

        if (isset($atts['headers'])) {
            $headers = $atts['headers'];
        }

        if (isset($atts['attachments'])) {
            $attachments = $atts['attachments'];
        }

        if (!is_array($attachments)) {
            $attachments = explode("\n", str_replace("\r\n", "\n", $attachments));
        }
        $phpmailer = new \Patrickfuchshofer\Giftvoucher\Classes\PHPMailer(true);

        // Headers
        $cc = $bcc = $reply_to = array();

        if (empty($headers)) {
            $headers = array();
        } else {
            if (!is_array($headers)) {
                // Explode the headers out, so this function can take both
                // string headers and an array of headers.
                $tempheaders = explode("\n", str_replace("\r\n", "\n", $headers));
            } else {
                $tempheaders = $headers;
            }
            $headers = array();

            // If it's actually got contents
            if (!empty($tempheaders)) {
                // Iterate through the raw headers
                foreach ((array) $tempheaders as $header) {
                    if (strpos($header, ':') === false) {
                        if (false !== stripos($header, 'boundary=')) {
                            $parts    = preg_split('/boundary=/i', trim($header));
                            $boundary = trim(str_replace(array("'", '"'), '', $parts[1]));
                        }
                        continue;
                    }
                    // Explode them out
                    list($name, $content) = explode(':', trim($header), 2);

                    // Cleanup crew
                    $name    = trim($name);
                    $content = trim($content);

                    switch (strtolower($name)) {
                            // Mainly for legacy -- process a From: header if it's there
                        case 'from':
                            $bracket_pos = strpos($content, '<');
                            if ($bracket_pos !== false) {
                                // Text before the bracketed email is the "From" name.
                                if ($bracket_pos > 0) {
                                    $from_name = substr($content, 0, $bracket_pos - 1);
                                    $from_name = str_replace('"', '', $from_name);
                                    $from_name = trim($from_name);
                                }

                                $from_email = substr($content, $bracket_pos + 1);
                                $from_email = str_replace('>', '', $from_email);
                                $from_email = trim($from_email);

                                // Avoid setting an empty $from_email.
                            } elseif ('' !== trim($content)) {
                                $from_email = trim($content);
                            }
                            break;
                        case 'content-type':
                            if (strpos($content, ';') !== false) {
                                list($type, $charset_content) = explode(';', $content);
                                $content_type                   = trim($type);
                                if (false !== stripos($charset_content, 'charset=')) {
                                    $charset = trim(str_replace(array('charset=', '"'), '', $charset_content));
                                } elseif (false !== stripos($charset_content, 'boundary=')) {
                                    $boundary = trim(str_replace(array('BOUNDARY=', 'boundary=', '"'), '', $charset_content));
                                    $charset  = '';
                                }

                                // Avoid setting an empty $content_type.
                            } elseif ('' !== trim($content)) {
                                $content_type = trim($content);
                            }
                            break;
                        case 'cc':
                            $cc = array_merge((array) $cc, explode(',', $content));
                            break;
                        case 'bcc':
                            $bcc = array_merge((array) $bcc, explode(',', $content));
                            break;
                        case 'reply-to':
                            $reply_to = array_merge((array) $reply_to, explode(',', $content));
                            break;
                        default:
                            // Add it to our grand headers array
                            $headers[trim($name)] = trim($content);
                            break;
                    }
                }
            }
        }

        // Empty out the values that may be set
        $phpmailer->clearAllRecipients();
        $phpmailer->clearAttachments();
        $phpmailer->clearCustomHeaders();
        $phpmailer->clearReplyTos();

        // From email and name
        // If we don't have a name from the input headers
        if (!isset($from_name)) {
            $from_name = 'WordPress';
        }

        /* If we don't have an email from the input headers default to wordpress@$sitename
     * Some hosts will block outgoing mail from this address if it doesn't exist but
     * there's no easy alternative. Defaulting to admin_email might appear to be another
     * option but some hosts may refuse to relay mail from an unknown domain. See
     * https://core.trac.wordpress.org/ticket/5007.
     */

        if (!isset($from_email)) {
            // Get the site domain and get rid of www.
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') {
                $sitename = substr($sitename, 4);
            }

            $from_email = 'magento@' . $sitename;
        }

        /**
         * Filters the email address to send from.
         *
         * @since 2.2.0
         *
         * @param string $from_email Email address to send from.
         */
        //$from_email = apply_filters('wp_mail_from', $from_email);

        /**
         * Filters the name to associate with the "from" email address.
         *
         * @since 2.3.0
         *
         * @param string $from_name Name associated with the "from" email address.
         */
        //$from_name = apply_filters('wp_mail_from_name', $from_name);

        $phpmailer->setFrom($from_email, $from_name, false);

        // Set mail's subject and body
        $phpmailer->Subject = $subject;
        $phpmailer->Body    = $message;

        // Set destination addresses, using appropriate methods for handling addresses
        $address_headers = compact('to', 'cc', 'bcc', 'reply_to');

        foreach ($address_headers as $address_header => $addresses) {
            if (empty($addresses)) {
                continue;
            }

            foreach ((array) $addresses as $address) {
                try {
                    // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                    $recipient_name = '';

                    if (preg_match('/(.*)<(.+)>/', $address, $matches)) {
                        if (count($matches) == 3) {
                            $recipient_name = $matches[1];
                            $address        = $matches[2];
                        }
                    }

                    switch ($address_header) {
                        case 'to':
                            $phpmailer->addAddress($address, $recipient_name);
                            break;
                        case 'cc':
                            $phpmailer->addCc($address, $recipient_name);
                            break;
                        case 'bcc':
                            $phpmailer->addBcc($address, $recipient_name);
                            break;
                        case 'reply_to':
                            $phpmailer->addReplyTo($address, $recipient_name);
                            break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        // Set to use PHP's mail()
        $phpmailer->isMail();

        // Set Content-Type and charset
        // If we don't have a content-type from the input headers
        if (!isset($content_type)) {
            $content_type = 'text/plain';
        }

        /**
         * Filters the wp_mail() content type.
         *
         * @since 2.3.0
         *
         * @param string $content_type Default wp_mail() content type.
         */
        //$content_type = apply_filters('wp_mail_content_type', $content_type);

        $phpmailer->ContentType = $content_type;

        // Set whether it's plaintext, depending on $content_type
        if ('text/html' == $content_type) {
            $phpmailer->isHTML(true);
        }

        // If we don't have a charset from the input headers
        if (!isset($charset)) {
            $charset = get_bloginfo('charset');
        }

        // Set the content-type and charset

        /**
         * Filters the default wp_mail() charset.
         *
         * @since 2.3.0
         *
         * @param string $charset Default email charset.
         */
        //$phpmailer->CharSet = apply_filters('wp_mail_charset', $charset);

        // Set custom headers
        if (!empty($headers)) {
            foreach ((array) $headers as $name => $content) {
                $phpmailer->addCustomHeader(sprintf('%1$s: %2$s', $name, $content));
            }

            if (false !== stripos($content_type, 'multipart') && !empty($boundary)) {
                $phpmailer->addCustomHeader(sprintf("Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary));
            }
        }

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                try {
                    $phpmailer->addAttachment($attachment);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        /**
         * Fires after PHPMailer is initialized.
         *
         * @since 2.2.0
         *
         * @param PHPMailer $phpmailer The PHPMailer instance (passed by reference).
         */
        //do_action_ref_array('phpmailer_init', array(&$phpmailer));

        // Send!
        return $phpmailer->send();
    }


    function create_object($className)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create($className);
    }

    function pdf_style1($image = null, $voucher_bgcolor = null, $formtype = null, $voucher_color = null, $itemid = null, $template_options = null, $buyingfor = null, $for = null, $from = null, $currency = null, $message = null, $expiry = null, $code = null, $setting_options = null, $preview = null, $watermark = null)
    {
        $helper = $this;
        // PDF Style 1

        $wpgv_hide_price = $helper->get_option('wpgv_hide_price') ? $helper->get_option('wpgv_hide_price') : 0;

        $pdf = new \Patrickfuchshofer\Giftvoucher\Classes\Fpdf('P', 'pt', array(595, 900));
        $pdf->SetAutoPageBreak(0);
        $pdf->AddPage();
        $pdf->Image($image, 0, 0, 595, 453);
        $pdf->SetFont('Arial', '', 16);
        $pdf->SetXY(0, 453);
        $pdf->SetFillColor($voucher_bgcolor[0], $voucher_bgcolor[1], $voucher_bgcolor[2]);
        $pdf->Cell(595, 450, '', 0, 1, 'L', 1);

        if ($formtype == 'item') {
            //Title
            $pdf->SetXY(30, 460);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(25);
            $pdf->MultiCell(550, 25, $helper->wpgv_em($helper->get_the_title($itemid)), 0, 'C');

            //Description
            $pdf->SetXY(30, 500);
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->MultiCell(550, 12, $helper->wpgv_em($helper->get_post_meta($itemid, 'description', true)), 0, 'C');
        } else {
            //Voucher
            $pdf->SetXY(30, 480);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(25);
            $pdf->MultiCell(550, 25, $helper->wpgv_em($template_options->title), 0, 'C');
        }
        //For
        $pdf->SetFont('Arial', '');
        $pdf->SetXY(30, 540);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(12);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Your Name', 'gift-voucher')), 0, 1, 'L', 0);
        //For Input
        $pdf->SetXY(33, 550);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(15);
        $pdf->Cell(265, 30, ' ' . $helper->wpgv_em($for), 0, 1, 'L', 1);

        if ($buyingfor != 'yourself') {
            //From
            $pdf->SetXY(310, 540);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(12);
            $pdf->Cell(0, 0, $helper->wpgv_em(__('Recipient Name', 'gift-voucher')), 0, 1, 'L', 0);
            //From Input
            $pdf->SetXY(313, 550);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(85, 85, 85);
            $pdf->SetFontSize(15);
            $pdf->Cell(265, 30, ' ' . $helper->wpgv_em($from), 0, 1, 'L', 1);
        }

        if (!$wpgv_hide_price) {
            //Voucher Value
            $pdf->SetXY(30, 600);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(12);
            $pdf->Cell(0, 0, $helper->wpgv_em(__('Voucher Value', 'gift-voucher')), 0, 1, 'L', 0);
            //Voucher Value Input
            $pdf->SetXY(33, 610);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(85, 85, 85);
            $pdf->SetFontSize(16);
            $pdf->Cell(265, 30, ' ' . $helper->wpgv_em($currency), 0, 1, 'L', 1);
        }

        //Personal Message
        $pdf->SetXY(30, 660);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(12);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Personal Message', 'gift-voucher')), 0, 1, 'L', 0);
        //Personal Message Input
        $pdf->SetXY(33, 670);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(14);
        $pdf->Cell(546, 100, '', 0, 1, 'L', 1);

        $pdf->SetXY(35, 672);
        $pdf->MultiCell(543, 20, $helper->wpgv_em($message), 0, 1, 'L', 1);
        //Date of Expiry
        $pdf->SetXY(30, 790);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(12);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Date of Expiry', 'gift-voucher')), 0, 1, 'L', 0);
        //Date of Expiry Input
        $pdf->SetXY(33, 800);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(16);
        $pdf->Cell(265, 30, ' ' . $helper->wpgv_em($expiry), 0, 1, 'L', 1);
        //Coupon Code
        $pdf->SetXY(310, 790);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(12);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Coupon Code', 'gift-voucher')), 0, 1, 'L', 0);
        //Coupon Code Input
        $pdf->SetXY(313, 800);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(16);
        $pdf->Cell(265, 30, ' ' . $helper->wpgv_em($code), 0, 1, 'L', 1);
        //Company Details
        $pdf->SetXY(30, 860);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(11);
        $pdf->Cell(0, 0, $setting_options->pdf_footer_url . ' | ' . $helper->wpgv_em($setting_options->pdf_footer_email), 0, 1, 'C', 0);
        //Terms
        $pdf->SetXY(0, 0);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(9);
        $pdf->RotatedText(20, 850, '* ' . $helper->wpgv_em(__('Cash payment is not possible. The terms and conditions apply.', 'gift-voucher')), 90);

        if ($preview) {
            //Put the watermark
            $pdf->SetXY(0, 0);
            $pdf->SetFont('Arial', 'B', 55);
            $pdf->SetTextColor(215, 215, 215);
            $pdf->RotatedText(75, 700, $helper->wpgv_em($watermark), 45);
        }

        return $pdf;
    }

    function pdf_style2($image = null, $voucher_bgcolor = null, $formtype = null, $voucher_color = null, $itemid = null, $template_options = null, $buyingfor = null, $for = null, $from = null, $currency = null, $message = null, $expiry = null, $code = null, $setting_options = null, $preview = null, $watermark = null)
    {
        $helper = $this;
        // PDF Style 2

        $wpgv_hide_price = $helper->get_option('wpgv_hide_price') ? $helper->get_option('wpgv_hide_price') : 0;

        $pdf = new \Patrickfuchshofer\Giftvoucher\Classes\Fpdf('P', 'pt', array(595, 760));
        $pdf->SetAutoPageBreak(0);
        $pdf->AddPage();
        //Image
        $pdf->SetXY(0, 0);
        $pdf->SetFillColor($voucher_bgcolor[0], $voucher_bgcolor[1], $voucher_bgcolor[2]);
        $pdf->Cell(595, 760, '', 0, 1, 'L', 1);
        $pdf->Image($image, 30, 40, 265, 370);
        $pdf->SetFont('Arial', '', 16);

        if ($formtype == 'item') {
            //Title
            $pdf->SetXY(310, 90);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(30);
            $pdf->MultiCell(265, 30, $helper->wpgv_em($helper->get_the_title($itemid)), 0, 'L');

            //Description
            $pdf->SetXY(310, 130);
            $pdf->SetFont('Arial', '', 13);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->MultiCell(265, 12, $helper->wpgv_em($helper->get_post_meta($itemid, 'description', true)), 0, 'L');
        } else {
            //Voucher
            $pdf->SetXY(310, 100);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(30);
            $pdf->MultiCell(265, 30, $helper->wpgv_em($template_options->title), 0, 'L');
        }

        //For
        $pdf->SetFont('Arial', '');
        $pdf->SetXY(310, 200);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(14);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Your Name', 'gift-voucher')), 0, 1, 'L', 0);
        //For Input
        $pdf->SetXY(313, 210);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(15);
        $pdf->Cell(265, 40, ' ' . $helper->wpgv_em($for), 0, 1, 'L', 1);

        if ($buyingfor != 'yourself') {
            //From
            $pdf->SetXY(310, 280);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(14);
            $pdf->Cell(0, 0, $helper->wpgv_em(__('Recipient Name', 'gift-voucher')), 0, 1, 'L', 0);
            //From Input
            $pdf->SetXY(313, 290);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(85, 85, 85);
            $pdf->SetFontSize(15);
            $pdf->Cell(265, 40, ' ' . $helper->wpgv_em($from), 0, 1, 'L', 1);
        }

        if (!$wpgv_hide_price) {
            //Voucher Value
            $pdf->SetXY(310, 360);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(14);
            $pdf->Cell(0, 0, $helper->wpgv_em(__('Voucher Value', 'gift-voucher')), 0, 1, 'L', 0);
            //Voucher Value Input
            $pdf->SetXY(313, 370);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(85, 85, 85);
            $pdf->SetFontSize(16);
            $pdf->Cell(265, 40, ' ' . $helper->wpgv_em($currency), 0, 1, 'L', 1);
        }

        //Personal Message
        $pdf->SetXY(30, 440);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(14);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Personal Message', 'gift-voucher')), 0, 1, 'L', 0);
        //Personal Message Input
        $pdf->SetXY(33, 455);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(15);
        $pdf->Cell(546, 140, '', 0, 1, 'L', 1);

        $pdf->SetXY(35, 458);
        $pdf->MultiCell(540, 23, $helper->wpgv_em($message), 0, 1, 'L', 1);
        //Date of Expiry
        $pdf->SetXY(30, 620);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(14);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Date of Expiry', 'gift-voucher')), 0, 1, 'L', 0);
        //Date of Expiry Input
        $pdf->SetXY(33, 630);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(16);
        $pdf->Cell(265, 30, ' ' . $helper->wpgv_em($expiry), 0, 1, 'L', 1);
        //Coupon Code
        $pdf->SetXY(310, 620);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(14);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Coupon Code', 'gift-voucher')), 0, 1, 'L', 0);
        //Coupon Code Input
        $pdf->SetXY(313, 630);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(16);
        $pdf->Cell(265, 30, ' ' . $helper->wpgv_em($code), 0, 1, 'L', 1);
        //Company Details
        $pdf->SetXY(30, 700);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(12);
        $pdf->Cell(0, 0, $setting_options->pdf_footer_url . ' | ' . $helper->wpgv_em($setting_options->pdf_footer_email), 0, 1, 'C', 0);
        //Terms
        $pdf->SetXY(30, 730);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(10);
        $pdf->Cell(0, 0, '* ' . $helper->wpgv_em(__('Cash payment is not possible. The terms and conditions apply.', 'gift-voucher')), 0, 1, 'C', 0);

        if ($preview) {
            //Put the watermark
            $pdf->SetXY(0, 0);
            $pdf->SetFont('Arial', 'B', 55);
            $pdf->SetTextColor(215, 215, 215);
            $pdf->RotatedText(75, 700, $helper->wpgv_em($watermark), 45);
        }

        return $pdf;
    }

    function pdf_style3($image = null, $voucher_bgcolor = null, $formtype = null, $voucher_color = null, $itemid = null, $template_options = null, $buyingfor = null, $for = null, $from = null, $currency = null, $message = null, $expiry = null, $code = null, $setting_options = null, $preview = null, $watermark = null)
    {
        $helper = $this;

        // PDF Style 3

        $wpgv_hide_price = $helper->get_option('wpgv_hide_price') ? $helper->get_option('wpgv_hide_price') : 0;

        $pdf = new \Patrickfuchshofer\Giftvoucher\Classes\Fpdf('P', 'pt', array(595, 660));
        $pdf->SetAutoPageBreak(0);
        $pdf->AddPage();
        $pdf->SetXY(0, 0);
        $pdf->SetFillColor($voucher_bgcolor[0], $voucher_bgcolor[1], $voucher_bgcolor[2]);
        $pdf->Cell(595, 660, '', 0, 1, 'L', 1);

        if ($formtype == 'item') {
            //Title
            $pdf->SetXY(30, 30);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(30);
            $pdf->MultiCell(550, 30, $helper->wpgv_em($helper->get_the_title($itemid)), 0, 'C');

            //Description
            $pdf->SetXY(30, 65);
            $pdf->SetFont('Arial', '', 13);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->MultiCell(550, 12, $helper->wpgv_em($helper->get_post_meta($itemid, 'description', true)), 0, 'C');
        } else {
            //Voucher
            $pdf->SetXY(30, 40);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(30);
            $pdf->MultiCell(550, 30, $helper->wpgv_em($template_options->title), 0, 'C');
        }

        //Image
        $pdf->Image($image, 30, 100, 265, 210);
        //For
        $pdf->SetFont('Arial', '');
        $pdf->SetXY(310, 100);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(14);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Your Name', 'gift-voucher')), 0, 1, 'L', 0);
        //For Input
        $pdf->SetXY(313, 110);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(15);
        $pdf->Cell(265, 40, ' ' . $helper->wpgv_em($for), 0, 1, 'L', 1);

        if ($buyingfor != 'yourself') {
            //From
            $pdf->SetXY(310, 180);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(14);
            $pdf->Cell(0, 0, $helper->wpgv_em(__('Recipient Name', 'gift-voucher')), 0, 1, 'L', 0);
            //From Input
            $pdf->SetXY(313, 190);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(85, 85, 85);
            $pdf->SetFontSize(15);
            $pdf->Cell(265, 40, ' ' . $helper->wpgv_em($from), 0, 1, 'L', 1);
        }

        if (!$wpgv_hide_price) {
            //Voucher Value
            $pdf->SetXY(310, 260);
            $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
            $pdf->SetFontSize(14);
            $pdf->Cell(0, 0, $helper->wpgv_em(__('Voucher Value', 'gift-voucher')), 0, 1, 'L', 0);
            //Voucher Value Input
            $pdf->SetXY(313, 270);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(85, 85, 85);
            $pdf->SetFontSize(16);
            $pdf->Cell(265, 40, ' ' . $helper->wpgv_em($currency), 0, 1, 'L', 1);
        }

        //Personal Message
        $pdf->SetXY(30, 340);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(14);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Personal Message', 'gift-voucher')), 0, 1, 'L', 0);
        //Personal Message Input
        $pdf->SetXY(33, 355);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(15);
        $pdf->Cell(546, 140, '', 0, 1, 'L', 1);

        $pdf->SetXY(35, 358);
        $pdf->MultiCell(540, 23, $helper->wpgv_em($message), 0, 1, 'L', 1);
        //Date of Expiry
        $pdf->SetXY(30, 520);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(14);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Date of Expiry', 'gift-voucher')), 0, 1, 'L', 0);
        //Date of Expiry Input
        $pdf->SetXY(33, 530);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(16);
        $pdf->Cell(265, 30, ' ' . $helper->wpgv_em($expiry), 0, 1, 'L', 1);
        //Coupon Code
        $pdf->SetXY(310, 520);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(14);
        $pdf->Cell(0, 0, $helper->wpgv_em(__('Coupon Code', 'gift-voucher')), 0, 1, 'L', 0);
        //Coupon Code Input
        $pdf->SetXY(313, 530);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(85, 85, 85);
        $pdf->SetFontSize(16);
        $pdf->Cell(265, 30, ' ' . $helper->wpgv_em($code), 0, 1, 'L', 1);
        //Company Details
        $pdf->SetXY(30, 600);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(12);
        $pdf->Cell(0, 0, $setting_options->pdf_footer_url . ' | ' . $helper->wpgv_em($setting_options->pdf_footer_email), 0, 1, 'C', 0);
        //Terms
        $pdf->SetXY(30, 630);
        $pdf->SetTextColor($voucher_color[0], $voucher_color[1], $voucher_color[2]);
        $pdf->SetFontSize(10);
        $pdf->Cell(0, 0, '* ' . $helper->wpgv_em(__('Cash payment is not possible. The terms and conditions apply.', 'gift-voucher')), 0, 1, 'C', 0);

        if ($preview) {
            //Put the watermark
            $pdf->SetXY(0, 0);
            $pdf->SetFont('Arial', 'B', 55);
            $pdf->SetTextColor(215, 215, 215);
            $pdf->RotatedText(75, 600, $helper->wpgv_em($watermark), 45);
        }

        return $pdf;
    }

    function pdf_receipt($setting_options = null, $lastid = null, $for = null, $buyingfor = null, $from = null, $email = null, $currency = null, $code = null, $expiry = null, $paymentmethod = null)
    {
        $helper = $this;

        $receipt = new WPGV_PDF('P', 'pt', array(595, 900));
        $receipt->SetAutoPageBreak(0);
        $receipt->AddPage();
        $receipt->SetTextColor(0, 0, 0);

        //Title
        $receipt->SetXY(30, 50);
        $receipt->SetFont('Arial', 'B', 16);
        $receipt->SetFontSize(20);
        $receipt->MultiCell(0, 0, $helper->wpgv_em(__('Customer Receipt', 'gift-voucher')), 0, 'C');

        $receipt->SetFontSize(12);

        //Company Name
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 100);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Company Name', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 100);
        $receipt->Cell(0, 0, ' ' . $helper->wpgv_em($setting_options->company_name), 0, 1, 'L', 0);

        //Company Email
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 120);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Company Email', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 120);
        $receipt->Cell(0, 0, ' ' . $helper->wpgv_em($setting_options->pdf_footer_email), 0, 1, 'L', 0);

        //Company Website
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 140);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Company Website', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 140);
        $receipt->Cell(0, 0, ' ' . $helper->wpgv_em($setting_options->pdf_footer_url), 0, 1, 'L', 0);

        //Order Number
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 160);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Order Number', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 160);
        $receipt->Cell(0, 0, $helper->wpgv_em(__(' #' . $lastid, 'gift-voucher')), 0, 1, 'L', 0);

        //Order Date
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 180);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Order Date', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 180);
        $receipt->Cell(0, 0, ' ' . date('d.m.Y'), 0, 1, 'L', 0);

        //For
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 200);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Your Name', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 200);
        $receipt->Cell(0, 0, ' ' . $helper->wpgv_em($for), 0, 1, 'L', 0);

        //From
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 220);
        if ($buyingfor != 'yourself') {
            $receipt->Cell(0, 0, $helper->wpgv_em(__('Recipient Name', 'gift-voucher')), 0, 1, 'L', 0);
            $receipt->SetFont('Arial', '');
            $receipt->SetXY(250, 220);
            $receipt->Cell(0, 0, ' ' . $helper->wpgv_em($from), 0, 1, 'L', 0);
        }

        //Email
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 240);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Email', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 240);
        $receipt->Cell(0, 0, ' ' . $helper->wpgv_em($email), 0, 1, 'L', 0);

        //Amount
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 260);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Amount', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 260);
        $receipt->Cell(0, 0, ' ' . $helper->wpgv_em($currency), 0, 1, 'L', 0);

        //Coupon Code
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 280);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Coupon Code', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 280);
        $receipt->Cell(0, 0, ' ' . $helper->wpgv_em($code), 0, 1, 'L', 0);

        //Coupon Expiry date
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 300);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Coupon Expiry date', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 300);
        $receipt->Cell(0, 0, ' ' . $helper->wpgv_em($expiry), 0, 1, 'L', 0);

        //Payment Method
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 320);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Payment Method', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 320);
        $receipt->Cell(0, 0, ' ' . $helper->wpgv_em($paymentmethod), 0, 1, 'L', 0);

        //Payment Status
        $receipt->SetFont('Arial', 'B');
        $receipt->SetXY(30, 340);
        $receipt->Cell(0, 0, $helper->wpgv_em(__('Payment Status', 'gift-voucher')), 0, 1, 'L', 0);
        $receipt->SetFont('Arial', '');
        $receipt->SetXY(250, 340);
        $receipt->Cell(0, 0, ' ' . $helper->wpgv_em(__('Paid', 'gift-voucher')), 0, 1, 'L', 0);

        return $receipt;
    }
}
