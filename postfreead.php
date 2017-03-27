<?php
@session_start();
@ob_start();
error_reporting(1);
if(isset($_SESSION['sessionid'])){
    include("includes/template.class.php");
    $template = new Template();
    $data['insertType'] = 'insert';
    $data = json_encode($data);
    
    $template->indexHtml("postaddguest.html", $data);
    $template->publish();
}else{
	header("location:login.php?redirect=postfreead.php");
}