<?php
      
  $attachment = $imap->getAttachment(intval($_GET['uid']), intval($_GET['attachment']));                  
  $filepatch = 'attachments/' . $attachment['name'];

  $file = fopen($filepatch, "w") or die("Unable to open file!");       
  fwrite($file, $attachment['content']);       
  fclose($file);

  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="' . $attachment['name'] . '"');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($filepatch));
  flush(); 
  readfile($filepatch);
  die();                    
?>