<div class="mailbox-messages">               
    <div class="card-body">
        <?php if(!isset($load_body_only)){ ?>
        <span><b>De: <?= $header_object->from ?></b></span><br>
        <span><b>Para: <?= $header_object->to ?></b></span><br>
        <?php if(isset($header_object->cc)){ ?>
        <span><b>Cc: <?= $header_object->cc ?></b></span><br>
        <?php } ?>
        <span><b>Data: <?= $header_object->date ?></b></span>        
        <h3><?= $header_object->subject ?></h3>                     
        <?php 
        }
        $msgn = imap_msgno($mailbox_instance, $_POST['id']);
        $body = getBody($msgn)['body'];
        
        $structure = imap_fetchstructure($mailbox_instance, $msgn);
        $attachments = getAttachments($msgn, $structure);

        echo embedImages($msgn, $attachments, $body);

        foreach($attachments as $attachment){ 
            $attachment_name = "";
            foreach (imap_mime_header_decode($attachment['name']) as $obj) {
                $attachment_name .= $obj->text;
            }           
        ?>   
        <br>     
        <a href="<?= $download_uri ?>?id=<?= $_POST['id'] ?>&partNum=<?= $attachment['partNum'] ?>&folder=<?= $folder ?>&loginid=<?= urlencode($_POST['loginid']) ?>">
            <?= $attachment_name ?>
        </a>                
        <?php
        }
              
        /**
         * returns body of the email. First search for html version of the email, then the plain part.
         *
         * @param int $uid message id
         * @return string email body
         */
        function getBody($id) {        
            $body = get_part($id, "TEXT/HTML");
            $html = true;
            // if HTML body is empty, try getting text body
            if ($body == "") {
                $body = get_part($id, "TEXT/PLAIN");
                $html = false;
            }
            $body = convertToUtf8($body);
            return array( 'body' => $body, 'html' => $html);
        }


        /**
         * convert to utf8 if necessary.
         *
         * @param string $str utf8 encoded string
         * @return bool
         */
        function convertToUtf8($str) {
            if (mb_detect_encoding($str, "UTF-8, ISO-8859-1, GBK")!="UTF-8") {
                $str = utf8_encode($str);
            }
            $str = iconv('UTF-8', 'UTF-8//IGNORE', $str);
            return $str;
        }


        /**
         * returns a part with a given mimetype
         * taken from http://www.sitepoint.com/exploring-phps-imap-library-2/
         *
         * @param false|resource $imap imap stream
         * @param int $uid id
         * @param string $mimetype
         * @param bool|false $structure
         * @param bool|false $partNumber
         * @return bool|string email body
         */
        function get_part($id, $mimetype, $structure = false, $partNumber = false) {            
            global $mailbox_instance;
            if (!$structure) {
                $structure = imap_fetchstructure($mailbox_instance, $id);
            }
            if ($structure) {
                if ($mimetype == get_mime_type($structure)) {
                    if (!$partNumber) {
                        $partNumber = 1;
                    }
                    $text = imap_fetchbody($mailbox_instance, $id, $partNumber);
                    switch ($structure->encoding) {
                        case 3: return imap_base64($text);
                        case 4: return imap_qprint($text);
                        default: return $text;
                    }
                }

                // multipart 
                if ($structure->type == 1) {
                    foreach ($structure->parts as $index => $subStruct) {
                        $prefix = "";
                        if ($partNumber) {
                            $prefix = $partNumber . ".";
                        }
                        $data = get_part($id, $mimetype, $subStruct, $prefix . ($index + 1));
                        if ($data) {
                            return $data;
                        }
                    }
                }
            }
            return false;
        }


        /**
         * extract mimetype
         * taken from http://www.sitepoint.com/exploring-phps-imap-library-2/
         *
         * @param object $structure
         * @return string mimetype
         */
        function get_mime_type($structure) {
            $primaryMimetype = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

            if ($structure->subtype) {
                return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
            }
            return "TEXT/PLAIN";
        }           
        

        /**
         * get attachments of given email
         * taken from http://www.sitepoint.com/exploring-phps-imap-library-2/
         *
         * @param false|resource $imap stream
         * @param int $mailNum email
         * @param object $part
         * @param string $partNum
         * @return array of attachments
         */
        function getAttachments($mailNum, $part, $partNum = '') {
            global $mailbox_instance;
            $attachments = array();

            if (isset($part->parts)) {
                foreach ($part->parts as $key => $subpart) {
                    if ($partNum != "") {
                        $newPartNum = $partNum . "." . ($key + 1);
                    } else {
                        $newPartNum = ($key+1);
                    }
                    $result = getAttachments($mailNum, $subpart, $newPartNum);
                    if (count($result) != 0) {
                        if (isset($result[0]['name'])) {
                            foreach($result as $inline) {
                                array_push($attachments, $inline);
                            }
                        } else {
                            array_push($attachments, $result);
                        }
                    }
                }
            } else if (isset($part->disposition)) {
                if (in_array(strtolower($part->disposition), array('attachment', 'inline'))) {
                    $partStruct = imap_bodystruct($mailbox_instance, $mailNum, $partNum);
                    $reference = isset($partStruct->id) ? $partStruct->id : "";                    

                      $filename = "unknown";
                      foreach($part->dparameters as $dparameter){
                        if(strtoupper($dparameter->attribute) == "FILENAME"){
                          $filename = $dparameter->value;
                        }
                      }
                    
                      if($filename == "unknown"){
                          foreach($part->parameters as $parameter){
                              if(strtoupper($parameter->attribute) == "FILENAME"){
                                $filename = $parameter->value;
                              }
                          }
                      }
                    $attachmentDetails = array(
                        "name"          => $filename,
                        "partNum"       => $partNum,
                        "enc"           => $partStruct->encoding,
                        "size"          => $part->bytes,
                        "reference"     => $reference,
                        "disposition"   => $part->disposition,
                        "type"          => $part->subtype
                    );
                    return $attachmentDetails;
                }
            } else if (isset($part->subtype) && in_array($part->subtype, array('JPEG', 'GIF', 'PNG'))) {

                $partStruct = imap_bodystruct($mailbox_instance, $mailNum, $partNum);
                $reference = isset($partStruct->id) ? $partStruct->id : "";
                $disposition = empty($reference) ? 'attachment' : 'inline';     
                
                  $filename = "unknown";
                  foreach($part->dparameters as $dparameter){
                    if(strtoupper($dparameter->attribute) == "FILENAME"){
                      $filename = $dparameter->value;
                    }
                  }
                
                  if($filename == "unknown"){
                      foreach($part->parameters as $parameter){
                          if(strtoupper($parameter->attribute) == "FILENAME"){
                            $filename = $parameter->value;
                          }
                      }
                  }

                $attachmentDetails = array(
                    "name"          => $filename,
                    "partNum"       => $partNum,
                    "enc"           => $partStruct->encoding,
                    "size"          => $part->bytes,
                    "reference"     => $reference,
                    "disposition"   => $disposition,
                    "type"          => $part->subtype
                );
                return $attachmentDetails;
            }
            return $attachments;
        }

        
        /**
         * HTML embed inline images
         *
         * @param array $email
         * @return string
         */
        function embedImages($msgn, $attachments, $body) {            
            foreach ($attachments as $attachment) {
                if ($attachment['disposition'] == 'inline' && !empty($attachment['reference'])){
                    $file = getAttachment($msgn, $attachment['partNum']);

                    $reference = str_replace(array("<", ">"), "", $attachment['reference']);
                    $img_embed = "data:image/" . strtolower($file['type']) . ";base64," . base64_encode($file['content']);

                    $body = str_replace("cid:" . $reference, $img_embed, $body);
                }
            }
            return $body;
        }

        function getAttachment($id, $partNum){            
            global $mailbox_instance;
            $partStruct = imap_bodystruct($mailbox_instance, $id, $partNum);              
            $content = imap_fetchbody($mailbox_instance, $id, $partNum);

            $encoding = $partStruct->encoding;
            if($encoding == 1){
                $content = imap_8bit($content);
            }
            if($encoding == 2){
                $content = imap_binary($content);
            }
            if($encoding == 3){
                $content = imap_base64($content);
            }
            if($encoding == 4){
                $content = quoted_printable_decode($content);
            }

            $file = array();
            $file['type'] = $partStruct->subtype;
            $file['content'] = $content;

            return $file;
        }
        ?>   
        
    </div>
</div>
