<?php 
session_start();
error_reporting(1);
if(isset($_SESSION['sessionid'])){
        $_SESSION['error'] = (isset($_SESSION['error']))?$_SESSION['error']:null;
	include("includes/paths.php");
	require($includesPath."user.class.php");
	$function=new user();
	include($includesPath."template.class.php");
	$template=new Template();
    $data['userFields'] = json_encode($_SESSION);
    
	$template->indexHtml("user.html", $data);
	unset($_SESSION['error']);
}
else{
	header("location:index.php");
}