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

var mtEditor = (function($) {

    var config = {
        log: 0,
        templateMaxWidth: 600,
        minWindowHeight: 600,
        fontFamilyOptions: {},
        template_id: 0,
        name: '',
        store: '',
        designConfig: {},
        design: 'one',
        color: {},
        text: {},
        image: {}
    };

    var request;

    var init = function(options) {
        $.extend(config, options);
        config.log = 1;

        initPopup();
        initBlock();
        initLayout();
        initEvent();
        initFileUpload();
        loadImageList();
        initGCEvents();
        initSaveEvent();
        initImagePreview();
    };

    var initImagePreview = function() {
        $(document).ajaxComplete(function( event, xhr, settings ) {
            if (settings.url == config.action.preview+'?isAjax=1' ) {
                var response = xhr.responseJSON;
                if (response.error) {
                    $('#giftcard-preview').html(response.error);
                } else {
                    $('#giftcard-preview').html('<img style="width: 100%" src="'+response.image+'" />');
                }
            }
            hideLoading();
        });
    };

    var initGCEvents = function() {
        $('.giftcard-design').click(function(){
            $('.giftcard-design').removeClass('active');
            $(this).addClass('active');
            config.design = $(this).data('design-code');
            updateImage();
        });
        $('*[data-design-code="'+config.design+'"]').addClass('active');
    };

    var initBlock = function() {
        if (!config.template_id || config.template_id == 0) {
            initNewTemplate();
            return false;
        } else {
            updateImage();
        }

    };

    /**
     * Init text tool panel
     */
    var initTextPanel = function()
    {
        $('#giftcard_text').html('');
        $.each(config.text[config.design], function(key, item) {
            if (item.value.length > 30) {
                var inputHtml = '<label class="long last">'+item.label+'</label><textarea class="long last gift-card-text" data-text-key="'+item.code+'"  name="'+item.code+'">'+item.value+'</textarea>';
            } else {
                var inputHtml = '<label class="long last">'+item.label+'</label><input class="long last gift-card-text" data-text-key="'+item.code+'" type="text" name="'+item.code+'" value="'+item.value+'"/>';
            }
            $('#giftcard_text').append(inputHtml);
        });

        $('input.gift-card-text').change(function() {
            config.text[config.design][$(this).data('text-key')]['value'] = $(this).val();
            updateImage();
        });

        $('textarea.gift-card-text').change(function() {
            config.text[config.design][$(this).data('text-key')]['value'] = $(this).val();
            updateImage();
        });
    };

    /**
     * Init text tool panel
     */
    var initSizePanel = function()
    {
        $('#giftcard_size').html('');
        if (!config.size[config.design]) {
            $('.empty-panel').show();
            $('.data-panel').hide();
            return;
        }

        $('.empty-panel').hide();
        $('.data-panel').show();
        $.each(config.size[config.design], function(key, item) {
            $('#giftcard_size').append(
                '<label class="long last">'+item.label+'</label><input class="long last gift-card-size" data-size-key="'+item.code+'" type="text" name="'+item.code+'" value="'+item.value+'"/>'
            );
        });

        $('input.gift-card-size').change(function() {
            config.size[config.design][$(this).data('size-key')]['value'] = $(this).val();
            updateImage();
        });
    };

    /**
     * Init Color change tools panel
     */
    var initColorPanel = function() {
        var listId = 'mtedit_bgcolor';
        $('#'+listId+' ul').html('');
        var counter = 0;
        $.each(config.color[config.design], function(key, colorConfig) {
            var cssColor = '#000000';
            if (colorPicker.isDarkColor(hexToRgb(colorConfig.value))) {
                cssColor = '#ffffff';
            }
            $('#'+listId+' ul').append('<li><span>'+colorConfig.label+'</span> <input data-color-key="'+colorConfig.code+'" class="color gift-card-color" name="'+colorConfig.code+'" value="'+colorConfig.value+'" style="background-color: '+colorConfig.value+'; color: '+cssColor+';"></li>');
        });
        var lastValue = {};

        $('input.gift-card-color').change(function() {
            var colorCode = $(this).val();
            if (lastValue[$(this).data('color-key')] == colorCode || colorCode.length != 7) {
                return;
            }
            lastValue[$(this).data('color-key')] = colorCode;
            config.color[config.design][$(this).data('color-key')]['value'] = colorCode;
            updateImage();
        });

        colorPicker.init();
    };

    var initImage = function() {
        $('.mteditor-image-list li').unbind('click').click(function() {
            var src = $(this).find('img').attr('src');
            config.image[config.design]['image_1']['value'] = src;
            updateImage();
        });
    };

    /**
     * Create new template popup windows
     */
    var initNewTemplate = function() {
        var validate = function() {
            var name = $('#esns_box_layer input[name="name"]').val();
            var design = $('#esns_box_layer select[name="design"]').val();
            if (name && design) {
                $('#esns_box_layer button[data-action="1"]').removeAttr('disabled');
                return true;
            } else {
                $('#esns_box_layer button[data-action="1"]').attr('disabled', 'disabled');
                return false;
            }
        };

        popup.content({
            contentSelector: '#init_new_template',
            disableClose: true
        }, function() {
        }, function() {
        }, function() {
        });

        $('select[name="design"]').change(function(){
            validate();
        });

        $('#esns_box_layer input[name="name"]').keyup(function(){
            validate();
        });

        $('#esns_box_layer button[data-action="0"]').click(function(){
            window.location = config.action.back;
        });

        $('#esns_box_layer button[data-action="1"]').click(function(){
            if (validate()) {
                var name = $('#esns_box_layer input[name="name"]').val();
                var design = $('#esns_box_layer select[name="design"]').val();
                var storeId = $('#esns_box_layer select[name="store_id"]').val();
                sendRequest(config.action.createTemplateUrl, {
                        name: name,
                        store_id: storeId,
                        design: design
                    }, function(response) {
                        if (response.success && response.success == 1) {
                            window.location = response.redirectTo;
                        } else if (response.error) {
                            $('#esns_box_layer .response-error').html(response.error);
                        }
                    }
                );
            }
        });
    };

    var loadImageList = function() {
        $.each(config.imageList, function(key, value){
            $('.mteditor-image-list').prepend('<li><img src="'+value+'"/></li>');
        });
    };



    var initLayout = function() {
        reloadSizes();
        $('#main-menu').metisMenu();
    };

    var reloadSizes = function() {
        var windowHeight = $(window).height();
        if (config.minWindowHeight > windowHeight) {
            windowHeight = config.minWindowHeight;
        }
        $('#editor_wrapper').height(windowHeight+'px');
        $('.sidebar').height(windowHeight+'px');
        $('#page-wrapper').height(windowHeight+'px');
        $('#email_body').css('max-width', config.templateMaxWidth+'px');
        $('.tools').height(windowHeight+'px');
    };

    var initEvent = function() {
        $( "a" ).click(function( event ) {
            event.preventDefault();
        });

        $(window).resize(function(){
            reloadSizes();
        });

        $('a[data-selector="edit-layout"]').unbind('click').click(function(){
            openLayoutTools();
        });

        $('a[data-selector="edit-style"]').unbind('click').click(function(){
            openStyleTools();
        });

        $('a[data-selector="edit-text"]').unbind('click').click(function(){
            openTextTools();
        });

        $('a[data-selector="edit-size"]').unbind('click').click(function(){
            openSizeTools();
        });

        $('a[data-selector="edit-image"]').unbind('click').click(function(){
            openImageTools();
        });


        $('.nav li a').click(function(){
            $('.nav li a').removeClass('active');
            $(this).addClass('active');
        });


        $('button[data-action="back"]').click(function(){
            popup.confirm({
                'msg': 'Do you want to save the changes?',
                'disableAutoClose': true
            }, function(){
                $('#esns_box_layer a[data-action="1"]').text('Saving...');
                sendRequest(
                    config.action.saveUrl,
                    getData(),
                    function(){
                        window.location = config.action.back;
                    }
                );
            }, function(){
                window.location = config.action.back;
            });
        });

        $('a[data-action="change-info"]').click(function(){
            popup.content({
                contentSelector: '#change_info',
                disableClose: false,
                disableCloseAfterSubmit: true
            }, function(){
                $('#esns_box_content .response-error').hide();
                $('#esns_box_content .response-success').hide();
                $('#esns_box_content input[name="name"]').val(config.name);
                $('#esns_box_content select[name="store_id"]').val(config.store_id);
            }, function(){
                $('#esns_box_layer a[data-action="1"]').text('Saving...');
                var newName = $('#esns_box_content input[name="name"]').val();
                var newStoreId = $('#esns_box_content select[name="store_id"]').val();
                sendRequest(config.action.saveInfo, {
                        name: newName,
                        store_id:  newStoreId,
                        template_id: config.template_id
                    }, function(response) {
                        if (response.success == 1) {
                            config.store_id = newStoreId;
                            config.name = newName;
                            $('#esns_box_content .response-error').hide();
                            $('#esns_box_content .response-success').text('Template has been saved successful!').show();
                            $('#esns_box_layer a[data-action="1"]').text('Save');
                            setTimeout(function(){
                                popup.config.disableClose = false;
                                popup.close(true);
                            }, 2000);
                        } else if (response.error.length > 0) {
                            $('#esns_box_content .response-error').text(response.error).show();
                            $('#esns_box_content .response-success').hide();
                        }

                    }
                );
            }, function(){
                popup.close();
            });
        });

        $('a[data-action="delete-template"]').click(function(){
            popup.confirm({
                'msg': 'Are you sure? Do You want to delete this template?',
                'disableAutoClose': true
            }, function(){
                $('#esns_box_layer a[data-action="1"]').text('Deleting...');
                sendRequest(config.action.deleteTemplateAjax, {
                        id: config.template_id
                    }, function(response) {
                        if (response.success == 1) {
                            window.location = config.action.back;
                        } else if (response.error.length > 0) {

                        }
                    }
                );
            }, function(){
                popup.close(true);
            });
        });
    };

    var openLayoutTools = function() {
        openTools('edit-layout');
    };

    var openImageTools = function() {
        initImage();
        openTools('edit-image');
    };

    var openStyleTools = function() {
        initColorPanel();
        openTools('edit-style');
    };

    var openTextTools = function() {
        initTextPanel();
        openTools('edit-text');
    };
    var openSizeTools = function() {
        initSizePanel();
        openTools('edit-size');
    };

    var openTools = function(className) {
        var openPanel = '.tools.' + className;
        if ($(openPanel).hasClass('active')) {
            return false;
        }
        $('.nav a[data-selector]').removeClass('active');
        $('.nav a[data-selector="'+className+'"]').addClass('active');
        $( '.tools').css('z-index', 3);
        $(openPanel).css('z-index', 4);
        $( '.tools.active' ).animate({
            left: '-108'
        }, 200, function() {
            $(openPanel).animate({
                left: '200'
            }, 200).addClass('active');
        }).removeClass('active');
    };

    var initFileUpload = function() {
        $('#imageupload').fileupload({
            singleFileUploads: true,
            url: config.action.uploadUrl+'?isAjax=1',
            formData: {form_key: config.formKey},
            dropZone: undefined
        }).bind('fileuploadchange', function (e, data) {
            $('#imageupload .mteditor_upload_button').text('Uploading....');
            $('#imageupload input[type="file"]').attr('disabled', 'disabled');
            $('#imageupload .fileupload-buttonbar i.glyphicon').removeClass('glyphicon-plus').addClass('glyphicon-upload');
        }).bind('fileuploaddone', function (e, data) {
            $('#imageupload .mteditor_upload_button').text('Select image');
            $('#imageupload input[type="file"]').removeAttr('disabled');
            $('#imageupload .fileupload-buttonbar i.glyphicon').addClass('glyphicon-plus').removeClass('glyphicon-upload');

            var result = data.result;
            if (result.success == 1) {
                $('.mteditor-image-list').prepend('<li><img src="'+result.fileUrl+'"/></li>');
                initImage();
            }
        });
    };

    var initPopup = function(){
        popup.init();
    };

    /**
     * Init save event
     */
    var initSaveEvent = function () {
        $('button[data-action="save"]').click(function(){
            showLoading();
            sendRequest(
                config.action.saveUrl,
                getData(),
                function(){
                    hideLoading();
                }
            )
        });
    };

    var sendRequest = function(url, data, callBack) {
        data.form_key = config.formKey;
        if(request && request.readyState != 4){
            request.abort();
        }
        request = $.ajax({
            url: url+'?isAjax=1',
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(response) {
                callBack(response);
            }
        });
    };

    var log = function(msg) {
        if (config.log == 1) {
            console.log(msg);
        }
    };

    var showLoading = function() {
        popup.content({
            contentSelector: '#loading',
            disableClose: true,
            disableCloseAfterSubmit: true
        }, function(){}, function(){});
    };

    var hideLoading = function()
    {
        popup.close(true);
    };

    var hexToRgb = function(hex) {
        var c;
        if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
            c= hex.substring(1).split('');
            if(c.length== 3){
                c= [c[0], c[0], c[1], c[1], c[2], c[2]];
            }
            c= '0x'+c.join('');
            return 'rgb('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+')';
        }
    };

    var updateImage = function()
    {
        var data = getData();
        showLoading();
        sendRequest(config.action.preview, data, function(response){
            hideLoading();
        });
    };

    var createImagePreviewUrl = function()
    {
        var url = config.action.preview;

        url = url+'data/'+Base64.encode(JSON.stringify(data));

        return url;
    };

    var getData = function()
    {
        var data = {
            template_id: config.template_id,
            color: config.color[config.design],
            text: config.text[config.design],
            image: config.image[config.design],
            size: config.size[config.design],
            design: config.design,
            image_width: $('#giftcard-preview').width()
        };
        return data;
    };

    return {
        init: init,
        config: config,
        log: log,
        initImage: initImage,
        openStyleTools: openStyleTools,
        openImageTools: openImageTools
    };

})(jQuery);
