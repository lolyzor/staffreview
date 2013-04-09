<?php 
switch ($_POST['action']) {
	case 'login':
		# mafaka login
		$user = filterOutStuff($_POST['user']);
		$pass = filterOutStuff($_POST['pass']);
		login($user,$pass);	
		break;
	case 'startDay':
		startDay();
		break;
	case 'checkHours':
		checkHours();
		break;
	//case ''
	default:
		# code...
		echo 'bad request bitch';
		break;
}
function filterOutStuff($var){
	$var2 = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($var, ENT_QUOTES));
	return $var2;
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
function startDay(){
	//echo 'not user received';
	//$date = explode(" ",$_POST['date']);
	//create date and time, seperate them so I can compate it later
	//also split the date into year month and day
	//format date is the key
	//when entering date specify the type of it, 'workbegin','workend'
	//when querying for each day of the month grab begin and end and get the work hours,
	//afther doing that for every user see who did not fill  the quota !
	//formating http://www.phpjabbers.com/date-and-time-formatting-with-php-php28.html
	$user = filterOutStuff($_POST['user']);
	$date = explode(' ',getTime());
	//echo date('l F Y H:i',time());
	$m = new MongoClient();
	$db = $m->db->test;
	$query = buildQuery($user);
	$fulltime = getFormatedTime();
	$query['fulltime'] = $fulltime;
	$db->insert($query);
	$vrijeme = 'upravo poceo..';
	$reply = array('status'=>'insreted','vrijeme'=>$vrijeme);
	echo json_encode($reply);
}
function getTime(){
	return date('l F Y H:i');
}
function getFormatedTime(){
	return date('Y-m-d H:i');	
}
function buildQuery($user){
	$time = explode(" ",getTime());
	return array('day'=>$time[0],'month'=>$time[1],'year'=>$time[2],'user'=>$user);
}

function checkHours(){
	$user = filterOutStuff($_POST['user']);
	//$date = $_POST['startdate'];
	//$time = $_POST['time'];	
	$status = '';
	$m = new MongoClient();
	$db = $m->db->test;
	$result = $db->findOne(buildQuery($user));
	$then = new DateTime($result['fulltime']);
	$now = new DateTime(getFormatedTime());
	$diff = $now->diff($then);
	if($diff->h > 0){
		$h = $diff->h;
		if($h == 1)
			$status .= strval($h).' sat ';
		elseif($h <= 4)
			$status .= strval($h).' sata ';
		elseif($h > 4)
			$status .= strval($h).' sati ';
	}
	if($diff->i > 0){
		$m = $diff->i;
		if($m >= 5 && <= 20)
			$status .= strval($m).' minuta';
		elseif($m%10 == 1)
			$status .= strval($m).' minutu';
		elseif($m%10 <= 4)
			$status .= strval($m).' minute';
		elseif($m%10 > 4 || $m%10 == 0)
			$status .= strval($m).' minuta';
	}
	//$output = array('status'=>'ok','vrijeme'=>$then->format('l F Y H:i').' '.$now->format('l F Y H:i'));
	//$output = array('status'=>'ok','vrijeme'=>strval($diff->h).' '.strval($diff->i).' status '.$status);
	$output = array('status'=>'ok','vrijeme'=>$status);
	echo json_encode($output);
		
}
?>