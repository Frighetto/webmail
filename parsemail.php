<?php 
require_once 'datahora.php';

function parse_header($header){
    $subject = '';
    if ( isset($header->subject) && strlen($header->subject) > 0 ) {
        foreach (imap_mime_header_decode($header->subject) as $obj) {
            $subject .= $obj->text;
        }
    }

    //convertToUtf8     
    if (mb_detect_encoding($subject, "UTF-8, ISO-8859-1, GBK")!="UTF-8") {
        $subject = utf8_encode($subject);
    }
    $subject = iconv('UTF-8', 'UTF-8//IGNORE', $subject);

    
    $header_object = new stdClass;
    $header_object->subject = $subject;         

    $from = $header->from[0];
    $header_object->from = $from->mailbox . '@' . $from->host;
    $to = '';
    foreach($header->to as $receiver){
        if($to != ''){
            $to .= ", ";
        }
        $to .= $receiver->mailbox . '@' . $receiver->host;
    }
    $header_object->to = $to;
    $header_object->date = substr(string_data_formato_brasileiro($header->date), 0, 16);
    $header_object->flagged = strlen(trim($header->Flagged))>0;
    $header_object->unseen = strlen(trim($header->Unseen))>0;
    $header_object->answered = strlen(trim($header->Answered))>0;

    return $header_object;
}
?>