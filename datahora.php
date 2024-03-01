<?php 

//date_default_timezone_set("America/Sao_Paulo");


function string_data_formato_brasileiro($strdate){    
    date_default_timezone_set($_SESSION['timezone']);
    $segundos = strtotime($strdate);
    return date('d/m/Y H:i:s', strtotime($strdate));    
}
