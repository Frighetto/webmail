<?php

session_start();
if(isset($_POST['sair'])){
  session_destroy();
  $_SESSION = array();
}

$warning = "";
if(isset($_POST['login'])){
  if($_POST['admin'] == "admin" && $_POST['adminpassword'] == "hostnames"){
    $_SESSION['admin'] = $_POST['admin'];
    $_SESSION['adminpassword'] = $_POST['adminpassword'];
  } else {
    $warning = "Usuário ou senha inválidos";    
  }
}

if(!isset($_SESSION['admin'])){
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>Webmail</title> 
        <meta charset="utf-8">    
        <meta name="viewport" content="width=device-width, initial-scale=1">    
        <meta name="description" content="Webmail Admin">
        <meta name="author" content="Lucas Fernando Frighetto">
        <link rel="icon" href="email.png">      
        <link href="bootstrap-3.3.6/docs/dist/css/bootstrap.min.css" rel="stylesheet">    
        <style>
            .form-signup { 
              margin: auto; 
              width: calc(100% / 3); 
            }
        </style>
    </head>
    <body>
        <div class="container">            
            <form method="POST" class="form-signup">          
                <input name="admin" value="" type="text" class="form-control input-lg" placeholder="Usuário" required autofocus>
                <input name="adminpassword" value="" type="password" class="form-control input-lg" placeholder="Senha" required>                
                <button type="submit" name="login" value="default" class="btn btn-primary btn-lg btn-block">Acessar</button>
                <?= $warning ?>
            </form>            
        </div>        
    </body>
</html>

<?php    
    exit;
}

$INDICE_PRIMARIO = 0;

$file = fopen("parametros.csv", "r") or die("Unable to open file!");       
$parametros = fread($file, filesize("parametros.csv"));       
fclose($file);

$parametros = explode("\n", $parametros);

$lista_parametros = array();

for($i = 1; $i < sizeof($parametros) - 1; $i++) {
    $parametro = explode(";", $parametros[$i]);
    $lista_parametros[sizeof($lista_parametros)] = $parametro[$INDICE_PRIMARIO];
}

if(isset($_POST['excluir_parametro'])){
    $nova_lista = array();
    for($i = 0; $i < sizeof($parametros) - 1; $i++) {
        $parametro = explode(";", $parametros[$i]);
        if($parametro[$INDICE_PRIMARIO] != $_POST['domain']){
            $nova_lista[sizeof($nova_lista)] = $parametros[$i];            
        }
    }
    $parametros = $nova_lista;
}

if(isset($_POST['salvar_parametro'])){  
    $nova_lista = array();
    for($i = 0; $i < sizeof($parametros) - 1; $i++) {
        $parametro = explode(";", $parametros[$i]);
        if($parametro[$INDICE_PRIMARIO] != $_POST['domain']){
            $nova_lista[sizeof($nova_lista)] = $parametros[$i];            
        }
    }
    $parametros = $nova_lista;

    $parametros[sizeof($parametros)] = $_POST['domain'] . ";" . $_POST['imap_server'] . ";" . $_POST['imap_port'] . ";" . $_POST['smtp_server'] . ";" . $_POST['smtp_port'];
}

for($i = 1; $i < sizeof($parametros); $i++) {
    $parametro_a = explode(";", $parametros[$i]);
    for($j = $i + 1; $j < sizeof($parametros); $j++) {
        $parametro_b = explode(";", $parametros[$j]);        
        if(strtoupper($parametro_a[$INDICE_PRIMARIO]) > strtoupper($parametro_b[$INDICE_PRIMARIO])) {
            $temp = $parametros[$i];
            $parametros[$i] = $parametros[$j];
            $parametros[$j] = $temp;
            $i = 1;
            break;
        }
    }   
}

$parametros_str = "";
for($i = 0; $i < sizeof($parametros); $i++) {    
    if(trim($parametros[$i]) != ""){
        $parametros_str = $parametros_str . $parametros[$i] . "\n";
    }
}

$file = fopen("parametros.csv", "w") or die("Unable to open file!");       
fwrite($file, $parametros_str);       
fclose($file);

?>

<!DOCTYPE html>
<html lang="pt">
  <head>
  <title>Webmail</title> 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <meta name="description" content="Webmail that suport any server">
    <meta name="author" content="Lucas Fernando Frighetto">
    <link rel="icon" href="email.png">      
    <link href="bootstrap-3.3.6/docs/dist/css/bootstrap.min.css" rel="stylesheet">          
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">        
        <div id="navbar" class="navbar-collapse collapse">                
          <ul class="nav navbar-nav navbar-right">                                           
            <li>
                <form class="navbar-form" method="POST">
                  <button name="sair" class="btn btn-default form-control" value="default" type="submit">
                    Sair
                  </button>
                </form>
            </li>
          </ul> 
        </div>
      </div>
    </nav>
    <center>
        <table class="table" style="margin-top: 5%">
            <thead>
                <tr>
                    <th style="width: fit-content">Domain</th>
                    <th style="width: fit-content">IMAP Server</th>  
                    <th>IMAP Port</th>
                    <th style="width: fit-content">SMTP Server</th> 
                    <th>SMTP Port</th>                                             
                    <th></th>
                </tr>
            </thead>
            <tbody> 
                <?php 
                $DOMAIN = 0;
                $IMAP_SERVER = 1;
                $IMAP_PORT = 2;
                $SMTP_SERVER = 3;
                $SMTP_PORT = 4;
                for($i = 1; $i < sizeof($parametros); $i++) {
                    $parametro = explode(";", $parametros[$i]);
                    if(sizeof($parametro) > 1){
                ?> 
                <tr>
                    <form method="POST">
                        <td><input readonly name="domain" class="form-control" value="<?= $parametro[$DOMAIN] ?>"></td>
                        <td><input readonly name="imap_server" class="form-control" value="<?= $parametro[$IMAP_SERVER] ?>"></td>
                        <td><input readonly name="imap_port" class="form-control" style="width: 100px" value="<?= $parametro[$IMAP_PORT] ?>"></td>
                        <td><input readonly name="smtp_server" class="form-control" value="<?= $parametro[$SMTP_SERVER] ?>"></td>
                        <td><input readonly name="smtp_port" class="form-control" style="width: 100px" value="<?= $parametro[$SMTP_PORT] ?>"></td>                            
                        <td><button name="excluir_parametro" class="btn btn-danger form-control">excluir</button></td>
                    </form>
                </tr> 
                <?php 
                    } 
                } 
                ?>
                <tr>
                    <form method="POST">
                        <td><input name="domain" class="form-control" value=""></td>
                        <td><input name="imap_server" class="form-control" value=""></td>
                        <td><input name="imap_port" class="form-control" style="width: 100px" value=""></td>
                        <td><input name="smtp_server" class="form-control" value=""></td>
                        <td><input name="smtp_port" class="form-control" style="width: 100px" value=""></td>
                        <td><button name="salvar_parametro" class="btn btn-success form-control">salvar</button></td>                            
                    </form>
                </tr>
            </tbody>
        </table>
    </center>
  
    <script src="bootstrap-3.3.6/docs/assets/js/vendor/jquery.min.js"></script>
    <script src="bootstrap-3.3.6/docs/dist/js/bootstrap.min.js"></script>
        
  </body>
</html>