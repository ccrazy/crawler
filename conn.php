<?php
$servername = "127.0.0.1:3306";
$username = "root";
$password = "ccnu";
 
// 创建连接
$conn = mysql_connect($servername, $username, $password);
 
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}else{
    mysql_select_db("crawler",$conn);	
}
mysql_query("SET NAMES UTF8");

?>