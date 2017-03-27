<?php
@session_start();
include("includes/paths.php");
error_reporting(1);

include($includesPath . "template.class.php");
$template = new Template();
$template->indexHtml("smspage.html", array('help'));
  
$template->publish();