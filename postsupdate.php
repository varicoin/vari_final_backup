<?php

final class Db 
	{
	private $mhost		= "dandmudik.ipagemysql.com";
	private $mdatabase	= "vari_connections";
	private $muser		= "varicoin";
	private $mpassword	= "tne%h)Y8?Z++";
    private $mresultset	= NULL;
	private static $connection = false; 

	
	function __construct()
	{
		if(!self::$connection)
		{
			self::$connection = mysql_connect($this->mhost,$this->muser,$this->mpassword);
			mysql_select_db($this->mdatabase,self::$connection);
			return self::$connection;
		}
		else
		{	
			return self::$connection;
		}
	}
	//for runsql connection
	function runSQL($rsql)
	{
		$result=mysql_query($rsql, self::$connection);
		
		return $result;
	}
	function getvalues($rsql)
	{
            $result=mysql_query($rsql, self::$connection);
            while($row = mysql_fetch_array($result)){
				$resArr[] = $row;
			}
			return $resArr;
	}
	function generateRandomString($length = 8) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	function RandomString($characters,$length=4) {
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	function getvalue($rsql)
	{
	$result=mysql_query($rsql, self::$connection);
	while ($row = mysql_fetch_array($result))
		{
			return $row[0];
		}
	}
}

$dbconnection = new Db();

$sqlupdqry = "SELECT `posts`.`userid` AS userid,`posts`.`id` AS `id` FROM `posts` LIMIT 14511, 600;";
$arrPostIds = $dbconnection->getvalues($sqlupdqry);
$i = 0;
foreach($arrPostIds AS $key=>$arr):
	$selectuserid = "SELECT id from posted_users where dummy='{$arr['userid']}';";
	$newuserid = $dbconnection->getvalue($selectuserid);
	$postsuptsqry = "UPDATE `posts` set userid='{$newuserid}' WHERE id='{$arr['id']}';";
	$i++;
	if(!$dbconnection->runSQL($postsuptsqry)){
		if(mysql_num_rows($dbconnection->runSQL($postsuptsqry))<1){
			echo $postsuptsqry.'<br>';
		}
	}
	
endforeach;

echo $postsuptsqry.'<br>'.$i;