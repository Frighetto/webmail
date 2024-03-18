<?php 

function string_data_formato_brasileiro($strdate){        
    $segundos = strtotime($strdate);
    return date('d/m/Y H:i:s', strtotime($strdate));    
}

?>