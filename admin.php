<?PHP

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
  $_SESSION['adminpassword'] = $_POST['adminpassword'];
  $user_values = get_usuario($_SESSION['admin']);  
  $_SESSION['admin_logedin'] = isset($user_values) && $user_values['permissao'] == 'MASTER';       
  $_SESSION['is_editor'] = isset($user_values) && ($user_values['permissao'] == 'MASTER' || $user_values['permissao'] == 'EMPRESA');
  $is_ativo = isset($user_values) && $user_values['ativo'] == 1;
  if($_SESSION['is_editor'] && $is_ativo){            
      $parametros = get_parametro($user_values['parametro']);   

      $_SESSION['mailbox'] = $parametros['imap_host'];
      $_SESSION['input_port'] = $parametros['input_door'];

      $mailbox = "{" . $_SESSION['mailbox'] . ":" . $_SESSION['input_port'] . "/imap/ssl/novalidate-cert". "}";  
      $mailbox_instance = imap_open($mailbox . 'INBOX', $_SESSION['admin'], $_SESSION['adminpassword']);
      if(!$mailbox_instance){ 
          $warning = imap_last_error();
          require_once "login_admin.php";
      }
  } else {
      $warning = "Usuário ou senha inválidos";
      require_once "login_admin.php";
  }
  $_SESSION['empresa'] = $user_values['empresa'];
}

if(!$_SESSION['admin_logedin'] && (isset($_POST['empresa']) && $_SESSION['is_editor'] && $_POST['empresa'] != $_SESSION['empresa'])){
    session_destroy();
    $_SESSION = array();
}

if(isset($_POST['tab'])){
    $_SESSION['tab'] = $_POST['tab'];
} else {
    $_SESSION['tab'] = isset($_SESSION['tab']) ? $_SESSION['tab'] : "usuarios"; 
}

function update_login_files(){
  
}

if(isset($_POST['excluir_usuario'])){
    remove_usuario($_POST['usuario']); 
    update_secret_identifiers();     
}

if(isset($_POST['salvar_usuario'])){ 
    $usuario = $_POST['usuario'];
    $parametro = $_POST['parametro'];
    $empresa = $_POST['empresa'];
    $senha = $_POST['senha'];
    $permissao = $_POST['permissao'];
    $ativo = (isset($_POST['ativo']) && $_POST['ativo']) == 'on' ? 1 : 0;    
    save_usuario($usuario, $parametro, $empresa, $senha, $permissao, $ativo);  
    update_secret_identifiers();  
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
    <link href="bootstrap-3.3.6/docs/examples/dashboard/dashboard.css" rel="stylesheet">
    
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
                  <button name="cadastro" class="btn btn-default form-control" value="default" type="submit">
                    Cadastro
                  </button>
                </form>
            </li> 
            <li>
                <form class="navbar-form" method="POST">
                  <button name="dashboard" class="btn btn-default form-control" value="default" type="submit">
                    Dashboard
                  </button>
                </form>
            </li>                                
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
    <?php 
    if(isset($_POST['dashboard'])){
        require_once 'admindashboard.php';
    } else {
        require_once 'admincadastro.php';
    }
    ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap-3.3.6/docs/assets/js/vendor/jquery.min.js"></script>
    <script src="bootstrap-3.3.6/docs/dist/js/bootstrap.min.js"></script>
        
  </body>
</html>