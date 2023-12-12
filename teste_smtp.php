<?php

$output = shell_exec('javac -cp ".:activation-1.1.jar:javax.mail-1.6.0.jar" Email.java');
echo "<pre>$output</pre>";

$output = shell_exec('java -cp ".:activation-1.1.jar:javax.mail-1.6.0.jar" Email "mail.helpdesk.tec.br" "465" "teste@helpdesk.tec.br" "Senha@135" "teste@helpdesk.tec.br" "debugging webmail linha comando spike" "Spike_sends_a_letter_to_Princess_Celestia_S5E18.webp" "test/teste@helpdesk.tec.br/Spike_sends_a_letter_to_Princess_Celestia_S5E18.webp" "Princess_Celestia_receives_a_letter_S5E18.webp" "test/teste@helpdesk.tec.br/Princess_Celestia_receives_a_letter_S5E18.webp"');
echo "<pre>$output</pre>";

?>