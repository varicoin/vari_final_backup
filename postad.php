<?php
@session_start();
error_reporting(1);
extract($_REQUEST);
extract($_SESSION);

if (isset($_SESSION['sessionid']) && !empty($_SESSION['userid'])) {
    
    require("newincludes/user.class.php");
    $function = new user();
    include("newincludes/template.class.php");
    $template = new Template();
    $state = $function->state("");
  
    //echo $state;
    $categories = $function->MainCategories();
   // echo json_encode($x);
    $data = array( 
        "categories" => $categories,
        "states" => $state
    );
   //echo $categories;
     
    $template->indexHtml("postadd.html", $data);
    $template->publish();
} else {
    header("location:index.php");	
}