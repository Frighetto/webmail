<?php
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Headers: *");

  require_once "database.php";

  $user_values = get_usuario_by_id($_GET['loginid']);
  $username = $user_values['usuario'];
  $password = $user_values['senha'];
  $folder = isset($_GET['folder']) ? $_GET['folder'] : "INBOX";    
    
  if(isset($user_values) && $user_values['ativo'] == 1){
      $parametros = get_parametro($user_values['parametro']);
      $imap_server = $parametros['imap_server'];
      $imap_port = $parametros['imap_port'];
      $smtp_server = $parametros['smtp_server'];
      $smtp_port = $parametros['smtp_port'];    
  }

  $mailbox = "{" . $imap_server . ":" . $imap_port . "/imap/ssl/novalidate-cert". "}";  
  $mailbox_instance = imap_open($mailbox . $folder, $username, $password);

  $msgn = imap_msgno($mailbox_instance, $_GET['id']);

  $partStruct = imap_bodystruct($mailbox_instance, $msgn, $_GET['partNum']);  
  $content = imap_fetchbody($mailbox_instance, $msgn, $_GET['partNum']);

  $encoding = $partStruct->encoding;
  if($encoding == 1){
    $content = imap_8bit($content);
  }
  if($encoding == 2){
    $content = imap_binary($content);
  }
  if($encoding == 3){
    $content = imap_base64($content);
  }
  if($encoding == 4){
    $content = quoted_printable_decode($content);
  }
  $filename = $partStruct->dparameters[0]->value;  

  $dir = 'attachments/';
  if(!is_dir($dir)){ 
      mkdir($dir);
  }
  
  $filepatch =  $dir . $filename;

  $file = fopen($filepatch, "w") or die("Unable to open file!");       
  fwrite($file, $content);       
  fclose($file);

  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="' . $filename . '"');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($filepatch));
  flush(); 
  readfile($filepatch);
  unlink($filepatch);
  
  die();                    
?>