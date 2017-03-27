<?php
@session_start();
include("includes/paths.php");
error_reporting(0);
if (isset($_POST['id'])) {
    $id = $_POST['id'];
}else{
	return;
}
require("includes/post.class.php");
$function = new post();
echo $function->subItems($id);
exit;
