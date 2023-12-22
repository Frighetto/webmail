<?php
$dir = 'temp/' . $_SESSION['username'] . "/";

if(!is_dir($dir)){ 
    mkdir($dir);
}

$filepatch = $dir . "mail.txt";

$from = $_SESSION['username'];
$password = $_SESSION['password'];
$to = $_POST['to'];
$subject = mb_encode_mimeheader($_POST['subject']);
$smtp = trim($_SESSION['smtp']);
$output_port = trim($_SESSION['output_port']);
$date = date("D, d M Y H:i:s T");
$content = $_POST['mail'];

$to_str = '';
foreach(explode(',', $to) as $an_rcpt){ 
    $an_rcpt = trim($an_rcpt);
    if($to_str != ''){
        $to_str .= ",";
    }
    $to_str .= "<$an_rcpt>";
}  

$mailtxt_attachment = "";
$have_attachment = false;
foreach ($_FILES as $attachment) {      
    if($attachment["size"] > 0){ 
        $have_attachment = true;
        $filepatch = $attachment["tmp_name"];
        $filename = $attachment["name"];    
        $file = fopen($filepatch, "r") or die("Unable to open file!");       
        $file_content = fread($file, filesize($filepatch));       
        fclose($file);
        $enconded_file = base64_encode($file_content);

        $mailtxt_attachment .=
"
--MULTIPART-ALTERNATIVE-BOUNDARY--
--MULTIPART-MIXED-BOUNDARY
Content-Disposition: attachment; filename=\"$filename\"
Content-Type: application/octet-stream
Content-Transfer-Encoding: base64

$enconded_file";
    }
}      

$mailtxt = "";
if($have_attachment){    
    $mailtxt =
"From: <$from>
To: $to_str
Subject: $subject
Date: $date
Cc: 
MIME-Version: 1.0
Content-Type: multipart/mixed; boundary=\"MULTIPART-MIXED-BOUNDARY\"

--MULTIPART-MIXED-BOUNDARY
Content-Type: multipart/alternative; boundary=\"MULTIPART-ALTERNATIVE-BOUNDARY\"

--MULTIPART-ALTERNATIVE-BOUNDARY
Content-Type: text/html; charset=utf-8

$content" . $mailtxt_attachment;
} else {
    $mailtxt =
"From: <$from>
To: $to_str
Subject: $subject
Date: $date
Cc: 
MIME-Version: 1.0
Content-Type: text/html; charset=utf-8

$content";    
}


if(isset($_POST['draft'])){
    imap_append($mailbox_instance, $mailbox . "INBOX.Drafts", $mailtxt);  
} else {

    $file = fopen($filepatch, "w") or die("Unable to open file!");
    fwrite($file, $mailtxt);
    fclose($file);

    $mailrcpt = "";
    foreach(explode(',', $to) as $an_rcpt){ 
        $an_rcpt = trim($an_rcpt);    
        $mailrcpt .= " --mail-rcpt \"$an_rcpt\"";
    }

    $comand = "curl --ssl-reqd --url \"smtps://$smtp:$output_port\" --user \"$from:$password\" --mail-from \"$from\" $mailrcpt --upload-file $filepatch";

    exec($comand);      

    imap_append($mailbox_instance, $mailbox . "INBOX.Sent", $mailtxt);  

    if($_POST['send'] != "default" && substr($_POST['subject'], 0, 3) == 'Re:'){    
        imap_setflag_full($mailbox_instance, $_POST['send'], '\\Answered');
    }

    delTree($dir);
}

function delTree($dir) {

    $files = array_diff(scandir($dir), array('.','..'));
    
        foreach ($files as $file) {
    
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    
        }
    
        return rmdir($dir);
    
}

?>    