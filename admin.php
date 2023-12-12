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

if(isset($_POST['admin'])){
    $_SESSION['admin'] = $_POST['admin'];
    $_SESSION['password'] = $_POST['password'];
}

if($_SESSION['password'] == "Senha@135" && $_SESSION['admin'] == "teste@helpdesk.tec.br"){
    
} else {
    $warning = "Usuário ou senha inválidos";
    session_destroy();
    require_once "login_admin.php";
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
    $usuarios[sizeof($usuarios)] = $_POST['usuario'] . ";" . $_POST['parametro'];
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
    $parametros[sizeof($parametros)] = $_POST['description'] . ";" . $_POST['imap'] . ";" . $_POST['input'] . ";" . $_POST['smtp'] . ";" . $_POST['output'];
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
    <form method="POST">
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
                        <th>#</th>                        
                    </tr>
                </thead>
                <tbody> 
                    <?php 
                    for($i = 1; $i < sizeof($usuarios); $i++) {
                        $usuario = explode(";", $usuarios[$i]);
                        if(sizeof($usuario) > 1){
                    ?> 
                    <tr>
                        <form method="POST">
                            <td><input readonly name="usuario" class="form-control" value="<?= $usuario[0] ?>"></td>
                            <td><input readonly name="parametro" class="form-control" value="<?= $usuario[1] ?>"></td>                                                                              
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
                        <th>#</th>
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
                            <td><input readonly name="imap" class="form-control" value="<?= $parametro[1] ?>"></td>
                            <td><input readonly name="input" class="form-control" style="width: 100px" value="<?= $parametro[2] ?>"></td>
                            <td><input readonly name="smtp" class="form-control" value="<?= $parametro[3] ?>"></td>
                            <td><input readonly name="output" class="form-control" style="width: 100px" value="<?= $parametro[4] ?>"></td>                            
                            <td><button name="excluir_parametro" class="btn btn-danger form-control">excluir</button></td>
                        </form>
                    </tr> 
                    <?php } } ?>
                    <tr>
                        <form method="POST">
                            <td><input name="description" class="form-control" value=""></td>
                            <td><input name="imap" class="form-control" value=""></td>
                            <td><input name="input" class="form-control" style="width: 100px" value=""></td>
                            <td><input name="smtp" class="form-control" value=""></td>
                            <td><input name="output" class="form-control" style="width: 100px" value=""></td>
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