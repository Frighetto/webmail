<html>
    <header>
      <link href="bootstrap-3.3.6/docs/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="icon" href="email.png"> 
      <style>
        .form-signup { 
          margin: auto; 
          width: calc(100% / 3); 
        }
      </style>
    </header>
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
