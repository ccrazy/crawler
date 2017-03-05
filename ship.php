<?php
include "time.php";
include "request.php";
include "conn.php";
include "file.php";

$url = "http://cruise.ctrip.com/Cruise-Booking-Online/CrystalProject/WebAPI/RecommendKeyWordSearch?type=4";
$data = array("type"=>4);
$result=send_post($url,$data);
$result = json_decode($result,true);
$companys = $result["TagSearch"]["Companies"];
$shipname = "0";
$shipid =0;
$company = "0";
$companyid = 0;
$count = 0;
foreach ($companys as $key => $v) {
    $companyid = $v['id'];
    $company = $v['name'];
    $shiplist = $v['ShipList'];
    $j=0;
    foreach ($shiplist as $k => $va) {
        $shipname = $va['name'];
        $shipid = $va['id'];
        $count = $va['count'];
        
        $sql = "insert into ship(shipname,shipid,company,companyid,count) values('".$shipname."',".$shipid.",'".$company."',".$companyid.",".$count.")";
        $r = mysql_query($sql);
        
        if($r){
            var_dump($r);
            $txt = "success---".$shipid."---ship--".date("Y-m-d H:i:s",time())."\n";
            fwrite($hand_log,$txt);
            $j++;
        }else{
            fwrite($error_log,mysql_error()."---ship--".$shipid."--".date("Y-m-d H:i:s",time())."\n");
        }
    }
    var_dump($j);
}
