<?php
@session_start();
error_reporting(1);
$mappingArr = array(
    '1645473c' => 'jammukashmir', 'bdada9e4' => 'himachal', '3d4a305a' => 'punjab',
    '9baaebe1' => 'delhi', '1b86e8da' => 'haryana', 'eb82b3716' => 'uttarakhand',
    'd418e699' => 'rajasthan', '9ac24d08' => 'gujarat', 'd2b52f84' => 'madhya',
    '7f6f40db' => 'uttar', '98a0215e' => 'chhattisgarh', 'ac580d3c' => 'orissa',
    '904848bc' => 'goa', '84e51610' => 'maharashtra', '185a5eb3' => 'karnataka',
    '16d11d5c' => 'andhra', 'e369d9fd' => 'kerala', '3a2d6295' => 'tamilnadu',
    '27559de2' => 'bihar', '5308f89b' => 'jharkhand', 'eab98c0b' => 'westbengal',
    '91da6dd9' => 'sikkim', 'ab7b0f1b' => 'arunachal', '44e0325f' => 'assam',
    '0eb635a1' => 'nagaland', '9f405b4c' => 'manipur', '4b8c5cc6' => 'mizoram',
    '0bad206b' => 'tripura', '418d4c56' => 'meghalaya'
);
$mappingArr = array_flip($mappingArr);
$filename = "cache/states/".$mappingArr[$_GET['state']].'.php';
if(!file_exists($filename)){
	echo 'Invalid State Selected';
	exit;
}else{
	include($filename);
	$listJson['states'] = json_encode($statesArr);
	$listJson['districts'] = json_encode($districtsArr);
}
$stateid = $statesArr[0]['id'];
include("includes/paths.php");
include("includes/template.class.php");
$templateDis = new Template();
include("includes/user.class.php");
$userAction = new user();
$listJson['recentPosts'] = json_encode($userAction->getPosts('state', $stateid, null));

$_SESSION['feedsecurity']=md5('feedsecurity' . rand(1123494489,98932892837847434));
$templateDis->indexHtml("locationwiseAdvertisements.html", $listJson);
unset($_REQUEST);
$templateDis->publish();
exit;