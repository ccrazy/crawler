<?php
include "conn.php";
include "request.php";
include "file.php";
include "time.php";

$ids =array();
$sql_id="select id from hot";
$result_id = mysql_query($sql_id);
while($ids=mysql_fetch_array($result_id)){
    $id = $ids['id'];
    var_dump($id);
    $json = 
    $sql = "select contentHot from hot where id=".$id;
    $hot = mysql_query($sql);
    $hot = mysql_fetch_array($hot);
    $hot = $hot[0];
    $hot = preg_replace("/\n/","",$hot);
    $arr=json_decode($hot,true);
    $name = $arr['Name'];
    $productList = $arr['ProductList'];
    foreach ($productList as $key => $v) {
        $VoyaId = $v['VoyaID'];
        $ProductID = $v['ProductID'];
        $DepartureCity = $v['DepartureCity'];
        $ArriveCity=$v['ArriveCity'];
        $VoyaRange = $v['VoyaRange'];
        $VoyaRangeId = $v['VoyaRangeID'];
        $DestinationCity=json_encode($v['DestinationCities'],JSON_UNESCAPED_UNICODE);
        $Score = $v['Score'];
        $CommentTotal = $v['CommentTotal'];
        $CompanyName = $v['CompanyName'];
        $ShipName = $v['ShipName'];
        $SubName = $v['SubName'];
        $Sailing = $v['Sailing'];
        $SailingId = $Sailing['SailingID'];
        $DepartureDate = $Sailing['DepartureDate'];
        $MinPrice = $Sailing['MinPrice'];
        $OriPrice = $Sailing['OriPrice'];
        $Characteristic = $Sailing['Characteristic'];
        $MarketingTags =json_encode($Sailing['MarketingTags'],JSON_UNESCAPED_UNICODE);
        $Promotion = $Sailing['PromotionPrice']['RecommandSaleCodeV2'];
        $PromoTypeId = $Promotion['PromoCodeTypeID'];
        $PromoTypeName = $Promotion['PromoCodeTypeName'];
        $PromoDescription = $Promotion['PromoCodeDescription'];
        $PromoAmount = $Promotion['PromoCodeAmount'];
        $DiscountType = $PromoAmount['DiscountType'];
        $DiscountAmount = $PromoAmount['DiscountAmount'];
        $DiscountRate = $PromoAmount['DiscountRate'];
        $PromoSold = $Promotion['PromoCodeInventory']['Sold'];
        $SuplierInfo = json_encode($Sailing['SupplierList'],JSON_UNESCAPED_UNICODE);
        $CategoryTypeList=json_encode($Sailing['CategoryTypeList'],JSON_UNESCAPED_UNICODE);
        $ShipId = $v['ShipID'];
        $sql = "insert into hot_detail(`name`,`VoyaId`,`ProductID`,`DepartureCity`,`ArriveCity`,
                `VoyaRange`,`VoyaRangeId`,`DestinationCity`,`Score`,`CommentTotal`,`CompanyName`,
                `ShipName`,`SubName`,`SailingId`,`DepartureDate`,`OriPrice`,`MinPrice`,`Characteristic`,
                `MarketingTags`,`PromoTypeId`,`PromoTypeName`,`PromoDescription`,`DiscountType`,`DiscountAmount`,
                `DiscountRate`,`PromoSold`,`SupplierInfo`,`CategoryTypeList`,`ShipId`) values('".$name."','".$VoyaId."','".
                $ProductID."','".$DepartureCity."','".$ArriveCity."','".$VoyaRange."','".$VoyaRangeId."','".
                $DepartureCity."',".$Score.",".$CommentTotal.",'".$CompanyName."','".$ShipName."','".$SubName."','".
                $SailingId."','".$DepartureDate."',".$OriPrice.",".$MinPrice.",'".$Characteristic."','".$MarketingTags."','".
                $PromoTypeId."','".$PromoTypeName."','".$PromoDescription."','".$DiscountType."','".$DiscountAmount."','".
                $DiscountRate."','".$PromoSold."','".$SuplierInfo."','".$CategoryTypeList."','".$ShipId."')";
        $r = mysql_query($sql);
        var_dump($r);
        if($r){
            $txt = "Success--clean_hot--".$ProductID."--".date("Y-m-d H:i:s",time())."\n";
            fwrite($hand_log,$txt);
        }else{
            $error = "Error--clean_hot--".mysql_error()."--".date("Y-m-d H:i:s",time())."\n";
            fwrite($error_log,$error);
        }
        
    }
}