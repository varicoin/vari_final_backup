<?php
@session_start();
class useractions {

    function __construct() {
	
        include_once('../newincludes/paths.php');
        include_once('newincludes/db.class.php');
        $this->db = new Db();
		
    }

    public function getMap($name) {
        return $this->db->getvalue("select map_code from states where image_name='$name'");
    }

    public function getParentId($name,$cond='image_name') {
        return $this->db->getvalue("select id from states where {$cond}='$name'");
    }

    public function getOneValue($fields,$tblName,$name,$cond) {
        return $this->db->getvalue("select {$fields} from {$tblName} where {$cond}='$name'");
    }

    public function getCategoryName($id) {
        return $this->db->getvalue("select category from categories where id='$id'");
    }

    public function getState($id) {
        return $this->db->getvalue("select name from states where id='$id'");
    }

    public function subCategoryid($categoryid) {
        return $this->db->getvalue("select id from categories where parentid='$categoryid' order by category LIMIT 1");
    }

    public function Categoryid($categoryname) {
        return $this->db->getvalue("select id from categories where category='$categoryname' LIMIT 1");
    }

    public function getId($name, $field) {
        return $this->db->getvalue("select $field from states where image_name='$name'");
    }

    public function getAssembly($id) {
        $x = null;
        $result = $this->db->getStates("select id,name from constituencies where parent_name='$id' order by name");
        while ($re = mysql_fetch_row($result)) {
            $bcount = $this->db->getvalue("select count(id) from posts where assemblyid='$re[0]'");
            //$scount = $this->db->getvalue("select count(id) from posts where assemblyid='$re[0]' and mode='0'");
            $x.="<tr>
            <td width='80%'>
            <a href='categories.php?constituency=$re[1]&&name=$re[0]'><font color='green'></font><b>" . $re[1] . "</b></a>
            </td>
            <td><font color='red'>({$bcount})</font></td>
            </tr>";
            // <td><font color='red'>Buyers($bcount)&nbsp;&nbsp;Sellers($scount)</font></td>
        }
        return $x;
    }

    public function getValues($id, $link='') {

        $result = $this->db->getStates("select name,image_name,id from states where parentid='$id' order by name");
        $x = null;
        
        while ($re = mysql_fetch_row($result)) {
            $count = 0;
            $count = $this->db->getvalue("select count(id) from posts where districtid='$re[2]'");
            $x.="<tr>
            <td width='80%'>
            <a href='$link$re[1]&&districtid=$re[2]'><font color='green'></font><b>" . $re[0] . "</b></a>
            </td>
            <td><font color='red'>( {$count} )</font></td>
            </tr>";
        }
        return $x;
    }
    public function getDistricts($id){
        return $this->db->getvalue("select group_concat(id separator ',') from states where parentid='$id' order by name");
    }

    public function getCategories($link, $id, $name, $subcat, $const) {

        $i = 0;
        $closing = 0;
        $x = null;
        $record_first = true;
        $assemblyCond = "";
        if ($name != null || !empty($name)) {
            $assemblyCond = " and assemblyid='{$name}'";
        }

        $result = $this->db->getStates("select id,category from categories where parentid='{$id}' order by category");
        while ($re = mysql_fetch_row($result)) {
            if ($id == 0) {
				$image = "<div class='cate_img'><a href='$link$re[0]'><img src='images/{$re[1]}.jpg' height='67' width='66' /></a></div>";
                $bcount = $this->db->getvalue("select count(id) from posts where categoryid IN (SELECT id FROM categories WHERE parentid='{$re[0]}'){$assemblyCond}");
            } else {
				$image = "";
                $bcount = $this->db->getvalue("select count(id) from posts where categoryid='{$re[0]}'{$assemblyCond}");
            }
            if ($re[0] == $const) {
                if ($closing / 2 == 0) {

                    $x.="<tr>
                            <td>&nbsp;</td>
                            <td>{$image}<div class='cate_bg'><a href='$link$re[0]'>" . trim($re[1]) . " ($bcount)</a></div></td";
                    $closing = 2;
                } else {
                    $x.="<td>&nbsp;</td>
                             <td>{$image}<div class='cate_bg'><a href='$link$re[0]'>" . trim($re[1]) . " ($bcount)</a></div></td>
                             <td>&nbsp;</td>
                             </tr>";
                    $closing = 0;
                }
            } else {
                if ($closing / 2 == 0) {
                    $x.="<tr>
                            <td>&nbsp;</td>
                            <td>{$image}<div class='cate_bg'><a href='$link$re[0]'>" . trim($re[1]) . " ($bcount)</a></div></td";
                    $closing = 2;
                } else {
                    $x.="<td>&nbsp;</td>
                             <td>{$image}<div class='cate_bg'><a href='$link$re[0]'>" . trim($re[1]) . " ($bcount)</a></div></td>
                             <td>&nbsp;</td>
                             </tr>";
                    $closing = 0;
                }
            }
        }

        return $x;
    }

    public function subCategories($link, $id, $name, $subcat, $const) {

        $closing = 0;
        $record_first = false;
        $assemblyCond = "";
        $x = null;
        if ($name != null || !empty($name)) {
            $assemblyCond = " and assemblyid='{$name}'";
        }
        $result = $this->db->getStates("select id,category from categories where parentid='{$id}' order by category");
        while ($re = mysql_fetch_row($result)) {
            $bcount = $this->db->getvalue("select count(id) from posts where cropid='{$re[0]}'{$assemblyCond}");
            if ($record_first) {
                if ($closing / 2 == 0) {
                    $x.="<tr>
                         <td width='84'></td>
                         <td width='351'>
                         <div class='cate_img'><a href='$link$re[0]'><img src='images/crops/" . trim($re[1]) . ".jpg' height='67' width='66' /></a></div>
                         <div class='cate_bg'><a href='$link$re[0]'>" . trim($re[1]) . " ($bcount)</a></div>
                         </td>";
                    $closing = 2;
                } else {
                    $x.="<td width='72'>&nbsp;</td>
                         <td width='351'><div class='cate_img'><a href='$link$re[0]'><img src='images/crops/" . trim($re[1]) . ".jpg' height='67' width='66' /></a></div>
                         <div class='cate_bg'><a href='$link$re[0]'>" . trim($re[1]) . " ($bcount)</a></div></td>
                         <td width='40'>&nbsp;</td>
                         </tr>";
                    $closing = 0;
                    $record_first = false;
                }
            } else {
                if ($closing / 2 == 0) {
                    $x.="<tr>
                         <td>&nbsp;</td>
                         <td><div class='cate_img'><a href='$link$re[0]'><img src='images/crops/" . trim($re[1]) . ".jpg' height='67' width='66' /></a></div>
                         <div class='cate_bg'><a href='$link$re[0]'>" . trim($re[1]) . " ($bcount)</a></div></td";
                    $closing = 2;
                } else {
                    $x.="<td>&nbsp;</td>
                         <td><div class='cate_img'><a href='$link$re[0]'><img src='images/crops/" . trim($re[1]) . ".jpg' height='67' width='66' /></a></div>
                         <div class='cate_bg'><a href='$link$re[0]'>" . trim($re[1]) . " ($bcount)</a></div></td>
                         <td>&nbsp;</td>
                         </tr>";
                    $closing = 0;
                }
            }
        }
        return $x;
    }

