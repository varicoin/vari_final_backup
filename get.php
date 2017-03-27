<?php
@session_start();
include("includes/paths.php");
error_reporting(0);
if (isset($_POST['page'])) {
        $page = $_POST['page'];
}else{
	$page = 1;
}
require("includes/feeds.php");
$function = new feeds();
echo $function->getRecentPosts($page, null, null);

exit;
