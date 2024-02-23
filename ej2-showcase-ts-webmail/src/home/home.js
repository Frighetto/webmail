define(["require", "exports", "@syncfusion/ej2-navigations", "@syncfusion/ej2-lists", "@syncfusion/ej2-buttons", "@syncfusion/ej2-dropdowns", "@syncfusion/ej2-popups", "@syncfusion/ej2-base", "@syncfusion/ej2-layouts", "./datasource", "./newmail", "./readingpane"], function (require, exports, ej2_navigations_1, ej2_lists_1, ej2_buttons_1, ej2_dropdowns_1, ej2_popups_1, ej2_base_1, ej2_layouts_1, datasource_1, newmail_1, readingpane_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.grpListObj = new ej2_lists_1.ListView();
    exports.messageDataSource = null;
    exports.dlgSentMail = new ej2_popups_1.Dialog();
    exports.dlgSentMailNew = new ej2_popups_1.Dialog();
    exports.dlgDiscard = new ej2_popups_1.Dialog();
    exports.dlgDiscardNew = new ej2_popups_1.Dialog();
    exports.dlgNewWindow = new ej2_popups_1.Dialog();
    exports.dlgReplyAllWindow = new ej2_popups_1.Dialog();
    var dlgFavorite = new ej2_popups_1.Dialog();
    var ddlReplyAll = new ej2_dropdowns_1.DropDownList();
    var dlgDelete = new ej2_popups_1.Dialog();
    var dropdownSelect = false;
    var acrdnObj = new ej2_navigations_1.Accordion();
    var treeObj = new ej2_navigations_1.TreeView();
    var toolbarHeader = new ej2_navigations_1.Toolbar();
    var toolbarMobile = new ej2_navigations_1.Toolbar();
    var defaultSidebar;
    var sidebarHeader;
    var splitObj;
    var treeContextMenu = new ej2_navigations_1.ContextMenu();
    var filterContextMenu = new ej2_navigations_1.ContextMenu();
    var selectedListElement = null;
    var acSearchMobile = new ej2_dropdowns_1.AutoComplete();
    var popup1;
    var treeviewSelectedData = null;
    var treeSelectedElement = null;
    var selectedFolderName = '';
    var treeDataSource = [];
    var isMenuClick = false;
    var isItemClick = false;
    var lastIndex = 31;
    var hoverOnPopup = false;
    var isNewMailClick = false;
    var actionMessageID = null;
    var foldersFullName = null;
    var folderData = null;
    var messageDataSourceNew = null;
    var downloadURI = null;
    var userName = null;
    var userMail = null;
    window.home = function () {
        var contentWrapper = document.getElementsByClassName('content-wrapper')[0];
        contentWrapper.onclick = hideSideBar;
        window.onresize = onWindowResize;
        window.onload = onWindowResize;
        document.onclick = documentClick;
        document.ondblclick = documentDoubleClick;
        var ip = location.host;
        if (ip.indexOf(":") !== -1) {
            ip = ip.split(":")[0];
        }
        var url = 'http://' + ip + '/webmail/api.php';
        var http_request = new XMLHttpRequest();
        http_request.open("POST", url, true);
        http_request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_request.onload = function (e) {
            var response = http_request.response;
            response = JSON.parse(response);
            messageDataSourceNew = response.messageDataSourceNew;
            userName = response.userName;
            userMail = response.userMail;
            folderData = response.folderData;
            foldersFullName = response.folders_full_name;
            downloadURI = response.server_uri;
            downloadURI = downloadURI.substr(0, downloadURI.indexOf("api.php")) + "download.php";
            renderMainSection();
            treeObj.selectedNodes = ['1'];
            renderToolbarMobile();
            renderSearchSection();
            createHeader();
            updateLoginDetails();
            renderFilterContextMenu();
            renderMailDialogs();
        };
        var loginid = document.getElementById('loginbutton').getAttribute("value");
        var params = 'loginid=' + encodeURIComponent(loginid);
        params = params + '&action=' + encodeURIComponent("getDataSource");
        http_request.send(params);
        var logout_url = 'http://' + ip + '/webmail/sair.php';
        document.getElementById('logout').setAttribute("onclick", "parent.document.location='" + logout_url + "'");
        var ajaxHTML;
        ajaxHTML = new ej2_base_1.Ajax('src/home/newmail.html', 'GET', true);
        ajaxHTML.send().then(function (value) {
            document.getElementById('newmailContent').innerHTML = value.toString();
            window.newmail();
            document.getElementById('btnSend').onclick = sendClick;
            document.getElementById('btnDiscard').onclick = discardButtonClick;
        });
        ajaxHTML = new ej2_base_1.Ajax('src/home/readingpane.html', 'GET', true);
        ajaxHTML.send().then(function (value) {
            document.getElementById('reading-pane-popup').innerHTML = value.toString();
            window.readingpane();
        });
        var appObject = new ej2_navigations_1.AppBar({
            colorMode: 'Dark'
        });
        appObject.appendTo("#appbar");
        defaultSidebar = new ej2_navigations_1.Sidebar({
            width: "280px",
            type: "Push",
            target: ".content-wrapper",
            enablePersistence: true,
            enableGestures: false,
            showBackdrop: false
        });
        defaultSidebar.appendTo('#sideBar');
        sidebarHeader = new ej2_navigations_1.Sidebar({
            position: "Right",
            width: "330px",
            type: "Push",
            target: ".content-wrapper",
            enableGestures: false,
        });
        sidebarHeader.appendTo('#headerSidebar');
    };
    function renderMainSection() {
        treeDataSource = folderData;
        treeObj = new ej2_navigations_1.TreeView({
            fields: { dataSource: treeDataSource, id: 'ID', text: 'Name', parentID: 'PID', hasChildren: 'HasChild', expanded: 'Expanded' },
            nodeTemplate: '<div class="treeviewdiv">' +
                '<div style="float:left">' +
                '<span class="treeName">${Name}</span>' +
                '</div>' +
                '<div class="count" style="margin-left: 5px; float:right">' +
                '<span class="treeCount ${Name}" >${Count}</span>' +
                '</div>' +
                '<button title="${FavoriteMessage}" class="treeview-btn-temp">' +
                '<span class="e-btn-icon ej-icon-${Favorite} ${Name}"></span>' +
                '</button>' +
                '</div>',
            nodeSelected: nodeSelected,
        });
        treeObj.appendTo('#tree');
        exports.messageDataSource = messageDataSourceNew;
        exports.messageDataSource = sortList(exports.messageDataSource);
        exports.grpListObj = new ej2_lists_1.ListView({
            dataSource: exports.messageDataSource,
            template: getListTemplate(),
            fields: { id: 'ContactID', text: 'text' },
            sortOrder: 'None'
        });
        exports.grpListObj.select = select;
        exports.grpListObj.appendTo('#listview-grp');
        acrdnObj.appendTo('#accordian');
        var replyTemplate = '<input type="text" tabindex="1" id="replyAllList" />';
        var movetoTemplate = '<input type="text" tabindex="1" id="moveToList" />';
        var categoryTemplate = '<input type="text" tabindex="1" id="categoryList" />';
        var moreTemplate = '<input type="text" tabindex="1" id="moreList" />';
        toolbarHeader = new ej2_navigations_1.Toolbar({
            items: [
                {
                    prefixIcon: 'ej-icon-New tb-icons', text: 'New', tooltipText: 'Write a new message',
                    cssClass: 'tb-item-new-mail'
                },
                {
                    prefixIcon: 'ej-icon-Mark-as-read tb-icons', text: 'Mark all as read', tooltipText: 'Mark all as read',
                    cssClass: 'tb-item-mark-read'
                },
                {
                    prefixIcon: 'ej-icon-Reply-All tb-icons', template: replyTemplate,
                    cssClass: 'tb-item-Selected tb-item-replyAll', tooltipText: 'Reply All'
                },
                {
                    prefixIcon: 'ej-icon-Delete tb-icons', text: 'Trash',
                    cssClass: 'tb-item-Selected', tooltipText: 'Delete'
                },
                {
                    text: 'Junk', cssClass: 'tb-item-Selected',
                    tooltipText: 'Mark the sender as unsafe and delete the message'
                },
                { template: movetoTemplate, cssClass: 'tb-item-Selected', tooltipText: 'Move To' },
                { template: moreTemplate, cssClass: 'tb-item-more tb-item-Selected', tooltipText: 'More actions' },
                {
                    prefixIcon: 'ej-icon-Copy tb-icons', align: 'Right',
                    tooltipText: 'Open in a separate window', cssClass: 'tb-item-Selected'
                },
            ],
            width: '100%',
            height: '100%'
        });
        toolbarHeader.overflowMode = 'Popup';
        toolbarHeader.appendTo('#toolbar_align');
        toolbarHeader.clicked = toolbarClick;
        renderTreeContextMenu();
        renderMoveToList();
        renderMoreList();
        renderReplyAllList();
    }
    function renderMoveToList() {
        var themeList = [];
        for (var i = 0; i < folderData.length; i = i + 1) {
            themeList.push({ text: folderData[i].Name });
        }
        var dropDownListObj = new ej2_dropdowns_1.DropDownList({
            dataSource: themeList,
            fields: { text: 'text', value: 'text' },
            valueTemplate: '<div class="tb-dropdowns"> Move to </div>',
            popupHeight: '310px',
            popupWidth: '150px',
            value: "",
            width: '80px',
            select: moveToSelect,
            allowFiltering: true
        });
        dropDownListObj.beforeOpen = dropDownListObj.clear;
        dropDownListObj.appendTo('#moveToList');
    }
    function renderReplyAllList() {
        var themeList = [
            { text: 'Reply' }, { text: 'Reply All' }, { text: 'Forward' }
        ];
        ddlReplyAll = new ej2_dropdowns_1.DropDownList({
            dataSource: themeList,
            fields: { text: 'text' },
            valueTemplate: '<div>' +
                '<div style="float:left;margin-top: 1px;">' +
                '<span style="font-weight:bold;" class="e-btn-icon ej-icon-Reply-All tb-icons e-icons tb-icon-rply-all">' +
                '</span>' +
                '</div>' +
                '<div class="tb-dropdowns" style="float:left" > Reply All </div>' +
                '<div>',
            popupHeight: '150px',
            popupWidth: '150px',
            width: '115px',
            change: replyAllSelect,
            value: 'Reply All'
        });
        ddlReplyAll.appendTo('#replyAllList');
    }
    function renderCategoryList() {
        var themeList = [
            { text: 'Blue category', color: 'blue' }, { text: 'Red category', color: 'red' },
            { text: 'Orange category', color: 'orange' }, { text: 'Purple category', color: 'purple' },
            { text: 'Green category', color: 'green' }, { text: 'Yellow category', color: 'yellow' },
            { text: 'Clear categories', color: 'transparent' }
        ];
        var dropDownListObj = new ej2_dropdowns_1.DropDownList({
            dataSource: themeList,
            fields: { text: 'text' },
            valueTemplate: '<div class="tb-dropdowns"> Categories </div>',
            itemTemplate: '<div class="e-list" style="padding:0px 15px">' +
                '<div style="width: 20px;float:left;top: 8px;position: absolute;">' +
                '<div style="width: 10px; height:15px; border-color: ${color}; background-color: ${color};"></div>' +
                '</div>' +
                '<div style="width: 170px;float:left;margin-left: 15px;font-size:12px;"><span>${text}</span></div>' +
                '</div>',
            popupHeight: '250px',
            popupWidth: '230px',
            value: 'Blue category',
            width: '100px'
        });
        dropDownListObj.appendTo('#categoryList');
    }
    function renderMoreList() {
        var themeList = [
            { text: 'Mark as unread' }, { text: 'Mark as read' }, { text: 'Flag' }, { text: 'Clear Flag' }
        ];
        var dropDownListObj = new ej2_dropdowns_1.DropDownList({
            dataSource: themeList,
            fields: { text: 'text' },
            valueTemplate: '<div class="tb-dropdowns" style ="font-size: 16px;margin-top: -2px;"><span class="e-btn-icon e-icons ej-icon-More"></span> </div>',
            popupHeight: '150px',
            popupWidth: '150px',
            value: 'Mark as read',
            width: '100%'
        });
        dropDownListObj.appendTo('#moreList');
        dropDownListObj.select = moreItemSelect;
    }
    function renderMoreListMobile() {
        var themeList = [
            { text: 'Mark as unread' }, { text: 'Mark as read' }, { text: 'Flag' },
            { text: 'Clear Flag' }
        ];
        var dropDownListObj1 = new ej2_dropdowns_1.DropDownList({
            dataSource: themeList,
            fields: { text: 'text' },
            valueTemplate: '<div class="tb-dropdowns" style ="font-size: 16px;margin-top: -2px;"><span class="e-btn-icon e-icons ej-icon-More"></span> </div>',
            popupHeight: '150px',
            popupWidth: '150px',
            value: 'Mark as read',
            width: '100%'
        });
        dropDownListObj1.appendTo('#moreList1');
        dropDownListObj1.select = moreItemSelect;
    }
    function replyAllSelect(args) {
        if (args.itemData.text) {
            showNewMailPopup(args.itemData.text);
        }
        dropdownSelect = true;
    }
    function moveToSelect(args) {
        var ip = location.host;
        if (ip.indexOf(":") !== -1) {
            ip = ip.split(":")[0];
        }
        var url = 'http://' + ip + '/webmail/api.php';
        var http_request = new XMLHttpRequest();
        http_request.open("POST", url, true);
        http_request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_request.onload = function (e) {
            if (args.itemData.text) {
                var selectedMessage_1 = getSelectedMessage();
                var key = 'Folder';
                var key2 = 'ContactID';
                selectedMessage_1[key] = args.itemData.text;
                selectedMessage_1[key2] = http_request.response;
                exports.grpListObj.dataSource = getFilteredDataSource(exports.messageDataSource, 'Folder', selectedFolderName);
                showEmptyMessage();
            }
        };
        var loginid = document.getElementById('loginbutton').getAttribute("value");
        var params = 'loginid=' + encodeURIComponent(loginid);
        params += '&folder=' + encodeURIComponent(foldersFullName[selectedFolderName]);
        params += '&movement_folder=' + encodeURIComponent(foldersFullName[args.itemData.text]);
        var selectedMessage = getSelectedMessage();
        params += '&id=' + encodeURIComponent(selectedMessage['ContactID'].toString());
        params += '&action=' + encodeURIComponent("move");
        http_request.send(params);
    }
    function moreItemSelect(args) {
        var selectedMessage = getSelectedMessage();
        var key = '';
        if (args.itemData.text === 'Mark as read') {
            key = 'ContactID';
            setReadStyleMessage(selectedMessage[key].toString(), 'Read');
        }
        else if (args.itemData.text === 'Mark as unread') {
            key = 'ContactID';
            setReadStyleMessage(selectedMessage[key].toString(), 'Unread');
        }
        else {
            var target = selectedListElement.getElementsByClassName('e-btn-icon ej-icon-Flag_1')[0];
            flagListItem(target, selectedMessage);
        }
    }
    function renderToolbarMobile() {
        var ele = '<div class="search-div1" style= "width:90%" >' +
            '<div style="height: 30px">' +
            '<input type="text" id="txtSearch1" tabindex="1" style="height: 30px" />' +
            '</div>' +
            '</div>';
        var moreTemplate = '<input type="text" tabindex="1" id="moreList1" />';
        toolbarMobile = new ej2_navigations_1.Toolbar({
            items: [
                { prefixIcon: 'ej-icon-Menu tb-icons', cssClass: 'tb-item-menu tb-item-front' },
                { prefixIcon: 'ej-icon-Back', cssClass: 'tb-item-back-icon tb-item-back' },
                { text: 'Inbox', cssClass: 'tb-item-inbox tb-item-front' },
                { text: 'Compose', cssClass: 'tb-item-inbox tb-item-back tb-item-newmail-option' },
                { template: ele, cssClass: 'tb-item-search-option', align: 'Center' },
                { prefixIcon: 'ej-icon-Close', tooltipText: 'Clear', align: 'Right', cssClass: 'tb-item-search-option' },
                { prefixIcon: 'ej-icon-Search', tooltipText: 'Search Mail', align: 'Right', cssClass: 'tb-item-front' },
                { prefixIcon: 'ej-icon-Create-New', tooltipText: 'Write a new message', align: 'Right', cssClass: 'tb-item-front' },
                { prefixIcon: 'ej-icon-Send', tooltipText: 'Send', align: 'Right', cssClass: 'tb-item-back tb-item-newmail-option' },
                { prefixIcon: 'ej-icon-Attach', tooltipText: 'Attach', align: 'Right', cssClass: 'tb-item-back  tb-item-newmail-option' },
                { prefixIcon: 'ej-icon-Delete', tooltipText: 'Delete', align: 'Right', cssClass: 'tb-item-back' },
                { prefixIcon: 'ej-icon-Reply-All', tooltipText: 'Reply All', align: 'Right', cssClass: 'tb-item-back' },
                { template: moreTemplate, cssClass: 'tb-item-more tb-item-back', tooltipText: 'More actions', align: 'Right' },
            ],
            width: '100%',
            height: '100%',
        });
        toolbarMobile.clicked = toolbarClick;
        toolbarMobile.appendTo('#toolbar_mobile');
        acSearchMobile = new ej2_dropdowns_1.AutoComplete({
            dataSource: datasource_1.getContacts(),
            fields: { text: 'MailId', value: 'MailId' },
            placeholder: 'Search Mail and People',
            change: autoSearchSelect,
            focus: autoSearchFocus1,
            blur: autoSearchBlur1,
            cssClass: 'search-text-box-device',
            showClearButton: false
        });
        acSearchMobile.appendTo('#txtSearch1');
        renderMoreListMobile();
    }
    function getListTemplate() {
        return '<div class="template-container ${ReadStyle}-parent">' +
            '<div style="height:30px; pointer-events:none;">' +
            '<div class="sender-style" style="float:left; margin-top: 2px">${text}</div>' +
            '<div style="right:25px; position: absolute; margin-top: 2px; pointer-events:all;">' +
            '<button id="btnListDelete" title="Delete" class="listview-btn">' +
            '<span class="e-btn-icon ej-icon-Delete"></span>' +
            '</button>' +
            '<button id="btnListFlag" title="${FlagTitle}" class="listview-btn">' +
            '<span class="e-btn-icon ej-icon-Flag_1 ${Flagged}"></span>' +
            '</button>' +
            '<button id="btnListRead" title="${ReadTitle}" class="listview-btn">' +
            '<span class="e-btn-icon ej-icon-Mark-as-read"></span>' +
            '</button>' +
            '</div>' +
            '</div>' +
            '<div class="subjectstyle ${ReadStyle}" style="height:25px">' +
            '<div style="float:left; margin-top: 2px">${ContactTitle}</div>' +
            '<div style="right:25px; position: absolute; margin-top: 2px">' +
            '<span>${Time}</span>' +
            '</div>' +
            '</div>' +
            '<div class="descriptionstyle">${Message}</div>' +
            '</div>';
    }
    function showToolbarItems(displayType) {
        var selectedFolder = document.getElementsByClassName('tb-item-Selected');
        for (var i = 0; i < selectedFolder.length; i++) {
            selectedFolder[i].style.display = displayType;
        }
    }
    exports.showToolbarItems = showToolbarItems;
    function nodeSelected(args) {
        updateNewMailClick();
        var key = 'id';
        treeSelectedElement = args.node;
        treeviewSelectedData = getTreeData1(args.nodeData[key].toString());
        selectedFolderName = args.node.getElementsByClassName('treeName')[0].innerHTML;
        exports.grpListObj.dataSource = sortList(getFilteredDataSource(exports.messageDataSource, 'Folder', selectedFolderName));
        showEmptyMessage();
        document.getElementById('spanFilterText').innerHTML = selectedFolderName;
        var element1 = document.getElementsByClassName('tb-item-inbox')[0];
        if (element1) {
            element1 = element1.getElementsByClassName('e-tbar-btn-text')[0];
            element1.innerHTML = selectedFolderName;
        }
        hideSideBar();
    }
    function showEmptyMessage() {
        updateNewMailClick();
        document.getElementById('emptyMessageDiv').style.display = '';
        document.getElementById('mailarea').style.display = 'none';
        document.getElementById('accordian').style.display = 'none';
        showToolbarItems('none');
        var readingPane = document.getElementById('reading-pane-div');
        readingPane.className = readingPane.className.replace(' new-mail', '');
        document.getElementsByClassName('tb-item-new-mail')[0].style.display = 'inline-flex';
        document.getElementsByClassName('tb-item-mark-read')[0].style.display = 'inline-flex';
        document.getElementById('toolbar_align').style.display = '';
        document.getElementById('rp-accContent').innerHTML = null;
    }
    exports.showEmptyMessage = showEmptyMessage;
    function showSelectedMessage() {
        updateNewMailClick();
        document.getElementById('emptyMessageDiv').style.display = 'none';
        document.getElementById('mailarea').style.display = 'none';
        document.getElementById('accordian').style.display = '';
        showToolbarItems('inline-flex');
        var readingPane = document.getElementById('reading-pane-div');
        readingPane.className = readingPane.className.replace(' new-mail', '');
        document.getElementsByClassName('tb-item-new-mail')[0].style.display = 'inline-flex';
        document.getElementsByClassName('tb-item-mark-read')[0].style.display = 'none';
        document.getElementById('toolbar_align').style.display = '';
    }
    exports.showSelectedMessage = showSelectedMessage;
    function getFilteredDataSource(dataSource, columnName, columnValue) {
        var folderData = [];
        for (var i = 0; i < dataSource.length; i++) {
            var data = dataSource[i];
            if (data[columnName] && data[columnName].toString() === columnValue) {
                folderData.push(data);
            }
        }
        return folderData;
    }
    function setReadStyleMessage(contactID, readStyle) {
        var data = getSelectedMessage();
        selectedFolderName = data.Folder;
        if (data !== null) {
            var newFlag = void 0;
            var key = 'ReadStyle';
            data[key] = readStyle;
            key = 'ReadTitle';
            var readNode = selectedListElement.getElementsByClassName('e-btn-icon ej-icon-Mark-as-read')[0].parentNode;
            if (readStyle === 'Read') {
                data[key] = 'Mark as unread';
                selectedListElement.getElementsByClassName('subjectstyle')[0].className = 'subjectstyle';
                selectedListElement.getElementsByClassName('template-container')[0].className = 'template-container';
                readNode.title = 'Mark as unread';
                setReadCount('Unread');
                newFlag = "seen";
            }
            else {
                data[key] = 'Mark as read';
                readNode.title = 'Mark as read';
                selectedListElement.getElementsByClassName('subjectstyle')[0].className = 'subjectstyle Unread';
                selectedListElement.getElementsByClassName('template-container')[0].className = 'template-container Unread-parent';
                setReadCount('Read');
                newFlag = "unseen";
            }
            var ip = location.host;
            if (ip.indexOf(":") !== -1) {
                ip = ip.split(":")[0];
            }
            var url = 'http://' + ip + '/webmail/api.php';
            var http_request = new XMLHttpRequest();
            http_request.open("POST", url, true);
            http_request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            http_request.onload = function (e) { };
            var loginid = document.getElementById('loginbutton').getAttribute("value");
            var params = 'loginid=' + encodeURIComponent(loginid);
            params += '&folder=' + encodeURIComponent(foldersFullName[selectedFolderName]);
            params += '&ids_to_flag=' + encodeURIComponent(contactID.toString());
            params += '&action=' + encodeURIComponent("flag");
            params += '&flag=' + encodeURIComponent(newFlag);
            http_request.send(params);
        }
    }
    function getSelectedMessage() {
        if (exports.grpListObj.getSelectedItems()) {
            var selectedData = exports.grpListObj.getSelectedItems().data;
            var key = 'ContactID';
            var key2 = 'Folder';
            for (var i = 0; i < exports.messageDataSource.length; i++) {
                if (exports.messageDataSource[i][key].toString() === selectedData[key].toString() && exports.messageDataSource[i][key2].toString() === selectedData[key2].toString()) {
                    return exports.messageDataSource[i];
                }
            }
        }
        return null;
    }
    function renderTreeContextMenu() {
        var menuItems = [
            { text: 'Create new subfolder' }, { text: 'Rename' }, { text: 'Trash' },
            { text: 'Add to Favorites' }, { text: 'Mark all as read' }
        ];
        var menuOptions = { target: '#tree', items: menuItems };
        treeContextMenu = new ej2_navigations_1.ContextMenu(menuOptions, '#treeContextMenu');
        treeContextMenu.beforeOpen = treeMenuBeforeOpen;
        treeContextMenu.select = treeMenuSelect;
    }
    function treeMenuSelect(args) {
        if (args.item) {
            var target = treeSelectedElement.getElementsByClassName('e-btn-icon')[0];
            if (args.item.text === 'Create new subfolder') {
                lastIndex += 1;
                var key = 'ID';
                var item = {
                    'ID': lastIndex, 'PID': treeviewSelectedData[key].toString(), 'Name': 'New Folder',
                    'HasChild': false, 'Expanded': false, 'Count': '',
                    'Favorite': 'Favorite', 'FavoriteMessage': 'Add to Favorites'
                };
                treeObj.addNodes([item], null, null);
                treeDataSource.push(item);
                treeObj.beginEdit(lastIndex.toString());
            }
            else if (args.item.text === 'Rename') {
                treeObj.beginEdit(treeviewSelectedData.ID.toString());
            }
            else if (args.item.text === 'Trash') {
                if (selectedFolderName === 'Deleted Items') {
                    dlgDelete.content = '<div class="dlg-content-style"><span>Are you sure you want to permanently' +
                        ' delete all the items in Deleted items?</span></div>';
                    dlgDelete.header = 'Delete All';
                }
                else {
                    dlgDelete.content = '<div class="dlg-content-style"><span>Are you sure you want to move all ' +
                        'its content to Deleted items?</span></div>';
                    dlgDelete.header = 'Delete Folder Items';
                }
                dlgDelete.show();
            }
            else if (args.item.text === 'Mark all as read') {
                markAllRead();
            }
            else if (args.item.text === 'Add to Favorites') {
                favoriteAction('add', target);
            }
            else if (args.item.text === 'Remove from Favorites') {
                favoriteAction('Remove', target);
            }
        }
    }
    function markAllRead() {
        var dataSource = getFilteredDataSource(exports.messageDataSource, 'Folder', selectedFolderName);
        for (var i = 0; i < dataSource.length; i++) {
            var key = 'ReadStyle';
            dataSource[i][key] = 'Read';
            key = 'ReadTitle';
            dataSource[i][key] = 'Mark as unread';
            setReadCount('Unread');
        }
        exports.grpListObj.dataSource = dataSource;
    }
    function treeMenuBeforeOpen(args) {
        var key = 'PID';
        var parentNode = treeviewSelectedData[key].toString();
        key = 'Favorite';
        var favorite = treeviewSelectedData[key].toString();
        if (favorite === 'Favorite-Composite') {
            favorite = 'Remove from Favorites';
        }
        else {
            favorite = 'Add to Favorites';
        }
        treeContextMenu.items[3].text = favorite;
        treeContextMenu.dataBind();
        if (parentNode === '1') {
            treeContextMenu.hideItems(['Create new subfolder', 'Rename']);
        }
        else {
            treeContextMenu.showItems(['Create new subfolder', 'Rename']);
        }
    }
    function setCategory(category, dataSource) {
        for (var i = 0; i < dataSource.length; i++) {
            var data = dataSource[i];
            var key = 'category';
            data[key] = category;
        }
        return dataSource;
    }
    function setReadCount(readType) {
        var selectedFolder = document.getElementsByClassName('treeCount ' + selectedFolderName);
        for (var i = 0; i < selectedFolder.length; i++) {
            var count = selectedFolder[i].innerHTML === '' ? 0 : Number(selectedFolder[i].innerHTML);
            if (readType === 'Unread') {
                if (count > 0) {
                    count -= 1;
                }
            }
            else {
                count += 1;
            }
            selectedFolder[i].innerHTML = count === 0 ? '' : count.toString();
        }
    }
    function select(args) {
        showEmptyMessage();
        selectedListElement = args.item;
        var data = args.data;
        setTimeout(function () {
            if (data['ContactID'].toString() !== actionMessageID) {
                var ip = location.host;
                if (ip.indexOf(":") !== -1) {
                    ip = ip.split(":")[0];
                }
                var url = 'http://' + ip + '/webmail/api.php';
                var http_request = new XMLHttpRequest();
                http_request.open("POST", url, true);
                http_request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                http_request.onload = function (e) {
                    data['Message'] = http_request.response;
                    ;
                    var key = 'ReadStyle';
                    if (data[key].toString() !== 'Read') {
                        key = 'ContactID';
                        setReadStyleMessage(data[key].toString(), 'Read');
                        isItemClick = true;
                    }
                    var contentElement = document.getElementsByClassName('row content')[0];
                    if (window.innerWidth < 605) {
                        contentElement.className = 'row content sidebar-hide show-reading-pane';
                    }
                    var contentWrapper = document.getElementsByClassName('content-wrapper')[0];
                    contentWrapper.className = 'content-wrapper';
                    showSelectedMessage();
                    key = 'ContactTitle';
                    if (acrdnObj.items.length === 0) {
                        acrdnObj.addItem({
                            content: '#accodianContent', expanded: true, header: data[key].toString()
                        });
                    }
                    var headerTitle = document.getElementById('accordian');
                    key = 'ContactTitle';
                    headerTitle.getElementsByClassName('e-acrdn-header-content')[0].innerHTML = data[key].toString();
                    key = 'Image';
                    key = 'text';
                    document.getElementById('sub').innerHTML = data[key].toString();
                    key = 'Date';
                    var dateString = data[key].toString();
                    key = 'Time';
                    document.getElementById('date').innerHTML = dateString + ' ' + data[key].toString();
                    key = 'CC';
                    document.getElementById('to').innerHTML = (data[key].toString()).replace(/,/g, ' ; ');
                    key = 'Message';
                    document.getElementById('accContent').innerHTML = data[key].toString();
                    document.getElementById('rp-accContent').innerHTML = data[key].toString();
                };
                var loginid = document.getElementById('loginbutton').getAttribute("value");
                var params = 'loginid=' + encodeURIComponent(loginid);
                params += '&id=' + encodeURIComponent(data['ContactID'].toString());
                params += '&folder=' + encodeURIComponent(foldersFullName[data['Folder'].toString()]);
                params += '&downloaduri=' + encodeURIComponent(downloadURI);
                params += '&action=' + encodeURIComponent("read");
                http_request.send(params);
            }
        }, 100);
    }
    function renderSearchSection() {
        var filterButton = new ej2_buttons_1.Button({
            iconCss: 'ej-icon-Dropdown-arrow',
            cssClass: 'btn-shadow-hide'
        });
        filterButton.appendTo('#btnFilter');
        document.getElementById('btnFilter').onclick = btnFilterClick;
        var atcObj = new ej2_dropdowns_1.AutoComplete({
            dataSource: exports.messageDataSource,
            fields: { text: 'ContactTitle', value: 'ContactTitle' },
            placeholder: 'Search',
            change: autoSearchSelect,
            focus: autoSearchFocus,
            blur: autoSearchBlur,
            showClearButton: false
        });
        atcObj.appendTo('#txtSearch');
        var button = new ej2_buttons_1.Button({
            iconCss: 'ej-icon-Search',
            cssClass: 'btn-shadow-hide'
        });
        button.appendTo('#btnSearch');
    }
    function autoSearchSelect(args) {
        if (args.value) {
            var dataSource = exports.messageDataSource;
            exports.grpListObj.dataSource = getFilteredDataSource(dataSource, 'ContactTitle', args.value.toString());
            document.getElementById('spanFilterText').innerHTML = 'All Search';
        }
        else {
            resetSelectedFolderData();
        }
    }
    function autoSearchFocus(args) {
        document.getElementsByClassName('search-div')[0].classList.add('search-focus');
    }
    function autoSearchBlur(args) {
        document.getElementsByClassName('search-div')[0].classList.remove('search-focus');
    }
    function autoSearchFocus1(args) {
        document.getElementsByClassName('search-div1')[0].classList.add('search-focus');
    }
    function autoSearchBlur1(args) {
        document.getElementsByClassName('search-div1')[0].classList.remove('search-focus');
    }
    function resetSelectedFolderData() {
        document.getElementById('spanFilterText').innerHTML = selectedFolderName;
        var dataSource = getFilteredDataSource(exports.messageDataSource, 'Folder', selectedFolderName);
        exports.grpListObj.dataSource = dataSource;
        clearFilterMenu();
        filterContextMenu.items[0].iconCss = 'ej-icon-Right';
        filterContextMenu.dataBind();
    }
    function btnFilterClick() {
        var clientRect = document.getElementById('btnFilter').getBoundingClientRect();
        filterContextMenu.open(clientRect.top + 25, clientRect.left);
    }
    function renderMailDialogs() {
        dlgFavorite = new ej2_popups_1.Dialog({
            width: '335px',
            header: 'Remove From Favorites',
            content: '<div class="dlg-content-style"><span>Do you want to remove from favorites?</span></div>',
            target: document.body,
            isModal: true,
            closeOnEscape: true,
            animationSettings: { effect: 'None' },
            buttons: [
                {
                    click: btnFavoriteOKClick, buttonModel: { content: 'Yes', cssClass: 'e-flat', isPrimary: true }
                },
                {
                    click: btnFavoriteCancelClick, buttonModel: { content: 'No', cssClass: 'e-flat' }
                }
            ]
        });
        dlgFavorite.appendTo('#favoriteDialog');
        dlgFavorite.hide();
        dlgDelete = new ej2_popups_1.Dialog({
            width: '335px',
            header: 'Delete Folder Items',
            content: '<div class="dlg-content-style"><span>Are you sure you want to move all its content to Deleted items?</span></div>',
            target: document.body,
            isModal: true,
            closeOnEscape: true,
            animationSettings: { effect: 'None' },
            buttons: [
                {
                    click: btnDeleteOKClick, buttonModel: { content: 'Yes', cssClass: 'e-flat', isPrimary: true }
                },
                {
                    click: btnDeleteCancelClick, buttonModel: { content: 'No', cssClass: 'e-flat' }
                }
            ]
        });
        dlgDelete.appendTo('#deleteDialog');
        dlgDelete.hide();
        exports.dlgNewWindow = new ej2_popups_1.Dialog({
            width: '80%',
            height: '93%',
            target: document.body,
            animationSettings: { effect: 'None' },
            closeOnEscape: true,
            allowDragging: true
        });
        exports.dlgNewWindow.appendTo('#newMailSeparateDialog');
        exports.dlgNewWindow.hide();
        exports.dlgReplyAllWindow = new ej2_popups_1.Dialog({
            width: '80%',
            height: '93%',
            target: document.body,
            animationSettings: { effect: 'None' },
            closeOnEscape: true,
        });
        exports.dlgReplyAllWindow.appendTo('#replyAllSeparateDialog');
        exports.dlgReplyAllWindow.hide();
        exports.dlgSentMail = sentMailDialog('#sentMailDialog', true);
        exports.dlgSentMailNew = sentMailDialog('#sentMailNewWindow', false);
        exports.dlgDiscard = discardDialog('#discardDialog', true);
        exports.dlgDiscardNew = discardDialog('#discardNewWindow', false);
    }
    function sentMailDialog(name, isModal) {
        var dialog = new ej2_popups_1.Dialog({
            width: '335px',
            header: 'Mail Sent',
            content: '<div class="dlg-content-style"><span>Your mail has been sent successfully.</span></div>',
            target: document.body,
            isModal: isModal,
            closeOnEscape: true,
            animationSettings: { effect: 'None' },
            buttons: [{
                    click: sendExitClick,
                    buttonModel: { content: 'OK', cssClass: 'e-flat', isPrimary: true }
                }]
        });
        dialog.appendTo(name);
        dialog.hide();
        return dialog;
    }
    function discardDialog(name, isModal) {
        var dialog = new ej2_popups_1.Dialog({
            width: '335px',
            header: 'Discard message',
            content: '<div id=' + name + 'discardOk' + ' style="cursor:pointer" class="dlg-content-style1">' +
                '<span style="color:white" class="dlg-discard-text-style">Discard</span> <br/>' +
                '<span style="color:white; font-weight:normal" class="dlg-discard-child-text-style">This message will be deleted</span>' +
                '</div> <br/>' +
                '<div id=' + name + 'discardCancel' + ' style="cursor:pointer" class="dlg-content-style">' +
                '<span class="dlg-discard-text-style">Don' + "'" + 't Discard</span> <br/>' +
                '<span style="font-weight:normal" class="dlg-discard-child-text-style">Return to the message for further editing</span>' +
                '</div>',
            target: document.body,
            isModal: isModal,
            closeOnEscape: true,
            animationSettings: { effect: 'None' }
        });
        dialog.appendTo(name);
        document.getElementById(name + 'discardOk').onclick = discardOkClick;
        document.getElementById(name + 'discardCancel').onclick = discardCancelClick;
        dialog.hide();
        return dialog;
    }
    function discardOkClick() {
        discardClick();
    }
    function discardCancelClick() {
        if (exports.dlgNewWindow.visible || exports.dlgReplyAllWindow.visible) {
            exports.dlgDiscardNew.hide();
        }
        else {
            exports.dlgDiscard.hide();
        }
    }
    function btnFavoriteOKClick() {
        var key = 'PID';
        var parentID = treeviewSelectedData[key].toString();
        if (parentID === '1') {
            key = 'ID';
            removeTreeItem(treeviewSelectedData[key].toString());
            treeDataSource.splice(treeDataSource.indexOf(treeviewSelectedData), 1);
        }
        else {
            for (var i = 0; i < treeDataSource.length; i++) {
                var key_1 = 'PID';
                var treeData = treeDataSource[i];
                if (treeData[key_1] && treeData[key_1].toString() === '1') {
                    key_1 = 'Name';
                    if (treeData[key_1].toString() === selectedFolderName) {
                        key_1 = 'ID';
                        removeTreeItem(treeData[key_1].toString());
                        treeDataSource.splice(i, 1);
                        break;
                    }
                }
            }
        }
        dlgFavorite.hide();
    }
    function btnFavoriteCancelClick() {
        dlgFavorite.hide();
    }
    function btnDeleteOKClick() {
        var folderMessages = getFilteredDataSource(exports.messageDataSource, 'Folder', selectedFolderName);
        if (selectedFolderName === 'Deleted Items') {
            for (var i = 0; i < folderMessages.length; i++) {
                exports.messageDataSource.splice(exports.messageDataSource.indexOf(folderMessages[i]), 1);
            }
        }
        else {
            for (var i = 0; i < folderMessages.length; i++) {
                var key = 'Folder';
                folderMessages[i][key] = 'Deleted Items';
            }
        }
        exports.grpListObj.dataSource = [];
        showEmptyMessage();
        dlgDelete.hide();
    }
    function btnDeleteCancelClick() {
        dlgDelete.hide();
    }
    function removeTreeItem(id) {
        treeObj.removeNodes([id]);
        var element = document.getElementsByClassName('ej-icon-Favorite-Composite ' + selectedFolderName)[0];
        element.className = 'e-btn-icon ej-icon-Favorite ' + selectedFolderName;
        var parent = element.parentNode;
        parent.title = 'Add to Favorites';
        var key = 'FavoriteMessage';
        treeviewSelectedData[key] = 'Add to Favorites';
        key = 'Favorite';
        treeviewSelectedData[key] = 'Favorite';
    }
    function updateLoginDetails() {
        document.getElementById('username').textContent = userName;
        document.getElementById('username1').textContent = userName;
        document.getElementById('usermail').textContent = userMail;
        document.getElementById('usermail1').textContent = userMail;
    }
    function createHeader() {
        var notificationButton = new ej2_buttons_1.Button({ iconCss: 'ej-icon-Notify', cssClass: 'btn-shadow-hide' });
        notificationButton.appendTo('#btnNotification');
        var btnSettings = new ej2_buttons_1.Button({ iconCss: 'ej-icon-Settings', cssClass: 'btn-shadow-hide' });
        btnSettings.appendTo('#btnSettings');
        var btnAbout = new ej2_buttons_1.Button({ iconCss: 'ej-icon-Help-white', cssClass: 'btn-shadow-hide' });
        btnAbout.appendTo('#btnAbout');
        var btnLoginName = new ej2_buttons_1.Button({ content: userName, cssClass: 'btn-shadow-hide' });
        btnLoginName.appendTo('#btnLoginName');
        var closeButton = new ej2_buttons_1.Button({ iconCss: 'ej-icon-Close', cssClass: 'btn-shadow-hide' });
        closeButton.appendTo('#btnCloseButton');
        document.getElementById('btnCloseButton').onclick = btnCloseClick;
        var closeButton1 = new ej2_buttons_1.Button({ iconCss: 'ej-icon-Close', cssClass: 'btn-shadow-hide' });
        closeButton.appendTo('#btnCloseButton1');
        document.getElementById('btnCloseButton1').onclick = hideSideBar;
    }
    function btnCloseClick() {
        var contentWrapper = document.getElementsByClassName('row content')[0];
        contentWrapper.className = contentWrapper.className.replace(' show-header-content', '');
        var headerRP = document.getElementsByClassName('header-right-pane selected')[0];
        headerRP.className = 'header-right-pane';
        sidebarHeader.hide();
    }
    function sortList(listItems) {
        for (var i = 0; i < listItems.length; i++) {
            listItems[i] = setCategory1(listItems[i]);
        }
        return listItems;
    }
    function setCategory1(listItem) {
        var key = 'Date';
        var date = new Date(listItem[key]);
        var currentData = new Date();
        var oldDate = date.getDate();
        var oldMonth = date.getMonth();
        var oldYear = date.getFullYear();
        var currentDate = currentData.getDate();
        var currentMonth = currentData.getMonth();
        var currentYear = currentData.getFullYear();
        key = 'category';
        if (oldYear === currentYear) {
            if (oldMonth === currentMonth) {
                if (oldDate === currentDate) {
                    listItem[key] = 'Today';
                }
                else if (oldDate === currentDate - 1) {
                    listItem[key] = 'Yesterday';
                }
                else if (oldDate + 8 >= currentDate) {
                    listItem[key] = 'Last Week';
                }
                else if (oldDate + 15 >= currentDate) {
                    listItem[key] = 'Two Weeks Ago';
                }
                else if (oldDate + 22 >= currentDate) {
                    listItem[key] = 'Three Weeks Ago';
                }
                else {
                    listItem[key] = 'Earlier this Month';
                }
            }
            else {
                listItem[key] = 'Last Month';
            }
        }
        else {
            listItem[key] = 'Older';
        }
        return listItem;
    }
    function headerContent(headerElement) {
        var headerRP = document.getElementsByClassName('header-right-pane selected')[0];
        if (headerRP) {
            headerRP.className = 'header-right-pane';
        }
        var contentWrapper = document.getElementsByClassName('row content')[0];
        contentWrapper.className = contentWrapper.className.replace(' show-header-content', '') + ' show-header-content';
        var notificationElement = document.getElementsByClassName('notification-content')[0];
        var settingsElement = document.getElementsByClassName('settings-content')[0];
        var aboutElement = document.getElementsByClassName('about-content')[0];
        var userElement = document.getElementsByClassName('profile-content')[0];
        var txtHeaderContent = document.getElementById('txtHeaderContent');
        notificationElement.style.display = 'none';
        settingsElement.style.display = 'none';
        aboutElement.style.display = 'none';
        userElement.style.display = 'none';
        headerElement.className = headerElement.className + ' ' + 'selected';
        switch (headerElement.id) {
            case 'notification-div':
                notificationElement.style.display = 'block';
                txtHeaderContent.innerHTML = 'Notification';
                break;
            case 'settings-div':
                settingsElement.style.display = 'block';
                txtHeaderContent.innerHTML = 'Settings';
                break;
            case 'profile-div':
                userElement.style.display = 'block';
                txtHeaderContent.innerHTML = 'My accounts';
                break;
            case 'about-div':
                aboutElement.style.display = 'block';
                txtHeaderContent.innerHTML = 'Help';
                break;
        }
    }
    function flagRequest(url, loginid, folder, ids_to_flag, flag) {
        var http_request = new XMLHttpRequest();
        http_request.open("POST", url, true);
        http_request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_request.onload = function (e) {
            markAllRead();
        };
        var params = 'loginid=' + encodeURIComponent(loginid);
        params += '&folder=' + encodeURIComponent(folder);
        params += '&flag=' + flag;
        params += '&ids_to_flag=' + ids_to_flag;
        params += '&action=' + encodeURIComponent("flag");
        http_request.send(params);
    }
    function toolbarClick(args) {
        if (args.item) {
            if (args.item.prefixIcon === 'ej-icon-Menu tb-icons') {
                defaultSidebar.show();
                isMenuClick = true;
            }
            else if (args.item.prefixIcon === 'ej-icon-Back') {
                var contentElement = document.getElementsByClassName('row content')[0];
                contentElement.className = contentElement.className.replace('show-reading-pane', 'show-message-pane');
                var contentWrapper = document.getElementsByClassName('content-wrapper')[0];
                if (contentWrapper.className.indexOf('show-search-option') !== -1) {
                    resetSelectedFolderData();
                }
                contentWrapper.className = 'content-wrapper';
            }
            else if (args.item.prefixIcon === 'ej-icon-Mark-as-read tb-icons') {
                var ip = location.host;
                if (ip.indexOf(":") !== -1) {
                    ip = ip.split(":")[0];
                }
                var url = 'http://' + ip + '/webmail/api.php';
                var loginid = document.getElementById('loginbutton').getAttribute("value");
                var folder = foldersFullName[selectedFolderName];
                var ids_to_flag = "";
                if (selectedFolderName === "") {
                    var folders = [];
                    var folders_names = [];
                    var key = 'Folder';
                    for (var i = 0; i < exports.messageDataSource.length; i++) {
                        var folder_1 = exports.messageDataSource[i][key].toString();
                        if (folders_names.indexOf(folder_1) === -1) {
                            folders_names.push(folder_1);
                            folders[folders_names.indexOf(folder_1)] = [];
                        }
                        folders[folders_names.indexOf(folder_1)].push(exports.messageDataSource[i]["ContactID"].toString());
                    }
                    for (var i = 0; i < folders_names.length; i++) {
                        var folder_2 = folders_names[i];
                        var folder_index = folders_names.indexOf(folder_2);
                        var ids_to_flag_1 = "";
                        for (var j = 0; j < folders[folder_index].length; j++) {
                            if (ids_to_flag_1 !== "") {
                                ids_to_flag_1 = ids_to_flag_1 + ",";
                            }
                            ids_to_flag_1 = ids_to_flag_1 + folders[folder_index][j];
                        }
                        flagRequest(url, loginid, foldersFullName[folder_2], ids_to_flag_1, "seen");
                    }
                }
                else {
                    var key = 'Folder';
                    for (var i = 0; i < exports.messageDataSource.length; i++) {
                        if (exports.messageDataSource[i][key].toString() === selectedFolderName) {
                            if (ids_to_flag !== "") {
                                ids_to_flag = ids_to_flag + ",";
                            }
                            ids_to_flag = ids_to_flag + exports.messageDataSource[i]["ContactID"];
                        }
                    }
                    flagRequest(url, loginid, folder, ids_to_flag, "seen");
                }
            }
            else if (args.item.text === 'Trash' || args.item.prefixIcon === 'ej-icon-Delete' || args.item.text === 'Junk') {
                var movement_folder = args.item.text === 'Junk' ? 'Junk' : 'Trash';
                var selectedMessage_2 = getSelectedMessage();
                var ip = location.host;
                if (ip.indexOf(":") !== -1) {
                    ip = ip.split(":")[0];
                }
                var api_value = selectedFolderName === "Trash" && args.item.text === 'Trash' ? "deleteapi.php" : "api.php";
                var url = 'http://' + ip + '/webmail/' + api_value;
                var http_request = new XMLHttpRequest();
                http_request.open("POST", url, true);
                http_request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                http_request.onload = function (e) {
                    if (foldersFullName[selectedFolderName] === "INBOX.Trash" && args.item.text === 'Trash') {
                        exports.messageDataSource.splice(exports.messageDataSource.indexOf(selectedMessage_2), 1);
                        var key = 'ContactID';
                        exports.grpListObj.removeItem({ id: selectedMessage_2[key].toString() });
                    }
                    else {
                        var key = 'Folder';
                        var key2 = 'ContactID';
                        selectedMessage_2[key] = 'Trash';
                        selectedMessage_2[key2] = http_request.response;
                    }
                    exports.grpListObj.dataSource = getFilteredDataSource(exports.messageDataSource, 'Folder', selectedFolderName);
                    showEmptyMessage();
                };
                var loginid = document.getElementById('loginbutton').getAttribute("value");
                var params = 'loginid=' + encodeURIComponent(loginid);
                params += '&folder=' + encodeURIComponent(foldersFullName[selectedFolderName]);
                params += '&movement_folder=' + encodeURIComponent(foldersFullName[movement_folder]);
                params += '&id=' + encodeURIComponent(selectedMessage_2['ContactID'].toString());
                params += '&action=' + encodeURIComponent("move");
                http_request.send(params);
                if (args.item.prefixIcon === 'ej-icon-Delete' && window.innerWidth < 605) {
                    var contentElement = document.getElementsByClassName('row content')[0];
                    contentElement.className = contentElement.className.replace('show-reading-pane', 'show-message-pane');
                }
                else {
                    showEmptyMessage();
                }
            }
            else if ((args.item.text === 'New' || args.item.prefixIcon === 'ej-icon-Create-New') ||
                (args.item.prefixIcon === 'ej-icon-Reply-All')) {
                if (args.item.prefixIcon === 'ej-icon-Create-New') {
                    var contentWrapper = document.getElementsByClassName('content-wrapper')[0];
                    contentWrapper.className = 'content-wrapper hide-message-option';
                }
                var option = 'New';
                if (args.item.prefixIcon === 'ej-icon-Reply-All') {
                    option = 'Reply All';
                }
                if (window.innerWidth < 605) {
                    var contentElement = document.getElementsByClassName('row content')[0];
                    contentElement.className = contentElement.className.replace('show-message-pane', 'show-reading-pane');
                }
                showNewMailPopup(option);
            }
            else if (args.item.prefixIcon === 'ej-icon-Send') {
                sendClick();
            }
            else if (args.item.prefixIcon === 'ej-icon-Search') {
                var contentWrapper = document.getElementsByClassName('content-wrapper')[0];
                contentWrapper.className = 'content-wrapper show-search-option';
                toolbarMobile.refreshOverflow();
            }
            else if (args.item.prefixIcon === 'ej-icon-Close') {
                acSearchMobile.value = '';
            }
            else if (args.item.prefixIcon === 'ej-icon-Copy tb-icons') {
                if (!exports.dlgReplyAllWindow.content) {
                    exports.dlgReplyAllWindow.content = document.getElementById('reading-pane-popup');
                    exports.dlgReplyAllWindow.refresh();
                }
                exports.dlgReplyAllWindow.show();
                readingpane_1.bindReadingPaneData(getSelectedMessage());
            }
        }
    }
    function showNewMailPopup(option) {
        isNewMailClick = true;
        if (window.innerWidth > 1090) {
            document.getElementById('list-pane-div').classList.add("msg-top-margin");
        }
        var selectedMessage = getSelectedMessage();
        showToolbarItems('none');
        document.getElementById('reading-pane-div').className += ' new-mail';
        document.getElementById('accordian').style.display = 'none';
        document.getElementById('emptyMessageDiv').style.display = 'none';
        document.getElementById('mailarea').style.display = '';
        document.getElementById('mailarea').appendChild(document.getElementById('newmailContent'));
        document.getElementsByClassName('tb-item-new-mail')[0].style.display = 'none';
        document.getElementsByClassName('tb-item-mark-read')[0].style.display = 'none';
        document.getElementById('toolbar_align').style.display = 'none';
        newmail_1.showMailDialog(option, selectedMessage);
    }
    function onWindowResize(evt) {
        var messagePane = document.getElementById('list-pane-div');
        var contentArea = document.getElementsByClassName('row content')[0];
        var isReadingPane = (contentArea.className.indexOf('show-reading-pane') === -1);
        if (!isReadingPane && window.innerWidth < 605) {
            return;
        }
        if (window.innerWidth < 1200) {
            var headerRP = document.getElementsByClassName('header-right-pane selected')[0];
            if (headerRP) {
                headerRP.className = 'header-right-pane';
            }
            contentArea.className = 'row content';
            sidebarHeader.type = "Over";
        }
        else {
            if (contentArea.className.indexOf('show-header-content') === -1) {
                contentArea.className = 'row content';
            }
            else {
                contentArea.className = 'row content show-header-content';
            }
            sidebarHeader.type = "Push";
        }
        if (window.innerWidth < 1090) {
            contentArea.className = 'row content sidebar-hide';
            messagePane.classList.remove("msg-top-margin");
            defaultSidebar.hide();
            defaultSidebar.type = 'Over';
            defaultSidebar.showBackdrop = true;
        }
        else {
            messagePane.classList[isNewMailClick ? 'add' : 'remove']('msg-top-margin');
            defaultSidebar.type = 'Push';
            defaultSidebar.showBackdrop = false;
            defaultSidebar.show();
        }
        if (window.innerWidth < 605) {
            if (isReadingPane) {
                contentArea.className = contentArea.className + ' ' + 'show-message-pane';
            }
            if (splitObj) {
                splitObj.destroy();
                splitObj = null;
                document.querySelector('.maincontent_pane').appendChild(document.querySelector('#list-pane-div'));
                document.querySelector('.maincontent_pane').appendChild(document.querySelector('#reading-pane-div'));
                document.querySelector('#list-pane-div').style.display = '';
                document.querySelector('#reading-pane-div').style.display = '';
            }
        }
        else {
            if (!splitObj) {
                splitObj = new ej2_layouts_1.Splitter({
                    paneSettings: [
                        { size: '37%', min: '37%', content: '#list-pane-div' },
                        { size: '63%', min: '40%', content: '#reading-pane-div' }
                    ],
                    width: '100%',
                    height: '100%'
                });
                splitObj.appendTo('#splitter');
            }
        }
        toolbarMobile.refreshOverflow();
    }
    function hideSideBar() {
        if (!isMenuClick) {
            if (defaultSidebar && window.innerWidth < 1090) {
                defaultSidebar.hide();
            }
        }
        isMenuClick = false;
    }
    function sendExitClick() {
        if (exports.dlgNewWindow.visible || exports.dlgReplyAllWindow.visible) {
            exports.dlgSentMailNew.hide();
        }
        else {
            exports.dlgSentMail.hide();
        }
        discardClick();
    }
    exports.sendExitClick = sendExitClick;
    function sendClick() {
        var ip = location.host;
        if (ip.indexOf(":") !== -1) {
            ip = ip.split(":")[0];
        }
        var url = 'http://' + ip + '/webmail/api.php';
        var http_request = new XMLHttpRequest();
        http_request.open("POST", url, true);
        var loginid = document.getElementById('loginbutton').getAttribute("value");
        var params = 'loginid=' + encodeURIComponent(loginid);
        params += '&action=' + encodeURIComponent("send");
        params += '&to=' + encodeURIComponent(document.getElementById('autoTo').value);
        params += '&cc=' + encodeURIComponent(document.getElementById('autoCc').value);
        params += '&subject=' + encodeURIComponent(document.getElementById('txtSubject').value);
        var sendMailContentMessage = document.getElementById('mailContentMessage');
        params += '&MailContentMessage=' + encodeURIComponent(sendMailContentMessage.innerHTML);
        http_request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_request.onload = function (e) {
            if (exports.dlgNewWindow.visible || exports.dlgReplyAllWindow.visible) {
                exports.dlgSentMailNew.show();
            }
            else {
                exports.dlgSentMail.show();
            }
        };
        http_request.send(params);
    }
    exports.sendClick = sendClick;
    function discardButtonClick() {
        if (exports.dlgNewWindow.visible || exports.dlgReplyAllWindow.visible) {
            exports.dlgDiscardNew.show();
        }
        else {
            exports.dlgDiscard.show();
        }
    }
    exports.discardButtonClick = discardButtonClick;
    function discardClick() {
        if (exports.grpListObj.getSelectedItems()) {
            showSelectedMessage();
        }
        else {
            showEmptyMessage();
        }
        if (exports.dlgNewWindow.visible || exports.dlgReplyAllWindow.visible) {
            exports.dlgDiscardNew.hide();
            if (exports.dlgNewWindow.visible) {
                exports.dlgNewWindow.hide();
            }
            else if (exports.dlgReplyAllWindow.visible) {
                exports.dlgReplyAllWindow.hide();
            }
        }
        else {
            exports.dlgDiscard.hide();
        }
        var contentWrapper = document.getElementsByClassName('content-wrapper')[0];
        contentWrapper.className = 'content-wrapper';
    }
    exports.discardClick = discardClick;
    function getTreeData1(id) {
        for (var i = 0; i < treeDataSource.length; i++) {
            var key = 'ID';
            if (treeDataSource[i][key].toString() === id) {
                return treeDataSource[i];
            }
        }
        return null;
    }
    function renderFilterContextMenu() {
        var menuItems = [
            { text: 'All', iconCss: 'ej-icon-Right' }, { text: 'Unread' },
            { text: 'Flagged' }, { separator: true }, {
                text: 'Sort by', items: [{ text: 'None' },
                    { text: 'Ascending', iconCss: 'ej-icon-Right' }, { text: 'Descending' }]
            }
        ];
        var menuOptions = { items: menuItems };
        filterContextMenu = new ej2_navigations_1.ContextMenu(menuOptions, '#filterContextMenu');
        filterContextMenu.select = filterMenuSelect;
    }
    function filterMenuSelect(args) {
        if (args.item) {
            if (args.item.text === 'Ascending' || args.item.text === 'Descending' || args.item.text === 'None') {
                exports.grpListObj.sortOrder = args.item.text;
                for (var i = 0; i < filterContextMenu.items[4].items.length; i++) {
                    filterContextMenu.items[4].items[i].iconCss = '';
                }
                args.item.iconCss = 'ej-icon-Right';
            }
            else if (args.item.text !== 'Sort by') {
                clearFilterMenu();
                var dataSource = getFilteredDataSource(exports.messageDataSource, 'Folder', selectedFolderName);
                if (args.item.text === 'All') {
                    exports.grpListObj.dataSource = dataSource;
                }
                else if (args.item.text === 'Flagged') {
                    exports.grpListObj.dataSource = getFilteredDataSource(dataSource, 'Flagged', 'Flagged');
                }
                else if (args.item.text === 'Unread') {
                    exports.grpListObj.dataSource = getFilteredDataSource(dataSource, 'ReadStyle', 'Unread');
                }
                args.item.iconCss = 'ej-icon-Right';
            }
        }
    }
    function clearFilterMenu() {
        for (var i = 0; i < filterContextMenu.items.length; i++) {
            if (filterContextMenu.items[i].items.length === 0) {
                filterContextMenu.items[i].iconCss = '';
            }
        }
    }
    function cloneObject(obj) {
        var keys = Object.keys(obj);
        var cloneObject = {};
        for (var i = 0; i < keys.length; i++) {
            cloneObject[keys[i]] = obj[keys[i]];
        }
        return cloneObject;
    }
    function documentClick(evt) {
        var key = 'parentID';
        if (evt.target instanceof HTMLElement) {
            var target = evt.target;
            if (target.className.indexOf('header-right-pane') !== -1) {
                headerContent(evt.target);
                sidebarHeader.show();
            }
            else if (!readingpane_1.dropdownSelectRP && exports.dlgReplyAllWindow.visible && target.innerText === readingpane_1.ddlLastRplyValueRP) {
                readingpane_1.showMailDialogRP(readingpane_1.ddlLastRplyValueRP);
            }
            else if (!dropdownSelect && !exports.dlgReplyAllWindow.visible && target.innerText === ddlReplyAll.value) {
                showNewMailPopup(ddlReplyAll.value);
            }
            else {
                if (target.tagName === 'SPAN' || (target.children && target.children.length > 0)) {
                    target = target.tagName === 'SPAN' ? target : target.children[0];
                    if (target.className === 'e-btn-icon ej-icon-Favorite ' + selectedFolderName) {
                        favoriteAction('add', target);
                    }
                    else if (target.className === 'e-btn-icon ej-icon-Favorite-Composite ' + selectedFolderName) {
                        favoriteAction('remove', target);
                    }
                    else if (target.parentNode.className === 'listview-btn') {
                        var selectedMessage_3 = getSelectedMessage();
                        actionMessageID = selectedMessage_3['ContactID'].toString();
                        if (target.className.indexOf('ej-icon-Delete') !== -1) {
                            var ip = location.host;
                            if (ip.indexOf(":") !== -1) {
                                ip = ip.split(":")[0];
                            }
                            var url = 'http://' + ip + '/webmail/api.php';
                            var http_request = new XMLHttpRequest();
                            http_request.open("POST", url, true);
                            http_request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                            http_request.onload = function (e) {
                                if (selectedFolderName === "Trash") {
                                    exports.messageDataSource.splice(exports.messageDataSource.indexOf(selectedMessage_3), 1);
                                    var key_2 = 'ContactID';
                                    exports.grpListObj.removeItem({ id: selectedMessage_3[key_2].toString() });
                                    exports.grpListObj.dataSource = getFilteredDataSource(exports.messageDataSource, 'Folder', selectedFolderName);
                                    showEmptyMessage();
                                }
                                else {
                                    var key_3 = 'Folder';
                                    var key2 = 'ContactID';
                                    selectedMessage_3[key_3] = "Trash";
                                    selectedMessage_3[key2] = http_request.response;
                                    exports.grpListObj.dataSource = getFilteredDataSource(exports.messageDataSource, 'Folder', selectedFolderName);
                                    showEmptyMessage();
                                }
                            };
                            var loginid = document.getElementById('loginbutton').getAttribute("value");
                            var params = 'loginid=' + encodeURIComponent(loginid);
                            params += '&folder=' + encodeURIComponent(foldersFullName[selectedFolderName]);
                            params += '&movement_folder=' + encodeURIComponent("INBOX.Trash");
                            params += '&id=' + encodeURIComponent(selectedMessage_3['ContactID'].toString());
                            params += '&action=' + encodeURIComponent(selectedFolderName === "Trash" ? "delete" : "move");
                            http_request.send(params);
                        }
                        else if (target.className.indexOf('ej-icon-Flag_1') !== -1) {
                            flagListItem(target, selectedMessage_3);
                        }
                        else if (target.className.indexOf('ej-icon-Mark-as-read') !== -1 && !isItemClick) {
                            var parentNode = target.parentNode;
                            if (parentNode.title === 'Mark as read') {
                                parentNode.title = 'Mark as unread';
                                key = 'ContactID';
                                actionMessageID = selectedMessage_3[key].toString();
                                setReadStyleMessage(selectedMessage_3[key].toString(), 'Read');
                            }
                            else if (parentNode.title === 'Mark as unread') {
                                parentNode.title = 'Mark as read';
                                key = 'ContactID';
                                actionMessageID = selectedMessage_3[key].toString();
                                setReadStyleMessage(selectedMessage_3[key].toString(), 'Unread');
                            }
                        }
                        resetSelectedFolderData();
                        setTimeout(function () {
                            actionMessageID = null;
                        }, 200);
                    }
                }
            }
        }
        newmailWindowItemClick();
        readingPaneItemClick();
        isItemClick = false;
        dropdownSelect = false;
    }
    function documentDoubleClick(evt) {
        if (evt.target instanceof HTMLElement) {
            var target = evt.target;
            if (target.className.indexOf('template-container') !== -1) {
                if (!exports.dlgReplyAllWindow.content) {
                    exports.dlgReplyAllWindow.content = document.getElementById('reading-pane-popup');
                    exports.dlgReplyAllWindow.refresh();
                }
                exports.dlgReplyAllWindow.show();
                readingpane_1.bindReadingPaneData(getSelectedMessage());
            }
        }
    }
    function newmailWindowItemClick() {
        if (newmail_1.selectedToolbarItem) {
            if (newmail_1.selectedToolbarItem === 'tb-item-window-mail') {
                discardClick();
                exports.dlgNewWindow.content = document.getElementById('newmailContent');
                exports.dlgNewWindow.refresh();
                exports.dlgNewWindow.show();
            }
            else if (newmail_1.selectedToolbarItem === 'tb-item-back-mail') {
                exports.dlgNewWindow.hide();
            }
            else if (newmail_1.selectedToolbarItem === 'Send') {
                sendClick();
            }
            else if (newmail_1.selectedToolbarItem === 'Discard') {
                discardButtonClick();
            }
        }
        newmail_1.resetSelectedToolbarItem('');
    }
    function readingPaneItemClick() {
        if (readingpane_1.selectedRPToolbarItem) {
            if (readingpane_1.selectedRPToolbarItem === 'SendClick') {
                sendClick();
            }
            else if (readingpane_1.selectedRPToolbarItem === 'DiscardClick') {
                discardButtonClick();
            }
            else if (readingpane_1.selectedRPToolbarItem === 'DeleteClick' || readingpane_1.selectedRPToolbarItem === 'JunkClick') {
                var selectedMessage = getSelectedMessage();
                exports.messageDataSource.splice(exports.messageDataSource.indexOf(selectedMessage), 1);
                var key = 'ContactID';
                var contactName = 'text';
                exports.grpListObj.removeItem({ id: selectedMessage[key].toString(), text: selectedMessage[contactName].toString() });
                showEmptyMessage();
                exports.dlgReplyAllWindow.hide();
            }
            else if (readingpane_1.selectedRPToolbarItem === 'ClosePopup') {
                exports.dlgReplyAllWindow.hide();
            }
        }
        readingpane_1.resetRPSelectedItem('');
    }
    function favoriteAction(type, target) {
        if (type === 'add') {
            target.className = 'e-btn-icon ej-icon-Favorite-Composite ' + selectedFolderName;
            target.parentNode.title = 'Remove from Favorites';
            var treeData = cloneObject(treeviewSelectedData);
            var key = 'PID';
            treeData[key] = '1';
            key = 'ID';
            treeData[key] = Number(treeData[key]) + 111;
            key = 'Favorite';
            treeviewSelectedData[key] = treeData[key] = 'Favorite-Composite';
            key = 'Count';
            treeData[key] = target.parentNode.parentNode.childNodes[1].childNodes[0].innerHTML;
            key = 'FavoriteMessage';
            treeviewSelectedData[key] = treeData[key] = 'Remove from Favorites';
            treeDataSource.push(treeData);
            treeObj.addNodes([treeData], null, null);
        }
        else {
            var ss = document.getElementsByClassName('sidebar')[0];
            dlgFavorite.show();
        }
    }
    function flagListItem(target, selectedMessage) {
        var key = 'Flagged';
        var parentNode = target.parentNode;
        var newFlag;
        if (target.className.indexOf('Flagged') !== -1) {
            parentNode.title = 'Flag this Message';
            target.className = 'e-btn-icon ej-icon-Flag_1';
            selectedMessage[key] = 'None';
            key = 'FlagTitle';
            selectedMessage[key] = 'Flag this Message';
            newFlag = "unflag";
        }
        else {
            parentNode.title = 'Remove the flag from this message';
            target.className = 'e-btn-icon ej-icon-Flag_1 Flagged';
            selectedMessage[key] = 'Flagged';
            key = 'FlagTitle';
            selectedMessage[key] = 'Remove the flag from this message';
            newFlag = "flag";
        }
        var ip = location.host;
        if (ip.indexOf(":") !== -1) {
            ip = ip.split(":")[0];
        }
        var url = 'http://' + ip + '/webmail/api.php';
        var http_request = new XMLHttpRequest();
        http_request.open("POST", url, true);
        http_request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http_request.onload = function (e) { };
        var loginid = document.getElementById('loginbutton').getAttribute("value");
        var params = 'loginid=' + encodeURIComponent(loginid);
        params += '&folder=' + encodeURIComponent(foldersFullName[selectedFolderName]);
        params += '&ids_to_flag=' + encodeURIComponent(selectedMessage['ContactID'].toString());
        params += '&flag=' + encodeURIComponent(newFlag);
        params += '&action=' + encodeURIComponent("flag");
        http_request.send(params);
    }
    function popupContentClick(evt) {
        if (evt.target instanceof HTMLElement) {
            var target = evt.target;
            if (target.className !== 'e-btn-icon ej-icon-Close' && window.innerWidth >= 1090) {
                var key = 'ContactID';
                exports.grpListObj.selectItem({ id: exports.messageDataSource[0][key].toString() });
                if (!exports.dlgReplyAllWindow.content) {
                    exports.dlgReplyAllWindow.content = document.getElementById('reading-pane-popup');
                    exports.dlgReplyAllWindow.refresh();
                }
                exports.dlgReplyAllWindow.show();
                readingpane_1.bindReadingPaneData(exports.messageDataSource[0]);
            }
            popup1.hide();
        }
    }
    function popupMouseEnter() {
        hoverOnPopup = true;
    }
    function popupMouseLeave() {
        hoverOnPopup = false;
        hidePopup();
    }
    function hidePopup() {
        setTimeout(function () { if (!hoverOnPopup) {
            popup1.hide();
        } }, 2000);
    }
    function openPopup() {
        var newMessageData = cloneObject(exports.messageDataSource[Math.floor(Math.random() * (50 - 3) + 2)]);
        var key = 'text';
        document.getElementById('popup-contact').innerHTML = newMessageData[key].toString();
        key = 'ContactTitle';
        document.getElementById('popup-subject').innerHTML = newMessageData[key].toString();
        key = 'Message';
        document.getElementById('popup-message-content').innerHTML = newMessageData[key].toString();
        key = 'Image';
        document.getElementById('popup-image').style.background = 'url(' +
            newMessageData[key].toString().replace('styles/images/images/', 'styles/images/large/') + ') no-repeat 50% 50%';
        key = 'Folder';
        newMessageData[key] = 'Inbox';
        key = 'ReadStyle';
        newMessageData[key] = 'Unread';
        key = 'ReadTitle';
        newMessageData[key] = 'Mark as read';
        key = 'Flagged';
        newMessageData[key] = 'None';
        key = 'FlagTitle';
        newMessageData[key] = 'Flag this message';
        key = 'ContactID';
        newMessageData[key] = 'SF20032';
        var element = document.querySelector('#popup');
        element.onmouseenter = popupMouseEnter;
        element.onmouseleave = popupMouseLeave;
        popup1 = new ej2_popups_1.Popup(element, {
            offsetX: -5, offsetY: 5, relateTo: '#content-area',
            position: { X: 'right', Y: 'top' },
        });
        if (window.innerWidth > 605) {
            popup1.show();
        }
        else {
            popup1.hide();
        }
        var dataSource = getFilteredDataSource(exports.messageDataSource, 'Folder', selectedFolderName);
        dataSource.splice(0, 0, newMessageData);
        exports.messageDataSource.splice(0, 0, newMessageData);
        exports.grpListObj.dataSource = dataSource;
        setReadCount('Read');
        setTimeout(function () { hidePopup(); }, 2000);
    }
    setTimeout(openPopup, 3000);
    function updateNewMailClick() {
        isNewMailClick = false;
        document.getElementById('list-pane-div').classList.remove("msg-top-margin");
    }
});
