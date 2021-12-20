

define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    'priceBox',
    'jquery/ui',
    'jquery/jquery.parsequery'
], function ($, _, mageTemplate) {
    'use strict';

    $.widget('mage.giftCardForm', {
        options: {
            formSelector: '#block_giftcard',
            formToggleSelector: '#block_giftcard .title',
            formContent: '#block_giftcard .content',
            formSubmitSelector: '#block_giftcard .action.add-giftcard',
            formFormSelector: 'form#add_gift_card_form',
            giftCardCodeSelector: '#gift_card_code',
            giftCardCodeErrorSelector: '#gift_card_code_error',
            formKeySelector: '#add_gift_card_form input[name=form_key]',
            giftCardConfig: {},
            isFormOpen: false

        },

        _create: function () {
            this._hiddeAllMessage();
            this._setupEvents();
            this._setupForm();
        },

        _setupForm: function () {
            if (this.options.isFormOpen) {
                $(this.options.formToggleSelector).trigger('click');
            }
        },

        _setupEvents: function () {
             $(this.options.formFormSelector).on('submit', this, this._addGiftCardEvent);
             $(this.options.formToggleSelector).on('click', this, this._formToggleEvent);
             $(this.options.formSubmitSelector).on('click', this, this._submitFormEvent);
        },

        _submitFormEvent: function (event) {
            event.data._submitForm();
            event.preventDefault();
        },

        _submitForm: function () {
            $(this.options.formFormSelector).submit();
        },

        _addGiftCardEvent: function (event) {
            if (event.data.options.isAjax || !event.data._addGiftCard()) {
                event.preventDefault();
            }
        },

        _addGiftCard: function() {
            this._hiddeAllMessage();
            if (!this._validateForm()) {
                return false;
            }

            this._showLoading(true);
            if (this.options.isAjax) {
                return this._addGiftCardAjax();
            }

            return true;
        },

        _addGiftCardAjax: function () {
            this._sendRequest($(this.options.formFormSelector).attr('action'), {
                    giftCardCode: $(this.options.giftCardCodeSelector).val(),
                    form_key: $(this.options.formKeySelector).val(),
                    ajax: true
                }, function (response, dataObject) {
                    dataObject._showLoading(false);
                }
            );

            return true;
        },

        /**
         * Send ajax request via POST
         * @param actionUrl
         * @param postData
         * @param callBack
         * @private
         */
        _sendRequest: function (actionUrl, postData, callBack) {
            var dataObject = this;
            $.ajax({
                url: actionUrl,
                type: 'POST',
                dataType: 'json',
                data: postData,
                complete: function(response) {
                    callBack(response, dataObject);
                }
            });
        },

        /**
         * Validate gift card form
         * @private
         */
        _validateForm: function () {
            var giftCardCode = $(this.options.giftCardCodeSelector).val();
            if (giftCardCode.length < 4) {
                this._showError(this.options.giftCardConfig.translate.badGiftCardCode);
                return false;
            }

            return true;
        },

        _showLoading: function (show) {
            if (show) {
                $(this.options.formSubmitSelector+' span').text(this.options.giftCardConfig.translate.applying);
            } else {
                $(this.options.formSubmitSelector+' span').text(this.options.giftCardConfig.translate.applyGiftCard);
            }
        },

        _showError: function(msg)
        {
            $(this.options.giftCardCodeErrorSelector).html(msg).show();
        },

        _hiddeAllMessage: function () {
            $(this.options.giftCardCodeErrorSelector).hide();
        },

        _formToggleEvent: function (event) {
            event.data._toggleForm();
        },

        _toggleForm: function() {
            $(this.options.formContent).toggle();
            if ($(this.options.formContent).is(':visible')) {
                $(this.options.formSelector).addClass('active');
            } else {
                $(this.options.formSelector).removeClass('active');
            }
        }
    });

    return $.mage.giftCardForm;
});
