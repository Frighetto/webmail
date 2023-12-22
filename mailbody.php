<div class="mailbox-messages">               
    <div class="card-body">
        <?php if(!isset($load_body_only)){ ?>
        <span><b>De: <?= $header_object->from ?></b></span><br>
        <span><b>Para: <?= $header_object->to ?></b></span><br>
        <span><b>Data: <?= $header_object->date ?></b></span>        
        <h3><?= $header_object->subject ?></h3>
        <div>
        <?php 
        }           
        $mailStruct = imap_fetchstructure($mailbox_instance, $_POST['id']);           
        if ($mailStruct->type == 0) {           
            $body_part_number = 1;
        } else if ($mailStruct->type == 1) {
            $parts = $mailStruct->parts;
            for($index = 0; $index < sizeof($parts); $index = $index + 1){
                if($parts[$index]->type == 0){
                    $body_part_number = ($index + 1);
                } else if($parts[$index]->type == 1){
                    $subparts = $parts[$index]->parts;
                    for($sub_index = 0; $sub_index < sizeof($subparts); $sub_index = $sub_index + 1){
                        if($subparts[$sub_index]->type == 0){
                            $body_part_number = floatval(($index + 1) . '.' . ($sub_index + 1));
                        }
                    }
                }
            }
        }        
        /*
        O body_part_number da função imap_fetchbody é um número um pouco irregular comparado com a estrutura de dados
        que a função imap_fetchstructure fornece, pois o índice 0 é sobre o cabeçalho. Além disso, quando o conteúdo é misto,
        ou seja, html + anexos, a estrutura de dados possui arrays dentro de arrays, por isso usa-se body_part_number com valor
        tipo float, como 1.1, onde a parte não inteira, por exemplo 0.1, representa o array dentro do array;
        */
        $body = imap_fetchbody($mailbox_instance, $_POST['id'], $body_part_number);
        if ($mailStruct->encoding == 3) {
            $body = imap_base64($body);
        } else if ($mailStruct->encoding == 4) {
            $body = imap_qprint($body);
        }            
        if (mb_detect_encoding($body, "UTF-8, ISO-8859-1, GBK")!="UTF-8") {
            $body = utf8_encode($body);
        }
        echo iconv('UTF-8', 'UTF-8//IGNORE', $body);     
        if ($mailStruct->type == 1 && !isset($cancel_attachments)) {
            $parts = $mailStruct->parts;
            for($index = 0; $index < sizeof($parts); $index = $index + 1){
                $part = $parts[$index];
                if($part->ifdisposition == 1 && $part->disposition == "attachment"){
                    $filename = $part->dparameters[0]->value;
                    $attachment_id = $index + 1;                            
        ?>        
        <a href="download.php?id=<?= $_POST['id'] ?>&attachment=<?= $attachment_id ?>">
            <?= $filename ?>
        </a>        
        <br>
        <?php
                }
            }   
        }    
        ?>   
        </div>                   
    </div>
</div>