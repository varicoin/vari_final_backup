<?php
@session_start();
error_reporting(0);
include("includes/paths.php");
require($includesPath . "market.class.php");
$market = new market();
include($includesPath . "template.class.php");
$template = new Template();
extract($_REQUEST);
//extract($_SESSION);
$template->marketHtml("market_list.html",array('grid','help'));
$array_headings = array('@Instock@','@Open@');
switch ($mode) {
    
    case 'category':
        $list = $market->marketList('category',$category);
		$template->ArrReplace($array_headings, array('',''));
        break;
    case 'crop':
        $list = $market->marketList('crop',$crop);
		$template->ArrReplace($array_headings, array('',''));
        break;
	case 'bumper':
		$list = $market->marketList('bumper');
		$template->ArrReplace($array_headings, array('Instock ','Open '));
		break;
	case 'low':
		$list = $market->marketList('low');
		$template->ArrReplace($array_headings, array('Instock ','Open '));
		break;
	case 'new':
		$list = $market->marketList('new');
		$template->ArrReplace($array_headings, array('Instock ','Open '));
		break;
    default:
	$list = $market->marketList();
	$template->ArrReplace($array_headings, array('',''));
}
$categories = $market->categoriesList();
$template->replace("LoginError", '');
$template->replace("@categorieslist@", $categories);
$template->replace("@items_list@", $list);
require($includesPath . "userActions.php");
$function = new useractions();
$recentPosts = $function->getRecentPosts(null,null,null);
$viewSellingAds = $market->viewAds('Selling',$mode,'Instock');
$viewBuyingAds = $market->viewAds('Buying',$mode,'Open');
$template->replace("@selling_ads@", $viewSellingAds);
$template->replace("@buying_ads@", $viewBuyingAds);
$template->replace("@left_categories@", $market->getLeftCategoryList($sel,$menu,$crop));
$template->replace("jsonString", '[{"subject":"1","category":"2","name":"3","district_name":"4","first_name":"5","date":"6","type":"7","action":"8"}]');
$template->replace("@currentURL@",$currentURL);
unset($_REQUEST);
unset($_SESSION['error']);
$template->publish();
?>
