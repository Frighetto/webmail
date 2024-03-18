<?php 
/*

http://189.4.83.169:888/phpmyadmin_fc833e3a98766eda/index.php
Database name：sql_webmail_pfm_
User：sql_webmail_pfm_
Password：c6c8XjrP6GbBhTGA

*/

$ip = "127.0.0.1";			    
$database_username = "sql_webmail_pfm_";	
$database_password = "c6c8XjrP6GbBhTGA";		
$database = "sql_webmail_pfm_";	    

$port = 3306;


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

function get_secret_ids(){ 
    return query_result("SELECT secret_identifier FROM usuarios");
}

function get_usuario($usuario){    
    $sql = "SELECT usuario, parametro, empresa, senha, permissao, ativo, secret_identifier FROM usuarios WHERE UPPER('$usuario') = UPPER(usuario)";
    return query_result($sql, false);
}

function get_usuario_by_id($id){    
    $sql = "SELECT usuario, parametro, empresa, senha, permissao, ativo, secret_identifier FROM usuarios WHERE '$id' = secret_identifier";    
    return query_result($sql, false);
}

function get_usuarios(){    
    $sql = "SELECT usuario, parametro, empresa, senha, permissao, ativo FROM usuarios ORDER BY usuario";
    return query_result($sql);
}

function save_usuario($usuario, $parametro, $empresa, $senha, $permissao, $ativo){
    global $mysqli;
    remove_usuario($usuario);
    $sql = "INSERT INTO usuarios (usuario, parametro, empresa, senha, permissao, ativo) VALUES ('$usuario', '$parametro', '$empresa', '$senha', '$permissao', $ativo)";
    $mysqli->query($sql);    
}

function remove_usuario($usuario){
    global $mysqli;
    $sql = "DELETE FROM usuarios WHERE UPPER(usuario) = UPPER('$usuario')";
    $mysqli->query($sql);  
}

function get_parametro($parametro){    
    $sql = "SELECT description, imap_server, imap_port, smtp_server, smtp_port FROM parametros WHERE UPPER('$parametro') = UPPER(description)";    
    return query_result($sql, false);
}

function get_parametros(){    
    $sql = "SELECT description, imap_server, imap_port, smtp_server, smtp_port FROM parametros ORDER BY description";    
    return query_result($sql);
}

function save_parametro($description, $imap_server, $imap_port, $smtp_server, $smtp_port){
    global $mysqli;
    remove_parametro($description);
    $sql = "INSERT INTO parametros (description, imap_server, imap_port, smtp_server, smtp_port) VALUES ('$description', '$imap_server', $imap_port, '$smtp_server', $smtp_port)";   
    $mysqli->query($sql);       
}

function remove_parametro($description){
    global $mysqli;
    $sql = "DELETE FROM parametros WHERE UPPER(description) = UPPER('$description')";
    $mysqli->query($sql);  
}

function get_usuarios_parametros(){    
    $sql = 
    "SELECT     usuario,
                senha,
                description, 
                imap_server, 
                imap_port, 
                smtp_server, 
                smtp_port                
    FROM        usuarios
    LEFT JOIN   parametros
        ON      parametros.description = usuarios.parametro";    
    return query_result($sql);
}

function update_secret_identifiers(){
    global $mysqli;
    $usuarios = query_result("SELECT usuario FROM usuarios");
    for($i = 0; $i < sizeof($usuarios); $i = $i + 1){
        $usuario = $usuarios[$i]["usuario"];
        $new_secret_identifier = new_secret_identifier();
        $sql = "UPDATE usuarios SET secret_identifier = '$new_secret_identifier' WHERE usuario = '$usuario'";
        $mysqli->query($sql);  
    }
}

function new_secret_identifier(){
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    $new_secret_identifier = "";
    for($i = 0; $i < 100; $i = $i + 1){        
      $new_secret_identifier = $new_secret_identifier . $chars[rand(0, strlen($chars) - 1)];
    }
    return $new_secret_identifier;
  }

?>