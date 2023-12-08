<?php
    $filepatch = 'temp/' . $_SESSION['username'] . "/";

    if(!is_dir($filepatch)){ 
    mkdir($filepatch);
    }

    $file = fopen($filepatch . "message_body", "w") or die("Unable to open file!");       
    fwrite($file, $_POST['message_body']);       
    fclose($file);       

    $attachments = "";
    foreach ($_FILES as $attachment) {  
    $tmp_name = $attachment["tmp_name"];
    $tmp_filename = "";
    for($index = strlen($tmp_name) - 1; $index >= 0; $index = $index - 1){              
        if($tmp_name[$index] == '\\'){ 
        $tmp_filename = substr($tmp_name, $index + 1);                
        break;
        }
    }
    
    if($attachment["size"] > 0){
        $attachments .= " \"" . $attachment["name"] . "\" \"" . "temp\\" . $_SESSION['username'] . "\\" . $tmp_filename . "\"";
    }           

    $comand = "copy " . $attachment["tmp_name"] . " " . "temp\\" . $_SESSION['username'] . "\\" . $tmp_filename;
    $output = array();            
    exec($comand, $output);          
    
    }            
    $from = $_SESSION['username'];
    $password = $_SESSION['password'];
    $to = $_POST['to'];
    $subject = $_POST['subject'];
    $mailbox = $_SESSION['mailbox'];
    $output_port = $_SESSION['output_port'];
    
    $comand = "java -cp \".;activation-1.1.jar;javax.mail-1.6.0.jar\" Email \"$mailbox\" \"$output_port\" \"$from\" \"$password\" \"$to\" \"$subject\" $attachments";                    
    $output = array();
    exec($comand, $output);        
    
    delTree($filepatch);

    function delTree($dir) {

        $files = array_diff(scandir($dir), array('.','..'));
     
         foreach ($files as $file) {
     
           (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
     
         }
     
         return rmdir($dir);
     
    }
?>    