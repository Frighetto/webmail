<?php   

if(isset($_SESSION['search'])){         
    $search_criteria = 'SUBJECT "' . $_SESSION['search'] . '"';
    $mail_index_list = imap_search($mailbox_instance, $search_criteria);
    if($mail_index_list == false){  
        $mail_index_list = array();
    }   

    $search_criteria = 'FROM "' . $_SESSION['search'] . '"';
    $mail_by_subject_index_list = imap_search($mailbox_instance, $search_criteria);
    if($mail_by_subject_index_list != false){            
        for ($i = 0; $i < sizeof($mail_by_subject_index_list); $i = $i + 1) {
            $already_exists = false;
            for ($j = 0; $j < sizeof($mail_index_list); $j = $j + 1) {
                if($mail_by_subject_index_list[$i] == $mail_index_list[$j]){
                    $already_exists = true;
                }        
            }
            if(!$already_exists){
                $mail_index_list[sizeof($mail_index_list)] = $mail_by_subject_index_list[$i];
            }
        }
    }    

    $search_criteria = 'BODY "' . $_SESSION['search'] . '"';
    $mail_by_body_index_list = imap_search($mailbox_instance, $search_criteria);
    if($mail_by_body_index_list != false){            
        for ($i = 0; $i < sizeof($mail_by_body_index_list); $i = $i + 1) {
            $already_exists = false;
            for ($j = 0; $j < sizeof($mail_index_list); $j = $j + 1) {
                if($mail_by_body_index_list[$i] == $mail_index_list[$j]){
                    $already_exists = true;
                }        
            }
            if(!$already_exists){
                $mail_index_list[sizeof($mail_index_list)] = $mail_by_body_index_list[$i];
            }
        }
    }    

} else {
    $mail_index_list = imap_search($mailbox_instance,  'ALL');
}

$list_size = $mail_index_list == false ? 0 : sizeof($mail_index_list);

$sublist_begin = (($_SESSION['page'] - 1) * $_SESSION['page_size']) + 1;
$sublist_end = $sublist_begin  + $_SESSION['page_size'];
$sublist_end = $sublist_end <= $list_size ? $sublist_end : $list_size + 1;

$mail_info_list = array(); 

require_once 'datahora.php';
require_once 'parsemail.php';
for ($i = $sublist_end - 1; $sublist_begin <= $i; $i = $i - 1) {         
    $mail_list_last_index = sizeof($mail_info_list);
    $mail_index = $mail_index_list[$list_size - $i];
    
    $header = imap_headerinfo($mailbox_instance, $mail_index);
    $header_object = parse_header($header);   
    $header_object->id = $mail_index;
    $mail_info_list[$mail_list_last_index] = $header_object;
}

$page_size = intval($_SESSION['page_size']);
$current_page = intval($_SESSION['page']);
$last_page = intval($list_size / $page_size);
$last_page = $last_page <= ($list_size / $page_size) ? $last_page + 1 : $last_page;
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
<div>
    <form method="POST" style="float:left"> 
        Tamanho página:
        <select onchange="this.form.submit()" style="height: 32px" name="page_size">                
            <option value="10" <?= $page_size == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $page_size == 25 ? 'selected' : '' ?>>25</option>
            <option value="100" <?= $page_size == 100 ? 'selected' : '' ?>>100</option>
        </select>        
    </form>
    <form method="POST" style="float:left; margin-left: 15px"> 
        Mover Para:
        <select onchange="this.form.submit()" style="height: 32px" name="movement_folder">    
            <?php foreach($folders as $folder){  ?>
                <option <?= $folder == $_SESSION['folder'] ? "selected" : "" ?> value="<?= $folder ?>"><?= $folder ?></option>             
            <?php  } ?> 
        </select>
        <input id="ids_to_move" name="ids_to_move" hidden />     
    </form>        
    <form method="POST" style="float:left; margin-left: 15px"> 
        Marcar Como:
        <select onchange="this.form.submit()" style="height: 32px" name="flag">            
            <option value="seen">Lido</option>
            <option value="unseen">Não Lido</option> 
            <option value="flag">&#9873;</option>
            <option value="unflag">&#9872;</option>
            <option value="seen" selected hidden>Lido</option> 
        </select>    
        <input id="ids_to_flag" name="ids_to_flag" hidden /> 
    </form>  
    <form method="POST" style="float:left; margin-left: 15px">                 
        <button class="btn btn-danger" id="ids_to_delete" name="ids_to_delete" type="submit">Excluir</button>
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
</div>

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

<table class="table">
    <thead>
        <tr>
            <th style="width: fit-content"><input onchange="select_all(this.checked)" type="checkbox"/></th>
            <th style="width: fit-content">De</th>
            <th style="width: 100%">Assunto</th>
            <th style="width: fit-content">&#9873;</th>
            <th style="min-width: 127px">Data</th>                  
        </tr>
    </thead>
    <tbody>                
        <?php 
        require_once "datahora.php";
        for($i = sizeof($mail_info_list) - 1; $i >= 0; $i--){                 
            $mail = $mail_info_list[$i];                
        ?>                        
        <tr style="<?= $mail->unseen ? "font-weight: bold;" : "" ?>">
            <td><input onchange="setIds()" id="<?= $mail->id ?>" class="maillist" type="checkbox"/></td>
            <td><?= $mail->from ?></td>
            <td><form method="POST"><button class="hbtn" type="submit" name="id" value="<?= $mail->id ?>"><?= $mail->subject ?></button></form></td>
            <td><?= $mail->flagged ? '&#9873;' : '&#9872;' ?></td>
            <td><?= $mail->date ?></td> 
        </tr> </a>                          
        <?php } ?>               
    </tbody>
</table>

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
    document.getElementById("ids_to_flag").value = ids;
    document.getElementById("ids_to_delete").value = ids;
}
</script>