<?php

require_once 'parsemail.php';

$header = imap_headerinfo($mailbox_instance, $_POST['id']);
$header_object = parse_header($header);

imap_setflag_full($mailbox_instance, $_POST['id'], '\\Seen');

?>

<form method="POST" style="float:left; margin-right: 15px;"> 
    Mover Para:
    <select style="height: 32px" name="movement_folder">;     
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
    <button class="btn btn-default" id="ids_to_move" name="ids_to_move" value="<?= $_POST['id'] ?>" type="submit">OK</button>
</form>
<?php if($_SESSION['folder'] == 'INBOX.Trash'){ ?>
    <form method="POST" style="float:left; margin-right: 15px;">                 
        <button class="btn btn-default" id="ids_to_delete" name="ids_to_delete" value="<?= $_POST['id'] ?>" type="submit">Excluir</button>
    </form>
<?php } ?>
<form method="POST" style="margin-left: 15px"> 
    Marcar Como:
    <select style="height: 32px" name="flag">;   
        <option value="seen">Lido</option>
        <option value="unseen">Não Lido</option> 
    </select>
    <button class="btn btn-default" id="ids_to_flag" name="ids_to_flag" value="<?= $_POST['id'] ?>" type="submit">OK</button>
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