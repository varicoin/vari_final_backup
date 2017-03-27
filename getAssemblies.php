<?php
@session_start();
include("includes/paths.php");
error_reporting(1);
if (isset($_POST['id'])) {
    $id = $_POST['id'];
	if(!is_array($id)){
		$id = array($id);
	}
}else{
	return;
}
require("includes/post.class.php");
$function = new post();
echo $function->getAssemblies($id);
exit;
