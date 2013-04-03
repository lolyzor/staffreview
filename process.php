<?php 
if($_POST['action'] == 'login'){
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
if($_POST['action'] == 'startDay'){
	//echo 'not user received';
	$user = $_POST['user'];
	//$date = explode(" ",$_POST['date']);
	//create date and time, seperate them so I can compate it later
	//also split the date into year month and day
	//format date is the key
	//when entering date specify the type of it, 'workbegin','workend'
	//when querying for each day of the month grab begin and end and get the work hours,
	//afther doing that for every user see who did not fill  the quota !
	//formating http://www.phpjabbers.com/date-and-time-formatting-with-php-php28.html
	$m = new MongoClient();
	$db = $m->db->test;
	$query = array('user'=>$user,'year'=>$date[0],'month'=>$date[1],'day'=>$date[2]);
	$db->insert($query);
	$reply = array('status'=>'insreted','vrijeme'=>$vrijeme);
	echo json_encode($reply);
}
if($_POST['action']=='checkHours'){
	$user = $_POST['user'];
	$date = $_POST['startdate'];
	$time = $_POST['time'];
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