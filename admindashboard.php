<?php 

$usuarios = get_usuarios_parametros();
$quantidade_total = 0;
$quantidades = array();
foreach($usuarios as $usuario){   
    $mailbox = "{" . $usuario['imap_host'] . ":" . $usuario['input_door'] . "/imap/ssl/novalidate-cert". "}";  
    $mailbox_instance = imap_open($mailbox, $usuario['usuario'], $usuario['senha']);
    $folders = imap_list($mailbox_instance, $mailbox, "*");
    $folders = str_replace($mailbox, "", $folders);
    foreach($folders as $folder){
        if(!isset($quantidades[$folder])){
            $quantidades[$folder] = 0;
        }
        imap_reopen($mailbox_instance, $mailbox . $folder);
        $quantidade = imap_num_msg($mailbox_instance); 
        $quantidades[$folder] += $quantidade; 
        $quantidade_total += $quantidade;
    }
}
   
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages':['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawChart);

    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Diretório');
        data.addColumn('number', 'Quantidade');
        data.addRows([<?php
            foreach($quantidades as $folder => $quantidade){
                echo "['$folder', $quantidade],";
            }
        ?>]);
  
        // Set chart options
        var options = {'title':'<?= $quantidade_total ?> emails no total'};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>
<style>
    body {
        height: 99vh
    }
</style>
<div id="chart_div" style="height: 100%;"></div>