    public function getEmails() {
        $result = $this->db->getStates("SELECT id
            ,email
            FROM
            users ORDER BY id");
        $i = 0;
        $j = 0;
        $emails = array();
        while ($re = mysql_fetch_row($result)) {
            if ($i > 15) {
                $j++;
                $i = 0;
            }
            $emails[$j] .= trim($re[1]) . ',';
            $i++;
        }
        return $emails;
    }

    public function getPosts($nameid, $categoryid, $subcategoryid, $cropid, $mode) {
        $assmblyCond = "";
        $hyperLink = "";
        if ($nameid != null && !empty($nameid)) {
            $assmblyCond = "AND assemblyid='{$nameid}'";
            $hyperLink = "&name={$nameid}";
        }
        $result = $this->db->getStates("SELECT id
                ,title
                ,state_name
                ,DATE_FORMAT(date,'%d/%m/%Y') AS DATE
                ,userid
                ,category
                ,assembly
                ,district_name
                FROM
                view_posts
                WHERE
                categoryid='{$subcategoryid}'
                AND cropid='{$cropid}'
                {$assmblyCond}
                AND mode='{$mode}'
                ORDER BY date desc");
        $i = 1;

        while ($re = mysql_fetch_row($result)) {
            if ($_SESSION['userid'] == $re[4]) {
                if ($i % 2 == 0) {
                    $x.='<tr class="td_lightblack_color">
                        <td>' . trim($i) . '</td>
                            <td width="640" height="25" align="left" valign="middle"><a href=post.php?postid=' . $re[0] . $hyperLink . ' class="link1">' . trim($re[1]) . '</a> (<a href=postEdit.php?postid=' . $re[0] . ' class="link1">edit</a> &nbsp;|&nbsp; <a href=submit.php?action=delete&&postid=' . $re[0] . ' class="link1">delete</a>) </td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[5]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[6]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[7]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[2]) . '</td>
                                    <td width="100" width="100" height="25" align="center" valign="middle">' . trim($re[3]) . '</td>
                                        </tr>';
                } else {
                    $x.='<tr>
                        <td>' . trim($i) . '</td>
                            <td width="640" height="25" align="left" valign="middle" ><a href=post.php?postid=' . $re[0] . $hyperLink . ' class="link1">' . trim($re[1]) . '</a> (<a href=postEdit.php?postid=' . $re[0] . ' class="link1">edit</a> &nbsp;|&nbsp; <a href=submit.php?action=delete&&postid=' . $re[0] . ' class="link1">delete</a>) </td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[5]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[6]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[7]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[2]) . '</td>
                                    <td width="100" width="100" height="25" align="center" valign="middle">' . trim($re[3]) . '</td>
                                        </tr>';
                }
            } else {
                if ($i % 2 == 0) {
                    $x.='<tr class="td_lightblack_color">
                        <td>' . trim($i) . '</td>
                            <td width="640" height="25" align="left" valign="middle" align="left" ><a href=post.php?postid=' . $re[0] . $hyperLink . ' class="link1">' . trim($re[1]) . '</a> </td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[5]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[6]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[7]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[2]) . '</td>
                                    <td width="100" width="100" height="25" align="center" valign="middle" >' . trim($re[3]) . '</td>
                                        </tr>';
                } else {
                    $x.='<tr>
                        <td>' . trim($i) . '</td>
                            <td width="640" height="25" valign="middle" align="left" ><a href=post.php?postid=' . $re[0] . $hyperLink . ' class="link1">' . trim($re[1]) . '</a> </td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[5]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[6]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[7]) . '</td>
                                <td width="100" height="25" align="center" valign="middle">' . trim($re[2]) . '</td>
                                    <td width="100" width="100" height="25" align="center" valign="middle">' . trim($re[3]) . '</td>
                                        </tr>';
                }
            }
            $i++;
        }
        return $x;
    }

    public function getSubCategories($categoryid) {
        $strIds = null;
        $strQry = <<<Qry
            SELECT id
		FROM categories
		WHERE parentid =$categoryid
		ORDER BY id
Qry;
        $result = $this->db->getStates($strQry);
        while ($record = mysql_fetch_assoc($result)) {
            $strIds .= $record['id'] . ', ';
        }
        return substr($strIds, 0, strlen($strIds) - 2);
    }

    public function getRecentPosts($mode, $id, $const) {
        if($const!=null && !empty($const)){
            $assemblyCond = "AND assemblyid={$const}";
        }
        else{
            $assemblyCond = "";
        }
        switch ($mode) {
            case 'category':
                $strQry = <<<Qry
                SELECT id
                ,CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">",title, "<\/a>" ) AS subject
                ,category
                ,assembly AS name
                ,district_name AS district_name
                ,state_name AS first_name
                ,editedon AS date
                ,IF( mode =1, "Buying", "Selling" ) AS type
                , CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">View<\/a>" ) AS action
                FROM view_posts
                WHERE  categoryid IN ({$id})
                        {$assemblyCond}
				GROUP BY id
