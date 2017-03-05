<?php
$errfile = date("YmdHis",time())."error.txt";
$logfile = date("YmdHis",time())."log.txt";
$error_log = fopen("./logs/".$errfile,"a");
$hand_log=fopen("./logs/".$logfile,"a");