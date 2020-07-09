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
var config = {
    map: {
        '*': {
            mtEditor_jquery: 'Magetrend_GiftCard/js/mteditor/jquery-2.1.3',
            mtEditor_bootstrap: 'Magetrend_GiftCard/js/mteditor/bootstrap.min',
            mtEditor_cookie: 'Magetrend_GiftCard/js/mteditor/jquery.cookie',
            mtEditor_jquery_ui: 'Magetrend_GiftCard/js/mteditor/jquery-ui',
            mtEditor_ui_widget: 'Magetrend_GiftCard/js/mteditor/jquery.ui.widget',
            mtEditor_iframe_transport: 'Magetrend_GiftCard/js/mteditor/jquery.iframe-transport',
            mtEditor_file_upload: 'Magetrend_GiftCard/js/mteditor/jquery.fileupload',
            mtEditor_color_picker: 'Magetrend_GiftCard/js/mteditor/colorpicker',
            mtEditor_popup: 'Magetrend_GiftCard/js/mteditor/popup',
            mtEditor_metis_menu: 'Magetrend_GiftCard/js/mteditor/jquery.metisMenu',
            mtEditor_editor: 'Magetrend_GiftCard/js/mteditor/editor'

        },
        shim: {
            'mtEditor_bootstrap': {
                deps: ['jquery']
            },
            'mtEditor_cookie': {
                deps: ['jquery']
            },
            'mtEditor_jquery_ui': {
                deps: ['jquery']
            },
            'mtEditor_ui_widget': {
                deps: ['jquery']
            },
            'mtEditor_iframe_transport': {
                deps: ['jquery']
            },
            'mtEditor_file_upload': {
                deps: ['jquery']
            },
            'mtEditor_color_picker': {
                deps: ['jquery']
            },
            'mtEditor_popup': {
                deps: ['jquery']
            },
            'mtEditor_metis_menu': {
                deps: ['jquery']
            },
            'mtEditor_editor': {
                deps: ['jquery']
            }
        }
    }
};