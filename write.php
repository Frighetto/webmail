<script>
    var anexos_extras = 0;
    sizes = [];
        
    function reset_anexo(_this, id){
        id = id.replace("attachment", "");
        sizes[id] = 0;
        _this.value = null;
    }

    function checksize(_this, id){
        id = id.replace("attachment", "");
        sizes[id] = _this.files[0].size;
        var total = 0;
        for(var i = 0; i < sizes.length; i = i + 1){
            total = total + sizes[i];
        }
        var total_MB = total / (1024 * 1024);
        if(total_MB > 40){
            _this.value = null;
            sizes[id] = 0;
            alert("Ultrapassou o limite de 40MB");
        }
    }

    function anexo_extra(){
      anexos_extras = anexos_extras + 1;           

      var div_anexo = document.createElement("div");
      div_anexo.setAttribute("class", "row mb-3");

      var label_anexo = document.createElement("label");
      label_anexo.setAttribute("for", "data");
      label_anexo.setAttribute("class", "col-sm-2 col-form-label");

      var icone_anexo = document.createElement("i");
      icone_anexo.setAttribute("class", "fas fa-paperclip");
      icone_anexo.innerHTML = "Anexo";

      label_anexo.appendChild(icone_anexo);      

      div_anexo.appendChild(label_anexo);
      
      var subdiv_anexo = document.createElement("div");
      subdiv_anexo.setAttribute("class", "col-sm-10");

      var input_anexo = document.createElement("input");
      input_anexo.setAttribute("id", "attachment"+anexos_extras);
      input_anexo.setAttribute("class", "form-control");
      input_anexo.setAttribute("onchange", "checksize(this, 'attachment"+anexos_extras+"')");
      input_anexo.setAttribute("onclick", "reset_anexo(this, 'attachment"+anexos_extras+"')");
      input_anexo.setAttribute("type", "file");
      input_anexo.setAttribute("name", "attachment"+anexos_extras);

      subdiv_anexo.appendChild(input_anexo);

      div_anexo.appendChild(subdiv_anexo);

      document.getElementById("anexos").appendChild(div_anexo);

    };

</script>
<div class="form-group" id="anexosextras" hidden>
  
    <div class="row mb-3">
        <label for="data" class="col-sm-2 col-form-label">
            <i class="fas fa-paperclip"></i> Anexo
        </label>
        <div class="col-sm-10">
            <input id="attachment" class="form-control" onchange="checksize(this, 'attachment')" onclick="reset_anexo(this, 'attachment')" type="file" name="attachment">
        </div>
    </div>   
                                            
</div>

<?php 
$selected_message = null;
if(isset($_POST['reply'])){
    $selected_message = $imap->readMessage($_POST['reply']);
    $to = $selected_message['from'];
    $subject = $selected_message['subject'];
    if(substr($subject, 0, strlen('Re: ')) != 'Re: '){
        $subject = 'Re: ' . $subject;
    }
} else if(isset($_POST['redirect'])){
    $selected_message = $imap->readMessage($_POST['redirect']);
    $subject = $selected_message['subject'];
    if(substr($subject, 0, strlen('Fwd: ')) != 'Fwd: '){
        $subject = 'Fwd: ' . $subject;
    }
}
if($selected_message != null){
    
}
?>
<div class="col-md-12">
    <div class="card card-primary card-outline">        
        <form method="POST" enctype="multipart/form-data">
            <div style="margin-right: 1%; margin-bottom: 1%;">
                <button id="send" type="submit" name="send" value="default" class="btn btn-primary"><i class="far fa-envelope"></i> Enviar</button>
                <button id="draft" type="submit" name="draft" value="default" class="btn btn-default"><i class="fas fa-pencil-alt"></i> Rascunho</button>
            </div>
            <div class="card-body">            
                <div class="form-group">
                    <input class="form-control" placeholder="Destinatário" value="<?= isset($to) ? $to : '' ?>" name="to" required>
                </div>
                <div class="form-group">
                    <input class="form-control" maxlength="100" placeholder="Assunto" value="<?= isset($subject) ? $subject : '' ?>" name="subject">
                </div>              
                <div class="form-group">                    
                    <div class="note-editor note-frame card">                                       
                        <style>
                            .ck-editor__editable[role="textbox"] {
                                /* editing area */
                                min-height: 360px;
                                max-height: 360px;
                            }
                        </style>
                        <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
                        <textarea name="message_body" id="editor"><?php                             
                            if($selected_message != null){
                            ?>
                            <br>
                            <br>
                            ------------------------------------------------------------------
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
                                </div>
                            </div>
                            <?php
                            }
                        ?></textarea>                    
                        <script>                        
                            ClassicEditor
                                .create( document.querySelector( '#editor' ) )                               
                                .catch( error => {
                                    console.error( error );
                                } );                           
                        </script>                      
                    </div>
                </div>
                                
                <div class="form-group" id="anexos">
                    <div class="row mb-3">
                        <label for="data" class="col-sm-2 col-form-label">
                            <i class="fas fa-paperclip"></i> Anexo
                        </label>
                        <div class="col-sm-10">
                            <input id="attachment" class="form-control" onchange="checksize(this, 'attachment0')" onclick="reset_anexo(this, 'attachment0')" type="file" name="attachment">
                        </div>
                    </div>                     
                </div>   
                <button onclick="anexo_extra()" type="button" class="btn btn-default"><i class="fas fa-paperclip"></i> + Anexo</button>
                                     
            </div>
                        
        </form>
    </div>

</div>