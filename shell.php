<p>
Enviar mensagem:
</p>
curl --ssl-reqd --url "smtps://mail.teleatendimento.com.br:465" --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --mail-from "teste@teleatendimento.com.br" --mail-rcpt "teste@teleatendimento.com.br" --mail-rcpt "lucas.frighetto@gmail.com" --upload-file shell/mail.txt
<p>
Listar diretórios (Folders):
</p>
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/"
<p>
Listar mensagens em número sequencial e UID:
</p>
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/INBOX" -X "fetch 1:* (UID FLAGS)"
<p>
Listar mensagens com o assunto a partir do número sequencial:    
</p>
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/INBOX;mailindex=[1-19];section=header.fields%20(subject)"
<p>
Ler mensagem:     
</p>
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/INBOX;MAILINDEX=1";
<p>
FLAGS (\Answered \Flagged \Draft \Deleted \Seen) ... +Flags pra adicionar e -Flags pra remover:   
</p>
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/INBOX" -X "UID STORE 10 +Flags \Deleted"
<p>
Mover mensagem pra outro diretório: 
</p>
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/INBOX" -X "UID MOVE 10 INBOX.Archive"
<p>
Criação de diretórios (CREATE, DELETE, RENAME):
</p>
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/" --request "CREATE INBOX.MyNewFolder"
<?php 
/*
Enviar email:
curl --ssl-reqd --url "smtps://mail.teleatendimento.com.br:465" --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --mail-from "teste@teleatendimento.com.br" --mail-rcpt "teste@teleatendimento.com.br" --mail-rcpt "lucas.frighetto@gmail.com" --upload-file shell/mail.txt

Listar diretórios (Folders):
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/"

Listar mensagens em número sequencial e UID:
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/INBOX" -X "fetch 1:* (UID FLAGS)"

Listar mensagens com o assunto a partir do número sequencial:
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/INBOX;mailindex=[1-19];section=header.fields%20(subject)"

Ler mensagem:
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/INBOX;MAILINDEX=1"

FLAGS (\Answered \Flagged \Draft \Deleted \Seen) ... +Flags pra adicionar e -Flags pra remover, adicionar flags de deletdo na mensagem com uid 10:
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/INBOX" -X "UID STORE 10 +Flags \Deleted"

Mover mensagem pra outro diretório:
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/INBOX" -X "UID MOVE 10 INBOX.Archive"

Criação de diretórios:
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/" --request "CREATE INBOX.MyNewFolder"
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/" --request "RENAME INBOX.MyNewFolder INBOX.YourNewFolder"
curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/" --request "DELETE INBOX.YourNewFolder"

Débito técnico (ver usos em send.php e mailbody.php): 
    -codificador e decodificador base64: https://pt.wikipedia.org/wiki/Base64
    -codificador e decodificador MIME: https://www.rfc-editor.org/rfc/rfc2047
        Primary Mimetype = ("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
    -codificador e decodificador 8bit;
    -codificador e decodificador binario;
    -codificador e decodificador quoted printable;       
*/

$smtp = "mail.teleatendimento.com.br";
$output_port = 465;
$from = "teste@teleatendimento.com.br";
$password = "&lBCEyO8,C*y";
$mailrcpt = '--mail-rcpt "teste@teleatendimento.com.br" ';
$mailrcpt .= '--mail-rcpt "lucas.frighetto@gmail.com" ';
$filepatch = "shell/mail.txt";

$comand = "curl --ssl-reqd --url \"smtps://$smtp:$output_port\" --user \"$from:$password\" --mail-from \"$from\" $mailrcpt --upload-file $filepatch";  

$comand = 'curl --user "teste@teleatendimento.com.br:&lBCEyO8,C*y" --url "imaps://mail.teleatendimento.com.br:993/INBOX;MAILINDEX=3"';
$output = null;
exec($comand, $output);

$str = "";
foreach($output as $line){
    $str = $str . $line . '<br>';
}
echo '<pre>';
var_dump($str);

?>