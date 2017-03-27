<?php
@session_start();
error_reporting(1);
$mappingArr = array(
	'1d7ad6df'  =>  1,
	'901bc8d1'  =>  369,
	'4c810fe5'  =>  3,
	'df70008c'  =>  360,
	'88c2a98e'  =>  345,
	'c13297fb'  =>  2,
	'1ff10f8e'  =>  6,
	'9d5ff573'  =>  4,
	'554a9436'  =>  319,
	'867549f5'  =>  303
);

$filename = "cache/".$_GET['categoryid'].'.php';
if(!file_exists($filename)){
	echo 'Invalid Category Selected';
	exit;
}else{
	include($filename);
	$listJson['category'] = json_encode($categoriesArr);
	$listJson['subCategories'] = json_encode($subCategoriesArr);
}
$categoryid = $mappingArr[$_GET['categoryid']];
include("includes/paths.php");
include("includes/template.class.php");
$templateDis = new Template();
include("includes/user.class.php");
$userAction = new user();
$listJson['recentPosts'] = json_encode($userAction->getPosts('category', $categoryid, null));

$_SESSION['feedsecurity']=md5('feedsecurity' . rand(1123494489,98932892837847434));
$templateDis->indexHtml("advertisements.html", $listJson);
unset($_REQUEST);
$templateDis->publish();
exit;
