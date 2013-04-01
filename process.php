<?php 
if(isset($_POST['user'])){
	//$user = mysql_real_escape_string($_POST['user']);
	//$pass = mysql_real_escape_string($_POST['pass']);
	$user = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($_POST['user'], ENT_QUOTES));
	$pass = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($_POST['pass'], ENT_QUOTES));
	login($user,$pass);
} 
else{
	echo 'not user received';
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