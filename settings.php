<form method="POST"> 
    Diretório:
    <select style="height: 32px" name="remove_folder">  
    <?php 
    foreach($folders as $folder){             
        if($folder != $_SESSION['folder']){
    ?>
        <option value="<?= $folder ?>"><?= str_replace("INBOX.", "", $folder) ?></option>             
    <?php 
        }
    }
    ?>      
    </select>
    <button class="btn btn-danger" type="submit" name="settings" value="default">Deletar</button>    
</form>
<br>
<br>
<form method="POST">
    <input type="text" style="width: 200px" name="add_folder" placeholder="Novo diretório">
    <button class="btn btn-default" type="submit" name="settings" value="default">Adicionar</button>
</form>     