Qry;
                break;
            case 'subcategory':
                $strQry = <<<Qry
                SELECT id
                ,CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">",title, "<\/a>" ) AS subject
                ,category
                ,assembly AS name
                ,district_name AS district_name
                ,state_name AS first_name
                ,editedon AS date
                ,IF( mode =1, "Buying", "Selling" ) AS type
                , CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">View<\/a>" ) AS action
                FROM view_posts
        WHERE  categoryid={$id}
		{$assemblyCond}
		GROUP BY id
Qry;
                break;
            case 'crop':
                $strQry = <<<Qry
                SELECT id
                ,CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">",title, "<\/a>" ) AS subject
                ,category
                ,assembly AS name
                ,district_name AS district_name
                ,state_name AS first_name
                ,editedon AS date
                ,IF( mode =1, "Buying", "Selling" ) AS type
                , CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">View<\/a>" ) AS action
                FROM view_posts
        WHERE  cropid={$id}
		{$assemblyCond}
		GROUP BY id
Qry;
                break;
            case 'states':
                $strQry = <<<Qry
                SELECT id
                ,CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">",title, "<\/a>" ) AS subject
                ,category
                ,assembly AS name
                ,district_name AS district_name
                ,state_name AS first_name
                ,editedon AS date
                ,IF( mode =1, "Buying", "Selling" ) AS type
                , CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">View<\/a>" ) AS action
                FROM view_posts
        WHERE  stateid={$id}
		GROUP BY id
Qry;
                break;
            case 'district':
                $strQry = <<<Qry
                SELECT id
                ,CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">",title, "<\/a>" ) AS subject
                ,category
                ,assembly AS name
                ,district_name AS district_name
                ,state_name AS first_name
                ,editedon AS date
                ,IF( mode =1, "Buying", "Selling" ) AS type
                , CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">View<\/a>" ) AS action
                FROM view_posts
        WHERE  districtid={$id}
		GROUP BY id
Qry;
                break;
            default:
                $strQry = <<<Qry
                SELECT CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">",title, "<\/a>" ) AS subject
                ,category
                ,assembly AS assembly
                ,district_name AS district
                ,state_name AS state
				,IF( view_posts.mode =1, "Buying", "Selling" ) AS type
                ,view_posts.editedon AS postedon                
                FROM view_posts
				GROUP BY id
                LIMIT 150
