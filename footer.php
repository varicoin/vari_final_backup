<?php
@session_start();
include("includes/paths.php");
error_reporting(0);
require($includesPath . "userActions.php");
$function = new useractions();
include($includesPath . "template.class.php");
$template = new Template();

switch ($mode) {
	case 'safetytips':
		if (isset($_SESSION['sessionid'])) {
			unset($_SESSION['sessionid']);
			session_destroy();
		}
		$template->indexHtml("safetytips.html", array('help'));
		break;
	case 'terms':
		if (isset($_SESSION['sessionid'])) {
			unset($_SESSION['sessionid']);
			session_destroy();
		}
		$template->indexHtml("terms.html",array('help'));
		break;
}
unset($_REQUEST);
$template->publish();