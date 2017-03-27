<?php
@session_start();
include("includes/paths.php");
error_reporting(1);

include($includesPath . "template.class.php");

$template = new Template();
$template->indexHtml("vari_analytics.html", array('help','analytics'));
$template->publish();
