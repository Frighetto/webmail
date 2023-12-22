<?php
 
  session_start();
  
  $mailbox = "{" . $_SESSION['mailbox'] . ":" . $_SESSION['input_port'] . "/imap/ssl/novalidate-cert". "}";  
  $mailbox_instance = imap_open($mailbox . $_SESSION['folder'], $_SESSION['username'], $_SESSION['password']);
  $partStruct = imap_bodystruct($mailbox_instance, $_GET['id'], $_GET['attachment']);  
  $content = imap_fetchbody($mailbox_instance, $_GET['id'], $_GET['attachment']);
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
  
  $filepatch = 'attachments/' . $filename;

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