<?php

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: http://localhost:3000");

date_default_timezone_set("America/Sao_Paulo"); 

if(isset($_GET['action']) && $_GET['action'] == "getLoginID"){
    require_once "database.php";   
    echo json_encode(get_secret_ids());
    exit;
}

if(isset($_POST['loginid'])){
    require_once "database.php";        
    $user_values = get_usuario_by_id($_POST['loginid']);      
    $username = $user_values['usuario'];
    $password = $user_values['senha'];
    $folder = isset($_POST['folder']) ? $_POST['folder'] : "INBOX";    
     
    if(isset($user_values) && $user_values['ativo'] == 1){
        $parametros = get_parametro($user_values['parametro']);          
        $mailbox = $parametros['imap_host'];
        $input_port = $parametros['input_door'];
        $smtp = $parametros['smtp_host'];
        $output_port = $parametros['output_door'];                       
    } else {
        exit;
    }
}

$mailbox = "{" . $mailbox . ":" . $input_port . "/imap/ssl/novalidate-cert". "}";  
$mailbox_instance = imap_open($mailbox . $folder, $username, $password);

if($_POST['action'] == "getDataSource"){

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

        $search_criteria = 'ALL';
        
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
            $message_attributes->ReadStyle = $header_object->unseen ? "Unread" : "Read";
            $message_attributes->ReadTitle = $header_object->unseen ? "Mark as read" :  "Mark as unread";
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

    $response = new stdClass;
    $response->messageDataSourceNew = $mail_info_list;
    $response->folderData = $folders_attributes;
    $response->userName = $username;
    $response->userMail = $username;
    $response->folders_full_name = $folders_full_name;
    $response->server_uri = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];

    echo json_encode($response);

}

if($_POST['action'] == "flag"){

    if($_POST["flag"] == 'seen'){ 
        imap_setflag_full($mailbox_instance, $_POST["ids_to_flag"], '\\Seen');  
    } else if($_POST["flag"] == 'unseen'){ 
        imap_clearflag_full($mailbox_instance, $_POST["ids_to_flag"], '\\Seen');  
    } else if($_POST["flag"] == 'flag'){ 
        imap_setflag_full($mailbox_instance, $_POST["ids_to_flag"], '\\Flagged');  
    } else if($_POST["flag"] == 'unflag'){ 
        imap_clearflag_full($mailbox_instance, $_POST["ids_to_flag"], '\\Flagged');  
    }

}

if($_POST['action'] == "send"){

    $_POST['to'] = $_POST['to'];
    $_POST['subject'] = $_POST['subject'];
    $_POST['selectedwriter'] = 'mail';
    $_POST['mail'] = $_POST['MailContentMessage'];
    $_POST['send'] = "default";
    require_once "send.php";    

}

if($_POST['action'] == "read"){

    $load_body_only = true;
    $download_uri = isset($_POST['downloaduri']) ? $_POST['downloaduri'] : null;
    require_once 'mailbody.php';

}

if($_POST['action'] == "move"){

    $header = imap_headerinfo($mailbox_instance, $_POST["id"]);

    $from = $header->from[0];
    $from = $from->mailbox . "@" . $from->host;

    if (imap_mail_move($mailbox_instance, $_POST["id"], $_POST["movement_folder"])) {        
        imap_expunge($mailbox_instance);
    }

    imap_reopen($mailbox_instance, $mailbox . $_POST["movement_folder"]);

    $search_criteria = 'FROM "' . $from . '"';
        
    $mail_index_list = imap_search($mailbox_instance, $search_criteria); 

    $new_id = $mail_index_list[sizeof($mail_index_list) - 1];

    echo $new_id;

}

if($_POST['action'] == "delete"){

    if($_POST['folder'] == 'INBOX.Trash'){
        imap_setflag_full($mailbox_instance, $_POST["id"], '\\Deleted');  
        imap_expunge($mailbox_instance);
    } else {
        if (imap_mail_move($mailbox_instance, $_POST["id"], 'INBOX.Trash')) {        
            imap_expunge($mailbox_instance);
        }
    }

}

if($_POST['action'] == "deletefolder"){
    imap_deletemailbox($mailbox_instance, $mailbox . $_POST['removed_folder']);
    echo "deleted";
}

if($_POST['action'] == "createfolder"){
    $new_folder = $_POST['parent_folder'] != "" ? $_POST['parent_folder'] . "." . $_POST['add_folder'] : $_POST['add_folder'];
    imap_createmailbox($mailbox_instance, $mailbox . $new_folder);    
    echo "created";
}

if($_POST['action'] == "rename_folder"){
    imap_rename($mailbox_instance, $mailbox . $_POST['renamed_folder'], $mailbox . $_POST['new_folder_name']);
    echo "renamed";
}

?>