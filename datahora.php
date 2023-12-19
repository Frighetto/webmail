<?php 
//date_default_timezone_set('Europe/London');

//$date = date("D, d M Y H:i:s T");
//echo string_data_formato_brasileiro($date);

function string_data_formato_brasileiro($strdate){
    $segundos = strtotime($strdate);
    return segundos_data_formato_brasileiro($segundos);
}

function segundos_data_formato_brasileiro($segundos){
    date_default_timezone_set('America/Sao_Paulo');

    $quantidade_segundos_1_segundo = 1;
    $quantidade_segundos_1_minuto = $quantidade_segundos_1_segundo * 60;
    $quantidade_segundos_1_hora = $quantidade_segundos_1_minuto * 60;
    $quantidade_segundos_1_dia = $quantidade_segundos_1_hora * 24;
    $quantidade_dias_janeiro = 31;
    $quantidade_segundos_janeiro = $quantidade_segundos_1_dia * $quantidade_dias_janeiro;
    $quantidade_dias_fevereiro = 28;
    $quantidade_segundos_fevereiro = $quantidade_segundos_1_dia * $quantidade_dias_fevereiro;
    $quantidade_dias_fevereiro_bisexto = 29;
    $quantidade_segundos_fevereiro_bisexto = $quantidade_segundos_1_dia * $quantidade_dias_fevereiro_bisexto;
    $quantidade_dias_marco = 31;
    $quantidade_segundos_marco = $quantidade_segundos_1_dia * $quantidade_dias_marco;
    $quantidade_dias_abril = 30;
    $quantidade_segundos_abril = $quantidade_segundos_1_dia * $quantidade_dias_abril;
    $quantidade_dias_maio = 31;
    $quantidade_segundos_maio = $quantidade_segundos_1_dia * $quantidade_dias_maio;
    $quantidade_dias_junho = 30;
    $quantidade_segundos_junho = $quantidade_segundos_1_dia * $quantidade_dias_junho;
    $quantidade_dias_julho = 31;
    $quantidade_segundos_julho = $quantidade_segundos_1_dia * $quantidade_dias_julho;
    $quantidade_dias_agosto = 31;
    $quantidade_segundos_agosto = $quantidade_segundos_1_dia * $quantidade_dias_agosto;
    $quantidade_dias_setembro = 30;
    $quantidade_segundos_setembro = $quantidade_segundos_1_dia * $quantidade_dias_setembro;
    $quantidade_dias_outubro = 31;
    $quantidade_segundos_outubro = $quantidade_segundos_1_dia * $quantidade_dias_outubro;
    $quantidade_dias_novembro = 30;
    $quantidade_segundos_novembro = $quantidade_segundos_1_dia * $quantidade_dias_novembro;
    $quantidade_dias_dezembro = 31;
    $quantidade_segundos_dezembro = $quantidade_segundos_1_dia * $quantidade_dias_dezembro;

    $quantidade_dias_1_ano =
    $quantidade_dias_janeiro + 
    $quantidade_dias_fevereiro + 
    $quantidade_dias_marco + 
    $quantidade_dias_abril +
    $quantidade_dias_maio +
    $quantidade_dias_junho + 
    $quantidade_dias_julho +
    $quantidade_dias_agosto +
    $quantidade_dias_setembro + 
    $quantidade_dias_outubro +
    $quantidade_dias_novembro +
    $quantidade_dias_dezembro;

    $quantidade_segundos_1_ano = $quantidade_dias_1_ano * $quantidade_segundos_1_dia;
    $frequencia_ano_bisexto = 1 / 4;
    $quantidade_media_segundos_1_ano = $quantidade_segundos_1_dia * ($quantidade_dias_1_ano + $frequencia_ano_bisexto);

    $primeiro_ano = 1970;
    $primeiro_ano_bisexto = 1972;

    $segundos_antes_primeiro_ano_bisexto = $quantidade_segundos_1_ano * ($primeiro_ano_bisexto - $primeiro_ano);
    $segundos_depois_primeiro_ano_bisexto = $segundos - $segundos_antes_primeiro_ano_bisexto;
    $ano = $primeiro_ano + ($segundos_antes_primeiro_ano_bisexto / $quantidade_segundos_1_ano) + intval($segundos_depois_primeiro_ano_bisexto / $quantidade_media_segundos_1_ano);
    $ultimos_anos_nao_bisextos = ($ano - $primeiro_ano_bisexto) % 4;
    $anos_com_bisextos = $ano - $primeiro_ano_bisexto - $ultimos_anos_nao_bisextos;
    $ano_bisexto = $anos_com_bisextos == 0;
    $anos_restantes = ($primeiro_ano_bisexto - $primeiro_ano) + $ultimos_anos_nao_bisextos;
    $total_segundos_ano = intval($segundos - ($quantidade_media_segundos_1_ano * $anos_com_bisextos + $quantidade_segundos_1_ano * $anos_restantes));
    $segundos_ano = $total_segundos_ano;
    
    $mes = 1;
    if($segundos_ano - $quantidade_segundos_janeiro >= 0){
        $segundos_ano = $segundos_ano - $quantidade_segundos_janeiro;
        $mes = 2;
        if($segundos_ano - ($ano_bisexto ? $quantidade_segundos_fevereiro_bisexto : $quantidade_segundos_fevereiro) >= 0){
            $segundos_ano = $segundos_ano - ($ano_bisexto ? $quantidade_segundos_fevereiro_bisexto : $quantidade_segundos_fevereiro);
            $mes = 3;
            if($segundos_ano - $quantidade_segundos_marco >= 0){
                $segundos_ano = $segundos_ano - $quantidade_segundos_marco;
                $mes = 4;
                if($segundos_ano - $quantidade_segundos_abril >= 0){
                    $segundos_ano = $segundos_ano - $quantidade_segundos_abril;
                    $mes = 5;
                    if($segundos_ano - $quantidade_segundos_maio >= 0){
                        $segundos_ano = $segundos_ano - $quantidade_segundos_maio;
                        $mes = 6;
                        if($segundos_ano - $quantidade_segundos_junho >= 0){
                            $segundos_ano = $segundos_ano - $quantidade_segundos_junho;
                            $mes = 7;
                            if($segundos_ano - $quantidade_segundos_julho >= 0){
                                $segundos_ano = $segundos_ano - $quantidade_segundos_julho;
                                $mes = 8;
                                if($segundos_ano - $quantidade_segundos_agosto >= 0){
                                    $segundos_ano = $segundos_ano - $quantidade_segundos_agosto;
                                    $mes = 9;
                                    if($segundos_ano - $quantidade_segundos_setembro >= 0){
                                        $segundos_ano = $segundos_ano - $quantidade_segundos_setembro;
                                        $mes = 10;
                                        if($segundos_ano - $quantidade_segundos_outubro >= 0){
                                            $segundos_ano = $segundos_ano - $quantidade_segundos_outubro;
                                            $mes = 11;
                                            if($segundos_ano - $quantidade_segundos_novembro >= 0){
                                                $segundos_ano = $segundos_ano - $quantidade_segundos_novembro;
                                                $mes = 12;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $dia_do_mes = intval($segundos_ano / $quantidade_segundos_1_dia);

    $segundos_ano = $segundos_ano - ($dia_do_mes * $quantidade_segundos_1_dia);

    $hora = intval($segundos_ano / $quantidade_segundos_1_hora);

    $segundos_ano = $segundos_ano - $hora * $quantidade_segundos_1_hora;

    $minutos = intval($segundos_ano / $quantidade_segundos_1_minuto);

    $segundos = $segundos_ano - $minutos * $quantidade_segundos_1_minuto;
    $data = ($dia_do_mes < 10 ? "0" : "") . $dia_do_mes . "/" . ($mes < 10 ? "0" : "") . $mes . "/" . $ano;
    $hora = ($hora < 10 ? "0" : "") . $hora . ":" . ($minutos < 10 ? "0" : "") . $minutos . ":" . ($segundos < 10 ? "0" : "") . $segundos;
    $datahora = $data . " " . $hora;
    
    return $datahora;
}
?>