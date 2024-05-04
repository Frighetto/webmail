<?php
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Headers: *");

  session_start();
  
  $mailbox = "{" . $_SESSION['imap_server'] . ":" . $_SESSION['imap_port'] . "/imap/ssl/novalidate-cert". "}";  
  $mailbox_instance = imap_open($mailbox . $_GET['folder'], $_SESSION['username'], $_SESSION['password']);
  $part = imap_bodystruct($mailbox_instance, $_GET['id'], $_GET['partNum']);  
  $content = imap_fetchbody($mailbox_instance, $_GET['id'], $_GET['partNum']);

  $encoding = $part->encoding;
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
  $filename = "unknown";
  if(isset($part->dparameters)){
      foreach($part->dparameters as $dparameter){
          if(strtoupper($dparameter->attribute) == "FILENAME"){
          $filename = $dparameter->value;
          }
      }
  }

  if($filename == "unknown"){
      if(isset($part->parameters)){
          foreach($part->parameters as $parameter){
              if(strtoupper($parameter->attribute) == "FILENAME"){
                  $filename = $parameter->value;
              }
          }
      }
  } 

  $dir = 'attachments/';
  if(!is_dir($dir)){ 
      mkdir($dir);
  }

  $dir = 'attachments/' . $_SESSION['username'] . '/';
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
  rmdir($dir);
  die();                    
?>
