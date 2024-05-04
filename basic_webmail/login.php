<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>Webmail</title> 
        <meta charset="utf-8">    
        <meta name="viewport" content="width=device-width, initial-scale=1">    
        <meta name="description" content="Webmail">
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
                <input name="username" value="" type="text" class="form-control input-lg" placeholder="Usuário" required autofocus>
                <input name="password" value="" type="password" class="form-control input-lg" placeholder="Senha" required>                                
                <button type="submit" name="login" value="default" class="btn btn-primary btn-lg btn-block">Acessar</button>
                <?= $warning ?>
            </form>                        
        </div>        
    </body>
</html>