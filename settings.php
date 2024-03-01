<h2><?= $_SESSION['username'] ?></h2>
<h3>Diretórios</h3>
<form method="POST">    
    <select style="height: 32px" name="remove_folder">  
    <?php foreach($folders as $folder){ ?>                   
        <option value="<?= $folder ?>"><?= $folder ?></option>  
    <?php } ?>      
    </select>
    <button class="btn btn-danger" type="submit" name="settings" value="default">Deletar</button>    
</form>
<br>
<br>
<form method="POST">     
    <select style="height: 32px; float: left" name="renamed_folder">  
    <?php foreach($folders as $folder){ ?>
        <option value="<?= $folder ?>"><?= $folder ?></option>             
    <?php } ?>      
    </select>
    <input type="text" style="width: 200px; float: left" class="form-control" name="new_folder_name" placeholder="Novo nome do diretório">
    <button class="btn btn-primary" type="submit" name="settings" value="default">Renomear</button>    
</form>
<br>
<br>
<form method="POST">
    <span style="font-size: 21px; float: left">Diretório Superior: </span>
    <select style="height: 32px; float: left" name="parent_folder">      
    <?php foreach($folders as $folder){ ?>
        <option value="<?= $folder ?>"><?= $folder ?></option>             
    <?php } ?>      
    </select>
    <input type="text" style="width: 200px; float: left" class="form-control" name="add_folder" placeholder="Novo diretório">
    <button class="btn btn-default" style="float: left" type="submit" name="settings" value="default">Adicionar</button>
</form>    
<br>
<br>
<h3>Fuso Horário</h3>
<form method="POST">
    <select onchange="this.form.submit()" style="height: 32px;" name="timezone">  
        <option <?= $_SESSION['timezone'] == "UTC" ? "selected" : "" ?> value="UTC">Coordinated Universal Time</option>
        <option <?= $_SESSION['timezone'] == "America/Sao_Paulo" ? "selected" : "" ?> value="America/Sao_Paulo">America/Sao_Paulo</option>       
    </select>        
</form>  