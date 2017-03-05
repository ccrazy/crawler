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
$url='http://cruise.ctrip.com/Cruise-Booking-Online/CrystalProject/CrystalComment/GetCommentJson';

$sql = "select distinct(ShipId) from hot_detail";
$shipInfo = mysql_query($sql);
$data = fopen("data.txt","a");
while ($ship = mysql_fetch_array($shipInfo)) {
    $j=0;
    $ship['ShipId'] = $ship['ShipId']+0;//获取邮轮编号
    $post_data['ShipID'] = $ship['ShipId'];
    $post_data['PageIndex']=1;
    $total = send_post($url,$post_data);
    $total = json_decode($total,true);
    $commentTotal = $total['TotalCount'];#获取邮轮所有评论数
    $len = intval(ceil($commentTotal/20));
    // var_dump($len);continue;
    for($i=1;$i<=$len;$i++){#输出邮轮评论数据
        $interval = rand(1,4)*10;
        $post_data['PageIndex']=$i;
        $result = send_post($url, $post_data);
        $arr_result=json_decode($result,true);
        $comments = $arr_result['Comments'];
        if($comments){
            $d = 0;
            $result = str_replace("'","\'",$result);
            $sql = "insert into alldata_copy(content,shipid) values('".$result."',".$ship['ShipId'].")";
            
            $r = mysql_query($sql);
            if($r){
                var_dump($r);
                var_dump("alldata_success");
                $txt = "success---".$post_data['PageIndex']."---crawler--".$ship['ShipId']."---".$ship['VoyaId']."--".date("Y-m-d H:i:s",time())."\n";
                fwrite($hand_log,$txt);
                $j=0;
            }else{
                fwrite($error_log,mysql_error()."---crawler--".$ship['ShipId']."---".$ship['VoyaId']."--".date("Y-m-d H:i:s",time())."\n");
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
                $sql = "insert into comment_data_copy(VoyaId,Score,ServiceScore,ResturantmentScore,EnterainmentScore,GroundTourScore,LeaderScore,CommentContent,CommentDate,IsRecommend,ShipId) 
                values('".$ship['VoyaId']."',".$score_z.",".$serviceScore.",".$resturantmentScore.",".$enterainmentScore.",".$leaderScore.",".$groundTourScore.",'".$commentContent."','".$commentDate."',".$IsRecommend.",'".$ship['ShipId']."')";
                
                $r = mysql_query($sql);
                if($r){
                    $d++;
                }else{
                    fwrite($error_log,mysql_error()."---crawler--".$ship['ShipId']."---".$ship['VoyaId']."--".date("Y-m-d H:i:s",time())."\n");
                }
            }
            var_dump($d);
        }else{
            $txt = "error---".$post_data['PageIndex']."---crawler--comment is empty--".$ship['ShipId']."---".$ship['VoyaId']."--".date("Y-m-d H:i:s",time())."\n";
            fwrite($error_log,$txt);
            $j++;
        }
        if($j>10){
            continue;
        }
        sleep($interval);//暂停时间（单位为秒）
    }
}

mysql_close($conn);
