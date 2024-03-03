define(["require", "exports", "crossroads", "@syncfusion/ej2-base", "hasher"], function (require, exports, crossroads_1, ej2_base_1, hasher) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    routeDefault();
    function loadAjaxHTML(secret_identifier) {
        var ajaxHTML = new ej2_base_1.Ajax('src/home/home.html', 'GET', true);
        ajaxHTML.send().then(function (value) {
            document.getElementById('content-area').innerHTML = value.toString();
            document.getElementById('loginbutton').setAttribute("value", secret_identifier);
            window.home();
        });
    }
    var ip = location.host;
    if (ip.indexOf(":") !== -1) {
        ip = ip.split(":")[0];
    }
    var url = 'http://' + ip + '/webmail/api.php?action=getLoginID';
    var http_request = new XMLHttpRequest();
    http_request.open("GET", url, false);
    http_request.onload = function (e) {
        var response = JSON.parse(http_request.response);
        var _loop_1 = function (i) {
            var id = response[i].secret_identifier;
            crossroads_1.addRoute('/home/' + id, function () {
                loadAjaxHTML(id);
            });
        };
        for (var i = 0; i < response.length; i = i + 1) {
            _loop_1(i);
        }
    };
    http_request.send();
    hasher.initialized.add(function (h) {
        crossroads_1.parse(h);
    });
    hasher.changed.add(function (h) {
        crossroads_1.parse(h);
    });
    hasher.init();
    function routeDefault() {
        var ip = location.host;
        if (ip.indexOf(":") !== -1) {
            ip = ip.split(":")[0];
        }
        var url = 'http://' + ip + '/webmail/';
        crossroads_1.addRoute('', function () {
            window.location.href = url;
        });
    }
});
