<html>
    <header>
      <link href="bootstrap-3.3.6/docs/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <input name="username" value="teste@helpdesk.tec.br" type="text" class="form-control input-lg" placeholder="Usuário" required autofocus>
                <input name="password" value="Senha@135" type="password" class="form-control input-lg" placeholder="Senha" required>                                
                <button type="submit" name="login" value="default" class="btn btn-primary btn-lg btn-block">Acessar</button>
                <?= $warning ?>
            </form>                        
        </div>        
    </body>
</html>
<?PHP exit; ?>