<?php
@session_start();
error_reporting(0);
$comment = '';
include("includes/paths.php");
require("includes/post.class.php");
$post = new post();
include("includes/template.class.php");
$templateDis = new Template();

$postid = intval($_GET['postid']);

$data[0] = json_encode($post->getPost($postid));

$rand = rand(15,456);
$data[1] = md5(time().$rand);

$templateDis->indexHtml("post.html", $data);
unset($_REQUEST);
$templateDis->publish();
exit;
