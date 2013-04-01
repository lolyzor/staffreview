<?php 
	if(isset($_POST['user'])){
		$user = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($_POST['user'], ENT_QUOTES));
		$pass = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($_POST['pass'], ENT_QUOTES));
		procesS($user,$pass);
	}
	function procesS($user,$pass){
		$m = new MongoClient();
		$db = $m->db->test;
		$db->insert(array('user'=>$user,'pass'=>$pass));
		echo 'success';
	}
 ?>