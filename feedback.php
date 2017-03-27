<?php
@session_start();
$comment = '';
error_reporting(1);
include("includes/paths.php");

if($_POST):
	require($includesPath."function.class.php");
	$function=new functions();
	$function->insertAction("comments","'','{$_POST['postid']}',now(),'{$_SESSION['userid']}','{$_POST['comment']}','{$_POST['rating']}'");
        $function->insertAction("recent_actions","'','{$_POST['postid']}','3',now(),'{$_SESSION['userid']}'");
	$_SESSION['error'] = "Feedback Posted sucessful";
	header("location:feedback.php?postid=".$_POST['postid']);
endif;
extract($_REQUEST);
extract($_SESSION);
require($includesPath . "post.class.php");
$function = new post();
include($includesPath . "template.class.php");
$template = new Template();
$_SESSION['error'] = (isSet($_SESSION['error']) && empty($_SESSION['error']))?'':$_SESSION['error'];
if (isset($_SESSION['sessionid'])) {
	$_SESSION['error'] = (isSet($_SESSION['error']))?$_SESSION['error']:'';
    $template->indexHtml("feedback.html", array('help'));
    $template->replace("@postid@", $postid);
    $template->replace("@error@", $_SESSION['error']);
	unset($_SESSION['error']);
} else {
    $template->indexHtml("feedback_guest.html", array('help'));
}
$comments = $function->getComments($postid);
$template->replace("@comments@",$comments[0]);
$template->replace("@comments_feedback@",$comments[1]);
unset($comments);
$template->replace("@commentpost@",$comment);
$template->replace("LoginError", $_SESSION['error']);
unset($_REQUEST);
unset($_SESSION['LoginError']);
$template->publish();
?>
