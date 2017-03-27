<?php
@session_start();
error_reporting(1);
if(isset($_SESSION['sessionid']) && isset($_SESSION['userid'])){
    include("includes/template.class.php");
    $template = new Template();
    include("includes/post.class.php");
    $post = new post();
    $postid = intval($_GET['postid']);
    $data = $post->getMyPost($postid);
    $data[0]['insertType'] ='update';
    $data = json_encode($data[0]);
    $template->indexHtml("postaddguest.html", $data);
    $template->publish();
}else{
	header("location:login.php?redirect=postEdit.php?postid=".$postid);
}