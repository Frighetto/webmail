<?php 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

$username = "teste@teleatendimento.com.br";
$password = "&lBCEyO8,C*y";

session_start();

$_SESSION['username'] = $username;
$_SESSION['password'] = $password;
require_once "database.php";    
$user_values = get_usuario($_SESSION['username']);    
if(isset($user_values) && $user_values['ativo'] == 1){
    $parametros = get_parametro($user_values['parametro']);          
    $_SESSION['mailbox'] = $parametros['imap_host'];
    $_SESSION['input_port'] = $parametros['input_door'];
    $_SESSION['smtp'] = $parametros['smtp_host'];
    $_SESSION['output_port'] = $parametros['output_door'];   
        
    date_default_timezone_set("America/Sao_Paulo"); 
} else {
    exit;
}

$mailbox = "{" . $_SESSION['mailbox'] . ":" . $_SESSION['input_port'] . "/imap/ssl/novalidate-cert". "}";  
$mailbox_instance = imap_open($mailbox , $_SESSION['username'], $_SESSION['password']);

$folders_unread = [];

$folders_messages = [];

$folders = imap_list($mailbox_instance, $mailbox, "*");

$folders_ids = [];
$folders_attributes = [];
$mail_info_list = array(); 
$folders_full_name = [];

$id = 1;
foreach($folders as $folder){
    imap_reopen($mailbox_instance, $folder);
    $unread_mails = imap_search($mailbox_instance, 'UNSEEN');    
    $unread_mails = $unread_mails ? sizeof($unread_mails) : 0;
    $folder_name = str_replace($mailbox, "", $folder);    
    $folders_unread[$folder_name] = $unread_mails;
    $folders_ids[$folder_name] = $id;

    $folder_attributes = new stdClass;
    
    $folder_attributes->ID = $id;

    if(strpos($folder_name, ".")){
        $parent_folder = substr($folder_name, 0, strpos($folder_name, "."));
        $folder_attributes->PID = $folders_ids[$parent_folder];
        $folder_attributes->Name = substr($folder_name, strpos($folder_name, ".") + 1);
        $folder_attributes->HasChild = false;
        $folder_attributes->Expanded = false;       
        $folders_full_name[$folder_attributes->Name] = $folder_name;
    } else {
        $folder_attributes->PID = null;
        $folder_attributes->Name = $folder_name;        
        $folder_attributes->HasChild = true;
        $folder_attributes->Expanded = true;
        $folders_full_name[$folder_name] = $folder_name;
    }

    $folder_attributes->Count = $unread_mails == 0 ? "" : $unread_mails;    

    $folders_attributes[sizeof($folders_attributes)] = $folder_attributes;

    $id = $id + 1;

    require_once 'datahora.php';
    require_once 'parsemail.php';

    $search_criteria = isset($_SESSION['search']) ? ('SUBJECT "' . $_SESSION['search'] . '"') : 'ALL';
    
    $mail_index_list = imap_search($mailbox_instance, $search_criteria); 
    $list_size = $mail_index_list == false ? 0 : sizeof($mail_index_list);
    
    for ($i = $list_size - 1; 0 <= $i; $i = $i - 1) {                         
        $mail_index = $mail_index_list[$i];
        
        $header = imap_headerinfo($mailbox_instance, $mail_index);           
        $header_object = parse_header($header);         
        $header_object->id = $mail_index;
        $message_attributes = new stdClass;
        $message_attributes->ContactID = $mail_index;
        $message_attributes->text = $header_object->fromaddress;
        $message_attributes->ContactTitle = $header_object->subject;
        $message_attributes->Message = "";
        $message_attributes->Email = $header_object->from;
        $message_attributes->CC = [];
        $message_attributes->CCMail = [];
        $message_attributes->BCC = [];
        $message_attributes->BCCMail = [];
        $message_attributes->To = $header_object->toaddress;
        $message_attributes->ToMail = $header_object->to;
        $message_attributes->Image = "";
        $message_attributes->Time = substr($header_object->date, strpos($header_object->date, " ") + 1);
        $message_attributes->Date = substr($header_object->date, 0, strpos($header_object->date, " "));
        $message_attributes->Day = "";
        $message_attributes->ReadStyle = $header_object->unseen ? "Read" : "Unread";
        $message_attributes->ReadTitle = $header_object->unseen ? "Mark as unread" : "Mark as read";
        $message_attributes->Flagged = $header_object->flagged ? "Flagged" : "None";
        $message_attributes->FlagTitle = $header_object->flagged ? "Remove the flag from this Message" : "Flag this message";
        if(strpos($folder_name, ".")){
            $message_attributes->Folder = substr($folder_name, strpos($folder_name, ".") + 1);
        } else {
            $message_attributes->Folder = $folder_name;
        }
        $mail_list_last_index = sizeof($mail_info_list);
        $mail_info_list[$mail_list_last_index] = $message_attributes;          
    }    
}

$messageDataSourceNew =
'[{
    "ContactID": "SF10153",
    "text": "Oleg Oneill",
    "ContactTitle": "Get Together on March",
    "Message": "<p>Hi Gretchen Justice,</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. -, sed ut hoc iudicaremus, non esse in iis partem maximam positam beate aut secus vivendi. Equidem, sed audistine modo de Carneade? </p>\r\n\r\n<p><b>Non quam nostram quidem, inquit Pomponius iocans;</b> <i>Tenent mordicus.</i> Quae qui non vident, nihil umquam magnum ac cognitione dignum amaverunt. Summum enim bonum exposuit vacuitatem doloris; In qua quid est boni praeter summam voluptatem, et eam sempiternam? Ut optime, secundum naturam affectum esse possit. </p>\r\n\r\n<p>Quae cum ita sint, effectum est nihil esse malum, quod turpe non sit. Quamvis enim depravatae non sint, pravae tamen esse possunt. Quid ait Aristoteles reliquique Platonis alumni? Quid ergo attinet gloriose loqui, nisi constanter loquare? </p>\r\n\r\n<p>Summus dolor plures dies manere non potest? Naturales divitias dixit parabiles esse, quod parvo esset natura contenta. Pugnant Stoici cum Peripateticis. Duo Reges: constructio interrete. Expressa vero in iis aetatibus, quae iam confirmatae sunt. <i>Scio enim esse quosdam, qui quavis lingua philosophari possint;</i> Qui autem esse poteris, nisi te amor ipse ceperit? Si qua in iis corrigere voluit, deteriora fecit. </p>\r\n\r\n<p>Thanks,</p><p>Oleg Oneill</p>",
    "Email": "olegoneill@syncfusion.com",
    "CC": [],
    "CCMail": [],
    "BCC": [],
    "BCCMail": [],
    "To": "Gretchen Justice",
    "ToMail": "gretchenjustice@syncfusion.com",
    "Image": "",
    "Time": "18:50",
    "Date": "24/10/2017",
    "Day": "",
    "Folder": "Archive",
    "ReadStyle": "Read",
    "ReadTitle": "Mark as unread",
    "Flagged": "None",
    "FlagTitle": "Flag this message"
}]';


$response = new stdClass;
$response->messageDataSourceNew = $mail_info_list;//$mail_info_list; //json_decode($messageDataSourceNew);
$response->folderData = $folders_attributes; //json_decode($teste_folder); //$folders_attributes;
$response->userName = $_SESSION['username'];
$response->userMail = $_SESSION['username'];
$response->folders_full_name = $folders_full_name;
$response->server_uri = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
echo '<pre>';
var_dump($response);
echo json_encode($response);

?>
