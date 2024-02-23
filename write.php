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

    function addimage() {
        var endereco = document.getElementById('enderecoimage').value;
        if(endereco != ""){
            var image = document.createElement("img");            
                image.src = endereco;   
                var largura = document.getElementById('largura-imagem').value;
                var altura = document.getElementById('altura-imagem').value;            
                if(largura != ""){
                    image.width = largura; 
                }      
                if(altura != ""){
                    image.width = altura; 
                }             
                document.getElementById('htmlcode').value += image.outerHTML;
                document.getElementById('closeimage').click();
                updatehtmlresult();
        } else {             
            
            var file = document.getElementById('fileimage').files[0];        
            var reader = new FileReader();        
            reader.onload = function(e) {

                var http_request = new XMLHttpRequest();			
                http_request.open("POST", 'upload.php', true);	    
                http_request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');		
                http_request.onload = function(e) {

                    var image = document.createElement("img");            
                    image.src = http_request.response;
                    var largura = document.getElementById('largura-imagem').value;
                    var altura = document.getElementById('altura-imagem').value;            
                    if(largura != ""){
                        image.width = largura; 
                    }      
                    if(altura != ""){
                        image.width = altura; 
                    }             
                    document.getElementById('htmlcode').value += image.outerHTML;
                    document.getElementById('closeimage').click();
                    updatehtmlresult();

                }     

                var params = 'name=' + encodeURIComponent(file['name']);    
                params = params + '&size=' + encodeURIComponent(file['size']); 
                params = params + '&type=' + encodeURIComponent(file['type']); 
                params = params + '&content=' + encodeURIComponent(e.target.result); 
                http_request.send(params); 
                                
            }        
            reader.readAsDataURL(file);  
            
        }      
    }

    function resetaddimage(){
        document.getElementById('enderecoimage').value = null;
        document.getElementById('fileimage').value = null;
        document.getElementById('largura-imagem').value = null;
        document.getElementById('altura-imagem').value = null;
    }
   

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
require_once 'parsemail.php';

if(isset($_POST['reply'])){
    $_POST['id'] = $_POST['reply'];
    $header = imap_headerinfo($mailbox_instance, $_POST['reply']);
    $header_object = parse_header($header);
    $to = $header_object->from;
    $subject = $header_object->subject;
    if(substr($subject, 0, strlen('Re: ')) != 'Re: '){
        $subject = 'Re: ' . $subject;
    }
} else if(isset($_POST['redirect'])){
    $_POST['id'] = $_POST['redirect'];
    $header = imap_headerinfo($mailbox_instance, $_POST['redirect']);
    $header_object = parse_header($header);    
    $subject = $header_object->subject;   
    if(substr($subject, 0, strlen('Fwd: ')) != 'Fwd: '){
        $subject = 'Fwd: ' . $subject;
    }
} else if(isset($_POST['edit'])){
    $_POST['id'] = $_POST['edit'];
    $header = imap_headerinfo($mailbox_instance, $_POST['edit']);
    $header_object = parse_header($header);
    $to = $header_object->from;
    $subject = $header_object->subject;
}

?>
<style>    
    .my-fluid-container {
        padding-left: 15px;
        padding-right: 15px;
        margin-left: auto;
        margin-right: auto;
    }

    .modal-backdrop {
        position: relative;
        z-index: -1;
    }
