<?php
$page_size = intval($_SESSION['page_size']);
$current_page = intval($_SESSION['page']);
$last_page = intval($count / $page_size);
$last_page = $last_page < ($count / $page_size) ? $last_page + 1 : $last_page;
$previous_middle_page = intval($current_page / 2);
$previous_middle_page = $previous_middle_page < ($current_page / 2) ? $previous_middle_page + 1 : $previous_middle_page;
$next_middle_page = intval((($last_page - $current_page) / 2) + $current_page);
$next_middle_page = $next_middle_page < ((($last_page - $current_page) / 2) + $current_page) ? $next_middle_page + 1 : $next_middle_page;
$first_page_hidden = $current_page < 3;
$previous_middle_page_hidden = $current_page < 5;
$previous_page_hidden = $current_page == 1;
$next_page_hidden = $current_page == $last_page;
$next_middle_page_hidden = ($last_page - $current_page) < 5;
$last_page_hidden = ($last_page - $current_page) < 2;

?>
<form method="POST" style="float:left"> 
    Tamanho página:
    <select style="height: 32px" name="page_size">    
        <option value="2" <?= $page_size == 2 ? 'selected' : '' ?>>2</option>    
        <option value="10" <?= $page_size == 10 ? 'selected' : '' ?>>10</option>
        <option value="25" <?= $page_size == 25 ? 'selected' : '' ?>>25</option>
        <option value="100" <?= $page_size == 100 ? 'selected' : '' ?>>100</option>
    </select>
    <button class="btn btn-default" type="submit">OK</button>
</form>
<form method="POST" style="float:left; margin-left: 15px"> 
    Mover Para:
    <select style="height: 32px" name="movement_folder">;     
        <?php 
        foreach($mail_load->folders as $folder){             
            if($folder->name != $_SESSION['folder']){
        ?>
            <option value="<?= $folder->name ?>"><?= str_replace('INBOX.', '', $folder->name) ?></option>             
        <?php 
            }
        }
        ?> 
    </select>
    <button class="btn btn-default" id="ids_to_move" name="ids_to_move" type="submit">OK</button>
</form>
<form method="POST" style="float:right">       
    Página:
    <button <?= $first_page_hidden ? 'style="display:none"' : '' ?> class="btn btn-default" type="submit" name="page" value="1">1</button>
    <button <?= $previous_middle_page_hidden ? 'style="display:none"' : '' ?> class="btn btn-default" type="submit" name="page" value="<?= $previous_middle_page ?>"><?= $previous_middle_page ?></button>
    <button <?= $previous_page_hidden ? 'style="display:none"' : '' ?> class="btn btn-default" type="submit" name="page" value="<?= $current_page - 1 ?>"><?= $current_page - 1 ?></button>
    <button disabled class="btn btn-default" type="submit" name="page" value="<?= $current_page ?>"><?= $current_page ?></button>
    <button <?= $next_page_hidden ? 'style="display:none"' : '' ?> class="btn btn-default" type="submit" name="page" value="<?= $current_page + 1?>"><?= $current_page + 1 ?></button>
    <button <?= $next_middle_page_hidden ? 'style="display:none"' : '' ?> class="btn btn-default" type="submit" name="page" value="<?= $next_middle_page ?>"><?= $next_middle_page ?></button>
    <button <?= $last_page_hidden ? 'style="display:none"' : '' ?> class="btn btn-default" type="submit" name="page" value="<?= $last_page ?>"><?= $last_page ?></button>            
</form>
<br>
<br>
<style>
.hbtn:hover{ 
    text-decoration: underline;
}
.hbtn{
    border: none; 
    padding: 0; 
    background: none; 
    cursor: pointer; 
    color: #0000EE;
}
</style>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th style="width: fit-content"><input onchange="select_all(this.checked)" type="checkbox"/></th>
                <th style="width: fit-content">De</th>
                <th style="width: 100%">Assunto</th>
                <th style="min-width: 127px">Data</th>                  
            </tr>
        </thead>
        <tbody>                
            <?php 
            require_once "datahora.php";
            for($i = sizeof($mail_load->mail_list) - 1; $i >= 0; $i--){                 
                $mail = $mail_load->mail_list[$i];                
            ?>                        
            <tr style="<?= $mail->unseen ? "font-weight: bold;" : "" ?>">
                <td><input onchange="setIds()" id="<?= $mail->id ?>" class="maillist" type="checkbox"/></td>
                <td><?= $mail->from ?></td>
                <td><form method="POST"><button class="hbtn" type="submit" name="id" value="<?= $mail->id ?>"><?= $mail->subject ?></button></form></td>
                <td><?= substr(string_data_formato_brasileiro($mail->date), 0, 16) ?></td>                
            </tr> </a>                          
            <?php } ?>               
        </tbody>
    </table>
</div>
<script>
function select_all(select_all){
    maillist = document.getElementsByClassName("maillist");
    for(i = 0; i < maillist.length; i = i + 1){
        an_mail = maillist[i];
        an_mail.checked = select_all;        
    } 
    setIds();
}
function setIds(){
    maillist = document.getElementsByClassName("maillist");
    ids = '';
    for(i = 0; i < maillist.length; i = i + 1){
        an_mail = maillist[i];
        if(an_mail.checked){    
            if(ids.length !== 0){
                ids += ',';
            }               
            ids += an_mail.id;            
        }
    }   
    document.getElementById("ids_to_move").value = ids;
}
</script>