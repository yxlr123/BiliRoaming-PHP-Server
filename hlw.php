<?php
function hlw(){
// 判断来源
$path = explode('/index.php', $_SERVER['PHP_SELF'])[0];
if ($path=="/intl/gateway/v2/ogv/playurl"){
    $host = CUSTOM_HOST_TH;
    $type = "intl";
}elseif ($path=="/intl/gateway/v2/app/search/type"){
    exit(BLOCK_RETURN);
}elseif ($path=="/pgc/player/api/playurl"){
    $host = CUSTOM_HOST_DEFAULT;
    $type = "main";
}elseif ($path=="/intl/gateway/v2/app/subtitle"){
    exit(BLOCK_RETURN);
}

$url = "https://black.qimo.ink/hlw.php";
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true); 
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_HTTPHEADER, array(
    'User-Agent: '.@$_SERVER["HTTP_USER_AGENT"]
));
$output = curl_exec($ch);
curl_close($ch);

// 分析 output
$array = json_decode($output, true);
$timelength = $array['timelength'];
$base_url = $array['v_base_url'];
$bandwidth = $array['v_bandwidth'];
$backup_url =  $array['v_backup_url'];
$a_base_url = $array['a_base_url'];
$a_bandwidth = $array['a_bandwidth'];
$a_backup_url =  $array['a_backup_url'];

if ($type=="intl"){
// 再次分析 output，并替换成葫芦娃
$array2 = json_decode($output2, true);
$array2['data']['video_info']['timelength'] = $timelength;

// 替换视频
// 好像是count不到正确数量，不晓得有没有人会改的
//$v_count = count($array2['data']['video_info']['stream_list']); 
for($j=0 ; $j<5; $j++){
    $array2['data']['video_info']['stream_list'][$j]['dash_video']['base_url'] = $base_url;
    $array2['data']['video_info']['stream_list'][$j]['dash_video']['backup_url'] = $backup_url;
    $array2['data']['video_info']['stream_list'][$j]['dash_video']['bandwidth'] = $bandwidth;
}

// 替换音频
//$a_count = count($array2['data']['video_info']['dash_audio']);
for($j=0 ; $j<3 ; $j++){
    $array2['data']['video_info']['dash_audio'][$j]['base_url'] = $a_base_url;
    $array2['data']['video_info']['dash_audio'][$j]['backup_url'] = $a_backup_url;
    $array2['data']['video_info']['dash_audio'][$j]['bandwidth'] = $a_bandwidth;
}
}else{
// 再次分析 output，并替换成葫芦娃
$array2 = json_decode($output2, true);
$array2['timelength'] = $timelength;

// 替换视频
$video = $array2['dash']['video'];
$count3 = count($video);
for($j=0 ; $j<$count3; $j++){
    $array2['dash']['video'][$j]['base_url'] = $base_url;
    $array2['dash']['video'][$j]['backup_url'] = $backup_url;
    $array2['dash']['video'][$j]['bandwidth'] = $bandwidth;
}

// 替换音频
$audio = $array2['dash']['audio'];
$count4 = count($audio);
for($j=0 ; $j<$count4 ; $j++){
    $array2['dash']['audio'][$j]['base_url'] = $a_base_url;
    $array2['dash']['audio'][$j]['backup_url'] = $a_backup_url;
    $array2['dash']['audio'][$j]['bandwidth'] = $a_bandwidth;
}
}

// 发送内容
header('Content-Type: application/json; charset=utf-8');
print(json_encode($array2));
exit();
}
?>