Qry;
        }

        $result = $this->db->getStates($strQry);
        $i = 0;
        $arrPosts = array();
        
        while ($record = mysql_fetch_assoc($result)) {
            $arrPosts[$i] = $record;
            $i++;
        }
        $json = json_encode($arrPosts);
        return $json;
    }

    public function getPost($id) {

        $result = $this->db->getStates("select u.id,u.subject,u.message,u.contact_info,u.location,u.price,u.quantity,s.first_name,DATE_FORMAT(u.date,'%d/%m/%Y') from posts u,users s where u.id='$id' and s.id=u.userid");
       		if(mysql_num_rows($result)<1){
			$x= '<h2 style="font-size:12px;font-weight:bold;font-family:tahoma;border-bottom:1px dashed;padding-bottom:3px;">Post not found</h2>';
		}
		else{
			while ($re = mysql_fetch_row($result)) {
				$x = '<h2 style="font-size:12px;font-weight:bold;font-family:tahoma;border-bottom:1px dashed;padding-bottom:3px;">
				' . trim($re[1]) . '</h2>
			<table width="97%" style="margin:5px;">
				<tr>
					<td width="29%" valign="top"><strong>Quantity</strong></td>
					<td width="4%" valign="top"><strong>:</strong></td>
					<td width="67%">' . trim($re[6]) . '</td>
				</tr>
				<tr>
					<td valign="top"><strong>Address </strong></td>
					<td valign="top"><strong>:</strong></td>
					<td>' . trim($re[4]) . '</td>
				</tr>
				<tr>
					<td valign="top"><strong>Contact Information</strong></td>
					<td valign="top"><strong>:</strong></td>
					<td>' . trim($re[3]) . '</td>
				</tr>
				<tr>
					<td valign="top"><strong>Posted by</strong></td>
					<td valign="top"><strong>:</strong></td>
					<td>' . trim($re[7]) . '</td>
				</tr>
				<tr>
				  <td><strong>Posted On</strong></td>
				  <td><strong>:</strong></td>
				  <td>' . trim($re[8]) . '</td>
				</tr>
				<tr>
				  <td>&nbsp;</td>
				  <td>&nbsp;</td>
				  <td>&nbsp;</td>
				</tr>
				<tr>
					<td valign="top"><strong>Description</strong></td>
					<td valign="top"><strong>:</strong></td>
					<td>' . trim($re[2]) . '</td>
				</tr>
			</table>';

				$i++;
			}
		}
        return $x;
    }

    public function getImage($id) {
        $result = $this->db->getStates("SELECT
                id
                ,name
                ,CONCAT('uploads/',location)
                FROM
                    uploads
                WHERE
                    postid={$id} LIMIT 1
            ");
        while ($re = mysql_fetch_row($result)) {
            $arr = array($re[2], $re[1]);
        }
        return $arr;
    }

    public function getmyPosts( $id, $isLoggedIn=true ) {
		
		$edit = (isset($_SESSION['sessionid']) )?" / <a href=postEdit.php?postid=',id,'>Edit</a>'":"'";
		$strQry = "SELECT id
                ,CONCAT( '<a href=post.php?postid=', id, '&name=', assemblyid,'>',title, '<\/a>' ) AS subject
                ,state_name AS first_name
                ,date
                ,userid
                ,item
                ,assembly AS name
                ,district_name
                ,category
                ,IF( mode =1, 'Buying', 'Selling' ) AS type
                ,CONCAT( '<a href=post.php?postid=', id, '&name=', assemblyid,'>View<\/a>".$edit." ) AS action
                FROM
                view_posts
                WHERE
                userid={$id}
                ORDER BY date desc";
        $result = $this->db->getStates( $strQry );
				
		$arrPosts = array();
        $i=0;
        while ($record = mysql_fetch_assoc($result)) {
            $arrPosts[$i] = $record;
            $i++;
        }
        $json = json_encode($arrPosts);
        return $json;
    }

    public function getLink() {
        $id = func_get_arg(0);
        $mode = func_get_arg(1);
        $param = func_get_arg(2);
        $link = null;
        switch ($mode) {
            case 'postid':
                $strQry = <<<str
                    SELECT
                            `state_name` AS state
                            ,`state_name` AS state_name
                            ,`districtid` AS district_id
                            ,`district_name` AS district
                            ,`district_name` AS district_name
                            ,`assemblyid` AS constituency_id
                            ,`assembly` AS constituency
                            ,`categoryid` AS sub_category_id
                            ,`subcategory` AS sub_category
                            ,`main_category_id` AS category_id
                            ,`category` AS category
                            ,`cropid` AS crop_id
                            ,`item` AS crop

                    FROM
                            `view_posts`
                    WHERE
                            `id`=$id
str;

                $result = $this->db->getStates($strQry);
                while ($record = mysql_fetch_array($result)) {
                    if ($param != null && !empty($param)) {
                        $link = "<a href='index.php'>India</a>  "
                                . "<a href='index.php?mode=states&state={$record['state_name']}'>" . $record['state'] . '</a> '
                                . "<a href='index.php?mode=districts&district={$record['district_name']}&&districtid={$record['district_id']}'>" . $record['district'] . '</a>' . '<span>'
                                . $record['constituency'] . '</span> '
                                . "<a href='categories.php?constituency={$record['constituency']}&name={$record['constituency_id']}'>Categories</a>" . ' ';
                        $hyperLink = "categories.php?constituency={$record['constituency']}&name={$record['constituency_id']}&";
                        $postLink = "posts.php?constituency={$record['constituency']}&name={$record['constituency_id']}&";
                    } else {
                        $link = "<a href='index.php'>Home</a>  ";
                        $hyperLink = "categories.php?";
                        $postLink = "posts.php?";
                    }
                    $link .= "<a href='{$hyperLink}categoryid={$record['category_id']}'>" . $record['category'] . '</a>'
                            . '  ' . "<a href='{$hyperLink}categoryid={$record['category_id']}&subcategoryid={$record['sub_category_id']}'>" . $record['sub_category'] . '</a>'
                            . '  ' . "<a href='{$postLink}categoryid={$record['category_id']}&subcategoryid={$record['sub_category_id']}&cropid={$record['crop_id']}'>" . $record['crop'] . '</a>';
                }
                break;
            case 'districtid':
                $strQry = <<<str
                SELECT
			`constituency` AS assembly_name
                        ,`districtid` AS district_id
                        ,`district` AS district_name
                        ,district_image
                        ,state_image
                        ,`state` AS state_name
                    FROM
                        `view_locations`
                    WHERE
                        `districtid`=$id LIMIT 1;
str;

                $result = $this->db->getStates($strQry);
                
                while ($record = mysql_fetch_array($result)) {
                    $link = "<a href='index.php'>India</a>  "
                            . "<a href='index.php?mode=states&state={$record['state_image']}'>" . $record['state_name'] . '</a> <span>'
                            . $record['district_name'].'</span>';
                }
                break;
            case 'assemblyid':
                if ($id !== null && !empty($id)) {
                    $strQry = <<<str
                        SELECT
                            `l`.`constituency` AS assembly_name
                            ,`l`.`constituencyid` AS constituencyid
                            ,`l`.`districtid` AS district_id
                            ,`l`.`district` AS district_name
                            ,`l`.`district_image` AS district_image
                            ,`l`.state_image AS state_image
                            ,`l`.`state` AS state_name
                        FROM
                            `view_locations` `l`
                        WHERE
                            `constituencyid`=$id LIMIT 1
str;

                    $result = $this->db->getStates($strQry);
                    while ($record = mysql_fetch_array($result)) {
                        $constituency = $record['assembly_name'];
                        $constituencyid = $record['constituencyid'];
                        $link = "<a href='index.php'>India</a>  "
                                . "<a href='index.php?mode=states&state={$record['state_image']}'>" . $record['state_name'] . '</a>  '
                                . "<a href='index.php?mode=districts&district={$record['district_image']}&&districtid={$record['district_id']}'>" . $record['district_name'] . '</a>' . '  '
                                .'<span>'. $record['assembly_name'].'</span>'
                                . "  <a href='categories.php?constituency={$constituency}&name={$constituencyid}'>Categories</a>";
                        $hyperLink = "categories.php?constituency={$constituency}&&name={$constituencyid}&&";
                    }
                } else {
                    $link = "";
                    $hyperLink = "categories.php?";
                }
				if(func_get_arg(3)){
					$param1 = func_get_arg(3);
				}
                
                if (!empty($param)) {
                    if (empty($param1)) {
                        $rsql = <<<str
                                    SELECT
                                        `c`.category
                                    FROM
                                        `view_categories` c
                                    WHERE
                                        `c`.`categoryid`=$param LIMIT 1
str;
                        $result = $this->db->getvalue($rsql);
                        $link .= " <span> {$result}</span>";
                    } else {
                        $rsql = <<<str
                                    SELECT
                                        `c`.categoryid,
                                        `c`.category,
                                        `c`.subcategory
                                    FROM
                                        `view_categories` c
                                    WHERE
                                        `c`.`subcategoryid`=$param LIMIT 1
str;
                        $result = $this->db->getvalues($rsql);
                        $link .= "  <a href='{$hyperLink}categoryid={$result['categoryid']}'>{$result['category']}</a> <span>{$result['subcategory']}</span>";
                    }
                }
                break;
            case 'cropid':
                if ($id != null && !empty($id)) {
                    $strQry = <<<str
                        SELECT
                            l.`constituency` AS assembly_name
                            ,l.`districtid` AS district_id
                            ,l.`district` AS district_name
                            ,l.district_image AS district_image
                            ,l.state_image AS state_image
                            ,l.`state` AS state_name
                            ,l.`constituencyid` AS constituencyid
                            ,c.categoryid AS categoryid
                            ,c.subcategoryid AS subcategoryid
                            ,c.subcategory AS subcategory
                            ,c.category AS category
                            ,c.item as crop
                        FROM
                            `view_locations` l, `view_categories` c

                        WHERE
                            l.`constituencyid`=$id
                            AND c.cropid=$param LIMIT 1;
str;
                } else {
                    $strQry = <<<str
                        SELECT
                            c.categoryid AS categoryid
                            ,c.subcategoryid AS subcategoryid
                            ,c.subcategory AS subcategory
                            ,c.category AS category
                            ,c.item as crop
                        FROM
                            `view_categories` c

                        WHERE
                            c.cropid=$param LIMIT 1;
str;
                }

                $result = $this->db->getStates($strQry);
                while ($record = mysql_fetch_array($result)) {
                    if ($id != null && !empty($id)) {
                        $redirectionLink = "categories.php?constituency={$record['assembly_name']}&name={$record['constituencyid']}&";
                        $link = "<a href='index.php'>India</a>  "
                                . "<a href='index.php?mode=states&state={$record['state_image']}'>{$record['state_name']} </a>  "
                                . "<a href='index.php?mode=districts&district={$record['district_image']}&&districtid={$record['district_id']}'>{$record['district_name']}</a> "
                                . '<span>'.$record['assembly_name'].'</span>'
                                . " <a href='{$redirectionLink}'>Categories</a>";
                    } else {
                        $redirectionLink = "categories.php?";
                        $link = "";
                    }
                    $link .= "  <a href='{$redirectionLink}categoryid={$record['categoryid']}'>{$record['category']}</a>"
                            . "  <a href='{$redirectionLink}categoryid={$record['categoryid']}&subcategoryid={$record['subcategoryid']}'>{$record['subcategory']}</a>"
                            . "  <span>{$record['crop']}</span>";
                }

                break;
        }
        if ($id == null) {
            $link = "<a href='index.php'>Home</a>" . $link;
        }
        return $link;
    }

    public function getComments($postid) {
        $x = '<table class="table_2">
	<tbody><tr>
    	<td><strong style="color:#060;">Comments</strong></td>
    </tr>
    <tr>
    	<td>';
        $result = $this->db->getStates("select u.id,u.comment,s.first_name,DATE_FORMAT(u.posted_on,'%d/%m/%Y') from comments u,users s where u.posted_by=s.id and u.posid={$postid} order by u.posted_on");
        while ($re = mysql_fetch_row($result)) {
            $x.='<p style="float:left;"><strong>'.$re[2].'</strong></p><p style="float:right;padding-right:5px;"><strong>'.$re[3].'</strong></p>
            <br /><br /><p style="border-bottom:1px dashed #999;padding-bottom:6px;">'.$re[1].'</p>';
    	}
        $x.='</td>
    </tr></tbody></table>';
        return $x;
    }

    public function mainCategory($select,$mode='',$required=true) {
        $mode = ($mode=='disabled')?'disabled="disabled"':'';
        $x = '<select title="Press Ctrl to select Sub categories." multiple="" '.$mode.' size="15" name="categoryid[]" id="categoryid" class="fld1" onChange="javascript:getSubCategories(this.value)">';
        $result = $this->db->getStates("select id,category from categories where parentid='0' order by category");
        while ($re = mysql_fetch_row($result)) {
            $x.="<optgroup id='optGrp1' label='$re[1]'>";
            $result1 = $this->db->getStates("select id,category from categories where parentid='$re[0]' order by category");
            while ($re1 = mysql_fetch_row($result1)) {
                if ($re1[0] == $select)
                    $x.="<option title='Press Ctrl to select Sub categories.' value=$re1[0] selected=selected>" . $re1[1] . "</option>";
                else
                    $x.="<option title='Press Ctrl to select Sub categories.' value=$re1[0]>" . $re1[1] . "</option>";
            }
            $x.="</optgroup>";
        }
        $x.='</select><span style="color: #767676; line-height: 14px; font-size: 10px;">Press Ctrl to select Sub categories.</span>';
        return $x;
    }

    public function RecentActions($mode=null){
        $limit =200;
        $x = null;
        $strQry = <<<Qry
                SELECT subject
                ,category
                ,name AS assembly
                ,district_name AS district
                ,first_name AS state
                ,date AS postedon
                ,type
                FROM view_recent_actions order by prty ASC
Qry;

		$result = $this->db->getStates($strQry);
        $i = 0;
        $arrPosts = array();
        
        while ($record = mysql_fetch_assoc($result)) {
            $arrPosts[$i] = $record;
            $i++;
        }
        $json = json_encode($arrPosts);
        return $json;
		
    }

   

    public function viewAllPosts($districtid, $assemblyid, $subcategoryid, $cropid,$type) {
        $strAssembly = null;
        $strCrop = null;
        $condition = "";
        $ampersand = false;
        if (!empty($districtid)) {
            $condition = "districtid IN ($districtid)";
            $ampersand = true;
        }
        if (!empty($assemblyid)) {
            $condition .= ($ampersand==TRUE)?' AND ':'';
			$assemblyid = explode(",",$assemblyid);
			for($i=0;$i<sizeof($assemblyid);$i++)
			{
				$assembly = explode("_",$assemblyid[$i]);
				$strAssembly .= $assembly[0].',';
			}
			$strAssembly = substr($strAssembly,0,strlen($strAssembly)-1);
            $condition .= "assemblyid IN ($strAssembly)";
			
            $ampersand = true;
        }
        if (!empty($subcategoryid)) {
            $condition .= ($ampersand==TRUE)?' AND ':'';
            $condition .= "categoryid IN ($subcategoryid)";
            $ampersand = true;
        }
        if (!empty($cropid)) {
            $condition .= ($ampersand==TRUE)?' AND ':'';
			$cropid = explode(",",$cropid);
			for($i=0;$i<sizeof($cropid);$i++) {
				$crop = explode("_",$cropid[$i]);
				$strCrop .= $crop[0].',';
			}
			$strCrop = substr($strCrop,0,strlen($strCrop)-1);
            $condition .= "cropid IN ($strCrop)";
        }
        if(!empty($type)) {
		$condition .= ($ampersand==TRUE)?' AND ':'';
		$type = ($type==2)?0:$type;
		$condition .= "mode='{$type}'";
	}
		$strQry = "SELECT id
                ,CONCAT('<a href=post.php?postid=', id, '&name=', assemblyid,'>',title, '<\/a>' ) AS subject
                ,state_name AS first_name
                ,date
                ,userid
                ,item AS category
                ,assembly AS name
                ,district_name
                ,IF( mode =1, 'Buying', 'Selling' ) AS type
                , CONCAT( '<a href=post.php?postid=', id, '&name=', assemblyid,'>View<\/a>' ) AS action
                FROM
                view_posts
                WHERE
            {$condition}
                ORDER BY id desc";
        $result = $this->db->getStates( $strQry );
		//echo $strQry;	
        
        $arrPosts = array();
        $i=0;
        while ($record = mysql_fetch_assoc($result)) {
            $arrPosts[$i] = $record;
            $i++;
        }
		
        $json = json_encode($arrPosts);
        return $json;
    }
    

    public function viewAllPostIdPosts($postid){
        $strQry = "SELECT id
                ,CONCAT(id,'.','<a href=post.php?postid=', id, '&name=', assemblyid,'>',title, '<\/a>' ) AS subject
                ,state_name AS first_name
                ,date
                ,userid
                ,item AS category
                ,assembly AS name
                ,district_name
                ,IF( mode =1, 'Buying', 'Selling' ) AS type
                , CONCAT( '<a href=post.php?postid=', id, '&name=', assemblyid,'>View<\/a>' ) AS action
                FROM
                view_posts
                WHERE
                id LIKE '%$postid'
                ORDER BY date desc";
        $result = $this->db->getStates( $strQry );
		//echo $strQry;

        $arrPosts = array();
        $i=0;
        while ($record = mysql_fetch_assoc($result)) {
            $arrPosts[$i] = $record;
            $i++;
        }

        $json = json_encode($arrPosts);
        return $json;
    }
    
	public function MainCategories($select)
    {
        $x=array();
        $result=$this->db->getStates("select id, category from categories where parentid='0' order by category");
        $temp = array();
        
        while($re=mysql_fetch_row($result))
        {		
            $result1 = $this->db->getStates("select id, category from categories where parentid='$re[0]' order by category");
            while($re1 = mysql_fetch_row($result1))
            {
                if($re1[0] == $select){
                   // $newArr .= "{'id'=>$re1[0], 'text'=>$re1[1], 'isSelected'=>true}";
                   $temp[] = array('id'=>$re1[0], 'text'=>$re1[1], 'isSelected'=>true);
                }                            
                else{
                    $temp[] = array('id'=>$re1[0], 'text'=>$re1[1]);
                }
            }
            $x[$re[1]] = $temp;
            $temp = array();
        }
        return json_encode($x);
    }
    
    public function state($select,$mode="",$required=true) {
        $x=array();
        $result = $this->db->getStates("select id,name from states where parentid='1' order by name");
        $temp = array();
        while ($re = mysql_fetch_row($result)) 
        {

            $result1 = $this->db->getStates("select id,name from states where parentid='$re[0]' order by name");
            while ($re1 = mysql_fetch_row($result1)) 
            {
                if ($re1[0] == $select)
                {
                     //$newArr .= "{'id'=>$re1[0], 'text'=>$re1[1], 'isSelected'=>true}";
                     
                      $temp[] = array('id'=>$re1[0], 'text'=>$re1[1], 'isSelected'=>true);
                }
                else
                {
                    $temp[] = array('id'=>$re1[0], 'text'=>$re1[1]);
                }
            
            }
            $x[$re[1]] = $temp;
            $temp = array();
        }
        return json_encode($x);
    }
       
	public  function getRelatedPosts($id, $const,$postid){
        if($const!=null && !empty($const)){
            $assemblyCond = "AND assemblyid={$const}";
        }
        else{
            $assemblyCond = "";
        }
        $x = null;
                $strQry = <<<Qry
                SELECT id
                ,CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">",title, "<\/a>" ) AS subject
                ,category
                ,assembly AS name
                ,district_name AS district_name
                ,state_name AS first_name
                ,date AS date
                ,IF( mode =1, "Buying", "Selling" ) AS type
                , CONCAT( "<a href=post.php?postid=", id, "&name=", assemblyid,">View<\/a>" ) AS action
                FROM view_posts
        WHERE  cropid={$id}
		{$assemblyCond}
        ORDER BY id DESC
Qry;
        $result = $this->db->getStates($strQry);
        $i = 0;
        $arrPosts = array();
        
        while ($record = mysql_fetch_assoc($result)) {
			if($record['id']!=$postid){
				$arrPosts[$i] = $record;
				$i++;
			}            
        }
        $json = json_encode($arrPosts);
        return $json;
    }


    public function getPostReplies( $id, $isLoggedIn=true ) {
		
		$edit = (isSet($_SESSION['sessionid']))?"&nbsp;/&nbsp;<a href=postEdit.php?postid=',id,'>Edit</a>'":"'";
		$strQry = "SELECT id
               ,IF(type=1,'Clasified','Market') AS first_name
                ,posted_by AS date
                ,name as category
                ,email AS name
                ,phone_number AS district_name
                ,message as subject
                ,CONCAT( '<a href=post.php?postid=', postid, '>',postid, '<\/a>' ) AS type
                ,CONCAT( '<a href=repliesSubmit.php?id=',id, '&action=accept>Accept<\/a>&nbsp;/&nbsp;<a href=repliesSubmit.php?id=',id,'&action=reject>Reject<\/a>' ) AS action
                FROM
                replies
		WHERE sent=0
                ORDER BY posted_by desc";
        $result = $this->db->getStates( $strQry );
				
		$arrPosts = array();
        $i=0;
        while ($record = mysql_fetch_assoc($result)) {
            $arrPosts[$i] = $record;
            $i++;
        }
        $json = json_encode($arrPosts);
        return $json;
    }

    public function RecentSMSPosts($limit,$districtid,$categoryid,$mode){

        switch ($mode){
            case 'recent':
                $districtid = '';
                $categoryid = '';
                break;
            case 'districts':
                $districtid = 'WHERE  districtid IN ('.$districtid.') ';
                $categoryid = '';
                break;
            case 'district':
                $districtid = 'WHERE  districtid='.$districtid.' ';
                $categoryid = '';
                break;
            case 'crop':
                $districtid = 'WHERE  districtid='.$districtid.' AND ';
                $categoryid = 'cropid IN ('.$categoryid.') ';
                 break;
             case 'assembly':
                 $districtid = '';
                 $categoryid = 'WHERE  cropid IN ('.$categoryid.')';
                 break;
             case 'userid':
                 $districtid = '';
                 $categoryid = 'WHERE userid='.$_SESSION['userid'].'';
                 
                 break;
        }
       
        $x = null;
        $strQry = <<<Qry
                SELECT id
                ,CONCAT( "<a href=smspost.php?postid=", id,">",title, "<\/a>" ) AS title
                ,item AS item
                ,district_name
                ,'xxxx' AS phone
                ,createdon AS date
                ,CONCAT( "<a href=smspost.php?postid=", id,">View<\/a>" ) AS action
                ,mode AS type
                ,location AS location
                ,state_name
                FROM view_sms_posts
                $districtid $categoryid 
                ORDER BY id DESC
                LIMIT $limit
Qry;
       
		$result = $this->db->getStates($strQry);
        $i = 0;
        $arrPosts = array();
        
        while ($record = mysql_fetch_assoc($result)) {
            $arrPosts[$i] = $record;
            $i++;
        }
        $json = json_encode($arrPosts);
        return $json;        
    }

    public function getCropsIds($categoryid){
        $strQry = <<<Qry
                SELECT group_CONCAT(id SEPARATOR ',') FROM categories WHERE parentid IN ({$categoryid});
Qry;

        return $this->db->getValue($strQry);
    }

    public function getOpenSourceCodes($districtid){
		 $strQry = <<<Qry
                SELECT bbcode,lat FROM states WHERE id={$districtid};
Qry;
		$result = $this->db->getStates($strQry);
        $arrPosts = array();
		 while ($record = mysql_fetch_assoc($result)) {
            $arrPosts[0] = $record['bbcode'];
            $arrPosts[1] = $record['lat'];
        }
		return $arrPosts;
	}


public function marketList($mode=null, $id=null,$html='list',$limit=''){
		
		$records = 0;
		switch($mode){
			case 'crop':
				$strQuery = "SELECT id, SUBSTRING(subject,1,24), SUBSTRING(price,1,24), location, mode,SUBSTRING(offer_price,1,24) from market_view_list WHERE cropid={$id} GROUP BY id {$limit};";
				$resultSet = $this->db->getStates( $strQuery );
				$records = mysql_num_rows($resultSet);
				$welcomeText = "No Market Advertisements found!";
				break;
			case 'category':
				$strQuery = "SELECT id, SUBSTRING(subject,1,24), SUBSTRING(price,1,24), location, mode,SUBSTRING(offer_price,1,24) from market_view_list WHERE categoryid={$id} GROUP BY id {$limit};";
				$resultSet = $this->db->getStates( $strQuery );	
				$records = mysql_num_rows($resultSet);
				$welcomeText = "No Market Advertisements found!";
				break;
			case 'low':
				$strQuery = "SELECT id, SUBSTRING(subject,1,24), SUBSTRING(price,1,24), location, mode from market_view_list WHERE division=2 GROUP BY id {$limit};";
				$resultSet = $this->db->getStates( $strQuery );	
				$records = mysql_num_rows($resultSet);
				$welcomeText = "Low Price Market Advertisements found!";
				break;
			case 'bumper':
				$strQuery = "SELECT id, SUBSTRING(subject,1,24), SUBSTRING(price,1,24), location, mode,SUBSTRING(offer_price,1,24) from market_view_list WHERE division=1 GROUP BY id {$limit};";
				$resultSet = $this->db->getStates( $strQuery );	
				$records = mysql_num_rows($resultSet);
				$welcomeText = "Bumper Offer Market Advertisements found!";
				break;
			case 'new':
				$strQuery = "SELECT id, SUBSTRING(subject,1,24), SUBSTRING(price,1,24), location, mode,SUBSTRING(offer_price,1,24) from market_view_list  GROUP BY id ORDER BY postedon {$limit};";
				$resultSet = $this->db->getStates( $strQuery );	
				$records = mysql_num_rows($resultSet);
				$welcomeText = "Most Recent Market Advertisements found!";
				break;
			case 'list':
				$strQuery = "SELECT id, SUBSTRING(subject,1,24), SUBSTRING(price,1,24), location, mode,SUBSTRING(offer_price,1,24) from market_view_list GROUP BY id {$limit};";
				$resultSet = $this->db->getStates( $strQuery );
				$welcomeText = "Recent Market Advertisements";
		}
		if($mode==null || $records<1 && $html=='list'){
			$strQuery = "SELECT id, SUBSTRING(subject,1,24), SUBSTRING(price,1,24), location, mode,SUBSTRING(offer_price,1,24) from market_view_list GROUP BY id {$limit};";
			$resultSet = $this->db->getStates( $strQuery );
			$welcomeText = ($mode==null)?"Recent Market Advertisements":"No Market Advertisements found!";
		}
		if($html=='list'){
			$htmlContent = '<div class="right-heading">'.$welcomeText.'</div>
				<div class="main_products">';
			while($record = mysql_fetch_row( $resultSet ) ) {
                                $style=($record[4]=='Buying')?' style="color:green !important;text-decoration:none !important;"':'';
				$htmlContent .= "<div class='items'>
					
						<h2>{$record[1]}</h2>
						<p><img src='market_uploads/{$record[3]}' alt='Image' height='148' width='186' /></p>
						<h1{$style}>{$record[2]}</h1>
						<div class='details-but'><a href='marketpost.php?postid={$record[0]}'>{$record[4]}</a>";
				$htmlContent .= (isSet($_SESSION['isAdmin']) && $_SESSION['isAdmin']==TRUE)?" &nbsp; / &nbsp;<a href='marketEdit.php?postid={$record[0]}'>Edit</a> &nbsp; / &nbsp;<a href='market_submit.php?postid={$record[0]}&action=delete'>Delete</a>":'';
				$htmlContent .= "</div>
					</div>";
			}	
				$htmlContent .= '</div>';
		}elseif($html=='horizontal'){
			$htmlContent='';
			while($record = mysql_fetch_row( $resultSet ) ) {
				 $style=($record[4]=='Buying')?' style="color:green !important;text-decoration:none !important;"':'';
				$htmlContent.="<div class='items market_items_horizantal'>
					<h2>{$record[1]}<a href='marketpost.php?postid={$record[0]}'><img src='market_uploads/{$record[3]}' height='148' width='186' style='float:left;margin-right:0px;' class='img_market_Item' /></a></h2><h1 $style><span>{$record[2]}</span><br  /></h1>				
				</div>";
			}
		}elseif($html=='vertical'){
			$htmlContent='';
			while($record = mysql_fetch_row( $resultSet ) ) {
				$htmlContent .= "<div class='right_items'>
				  <p>{$record[1]}</p>
				  <a href='marketpost.php?postid={$record[0]}'><img src='market_uploads/{$record[3]}' height='111' width='109' /></a>
				  
				  <p><span style='text-decoration: line-through;'>{$record[2]}</span><br  />
					<span  style='font-weight:bold;color:#CC0000;'>{$record[5]}</span></p>
					</div>";
			}
		}
		return $htmlContent;
	}


        public function viewAds($mode,$arrVal,$stock_value){
		if(in_array($arrVal, array('new','low','bumper'))){
			$stock = " AND stock='{$stock_value}' ";
		}else{
			$stock='';
		}
		$strQuery = "SELECT id, SUBSTRING(subject, 1, 18) AS label, location AS image, price AS maxprice, offer_price AS minprice from market_view_list WHERE mode='{$mode}'{$stock}GROUP BY id ORDER BY postedon DESC LIMIT 9;";
		$resultSet = $this->db->getStates( $strQuery ); 
		$i=0;
		$colorClassArr = array('yellow', 'green', 'skyBlue last', 'red', 'darkBlue', 'darlRed last', 'brown', 'greenLight', 'orange last');
		while($record = mysql_fetch_array( $resultSet ) ) {
		    $emptyDiv = ((in_array($i, array(2, 5, 8)))?",'emptyDiv':true":'');
			$newArr .= "{'image':'{$record[image]}', 'label':'{$record[label]}', 'maxprice':'{$record[maxprice]}', 'minprice':'{$record[minprice]}', 'id':'{$record[id]}', 'class':'{$colorClassArr[$i]}'".$emptyDiv."},";
			$i++;
		}
		return $newArr;		
	}
        
public function viewAds1($mode,$arrVal,$stock_value){
		if(in_array($arrVal, array('new','low','bumper'))){
			$stock = " AND stock='{$stock_value}' ";
		}else{
			$stock='';
		}
		$strQuery = "SELECT id, SUBSTRING(subject, 1, 18) AS label, subject, mode,crop, state_name AS state , location AS image, price AS maxprice, offer_price AS minprice from market_view_list WHERE mode='{$mode}'{$stock}GROUP BY id ORDER BY postedon DESC LIMIT 9;";
		$resultSet = $this->db->getStates( $strQuery ); 
		$i=0;
		
		while($record = mysql_fetch_array( $resultSet ) ) {
                        
			$newArr .= "{'image':'{$record[image]}', 'label':'{$record[label]}', 'subject':'{$record[subject]}', 'crop':'{$record[crop]}', 'mode':'{$record[mode]}', 'state':'{$record[state]}','maxprice':'{$record[maxprice]}', 'minprice':'{$record[minprice]}', 'id':'{$record[id]}'},";
			$i++;
		}
		return $newArr;		
	}
        
        public function viewAds2($mode,$arrVal,$stock_value){
                        
		$strQuery1 = "SELECT * FROM posts";
                $strQuery = "SELECT * FROM market_view_list";
		$resultSet = $this->db->getStates( $strQuery ); 
                $resultSet1 = $this->db->getStates( $strQuery1 ); 
		$i=0;
		if($stock_value=='Instock')
                {
		while($record = mysql_fetch_array( $resultSet ) ) {
		  
			//$newArr .= "{'image':'{$record[image]}', 'label':'{$record[subject]}', 'postby':'{$record[postingby]}', 'mode':'{$record[mode]}', 'crop':'{$record[cropid]}', 'location':'{$record[location]}','distict':'{$record[districtid]}','assemblyid':'{$record[assemblyid]}','maxprice':'{$record[price]}', 'minprice':'{$record[minprice]}', 'quantity':'{$record[quantity]}'},";
                        
                        $newArr .= "{'image':'{$record[image]}', 'label':'{$record[subject]}', 'postby':'{$record[postedby]}', 'mode':'{$record[mode]}', 'crop':'{$record[crop]}', 'location':'{$record[constituency]}','distict':'{$record[district]}','assemblyid':'{$record[state_name]}','maxprice':'{$record[price]}', 'minprice':'{$record[offer_price]}', 'quantity':'{$record[quantity]}'},";
			$i++;
		}
		return $newArr;	
                exit();
                }
                else
                {
		while($record = mysql_fetch_array( $resultSet1 ) ) {
		  
			//$newArr .= "{'image':'{$record[image]}', 'label':'{$record[subject]}', 'postby':'{$record[postingby]}', 'mode':'{$record[mode]}', 'crop':'{$record[cropid]}', 'location':'{$record[location]}','distict':'{$record[districtid]}','assemblyid':'{$record[assemblyid]}','maxprice':'{$record[price]}', 'minprice':'{$record[minprice]}', 'quantity':'{$record[quantity]}'},";
                        
                        $newArr .= "{'image':'{$record[image]}', 'label':'{$record[subject]}', 'postby':'{$record[postedby]}', 'mode':'{$record[mode]}', 'crop':'{$record[crop]}', 'location':'{$record[constituency]}','distict':'{$record[district]}','assemblyid':'{$record[state_name]}','maxprice':'{$record[price]}', 'minprice':'{$record[offer_price]}', 'quantity':'{$record[quantity]}'},";
			$i++;
		}
		return $newArr;	
                }
	}
        
}