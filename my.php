<?php 


$command = $_SERVER['DOCUMENT_ROOT'] . '/vl/plugins/tts/tts.sh  eyyaa';
echo "Command 1 :" . $command;
exec($command);

$command2 = $_SERVER['DOCUMENT_ROOT'] . '/vl/plugins/tts/try.sh  eyyaa';
echo " Command 2 :" . $command2;
exec($command2);

?>

