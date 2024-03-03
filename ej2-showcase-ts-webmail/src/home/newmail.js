define(["require", "exports", "@syncfusion/ej2-navigations", "@syncfusion/ej2-buttons", "@syncfusion/ej2-dropdowns", "./datasource", "@syncfusion/ej2-data"], function (require, exports, ej2_navigations_1, ej2_buttons_1, ej2_dropdowns_1, datasource_1, ej2_data_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    var autoToList = new ej2_dropdowns_1.MultiSelect();
    var autoCCList = new ej2_dropdowns_1.MultiSelect();
    var toolbarHeader1 = new ej2_navigations_1.Toolbar();
    var toolbarMail = new ej2_navigations_1.Toolbar();
    exports.isNewWindow = true;
    exports.selectedToolbarItem = '';
    window.newmail = function () {
        renderToolbar();
        createMailForm();
        var mentionObject = new ej2_dropdowns_1.Mention({
            dataSource: datasource_1.getContacts(),
            fields: { text: 'text' },
        });
        mentionObject.appendTo('#mailContentMessage');
    };
    function createMailForm() {
        var toButton = new ej2_buttons_1.Button();
        toButton.appendTo('#btnTo');
        var ccButton = new ej2_buttons_1.Button();
        ccButton.appendTo('#btnCc');
        var sendButton = new ej2_buttons_1.Button();
        sendButton.appendTo('#btnSend');
        var discardButton = new ej2_buttons_1.Button();
        discardButton.appendTo('#btnDiscard');
        autoToList = new ej2_dropdowns_1.MultiSelect({
            dataSource: datasource_1.getContacts(), placeholder: '', width: 'calc(100% - 60px)',
            cssClass: 'ac-new-mail',
            fields: { text: 'MailId', value: 'MailId' },
            delimiterChar: ';',
            popupWidth: '300px',
            itemTemplate: '<div class="multiselect-template parent-div"><img class="contacts-item-image-style"' +
                'src="${Image}" alt="employee"/>' +
                '<div class="contacts-item-text-style"> <div> ${text} </div> </div>' +
                '<div class="contacts-item-subtext-style"> ${MailId} </div>' +
                '</div>',
            valueTemplate: '<div>'
                + '<img class="contacts-value-img-style" src="${Image}" alt="employee"/>'
                + '<div class="contacts-value-text-style"> ${MailId} </div></div>',
            mode: 'Box',
            allowFiltering: true,
            filtering: function (e) {
                var query = new ej2_data_1.Query();
                query = (e.text !== '') ? query.where('text', 'startswith', e.text, true) : query;
                e.updateData(datasource_1.getContacts(), query);
            }
        });
        autoCCList = new ej2_dropdowns_1.MultiSelect({
            dataSource: datasource_1.getContacts(), placeholder: '', width: 'calc(100% - 60px)',
            cssClass: 'ac-new-mail',
            fields: { text: 'MailId', value: 'MailId' },
            popupWidth: '300px',
            itemTemplate: '<div class="multiselect-template parent-div"><img class="contacts-item-image-style"' +
                'src="${Image}" alt="employee"/>' +
                '<div class="contacts-item-text-style"> <div> ${text} </div> </div>' +
                '<div class="contacts-item-subtext-style"> ${MailId} </div>' +
                '</div>',
            valueTemplate: '<div>'
                + '<img class="contacts-value-img-style" src="${Image}" alt="employee"/>'
                + '<div class="contacts-value-text-style"> ${MailId} </div></div>',
            mode: 'Box',
            allowFiltering: true,
            filtering: function (e) {
                var query = new ej2_data_1.Query();
                query = (e.text !== '') ? query.where('text', 'startswith', e.text, true) : query;
                e.updateData(datasource_1.getContacts(), query);
            }
        });
        document.getElementById('txtSubject').onfocus = function (e) {
            document.getElementsByClassName('mail-subject e-input-group')[0].classList.add('e-input-focus');
        };
        document.getElementById('txtSubject').onblur = function (e) {
            document.getElementsByClassName('mail-subject e-input-group')[0].classList.remove('e-input-focus');
        };
        toolbarMail = new ej2_navigations_1.Toolbar({
            items: [
                { prefixIcon: 'ej-icon-Font tb-icons', tooltipText: 'Font' },
                { prefixIcon: 'ej-icon-Font-Size path2 tb-icons', tooltipText: 'Font Size' },
                { prefixIcon: 'ej-icon-Bold tb-icons', tooltipText: 'Bold' },
                { prefixIcon: 'ej-icon-Italic tb-icons', tooltipText: 'Italic' },
                { prefixIcon: 'ej-icon-Underlined tb-icons', tooltipText: 'Underline' },
                { prefixIcon: 'ej-icon-Highlight tb-icons', tooltipText: 'Highlight' },
                { prefixIcon: 'ej-icon-Font-Color-Icon tb-icons', tooltipText: 'Font color' },
                { prefixIcon: 'ej-icon-Bullets tb-icons', tooltipText: 'Bullets' },
                { prefixIcon: 'ej-icon-Numbering tb-icons', tooltipText: 'Numbering' },
                { prefixIcon: 'ej-icon-Decr-Indent tb-icons', tooltipText: 'Decrease Indent' },
                { prefixIcon: 'ej-icon-Incr-Indent tb-icons', tooltipText: 'Increase Indent' },
                { prefixIcon: 'ej-icon-Left-aligned tb-icons', tooltipText: 'Decrease Indent' },
                { prefixIcon: 'ej-icon-Centre-aligned tb-icons', tooltipText: 'Increase Indent' },
                { prefixIcon: 'ej-icon-Right-aligned tb-icons', tooltipText: 'Decrease Indent' },
                { prefixIcon: 'ej-icon-Hyperlink tb-icons', tooltipText: 'Hyperlink' }
            ],
            height: '100%',
            width: '100%',
            overflowMode: 'Scrollable'
        });
        toolbarMail.appendTo('#new_email_toolbar');
    }
    function renderToolbar() {
        var moreTemplate = '<input type="text" tabindex="1" id="moreList2" />';
        toolbarHeader1 = new ej2_navigations_1.Toolbar({
            items: [
                { prefixIcon: 'ej-icon-Send tb-icons', text: 'Send', tooltipText: 'Send' },
                { prefixIcon: 'ej-icon-Attach tb-icons', text: 'Attach', tooltipText: 'Attach' },
                { text: 'Discard' },
                { template: moreTemplate, cssClass: 'tb-item-more tb-item-more-mail', tooltipText: 'More actions' },
                {
                    prefixIcon: 'ej-icon-Copy tb-icons', align: 'Right',
                    tooltipText: 'Edit in a separate window', cssClass: 'tb-item-window-mail'
                },
                {
                    prefixIcon: 'ej-icon-Close tb-icons', align: 'Right',
                    tooltipText: 'Close', cssClass: 'tb-item-back-mail'
                },
            ],
            height: '100%'
        });
        toolbarHeader1.overflowMode = 'Popup';
        toolbarHeader1.appendTo('#toolbar_newmail');
        toolbarHeader1.clicked = toolbarNewMailClick;
        renderMoreList();
        toolbarHeader1.refreshOverflow();
    }
    function renderMoreList() {
        var themeList = [
            { text: 'Save draft' }, { text: 'Show From' }, { text: 'Check Names' }, { text: 'Show message options' }
        ];
        var dropDownListObj = new ej2_dropdowns_1.DropDownList({
            dataSource: themeList,
            fields: { text: 'text' },
            valueTemplate: '<div class="tb-dropdowns" style ="font-size: 16px;margin-top: -2px;">' +
                '<span class="e-btn-icon e-icons ej-icon-More"></span></div>',
            popupHeight: '150px',
            popupWidth: '150px',
            value: 'Show From',
            width: '100%'
        });
        dropDownListObj.appendTo('#moreList2');
    }
    function toolbarNewMailClick(args) {
        if (args.item) {
            if (args.item.cssClass === 'tb-item-window-mail' || args.item.cssClass === 'tb-item-back-mail') {
                exports.selectedToolbarItem = args.item.cssClass;
            }
            else if (args.item.text === 'Send' || args.item.text === 'Discard') {
                exports.selectedToolbarItem = args.item.text;
            }
        }
    }
    function resetSelectedToolbarItem(text) {
        exports.selectedToolbarItem = text;
    }
    exports.resetSelectedToolbarItem = resetSelectedToolbarItem;
    function clearMailForm() {
        if (autoCCList.value) {
            autoCCList.value = [];
        }
        if (autoToList.value) {
            autoToList.value = [];
        }
        document.getElementById('txtSubject').value = '';
        document.getElementById('mailContentMessage').innerHTML = '';
    }
    function showMailDialog(option, selectedMessage) {
        clearMailForm();
        var key = '';
        toolbarHeader1.refreshOverflow();
        toolbarMail.refreshOverflow();
        if (selectedMessage) {
            if (option === 'Reply Tooltip') {
                key = 'Email';
                autoToList.value = [selectedMessage[key].toString()];
            }
            else {
                if (option !== 'New') {
                    if (option !== 'Forward') {
                        if (option !== 'Reply') {
                            key = 'CCMail';
                            document.getElementById('autoCc').value = selectedMessage[key].toString();
                        }
                        key = 'Email';
                        document.getElementById('autoTo').value = selectedMessage[key].toString();
                    }
                    key = 'ContactTitle';
                    document.getElementById('txtSubject').value = selectedMessage[key].toString();
                    key = 'Message';
                    if (selectedMessage[key]) {
                        document.getElementById('mailContentMessage').innerHTML = selectedMessage[key].toString();
                    }
                    else {
                        document.getElementById('mailContentMessage').innerHTML =
                            decodeURI('%3Cdiv%20id=%22box%22%20style=%22padding:10px;%20border:%20none;%20height:%20auto;' +
                                '%22%20contenteditable=%22true%22%20data-gramm_id=%223898c552-c710-10db-69ec-08371185eb3f%22%20' +
                                'data-gramm=%22true%22%20spellcheck=%22false%22%20data-gramm_editor=%22true%22%3E%3Cp%20class=%22' +
                                'MsoNormal%22%3EDear%20Name,&nbsp;%3C/p%3E%0A%0A%3Cp%20class=%22MsoNormal%22%3EI%20really%20' +
                                'appreciate%20your%20understanding%20and%20support%20regarding%0Athe%20changes%20we\'re%20' +
                                'making%20to%20the%20project%20plan.&nbsp;%3C/p%3E%0A%0A%3Cp%20class=%22' +
                                'MsoNormal%22%3EThank%20you%20for%20your%20confidence%20in%20me.%20I\'m%20sure%20' +
                                'you\'re%20going%0Ato%20be%20pleased%20with%20the%20results.&nbsp;%3C/p%3E%0A%0A%3Cp%20class=%22' +
                                'MsoNormal%22%3EBest%20Regards,%3Cbr%3E%0AYour%20Name%3Co:p%3E%3C/o:p%3E%3C/p%3E%0A%0A%20%20%20%20%3C/div%3E');
                    }
                }
            }
        }
    }
    exports.showMailDialog = showMailDialog;
});
