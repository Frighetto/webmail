<?PHP
/*

cadastro de empresas
cadastro de pessoas nas empresas
conta de empresas
permissões de contas de empresas
ativar e desativar contas

dashboard - fazer pra conta de email ao em vez de admin sobre todas as contas 

somente admin pode determinar se é editor
quem é editor pode entrar no admin e cadastrar da propria empresa
editor só pode excluir da propria empresa e que não é editor

*/
$admins = array();
$adm = new stdClass;
$adm->username = "teste@helpdesk.tec.br";
$adm->password = "Senha@135";
$admins[sizeof($admins)] = $adm;

$adm = new stdClass;
$adm->username = "teste@teleatendimento.com.br";
$adm->password = "&lBCEyO8,C*y";
$admins[sizeof($admins)] = $adm;

session_start();
if(isset($_POST['sair'])){
  session_destroy();
  $_SESSION = array();
}

if(!isset($_POST['login']) && !isset($_SESSION['admin'])){
    $warning = "";
    require_once "login_admin.php";
}

function getFromCsv($filepatch, $column_index, $value){
    $file = fopen($filepatch, "r") or die("Unable to open file!");       
    $csv = fread($file, filesize($filepatch));       
    fclose($file);

    $list = explode("\n", $csv);

    for($i = 1; $i < sizeof($list); $i++){
      $line = explode(";", $list[$i]);
      if(trim($line[$column_index]) == trim($value)){
        return $line;
      }
    }
    return null;
}

$_SESSION['admin_logedin'] = isset($_SESSION['admin_logedin']) ? $_SESSION['admin_logedin'] : false;
$_SESSION['is_editor'] = isset($_SESSION['is_editor']) ? $_SESSION['is_editor'] : false;
if(isset($_POST['admin'])){
    $_SESSION['admin'] = $_POST['admin'];
    $_SESSION['password'] = $_POST['password'];
    foreach($admins as $adm){
        if($adm->username == $_POST['admin'] && $adm->password == $_POST['password']){
            $_SESSION['admin_logedin'] = true;
        }
    }
    if(!$_SESSION['admin_logedin']){
        $user_values = getFromCsv("usuarios.csv", 0, $_SESSION['admin']);            
        $_SESSION['is_editor'] = isset($user_values) && $user_values[3] == 'true';
        $is_ativo = isset($user_values) && $user_values[4] == 'true';
        if($_SESSION['is_editor'] && $is_ativo){
            $parametros = getFromCsv("parametros.csv", 0, $user_values[1]);
        
            $_SESSION['imap_server'] = $parametros[1];
            $_SESSION['imap_port'] = $parametros[2];

            $mailbox = "{" . $_SESSION['imap_server'] . ":" . $_SESSION['imap_port'] . "/imap/ssl/novalidate-cert". "}";  
            $mailbox_instance = imap_open($mailbox . 'INBOX', $_SESSION['admin'], $_SESSION['password']);
            if(!$mailbox_instance){ 
                $warning = imap_last_error();
                require_once "login_admin.php";
            }
        } else {
            $warning = "Usuário ou senha inválidos";
            require_once "login_admin.php";
        }
        $_SESSION['empresa'] = $user_values[2];
    }
}

if(isset($_POST['empresa']) && $_SESSION['is_editor'] && $_POST['empresa'] != $_SESSION['empresa']){
    session_destroy();
    $_SESSION = array();
}

if(isset($_POST['tab'])){
    $_SESSION['tab'] = $_POST['tab'];
} else {
    $_SESSION['tab'] = isset($_SESSION['tab']) ? $_SESSION['tab'] : "usuarios"; 
}

$file = fopen("usuarios.csv", "r") or die("Unable to open file!");       
$usuarios = fread($file, filesize("usuarios.csv"));       
fclose($file);

$usuarios = explode("\n", $usuarios);

if(isset($_POST['excluir_usuario'])){
    $nova_lista = array();
    for($i = 0; $i < sizeof($usuarios) - 1; $i++) {
        $usuario = explode(";", $usuarios[$i]);
        if($usuario[0] != $_POST['usuario']){
            $nova_lista[sizeof($nova_lista)] = $usuarios[$i];            
        }
    }
    $usuarios = $nova_lista;
}

