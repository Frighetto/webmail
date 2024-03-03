import { addRoute, parse } from 'crossroads';
import { Ajax } from '@syncfusion/ej2-base';
import * as hasher from 'hasher';

declare let window: IPages;
routeDefault();

function loadAjaxHTML(secret_identifier: string){
    let ajaxHTML: Ajax = new Ajax('src/home/home.html', 'GET', true);
    ajaxHTML.send().then((value: Object): void => {        
        document.getElementById('content-area').innerHTML = value.toString();    
        (document.getElementById('loginbutton') as HTMLElement).setAttribute("value", secret_identifier);        
        window.home();            
    });   
}

var ip = location.host;    
if(ip.indexOf(":") !== -1){
    ip = ip.split(":")[0];
}        
var url = 'http://' + ip + '/webmail/api.php?action=getLoginID';  
var http_request = new XMLHttpRequest();			
http_request.open("GET", url, false);	    	
http_request.onload = function(e) {    
    var response = JSON.parse(http_request.response);    
    for(let i = 0; i < response.length; i = i + 1){
        let id = response[i].secret_identifier;        
        addRoute('/home/' + id, () => {             
            loadAjaxHTML(id);
        });
    }    
}    
http_request.send();

hasher.initialized.add((h: string) => {
    parse(h);
});
hasher.changed.add((h: string) => {
    parse(h);
});
hasher.init();
function routeDefault(): void {  
    var ip = location.host;    
    if(ip.indexOf(":") !== -1){
        ip = ip.split(":")[0];
    }        
    var url = 'http://' + ip + '/webmail/';      
    addRoute('', () => {        
        window.location.href = url;
    });    
}
export interface IPages extends Window {
    home: () => void;
    newmail: () => void;
    readingpane: () => void;
}