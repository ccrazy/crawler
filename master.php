<?php
/**
 * 发送post请求
 * @param string $url 请求地址
 * @param array $post_data post键值对数据
 * @return string
 */
 include "time.php";
 include "conn.php";
 include "request.php";
 include "file.php";
 
$post_data = array(
    "VoyaID"=>0,
    "Section"=>"All",
    // "ShipID"=>4,
    // "IsRecommand"=>true,
    "NeedCount"=>true,
    "PageSize"=>20,
);
// $url='http://cruise.ctrip/Cruise-Booking-Online/CrystalProject/CrystalComment/GetCommentJson';
$url='http://cruise.ctrip.com/Cruise-Booking-Online/CrystalProject/CrystalComment/GetCommentJson';

$sql = "select distinct(shipid) from ship";
$shipInfo = mysql_query($sql);

while ($ship = mysql_fetch_array($shipInfo)) {
    
    $j=0;
    $post_data['ShipID'] = $ship['shipid']+0;//获取邮轮编号

    $post_data['PageIndex']=1;
    $total = send_post($url,$post_data);
    $total = json_decode($total,true);
    $commentTotal = $total['TotalCount'];#获取邮轮所有评论数
    $len = intval(ceil($commentTotal/20));

    for($i=1;$i<=$len;$i++){#输出邮轮评论数据
        $interval = rand(2,4)*5;
        $interval = 0;
        $post_data['PageIndex']=$i;
        $result = send_post($url, $post_data);
        $arr_result=json_decode($result,true);
        $comments = $arr_result['Comments'];
        if($comments){
            $d = 0;
            $result = str_replace("'","\'",$result);
            $sql = "insert into master(content,shipid,crawlerdate) values('".$result."',".$post_data['ShipID'].",'".date("YmdHis",time())."')";
            
            $r = mysql_query($sql);
            if($r){
                var_dump($r);
                var_dump("alldata_success");
                $txt = "success---".$post_data['ShipID']."---master---".date("Y-m-d H:i:s",time())."\n";
                fwrite($hand_log,$txt);
                $j=0;
            }else{
                fwrite($error_log,mysql_error()."---master--".$post_data['ShipID']."--".date("Y-m-d H:i:s",time())."\n");
            }
            
            foreach ($comments as $key => $value) {
                $score =$value['ProductScore'];
                $score_z = $score['Score'];
                $serviceScore = $score['ServiceScore'];
                $resturantmentScore=$score['ResturantmentScore'];
                $enterainmentScore=$score['EnterainmentScore'];
                $recommendationScore = $score['RecommendationScore'];
                $groundTourScore=$score['GroundTourScore'];
                $leaderScore=$score['LeaderScore'];
                
                $comment=$value['Comment'];
                $commentContent = $comment['CommentContent'];
                $commentDate = $comment['CommentDate'];
                $IsRecommend = 0;
                if($comment['IsRecommend']){
                    $IsRecommend = 1;
                }
                $sql = "insert into master_comment_data(VoyaId,Score,ServiceScore,ResturantmentScore,EnterainmentScore,GroundTourScore,LeaderScore,CommentContent,CommentDate,IsRecommend,ShipId,crawlerdate) 
                values('".$ship['VoyaId']."',".$score_z.",".$serviceScore.",".$resturantmentScore.",".$enterainmentScore.",".$leaderScore.",".$groundTourScore.",'".$commentContent."','".$commentDate."',".$IsRecommend.",'".$post_data['ShipID']."','".date("YmdHis",time())."')";
                
                $r = mysql_query($sql);
                if($r){
                    $d++;
                }else{
                    fwrite($error_log,mysql_error()."---master---".date("Y-m-d H:i:s",time())."\n");
                }
            }
            var_dump($d);
        }else{
            var_dump("empty");
            var_dump($ship['shipid']);
            $txt = "error---".$post_data['ShipID']."---master--comment is empty---".date("Y-m-d H:i:s",time())."\n";
            fwrite($error_log,$txt);
            $j++;
        }
        if($j>10){
            var_dump("internet die".$ship['shipid']);
            break;
        }
        sleep($interval);//暂停时间（单位为秒）
    }
}

mysql_close($conn);
