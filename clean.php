<?php
include "conn.php";
$sql = "select content from alldata where id =2";
$result=mysql_query($sql);
$result = mysql_fetch_array($result);
file_put_contents("clean.txt",var_export($result,true));
// var_dump(json_decode($result[0]));