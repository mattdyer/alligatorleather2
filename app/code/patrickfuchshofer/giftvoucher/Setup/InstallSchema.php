<?php

namespace Patrickfuchshofer\Giftvoucher\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema  implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();
        $charset_collate = 'CHARACTER SET utf8 COLLATE utf8_general_ci';

        $tableName = $installer->getTable('giftvouchers_setting');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create giftvouchers_setting table
            $sql = "CREATE TABLE giftvouchers_setting (
                id int(11) NOT NULL AUTO_INCREMENT,
                is_woocommerce_enable int(1) DEFAULT 0,
                is_style_choose_enable int(1) DEFAULT 0,
                voucher_style varchar(100) DEFAULT 0,
                company_name varchar(255) DEFAULT NULL,
                currency_code varchar(10) DEFAULT NULL,
                currency varchar(10) DEFAULT NULL,
                currency_position varchar(10) DEFAULT NULL,
                voucher_bgcolor varchar(6) DEFAULT NULL,
                voucher_color varchar(6) DEFAULT NULL,
                template_col int(2) DEFAULT 3,
                voucher_min_value int(4) DEFAULT NULL,
                voucher_max_value int(6) DEFAULT NULL,
                voucher_expiry_type varchar(6) DEFAULT NULL,
                voucher_expiry varchar(10) DEFAULT NULL,
                voucher_terms_note text DEFAULT NULL,
                custom_loader text DEFAULT NULL,
                pdf_footer_url varchar(255) DEFAULT NULL,
                pdf_footer_email varchar(255) DEFAULT NULL,
                post_shipping int(1) DEFAULT NULL,
                shipping_method text DEFAULT NULL,
                preview_button int(1) DEFAULT 1,
                paypal int(11) DEFAULT NULL,
                sofort int(11) DEFAULT NULL,
                stripe int(11) DEFAULT NULL,
                paypal_email varchar(100) DEFAULT NULL,
                sofort_configure_key varchar(100) DEFAULT NULL,
                reason_for_payment varchar(100) DEFAULT NULL,
                stripe_publishable_key varchar(100) DEFAULT NULL,
                stripe_secret_key varchar(100) DEFAULT NULL,
                sender_name varchar(100) DEFAULT NULL,
                sender_email varchar(100) DEFAULT NULL,
                test_mode int(10) NOT NULL,
                per_invoice int(10) NOT NULL,
                bank_info longtext,
                PRIMARY KEY (id)
              ) $charset_collate";

            $connection->query($sql);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $baseUrl = $storeManager->getStore()->getBaseUrl();
            // $connection->insertMultiple(
            //     'giftvouchers_setting',
            //     [
            //         array(
            //             'is_woocommerce_enable' => 0,
            //             'is_style_choose_enable' => 0,
            //             'voucher_style'      => 0,
            //             'company_name'       => 'giftvoucher',
            //             'paypal_email'       => 'giftvoucher@example.com',
            //             'reason_for_payment' => 'Payment for Gift Cards',
            //             'sender_name'        => 'giftvoucher',
            //             'sender_email'       => 'giftvoucher@example.com',
            //             'currency_code'      => 'USD',
            //             'currency'           => '$',
            //             'paypal'             => 1,
            //             'sofort'             => 0,
            //             'stripe'             => 0,
            //             'voucher_bgcolor'    => '81c6a9',
            //             'voucher_color'      => '555555',
            //             'template_col'       => 4,
            //             'voucher_min_value'  => 0,
            //             'voucher_max_value'  => 10000,
            //             'voucher_expiry_type' => 'days',
            //             'voucher_expiry'     => 60,
            //             'voucher_terms_note' => 'Note: The voucher is valid for 60 days and can be redeemed at giftvoucher. A cash payment is not possible.',
            //             //'custom_loader'      =>  $object->getUrl("Patrickfuchshofer_Giftvoucher::img/loader.gift"),
            //             //'pdf_footer_url'     => $baseUrl,
            //             'pdf_footer_email'   => 'giftvoucher@example.com',
            //             'post_shipping'      => 1,
            //             'shipping_method'    => '5.99 : Express Shipping - $5.99, 3.99 : Standard Shipping - $3.99',
            //             'preview_button'     => 1,
            //             'currency_position'  => 'Left',
            //             'test_mode'          => 0,
            //             'per_invoice'        => 0
            //         )
            //     ]
            // );

            $sql = 'INSERT INTO giftvouchers_setting(
                is_woocommerce_enable,
                is_style_choose_enable,
                voucher_style,
                company_name,
                paypal_email,
                reason_for_payment,
                sender_name,
                sender_email,
                currency_code,
                currency,
                paypal,
                sofort,
                stripe,
                voucher_bgcolor,
                voucher_color,
                template_col,
                voucher_min_value,
                voucher_max_value,
                voucher_expiry_type,
                voucher_expiry,
                voucher_terms_note,
                pdf_footer_email,
                post_shipping,
                shipping_method,
                preview_button,
                currency_position,
                test_mode,
                per_invoice,
                pdf_footer_url
            ) VALUES(
                :is_woocommerce_enable,
                :is_style_choose_enable,
                :voucher_style,
                :company_name,
                :paypal_email,
                :reason_for_payment,
                :sender_name,
                :sender_email,
                :currency_code,
                :currency,
                :paypal,
                :sofort,
                :stripe,
                :voucher_bgcolor,
                :voucher_color,
                :template_col,
                :voucher_min_value,
                :voucher_max_value,
                :voucher_expiry_type,
                :voucher_expiry,
                :voucher_terms_note,
                :pdf_footer_email,
                :post_shipping,
                :shipping_method,
                :preview_button,
                :currency_position,
                :test_mode,
                :per_invoice,
                :pdf_footer_url
            )';
            $connection->query($sql, [
                'is_woocommerce_enable' => 0,
                'is_style_choose_enable' => 0,
                'voucher_style'      => 0,
                'company_name'       => 'giftvoucher',
                'paypal_email'       => 'giftvoucher@example.com',
                'reason_for_payment' => 'Payment for Gift Cards',
                'sender_name'        => 'giftvoucher',
                'sender_email'       => 'giftvoucher@example.com',
                'currency_code'      => 'USD',
                'currency'           => '$',
                'paypal'             => 1,
                'sofort'             => 0,
                'stripe'             => 0,
                'voucher_bgcolor'    => '81c6a9',
                'voucher_color'      => '555555',
                'template_col'       => 4,
                'voucher_min_value'  => 0,
                'voucher_max_value'  => 10000,
                'voucher_expiry_type' => 'days',
                'voucher_expiry'     => 60,
                'voucher_terms_note' => 'Note: The voucher is valid for 60 days and can be redeemed at giftvoucher. A cash payment is not possible.',
                'pdf_footer_email'   => 'giftvoucher@example.com',
                'post_shipping'      => 1,
                'shipping_method'    => '5.99 : Express Shipping - $5.99, 3.99 : Standard Shipping - $3.99',
                'preview_button'     => 1,
                'currency_position'  => 'Left',
                'test_mode'          => 0,
                'per_invoice'        => 0,
                'pdf_footer_url'     => $baseUrl
            ]);
        }

        $tableName = $installer->getTable('giftvouchers_list');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $sql = "CREATE TABLE giftvouchers_list (
                id int(11) NOT NULL AUTO_INCREMENT,
                order_type varchar(255) NOT NULL DEFAULT 'vouchers',
                template_id int(11) NOT NULL,
                itemcat_id int(11) NOT NULL,
                item_id int(11) NOT NULL,
                buying_for varchar(255) NOT NULL DEFAULT 'someone_else',
                from_name varchar(255) NOT NULL,
                to_name varchar(255) NOT NULL,
                amount float NOT NULL,
                message text NOT NULL,
                firstname varchar(255) NOT NULL,
                lastname varchar(255) NOT NULL,
                email varchar(255) NOT NULL,
                address text NOT NULL,
                postcode varchar(10) NOT NULL,
                pay_method varchar(255) NOT NULL,
                shipping_type varchar(255) NOT NULL DEFAULT 'shipping_as_email',
                shipping_email varchar(255) NOT NULL,
                shipping_method varchar(255) NOT NULL,
                expiry varchar(100) NOT NULL,
                couponcode bigint(25) NOT NULL,
                voucherpdf_link text NOT NULL,
                voucheradd_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                status varchar(10) NOT NULL DEFAULT 'unused',
                payment_status varchar(10) NOT NULL DEFAULT 'Not Pay',
                PRIMARY KEY (id)
              ) $charset_collate";

            $connection->query($sql);
        }

        $tableName = $installer->getTable('giftvouchers_template');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $sql = "CREATE TABLE giftvouchers_template (
                id int(11) NOT NULL AUTO_INCREMENT,
                title text NOT NULL,
                image int(11) DEFAULT NULL,
                image_style varchar(100) DEFAULT NULL,
                orderno int(11) NOT NULL DEFAULT '0',
                active int(11) NOT NULL DEFAULT '0',
                templateadd_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
              ) $charset_collate";

            $connection->query($sql);


            $sql = 'INSERT INTO giftvouchers_template(title,active) VALUES("Demo Template", "1")';
            $connection->query($sql);
        }

        $tableName = $installer->getTable('giftvouchers_activity');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $sql = "CREATE TABLE giftvouchers_activity (
                id int(11) NOT NULL AUTO_INCREMENT,
                voucher_id int(11) NOT NULL,
                user_id int(11) NOT NULL,
                action varchar(60) DEFAULT NULL,
                amount decimal(15,6),
                note text NOT NULL,
                activity_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
              ) $charset_collate";

            $connection->query($sql);
        }


        $installer->endSetup();
    }
}
