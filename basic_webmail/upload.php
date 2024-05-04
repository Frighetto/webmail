<?php 

session_start();

$filename = $_POST["name"];
$filesize = $_POST["size"];
$filetype = $_POST["type"];
$content = $_POST["content"];
$content = substr($content, strlen("data:image/png;base64,"));
$content = base64_decode($content);

$dir = 'new_mail/';
if(!is_dir($dir)){ 
    mkdir($dir);
}

$dir = 'new_mail/' . $_SESSION["username"] . '/';
if(!is_dir($dir)){ 
    mkdir($dir);
}

$filepatch =  $dir . $filename;

$file = fopen($filepatch, "w") or die("Unable to open file!");       
fwrite($file, $content);       
fclose($file);

echo $filepatch;