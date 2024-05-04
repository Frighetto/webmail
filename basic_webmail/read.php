<?php

require_once 'parsemail.php';

$header = imap_headerinfo($mailbox_instance, $_POST['id']);
$header_object = parse_header($header);

imap_setflag_full($mailbox_instance, $_POST['id'], '\\Seen');

?>

<form method="POST" style="float:left"> 
    Mover Para:
    <select onchange="this.form.submit()" style="height: 32px" name="movement_folder">    
        <?php foreach($folders as $folder){  ?>
            <option <?= $folder == $_SESSION['folder'] ? "selected" : "" ?> value="<?= $folder ?>"><?= $folder ?></option>             
        <?php  } ?> 
    </select>
    <input id="ids_to_move" name="ids_to_move" value="<?= $_POST['id'] ?>" hidden />     
</form>        
<form method="POST" style="float:left; margin-left: 15px; margin-right: 15px"> 
    Marcar Como:
    <select onchange="this.form.submit()" style="height: 32px" name="flag">            
        <option value="seen">Lido</option>
        <option value="unseen">Não Lido</option> 
        <option value="flag">&#9873;</option>
        <option value="unflag">&#9872;</option>
        <option value="seen" selected hidden>Lido</option> 
    </select>    
    <input id="ids_to_flag" name="ids_to_flag" value="<?= $_POST['id'] ?>" hidden /> 
</form>  
<form method="POST">                 
    <button class="btn btn-danger" id="ids_to_delete" name="ids_to_delete" value="<?= $_POST['id'] ?>" type="submit">Excluir</button>
</form>


<?php
require_once 'mailbody.php';
?>
<?php if($_SESSION['folder'] == 'INBOX.Drafts'){ ?>
    <form class="form-group" method="POST"> 
        <button type="submit" name="edit" value="<?= $_POST['id'] ?>" class="btn btn-default">
            Editar Rascunho
        </button>         
    </form> 
<?php } else { ?>
    <form class="form-group" method="POST"> 
        <button type="submit" name="reply" value="<?= $_POST['id'] ?>" class="btn btn-default">
            Responder
        </button> 
        <button type="submit" name="redirect" value="<?= $_POST['id'] ?>" class="btn btn-default">
            Encaminhar
        </button> 
    </form> 
<?php } ?>