</style>
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Inserir Imagem</h4>
            </div>
            <div class="modal-body">
                <input id="fileimage" class="form-control" type="file">
                <br>
                <input id="enderecoimage" class="form-control" type="text" placeholder="url">
                <br>
                <div>
                <input id="largura-imagem" style="width: 30%; float: left; margin-right: 15px" placeholder="Largura" class="form-control">
                <input id="altura-imagem" style="width: 30%" placeholder="Altura" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
            <button id="closeimage" type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            <button onclick="addimage()" type="button" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="card card-primary card-outline">        
        <form method="POST" enctype="multipart/form-data">
            <div style="margin-right: 1%; margin-bottom: 1%;">
                <button id="send" type="submit" name="send" value="<?= isset($_POST['reply']) ? $_POST['id'] : "default" ?>" class="btn btn-primary">
                    <i class="far fa-envelope"></i> Enviar
                </button>
                <button id="draft" type="submit" name="draft" value="default" class="btn btn-default">
                    <i class="fas fa-pencil-alt"></i> Rascunho
                </button>
            </div>
            <input id="selectedwriter" value="mail" name="selectedwriter" hidden>
            <div class="card-body">            
                <div class="form-group">
                    <input class="form-control" placeholder="Destinatário" value="<?= isset($to) ? $to : '' ?>" name="to" required>
                </div>
                <div class="form-group">
                    <input class="form-control" maxlength="100" placeholder="Assunto" value="<?= isset($subject) ? $subject : '' ?>" name="subject">
                </div>     
                <div style="">    
                    <button id="btnshowtext" style="width: 49%; margin-left: 0px;" onclick="showtext()" class="btn btn-primary btn-lg" type="button">Texto</button>        
                    <button id="btnshowhtml" style="width: 49%; margin-right: 0px;" onclick="showhtml()" class="btn btn-default btn-lg" type="button">HTML</button> 
                </div>                                                   
                <div class="form-group" id="texteditor">                    
                    <div class="note-editor note-frame card">                                       
                        <style>
                            .ck-editor__editable[role="textbox"] {                               
                                min-height: 360px;
                                max-height: 360px;
                            }
                        </style>
                        <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
                        <textarea name="mail" id="editor">
                            <?php  
                            if(isset($_POST['edit']) || isset($subject)){
                                if(isset($_POST['edit'])){
                                    $load_body_only = true;
                                }
                                $cancel_attachments = true;
                                if(!isset($_POST['edit'])){
                            ?>
                                <br>
                                <br>
                                ---------------------------------------------------------------------
                            <?php
                                }
                                require_once 'mailbody.php';
                            }                             
                            ?>
                        </textarea>                    
                        <script>          
                            var myEditor;   
                            ClassicEditor
                                .create( document.querySelector( '#editor' ) )
                                .then( editor => {       
                                    editor.editorConfig = function( config ){
                                        config.allowedContent = true;  
                                    }                     
                                    myEditor = editor;                                    
                                } )
                                .catch( err => {
                                    console.error( err.stack );
                                } );  
                                
                                function showhtml(){
                                    document.getElementById('texteditor').setAttribute('hidden', true);
                                    document.getElementById('htmleditor').removeAttribute('hidden');
                                    document.getElementById('btnshowhtml').setAttribute('class', 'btn btn-primary btn-lg');
                                    document.getElementById('btnshowtext').setAttribute('class', 'btn btn-default btn-lg');

                                    document.getElementById('htmlcode').value = myEditor.getData(); 
                                     
                                    updatehtmlresult();
                                    document.getElementById('selectedwriter').value = "mailhtml";
                                    
                                }

                                function showtext(){
                                    document.getElementById('htmleditor').setAttribute('hidden', true);
                                    document.getElementById('texteditor').removeAttribute('hidden');
                                    document.getElementById('btnshowhtml').setAttribute('class', 'btn btn-default btn-lg');
                                    document.getElementById('btnshowtext').setAttribute('class', 'btn btn-primary btn-lg');

                                    myEditor.setData(document.getElementById('htmlcode').value);
                                    document.getElementById('selectedwriter').value = "mail";
                                } 

                                function updatehtmlresult(){
                                    document.getElementById('htmlresult').innerHTML = document.getElementById('htmlcode').value;
                                }
                        </script>                      
                    </div>
                </div> 
                <div id="htmleditor" hidden>
                    <button onclick="resetaddimage()" data-toggle="modal" href="#imageModal" class="btn btn-default" type="button">
                        Inserir Imagem
                    </button>     
                    <div>           
                        <textarea name="mailhtml" oninput="updatehtmlresult()" id="htmlcode" class="form-group" style="float: left; width: 49%; min-height: 360px; max-height: 360px;" contenteditable>
                        </textarea> 
                        <div id="htmlresult" class="form-group" style="border: dotted; float:left; width: 49%; min-height: 360px; max-height: 360px; overflow: auto">
                        </div>
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