if(isset($_POST['salvar_usuario'])){ 
    $nova_lista = array();
    for($i = 0; $i < sizeof($usuarios) - 1; $i++) {
        $usuario = explode(";", $usuarios[$i]);
        if($usuario[0] != $_POST['usuario']){
            $nova_lista[sizeof($nova_lista)] = $usuarios[$i];            
        }
    }
    $usuarios = $nova_lista;

    $usuario = $_POST['usuario'];
    $parametro = $_POST['parametro'];
    $empresa = $_POST['empresa'];
    $editor = (isset($_POST['editor']) && $_POST['editor']) == 'on' ? 'true' : 'false';
    $ativo = (isset($_POST['ativo']) && $_POST['ativo']) == 'on' ? 'true' : 'false';    
    $usuarios[sizeof($usuarios)] = $usuario . ";" . $parametro . ";" . $empresa . ";" . $editor . ";" . $ativo;
}

for($i = 1; $i < sizeof($usuarios); $i++) {
    $usuarioa = explode(";", $usuarios[$i]);
    for($j = $i + 1; $j < sizeof($usuarios); $j++) {
        $usuariob = explode(";", $usuarios[$j]);        
        if(strtoupper($usuarioa[0]) > strtoupper($usuariob[0])) {
            $temp = $usuarios[$i];
            $usuarios[$i] = $usuarios[$j];
            $usuarios[$j] = $temp;
            $i = 1;
            break;
        }
    }   
}

$usuarios_str = "";
for($i = 0; $i < sizeof($usuarios); $i++) {
    if(trim($usuarios[$i]) != ""){
        $usuarios_str = $usuarios_str . $usuarios[$i] . "\n";
    }
}

$file = fopen("usuarios.csv", "w") or die("Unable to open file!");       
fwrite($file, $usuarios_str);       
fclose($file);

$file = fopen("parametros.csv", "r") or die("Unable to open file!");       
$parametros = fread($file, filesize("parametros.csv"));       
fclose($file);

$parametros = explode("\n", $parametros);

$lista_parametros = array();

for($i = 1; $i < sizeof($parametros) - 1; $i++) {
    $parametro = explode(";", $parametros[$i]);
    $lista_parametros[sizeof($lista_parametros)] = $parametro[0];
}

if(isset($_POST['excluir_parametro'])){
    $nova_lista = array();
    for($i = 0; $i < sizeof($parametros) - 1; $i++) {
        $parametro = explode(";", $parametros[$i]);
        if($parametro[0] != $_POST['description']){
            $nova_lista[sizeof($nova_lista)] = $parametros[$i];            
        }
    }
    $parametros = $nova_lista;
}

if(isset($_POST['salvar_parametro'])){  
    $nova_lista = array();
    for($i = 0; $i < sizeof($parametros) - 1; $i++) {
        $parametro = explode(";", $parametros[$i]);
        if($parametro[0] != $_POST['description']){
            $nova_lista[sizeof($nova_lista)] = $parametros[$i];            
        }
    }
    $parametros = $nova_lista;

    $parametros[sizeof($parametros)] = $_POST['description'] . ";" . $_POST['imap_server'] . ";" . $_POST['imap_port'] . ";" . $_POST['smtp_server'] . ";" . $_POST['smtp_port'];
}

