<?php 
/*

http://189.4.83.169:888/phpmyadmin_fc833e3a98766eda/index.php
Database name：sql_webmail_pfm_
User：sql_webmail_pfm_
Password：c6c8XjrP6GbBhTGA

*/
/*
$ip = "127.0.0.1";			    
$database_username = "sql_webmail_pfm_";	
$database_password = "c6c8XjrP6GbBhTGA";		
$database = "sql_webmail_pfm_";	    

$port = 3306;
*/

$ip = "127.0.0.1";			    
$database_username = "root";	
$database_password = "";		
$database = "webmail";	    

$port = 3306;


$mysqli = new mysqli($ip, $database_username, $database_password, $database, $port);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}	

function query_result($sql, $list = true){
    global $mysqli;
    $resultQuery = $mysqli->query($sql);
    $result = array();
    for ($row_no = 0; $row_no < $resultQuery->num_rows; $row_no++) {
        $resultQuery->data_seek($row_no);
        $row = $resultQuery->fetch_assoc();  
        $result[sizeof($result)] = $row;
    }   
    return $list ? $result : (sizeof($result) == 1 ? $result[0] : null);   
}

function get_usuario($usuario){
    global $mysqli;
    $sql = "SELECT usuario, parametro, empresa, editor, ativo FROM usuarios WHERE UPPER('$usuario') = UPPER(usuario)";
    return query_result($sql, false);
}

function get_usuarios(){
    global $mysqli;
    $sql = "SELECT usuario, parametro, empresa, editor, ativo FROM usuarios ORDER BY usuario";
    return query_result($sql);
}

function save_usuario($usuario, $parametro, $empresa, $editor, $ativo){
    global $mysqli;
    remove_usuario($usuario);
    $sql = "INSERT INTO usuarios (usuario, parametro, empresa, editor, ativo) VALUES ('$usuario', '$parametro', '$empresa', $editor, $ativo)";
    $mysqli->query($sql);    
}

function remove_usuario($usuario){
    global $mysqli;
    $sql = "DELETE FROM usuarios WHERE UPPER(usuario) = UPPER('$usuario')";
    $mysqli->query($sql);  
}

function get_parametro($parametro){
    global $mysqli;
    $sql = "SELECT description, imap_host, input_door, smtp_host, output_door FROM parametros WHERE UPPER('$parametro') = UPPER(description)";    
    return query_result($sql, false);
}

function get_parametros(){
    global $mysqli;
    $sql = "SELECT description, imap_host, input_door, smtp_host, output_door FROM parametros ORDER BY description";    
    return query_result($sql);
}

function save_parametro($description, $imap_host, $input_door, $smtp_host, $output_door){
    global $mysqli;
    remove_parametro($description);
    $sql = "INSERT INTO parametros (description, imap_host, input_door, smtp_host, output_door) VALUES ('$description', '$imap_host', $input_door, '$smtp_host', $output_door)";   
    $mysqli->query($sql);       
}

function remove_parametro($description){
    global $mysqli;
    $sql = "DELETE FROM parametros WHERE UPPER(description) = UPPER('$description')";
    $mysqli->query($sql);  
}

?>