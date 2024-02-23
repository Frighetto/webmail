<?php

session_start();

if(isset($_POST['username'])){
    require_once "database.php";    
    $user_values = get_usuario($_POST['username']);    
    if(isset($user_values) && $user_values['ativo'] == 1 && $user_values['senha'] == $_POST['password']){
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['password'] = $_POST['password'];  
        $_SESSION['secret_identifier'] = $user_values['secret_identifier'];     
    } else {
        $warning = "Usuário ou senha inválidos";
        require_once "login.php";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>    
        <link href="bootstrap-3.3.6/docs/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .form-signup { 
            margin: auto; 
            width: calc(100% / 3); 
            }
        </style>
    </head>
    <body>                
        <?php 
        if(isset($_SESSION['secret_identifier'])) { 
            ?>           
            <script>               
                var ip = location.host;    
                if(ip.indexOf(":") !== -1){
                    ip = ip.split(":")[0];
                }        
                var url = 'http://' + ip + ':3000/#/home/<?= $_SESSION['secret_identifier'] ?>'; 
                window.location = url;                                
                </script>
            <?php
        } else { 
        ?>
        <div id="login-area" style="height: 100%">
            <div class="container">       
                <form method="POST" class="form-signup">                                                
                    <input id="loginusername" name="username" value="teste@teleatendimento.com.br" type="text" class="form-control input-lg" placeholder="Usuário" required autofocus>
                    <input id="loginpassword" name="password" value="&lBCEyO8,C*y" type="password" class="form-control input-lg" placeholder="Senha" required>                                
                    <button id="loginbutton" type="submit" name="login" value="<?= isset($_SESSION['secret_identifier']) ? $_SESSION['secret_identifier'] : '' ?>" class="btn btn-primary btn-lg btn-block">Acessar</button>                    
                </form>                     
            </div>       
        </div>
        <?php
        }
        ?>
    </body>
</html>