<?php
$selected_message = $imap->readMessage($_POST['uid']);
?>
<div class="table-responsive mailbox-messages">               
    <div class="card-body">
        <span><b>De: <?= $selected_message['from'] ?></b></span>
        <br>
        <span><b>Para: 
        <?php foreach ($selected_message['to'] as $receiver) {
            echo $receiver . '  ';
        } ?></b></span>                                        
        <br>
        <span><b><?= $selected_message['date'] ?></b></span>
        <br>
        <h3><?= $selected_message['subject']?></h3>
        <div style="border: solid">
        <?= $selected_message['body'] ?>   
        </div> 
        <?php 
        
        for($i = 0; isset($selected_message['attachments']) && $i < sizeof($selected_message['attachments']); $i++){ 
        $attachment = $selected_message['attachments'][$i];                                        
        ?>
        
        <form class="form-group" action="?uid=<?= $selected_message['uid'] ?>&attachment=<?=$i?>" method="POST" enctype="multipart/form-data"> 
            <input name="file_name" value="<?= $attachment['name']; ?>" hidden>
            <button type="submit" name="email_number" value="<?= $selected_message['uid'] ?>" class="btn btn-default">
                <i class="fas fa-paperclip"></i> <?= $attachment['name'] ?>
            </button>                                           
        </form> 
        <?php } ?>                  
    </div>
</div>
<form class="form-group" method="POST">             
    <button type="submit" name="reply" value="<?= $selected_message['uid'] ?>" class="btn btn-default">
        Responder
    </button> 
    <button type="submit" name="redirect" value="<?= $selected_message['uid'] ?>" class="btn btn-default">
        Encaminhar
    </button>                                          
</form> 