for($i = 1; $i < sizeof($parametros); $i++) {
    $parametroa = explode(";", $parametros[$i]);
    for($j = $i + 1; $j < sizeof($parametros); $j++) {
        $parametrob = explode(";", $parametros[$j]);        
        if(strtoupper($parametroa[0]) > strtoupper($parametrob[0])) {
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
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="bootstrap-3.3.6/docs/favicon.ico">

    <title>e-mail</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap-3.3.6/docs/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">
    
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header col-sm-3 col-md-2">
          <a class="navbar-brand"><?= $_SESSION['admin'] ?></a>
        </div>
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
    <form method="POST" <?= $_SESSION['is_editor'] ? 'hidden' : '' ?>>
        <div style="width: 50%; float: left">
            <button class="btn btn-<?= $_SESSION['tab'] == "usuarios" ? "primary" : "default" ?> btn-lg btn-block" name="tab" value="usuarios" type="submit">Usuários</button>
        </div>
        <div style="width: 50%; float: left">
            <button class="btn btn-<?= $_SESSION['tab'] == "parametros" ? "primary" : "default" ?> btn-lg btn-block" name="tab" value="parametros" type="submit">Parâmetros</button>
        </div>
    </form>
    <center>
        <div class="table-responsive" style="width: 90%">
        <?php 
        if($_SESSION['tab'] == "usuarios"){
        ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: fit-content">Usuário</th>
                        <th style="width: fit-content">Parâmetro</th> 
                        <th style="width: fit-content">Empresa</th> 
                        <th style="width: fit-content">Editor</th>
                        <th style="width: fit-content">Ativo</th>
                        <th></th>                        
                    </tr>
                </thead>
                <tbody> 
                    <?php 
                    for($i = 1; $i < sizeof($usuarios); $i++) {
                        $usuario = explode(";", $usuarios[$i]);
                        if(sizeof($usuario) > 1 && ($_SESSION['admin_logedin'] || $usuario[2] == $_SESSION['empresa'])){
                    ?> 
                    <tr>
                        <form method="POST">
                            <td><input readonly name="usuario" class="form-control" value="<?= $usuario[0] ?>"></td>
                            <td><input readonly name="parametro" class="form-control" value="<?= $usuario[1] ?>"></td>
                            <td><input readonly name="empresa" class="form-control" value="<?= $usuario[2] ?>"></td>
                            <td><input disabled name="editor" type="checkbox" class="form-control" <?= $usuario[3] == 'true' ? 'checked' : '' ?>></td>
                            <td><input disabled name="ativo" type="checkbox" class="form-control" <?= $usuario[4] == 'true' ? 'checked' : '' ?>></td>
                            <td><button name="excluir_usuario" class="btn btn-danger form-control">excluir</button></td>
                        </form>
                    </tr> 
                    <?php } }?>
                    <tr>
                        <form method="POST">
                            <td><input name="usuario" class="form-control" value=""></td>
                            <td>
                                <select style="height: 32px; width: 100%" name="parametro"> 
                                <?php for($j = 0; $j < sizeof($lista_parametros); $j++) { ?> 
                                    <option value="<?= trim($lista_parametros[$j]) ?>" ><?= $lista_parametros[$j] ?></option>
                                <?php } ?>
                                </select>
                            </td>
                            <td><input <?= $_SESSION['is_editor'] ? 'readonly' : '' ?> name="empresa" value="<?= $_SESSION['is_editor'] ? $_SESSION['empresa'] : '' ?>" class="form-control"></td>
                            <td><input name="editor" type="checkbox" class="form-control"></td>
                            <td><input name="ativo" type="checkbox" class="form-control"></td>
                            <td><button name="salvar_usuario" class="btn btn-success form-control">salvar</button></td>                           
                        </form>
                    </tr> 
                </tbody>
            </table>
            <?php } else if($_SESSION['tab'] == "parametros"){ ?>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: fit-content">Descrição</th>
                        <th style="width: fit-content">IMAP Server</th>  
                        <th>IMAP Port</th>
                        <th style="width: fit-content">SMTP Server</th> 
                        <th>SMTP Port</th>                                             
                        <th></th>
                    </tr>
                </thead>
                <tbody> 
                    <?php 
                    for($i = 1; $i < sizeof($parametros); $i++) {
                        $parametro = explode(";", $parametros[$i]);
                        if(sizeof($parametro) > 1){
                    ?> 
                    <tr>
                        <form method="POST">
                            <td><input readonly name="description" class="form-control" value="<?= $parametro[0] ?>"></td>
                            <td><input readonly name="imap_server" class="form-control" value="<?= $parametro[1] ?>"></td>
                            <td><input readonly name="imap_port" class="form-control" style="width: 100px" value="<?= $parametro[2] ?>"></td>
                            <td><input readonly name="smtp_server" class="form-control" value="<?= $parametro[3] ?>"></td>
                            <td><input readonly name="smtp_port" class="form-control" style="width: 100px" value="<?= $parametro[4] ?>"></td>                            
                            <td><button name="excluir_parametro" class="btn btn-danger form-control">excluir</button></td>
                        </form>
                    </tr> 
                    <?php } } ?>
                    <tr>
                        <form method="POST">
                            <td><input name="description" class="form-control" value=""></td>
                            <td><input name="imap_server" class="form-control" value=""></td>
                            <td><input name="imap_port" class="form-control" style="width: 100px" value=""></td>
                            <td><input name="smtp_server" class="form-control" value=""></td>
                            <td><input name="smtp_port" class="form-control" style="width: 100px" value=""></td>
                            <td><button name="salvar_parametro" class="btn btn-success form-control">salvar</button></td>                            
                        </form>
                    </tr>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </center>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap-3.3.6/docs/assets/js/vendor/jquery.min.js"></script>
    <script src="bootstrap-3.3.6/docs/dist/js/bootstrap.min.js"></script>
        
  </body>
</html>