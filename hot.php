<?php
include "conn.php";
include "request.php";
$url="http://cruise.ctrip.com/Cruise-Booking-Online/CrystalProject/SearchResultV2/WeekSaleSearch";
$data = array(
    "selectLines[0]"=>1
);
$result=send_post($url,$data);
$arr= json_decode($result,true);
$hot = $arr['Data'];
if($hot){
    foreach ($hot as $key => $v) {
        $sql = "insert into hot(contentHot,name) values('".json_encode($v,JSON_UNESCAPED_UNICODE)."','".$v['Name']."')";
        $r = mysql_query($sql);
        var_dump($r);
    }
}
mysql_close($conn);