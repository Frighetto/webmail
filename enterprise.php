<?PHP

session_start();

if(isset($_POST['sair'])){
  session_destroy();
  $_SESSION = array();
}
if(!isset($_POST['login']) && !isset($_SESSION['username'])){
  $warning = "";
  require_once "login.php";
} else {  

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

  if(isset($_POST['username'])){
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['password'] = $_POST['password'];
    require_once "database.php";    
    $user_values = get_usuario($_SESSION['username']);    
    if(isset($user_values) && $user_values['ativo'] == 1){
      $parametros = get_parametro($user_values['parametro']);          
      $_SESSION['mailbox'] = $parametros['imap_host'];
      $_SESSION['input_port'] = $parametros['input_door'];
      $_SESSION['smtp'] = $parametros['smtp_host'];
      $_SESSION['output_port'] = $parametros['output_door'];   
          
      date_default_timezone_set("America/Sao_Paulo"); 
    } else {
      $warning = "Usuário ou senha inválidos";
      require_once "login.php";
    }
  }

  /*
  if(isset($_POST['username'])){
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['password'] = $_POST['password'];
    $user_values = getFromCsv("usuarios.csv", 0, $_SESSION['username']);
    if(isset($user_values) && $user_values[4] == 'true'){
      $parametros = getFromCsv("parametros.csv", 0, $user_values[1]);

      $_SESSION['mailbox'] = $parametros[1];
      $_SESSION['input_port'] = $parametros[2];
      $_SESSION['smtp'] = $parametros[3];
      $_SESSION['output_port'] = $parametros[4];   
          
      date_default_timezone_set("America/Sao_Paulo"); 
    } else {
      $warning = "Usuário ou senha inválidos";
      require_once "login.php";
    }
  }                                     
  */

  if(!isset($_SESSION['folder'])){
    $_SESSION['folder'] = 'INBOX';
  }
  if(isset($_GET['folder']) && $_GET['folder'] != $_SESSION['folder']){
    $_SESSION['folder'] = $_GET['folder'];
    $_SESSION['page'] = 1;
    unset($_SESSION['search']);
  } 
  if(isset($_POST['page'])){
    $_SESSION['page'] = intval($_POST['page']);
  } else {
    if(!isset($_SESSION['page'])){
      $_SESSION['page'] = 1;
    }
  }
  if(isset($_POST['search'])){
    if(trim($_POST['search']) == ''){
      unset($_SESSION['search']);
    } else {
      $_SESSION['search'] = $_POST['search'];
    }
  } 
  if(isset($_POST['page_size'])){
    if($_SESSION['page_size'] != intval($_POST['page_size'])){
      $_SESSION['page_size'] = intval($_POST['page_size']);
      $_SESSION['page'] = 1;
    }
  } else {
    if(!isset($_SESSION['page_size'])){
      $_SESSION['page_size'] = 10;
    }
  }    
}

if(isset($_SESSION['username'])){  

  $mailbox = "{" . $_SESSION['mailbox'] . ":" . $_SESSION['input_port'] . "/imap/ssl/novalidate-cert". "}";  
  $mailbox_instance = imap_open($mailbox . $_SESSION['folder'], $_SESSION['username'], $_SESSION['password']);
  if(!$mailbox_instance){ 
    session_destroy();
    $warning = imap_last_error();
    require_once "login.php";
  }

  if(isset($_POST['remove_folder'])) {     
      imap_deletemailbox($mailbox_instance, $mailbox . $_POST['remove_folder']);
  }
  if(isset($_POST['add_folder'])) {    
      imap_createmailbox($mailbox_instance, $mailbox . 'INBOX.' . $_POST['add_folder']);
  }        

  if(isset($_POST["movement_folder"])){    
    if (imap_mail_move($mailbox_instance, $_POST["ids_to_move"], $_POST["movement_folder"])) {        
        imap_expunge($mailbox_instance);
    } else {
      die(imap_last_error());
    }  
  }
  
  if(isset($_POST["flag"])){    
    if($_POST["flag"] == 'seen'){ 
      imap_setflag_full($mailbox_instance, $_POST["ids_to_flag"], '\\Seen');  
    } else if($_POST["flag"] == 'unseen'){ 
      imap_clearflag_full($mailbox_instance, $_POST["ids_to_flag"], '\\Seen');  
    } else if($_POST["flag"] == 'flag'){ 
      imap_setflag_full($mailbox_instance, $_POST["ids_to_flag"], '\\Flagged');  
    } else if($_POST["flag"] == 'unflag'){ 
      imap_clearflag_full($mailbox_instance, $_POST["ids_to_flag"], '\\Flagged');  
    }
  }

  if(isset($_POST["ids_to_delete"])){        
    if($_SESSION['folder'] == 'INBOX.Trash'){
      imap_setflag_full($mailbox_instance, $_POST["ids_to_delete"], '\\Deleted');  
      imap_expunge($mailbox_instance);
    } 
  }

  $folders = imap_list($mailbox_instance, $mailbox, "*");
  $folders = str_replace($mailbox, "", $folders); 

  $folders_unread = [];
  $folders_total = [];
      
  foreach($folders as $folder){      
      imap_reopen($mailbox_instance, $mailbox . $folder);      
      $unread_mails = imap_search($mailbox_instance, 'UNSEEN');    
      $unread_mails = $unread_mails ? sizeof($unread_mails) : 0;
      $folder_name = str_replace($mailbox, "", $folder);          
      $folders_total[$folder_name] = imap_num_msg($mailbox_instance);
      $folders_unread[$folder_name] = $unread_mails;        
  }

  imap_reopen($mailbox_instance, $mailbox . $_SESSION['folder']);

}
 
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
          <a class="navbar-brand"><?= $_SESSION['username'] ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">     
          <form class="navbar-form navbar-left" method="POST">
            <input type="text" class="form-control" style="width:250%" name="search" placeholder="Search..." value="<?= isset($_SESSION['search']) ? $_SESSION['search'] : '' ?>">
          </form>     
          <ul class="nav navbar-nav navbar-right">       
            <li>
                <form class="navbar-form" method="POST">
                  <button name="settings" class="btn btn-default form-control" value="default" type="submit">
                    configurações
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

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <form method="POST">
            <button class="btn btn-default btn-lg btn-block" name="write" value="novo" type="submit">Escrever e-mail</button>
          </form>
          <br>
          <ul class="nav nav-sidebar">
            <?php foreach($folders as $folder){  ?>
            <li class="<?= $_SESSION['folder'] == $folder ? 'active' : '' ?>">
              <a href="?folder=<?= $folder ?>">
                <?= str_replace("INBOX.", "", $folder) ?><?= " (" . $folders_unread[$folder] . "/" . $folders_total[$folder] . ")" ?>
              </a>
            </li>
            <?php } ?>                       
          </ul>          
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">             
            
        <?php                         
        
        if(isset($_POST['write']) || isset($_POST['reply']) || isset($_POST['redirect']) || isset($_POST['edit'])) {  
          require_once "write.php";
        } else if(isset($_POST['id'])) {           
          require_once "read.php";
        } else if(isset($_POST['settings'])) {
          require_once "settings.php";
        } else {
          if(isset($_POST['send']) || isset($_POST['draft'])){                                
            require_once "send.php";
          } 
          require_once "select.php";
        } 
        
        ?> 

        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap-3.3.6/docs/assets/js/vendor/jquery.min.js"></script>
    <script src="bootstrap-3.3.6/docs/dist/js/bootstrap.min.js"></script>
        
  </body>
</html>
