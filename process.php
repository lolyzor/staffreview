<?php 
if(($_POST['action']) == 'login'){
	//$user = mysql_real_escape_string($_POST['user']);
	//$pass = mysql_real_escape_string($_POST['pass']);
	$user = filterOutStuff($_POST['user']);
	$pass = filterOutStuff($_POST['pass']);
	login($user,$pass);
} 
function filterOutStuff($var){
	$var2 = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($var, ENT_QUOTES));
	return $var2;
}
if(($_POST['action']) == 'startDay'){
	//echo 'not user received';
	$user = $_POST['user'];
	$date = explode(" ",$_POST['date']);
	$m = new MongoClient();
	$db = $m->db->test;
	$query = array('user'=>$user,'year'=>$date[0],'month'=>$date[1],'day'=>$date[2]);
	$db->insert($query);
}
function login($user,$pass){
	$m = new MongoClient();
	$db = $m->db;
	$test = $db->test;
	//$query = array("user"=>array('$eq'=>$user),"pass"=>array('$eq'=>$pass));
	$query = array('user'=>$user,'pass'=>$pass);
	$results = $test->find($query);
	if($results->hasNext()){
		echo 'success';
	}
	else{
		echo 'failed';
	}
}
?>