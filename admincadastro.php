<form method="POST" <?= $_SESSION['admin_logedin'] ? '' : 'hidden' ?>>
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
                    <th style="width: fit-content">Senha</th>
                    <th style="width: fit-content">Permissão</th>
                    <th style="width: fit-content">Ativo</th>
                    <th></th>                        
                </tr>
            </thead>
            <tbody> 
                <?php                 
                for($i = 0; $i < sizeof($usuarios); $i++) {
                    $usuario = $usuarios[$i];                                
                    if(($_SESSION['admin_logedin'] || ($usuario['empresa'] == $_SESSION['empresa'] && $usuario["permissao"] != 'MASTER'))){
                ?> 
                <tr>
                    <form method="POST">                        
                        <td><input readonly name="usuario" class="form-control" value="<?= $usuario["usuario"] ?>"></td>
                        <td><input readonly name="parametro" class="form-control" value="<?= $usuario["parametro"] ?>"></td>
                        <td><input readonly name="empresa" class="form-control" value="<?= $usuario["empresa"] ?>"></td>
                        <td><input readonly name="senha" class="form-control" value="<?= $usuario["senha"] ?>"></td>
                        <td><input readonly name="permissao" class="form-control" value="<?= $usuario["permissao"] ?>"></td>                        
                        <td><input disabled name="ativo" type="checkbox" class="form-control" <?= $usuario["ativo"] == 1 ? 'checked' : '' ?>></td>
                        <td><button name="excluir_usuario" class="btn btn-danger form-control">excluir</button></td>
                    </form>
                </tr> 
                <?php } }?>
                <tr>
                    <form method="POST">
                        <td><input name="usuario" placeholder="Usuário" class="form-control" value=""></td>
                        <td>
                            <select style="height: 32px; width: 100%" name="parametro"> 
                            <?php for($j = 0; $j < sizeof($parametros); $j++) { ?> 
                                <option value="<?= $parametros[$j]['description'] ?>" ><?= $parametros[$j]['description'] ?></option>
                            <?php } ?>
                            </select>
                        </td>
                        <td><input placeholder="empresa" <?= $_SESSION['admin_logedin'] ? '' : 'readonly' ?> name="empresa" value="<?= $_SESSION['admin_logedin'] ? '' : $_SESSION['empresa'] ?>" class="form-control"></td>
                        <td><input placeholder="senha" name="senha" class="form-control"></td>
                        <td>
                            <select style="height: 32px; width: 100%" name="permissao"> 
                                <option value="USUARIO">USUARIO</option>
                                <option value="EMPRESA">EMPRESA</option>                                
                                <?php if($_SESSION['admin_logedin']){ ?>
                                    <option value="MASTER">MASTER</option>
                                <?php } ?>
                            </select>
                        </td>
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
                for($i = 0; $i < sizeof($parametros); $i++) {
                    $parametro = $parametros[$i];                    
                ?> 
                <tr>
                    <form method="POST">
                        <td><input readonly name="description" class="form-control" value="<?= $parametro["description"] ?>"></td>
                        <td><input readonly name="imap_server" class="form-control" value="<?= $parametro["imap_server"] ?>"></td>
                        <td><input readonly name="imap_port" class="form-control" style="width: 100px" value="<?= $parametro["imap_port"] ?>"></td>
                        <td><input readonly name="smtp_server" class="form-control" value="<?= $parametro["smtp_server"] ?>"></td>
                        <td><input readonly name="smtp_port" class="form-control" style="width: 100px" value="<?= $parametro["smtp_port"] ?>"></td>                            
                        <td><button name="excluir_parametro" class="btn btn-danger form-control">excluir</button></td>
                    </form>
                </tr> 
                <?php } ?>
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