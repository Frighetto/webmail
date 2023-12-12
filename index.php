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
      $user_values = getFromCsv("usuarios.csv", 0, $_SESSION['username']);
      $parametros = getFromCsv("parametros.csv", 0, $user_values[1]);

      $_SESSION['mailbox'] = $parametros[1];
      $_SESSION['input_port'] = $parametros[2];
      $_SESSION['smtp'] = $parametros[3];
      $_SESSION['output_port'] = $parametros[4];    
    }
                   
    $encryption = 'ssl';            
           
    if(isset($_GET['folder'])){
      $_SESSION['folder'] = $_GET['folder'];
      $_SESSION['page'] = 1;
    } else {
      if(!isset($_SESSION['folder'])){
        $_SESSION['folder'] = 'INBOX';
      }
    }
    if(isset($_POST['page'])){
      $_SESSION['page'] = intval($_POST['page']);
    } else {
      if(!isset($_SESSION['page'])){
        $_SESSION['page'] = 1;
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
    require_once "Imap.php";

    $imap = new Imap($_SESSION['mailbox'] . ":" . $_SESSION['input_port'], $_SESSION['username'], $_SESSION['password'], $encryption);
    if(!$imap->isConnected()){
      $warning = "Usuário ou senha inválidos.";
      require_once "login.php";
    } 
    if(isset($_GET['attachment'])){
      require_once "download.php";
    }       

    if(isset($_POST['remove_folder'])) {
        $imap->removeFolder($_POST['remove_folder']);
    }
    if(isset($_POST['add_folder'])) {
        $imap->addFolder('INBOX.' . $_POST['add_folder']);
    }

    if(isset($_POST["movement_folder"])){          
      $imap->mover($_POST["ids_to_move"], $_POST["movement_folder"]);      
    }    

    $folders = $imap->getFolders();    

    $mail_load = new stdClass;
    $mail_load->mail_list = array();
    $mail_load->folders = array();
    $mail_load->page = $_SESSION['page'];
    $mail_load->page_size = $_SESSION['page_size'];

    foreach($folders as $folder){
        $imap->selectFolder($folder);    
        $overallMessages = $imap->countMessages();
        $unreadMessages = $imap->countUnreadMessages();
        $folder_count = new stdClass;
        $folder_count->name = $folder;
        $folder_count->unreadMessages = $unreadMessages;
        $folder_count->overallMessages = $overallMessages;
        $last_index = sizeof($mail_load->folders);
        $mail_load->folders[$last_index] = $folder_count;
    }

    $count = null;
    $imap->selectFolder($_SESSION['folder']);        
    if(isset($_POST['search'])){
      $search_result = $imap->searchByText($_POST['search']);        
      $count = sizeof($search_result);
    } else {
      $count = $imap->countMessages();
    }     
    $mail_load->total = $count;
    
    $mail_list_start_index = (($_SESSION['page'] - 1) * $_SESSION['page_size']) + 1;
    $mail_list_end_index = $mail_list_start_index + $_SESSION['page_size'];
    $mail_list_end_index = $mail_list_end_index < $count ? $mail_list_end_index : $count + 1;              
    for ($i = $mail_list_end_index - 1; $mail_list_start_index <= $i; $i = $i - 1) {         
        $mail_list_last_index = sizeof($mail_load->mail_list);
        if(isset($_POST['search'])){
          $mail_load->mail_list[$mail_list_last_index] = $imap->getHeader($folder, $search_result[$count - $i]); 
        } else {
          $mail_load->mail_list[$mail_list_last_index] = $imap->getHeader($folder, $count - $i + 1); 
        }       
    }
   
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
    <link href="dashboard.css" rel="stylesheet">
    
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header col-sm-3 col-md-2">          
          <a class="navbar-brand"><?= $_SESSION['username'] ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">     
          <form class="navbar-form navbar-left" method="POST">
            <input type="text" class="form-control" style="width:250%" name="search" placeholder="Search...">
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
            <?php 
                function format_folder_label($folder){
                    $folder_label = str_replace('INBOX.', '', $folder->name);
                    if($folder->unreadMessages > 0){
                        $folder_label = $folder_label . ' ' . $folder->unreadMessages;
                    }
                    return $folder_label;
                }

                foreach($mail_load->folders as $folder){
                    if($_SESSION['folder'] == $folder->name){
            ?>
            <li class="active"><a href="?folder=<?= $folder->name ?>"><?= format_folder_label($folder);?> <span class="sr-only">(current)</span></a></li>
            <?php
                    } else {
            ?>
            <li><a href="?folder=<?= $folder->name ?>"><?= format_folder_label($folder); ?></a></li>
            <?php
                    }
                }
            ?>                       
          </ul>          
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">             
            
        <?php                         
        
        if(isset($_POST['write']) || isset($_POST['reply']) || isset($_POST['redirect'])) {  
          require_once "write.php";
        } else if(isset($_POST['uid'])) {           
          require_once "read.php";
        } else if(isset($_POST['settings'])) {
          require_once "settings.php";
        } else {
          if(isset($_POST['send'])){                                
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
