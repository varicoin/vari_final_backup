<?php
@session_start();
include("includes/paths.php");
error_reporting(1);
$mode = (!isset ($_GET['mode']))?null:$_GET['mode'];

if($mode!=null){
    require("includes/userActions.php");
    $userAction = new useractions();
    include("includes/template.class.php");
    $templateDis = new Template();
    $categoryid = (!isset ($_GET['categoryid']))?null:$_GET['categoryid'];
}

switch ($mode) {
    case 'states':
        $state = $_GET['state'];
        $template->indexHtml("states.html",array('multi_grids', 'help'));
        $getMap = $function->getMap($state);
        $id = $function->getParentId($state);
        $recentPosts = $function->getRecentPosts('states',$id,null);
        
        $districts = $function->getValues($id, "index.php?mode=districts&district=");
        
        //$template->replace('width="587" height="659"', 'width="548" height="535"');
        $state = $function->getState($id);
        $links = $function->getLink($id, 'postid', null);
        $district_names = $function->getDistricts($id);
        $recentSMSPosts = $function->RecentSMSPosts(150,$district_names,null,'districts');

        $links = '<a href="index.php">India</a>' . ' <span> ' . $state.'</span>';
        
		$arrReplace = array('replace1', 'imagelocation', 'replace2', 'jsonString', '@sms_posts@', 'replace3', 'Links', 'Titlefield');
		$arrReplaceWith = array($getMap, 'images/'.$_GET['state'].'.gif', $districts, $recentPosts, $recentSMSPosts, 'Districts', $links, $state . '');
		$template->ArrReplace($arrReplace, $arrReplaceWith);
		
        break;
    case 'districts':
        $template->indexHtml("states.html",array('grid','multi_grids','help'));
        $district = $_GET['district'];
       
        $id = $function->getParentId($district);
        $stateid = $function->getOneValue('parentid','states',$id,'id');
        $recentSMSPosts = $function->RecentSMSPosts(150,$id,null,'district');
        $constituencies = $function->getAssembly($id);
        $_SESSION['district'] = $district;
        $_SESSION['districtid'] = $id;
        $_SESSION['districtname'] = $function->getState($id);
        $recentPosts = $function->getRecentPosts('district',$id,null);
        $links = $function->getLink($id, 'districtid', null);
        if($stateid=='24'){
	    $arr =  $function->getOpenSourceCodes($id);
	    $bbcode=$arr[0];
				$lat=$arr[1];
               $image = '<iframe width="548" height="535" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.openstreetmap.org/export/embed.html?'.$bbcode.'&amp;layer=mapnik" style="border: 1px solid black"></iframe><br /><small><a href="http://www.openstreetmap.org/?'.$lat.'&amp;zoom=10&amp;layers=M">View Larger Map</a></small>';
        }else{
               $image = "<img src='images/".$district.".gif' alt='No Map Available' width='548' height='535'>";
        }
		
	$arrReplace = array('jsonString', '@sms_posts@', 'replace1', 'replace2', 'replace3', 'Links', 'Titlefield');
	$arrReplaceWith = array($recentPosts, $recentSMSPosts, $image, $constituencies, 'Constituencies', $links, $_SESSION['districtname'] . '');
	$template->ArrReplace($arrReplace, $arrReplaceWith);
		
        break;
    case 'signup':
        if (isset($_SESSION['sessionid'])) {
            unset($_SESSION['sessionid']);
            @session_destroy();
        }
        $template = new Template(false);
	$template->indexHtml("signup_ie.html",array('signup_validate_form', 'help'));
        break;
    case 'contactus':
        $templateDis->indexHtml("contactus.html");
        break;
    case 'aboutus':
        $templateDis->indexHtml("aboutus.html", array('help'));
        break;
	case 'howitworks':
        $templateDis->indexHtml("How_it_works.html", array('help'));
        break;
    case 'search':
        $template->indexHtml("search.html", array('search_ajax', 'help'));
        $_SESSION['searchError'] = (isSet($_SESSION['searchError']))?$_SESSION['searchError']:'';
        $template->replace("mainCategory", $function->MainCategory("",'',false));
        $template->replace("states", $function->state("",'',false));
        $template->replace('@searchError@', $_SESSION['searchError']);
        unset($_SESSION['searchError']);
        break;
    case 'forgot':
        if (isset($_SESSION['sessionid'])) {
            unset($_SESSION['sessionid']);
            session_destroy();
        }
        $template->indexHtml("forgot.html", array('help'));
        
       $_SESSION['forgotError'] = (isSet($_SESSION['forgotError']))?$_SESSION['forgotError']:'';
       $template->replace("@forgotError@", $_SESSION['forgotError']);
        unset($_SESSION['forgotError']);
        break;
	case 'safetytips':
        if (isset($_SESSION['sessionid'])) {
            unset($_SESSION['sessionid']);
            session_destroy();
        }
        $templateDis->indexHtml("safetytips.html", array('help'));
        break;
	case 'terms':
        if (isset($_SESSION['sessionid'])) {
            unset($_SESSION['sessionid']);
            session_destroy();
        }
        $templateDis->indexHtml("terms.html",array('help'));
        break;
    default:
	require("includes/user.class.php");
	$userAction = new user();

	$categoriesCount = $userAction->categoriesCount();
	include("includes/template.class.php");
	$templateDis = new Template();
	$_SESSION['feedsecurity']=md5('feedsecurity' . rand(1123494489,98932892837847434));
    $data[0] = json_encode($categoriesCount);
    $data['isBanner'] = 0;
	$templateDis->indexHtml("layout.html", $data);
	$templateDis->publish();
    unset($_REQUEST);
    unset($_SESSION['isBanner']);
	exit;
}

unset($_REQUEST);
if($mode!=null){
	$templateDis->publish();
}
