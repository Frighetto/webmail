<?PHP

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

require_once "database.php";    

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
        $user_values = get_usuario($_SESSION['admin']);            
        $_SESSION['is_editor'] = isset($user_values) && $user_values['editor'] == 1;
        $is_ativo = isset($user_values) && $user_values['ativo'] == 1;
        if($_SESSION['is_editor'] && $is_ativo){            
            $parametros = get_parametro($user_values['parametro']);   
        
            $_SESSION['mailbox'] = $parametros['imap_host'];
            $_SESSION['input_port'] = $parametros['input_door'];

            $mailbox = "{" . $_SESSION['mailbox'] . ":" . $_SESSION['input_port'] . "/imap/ssl/novalidate-cert". "}";  
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

if(isset($_POST['excluir_usuario'])){
    remove_usuario($_POST['usuario']);    
}

if(isset($_POST['salvar_usuario'])){ 
    $usuario = $_POST['usuario'];
    $parametro = $_POST['parametro'];
    $empresa = $_POST['empresa'];
    $editor = (isset($_POST['editor']) && $_POST['editor']) == 'on' ? 1 : 0;
    $ativo = (isset($_POST['ativo']) && $_POST['ativo']) == 'on' ? 1 : 0;    
    save_usuario($usuario, $parametro, $empresa, $editor, $ativo);    
}

if(isset($_POST['excluir_parametro'])){
    remove_parametro($_POST['description']);        
}

if(isset($_POST['salvar_parametro'])){  
    $description = $_POST['description'];
    $imap_host = $_POST['imap_host'];
    $input_door = $_POST['input_door'];
    $smtp_host = $_POST['smtp_host'];
    $output_door = $_POST['output_door'];
    save_parametro($description, $imap_host, $input_door, $smtp_host, $output_door);
}

$usuarios = get_usuarios();
$parametros = get_parametros();

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
                    for($i = 0; $i < sizeof($usuarios); $i++) {
                        $usuario = $usuarios[$i];                        
                        if(($_SESSION['admin_logedin'] || $usuario['empresa'] == $_SESSION['empresa'])){
                    ?> 
                    <tr>
                        <form method="POST">
                            <td><input readonly name="usuario" class="form-control" value="<?= $usuario["usuario"] ?>"></td>
                            <td><input readonly name="parametro" class="form-control" value="<?= $usuario["parametro"] ?>"></td>
                            <td><input readonly name="empresa" class="form-control" value="<?= $usuario["empresa"] ?>"></td>
                            <td><input disabled name="editor" type="checkbox" class="form-control" <?= $usuario["editor"] == 1 ? 'checked' : '' ?>></td>
                            <td><input disabled name="ativo" type="checkbox" class="form-control" <?= $usuario["ativo"] == 1 ? 'checked' : '' ?>></td>
                            <td><button name="excluir_usuario" class="btn btn-danger form-control">excluir</button></td>
                        </form>
                    </tr> 
                    <?php } }?>
                    <tr>
                        <form method="POST">
                            <td><input name="usuario" class="form-control" value=""></td>
                            <td>
                                <select style="height: 32px; width: 100%" name="parametro"> 
                                <?php for($j = 0; $j < sizeof($parametros); $j++) { ?> 
                                    <option value="<?= $parametros[$j]['description'] ?>" ><?= $parametros[$j]['description'] ?></option>
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
                        <th style="width: fit-content">IMAP Host</th>  
                        <th>IMAP Porta</th>
                        <th style="width: fit-content">SMTP Host</th> 
                        <th>SMTP Porta</th>                                             
                        <th></th>
                    </tr>
                </thead>
                <tbody> 
                    <?php 
                    for($i = 0; $i < sizeof($parametros); $i++) {
                        $parametro = $parametros[$i];                    
                    ?> 
                    <tr>
                        <form method="POST">
                            <td><input readonly name="description" class="form-control" value="<?= $parametro["description"] ?>"></td>
                            <td><input readonly name="imap_host" class="form-control" value="<?= $parametro["imap_host"] ?>"></td>
                            <td><input readonly name="input_door" class="form-control" style="width: 100px" value="<?= $parametro["input_door"] ?>"></td>
                            <td><input readonly name="smtp_host" class="form-control" value="<?= $parametro["smtp_host"] ?>"></td>
                            <td><input readonly name="output_door" class="form-control" style="width: 100px" value="<?= $parametro["output_door"] ?>"></td>                            
                            <td><button name="excluir_parametro" class="btn btn-danger form-control">excluir</button></td>
                        </form>
                    </tr> 
                    <?php } ?>
                    <tr>
                        <form method="POST">
                            <td><input name="description" class="form-control" value=""></td>
                            <td><input name="imap_host" class="form-control" value=""></td>
                            <td><input name="input_door" class="form-control" style="width: 100px" value=""></td>
                            <td><input name="smtp_host" class="form-control" value=""></td>
                            <td><input name="output_door" class="form-control" style="width: 100px" value=""></td>
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