<?php

    //require_once "Imap.php";

    //$imap = new Imap("mail.helpdesk.tec.br:993", "teste@helpdesk.tec.br", "Senha@135", "ssl");

    /*
    //leitura de emails funcionando:
    $selected_message = $imap->readMessage(1);
    echo '<pre>';
    var_dump($selected_message);
    exit;
    */

    //referencias:
    //https://stackoverflow.com/questions/2334250/warning-mail-function-mail-failed-to-connect-to-mailserver-at-localhost
    //https://www.w3schools.com/php/func_mail_mail.asp
    ini_set('SMTP', "mail.helpdesk.tec.br");
    ini_set('smtp_port', '465');    
    ini_set('username', "teste@helpdesk.tec.br");
    ini_set('password', "Senha@135");
    ini_set('sendmail_from', "teste@helpdesk.tec.br");

    $to = "teste@helpdesk.tec.br";
    $subject = "test";
    $txt = "Hello world!";
    $headers = "From: teste@helpdesk.tec.br" . "\r\n" .
    "CC: somebodyelse@example.com";

    mail($to, $subject, $txt, $headers);      

?>