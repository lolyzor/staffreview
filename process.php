<?php 
switch ($_POST['action']) {
	case 'login':
		# mafaka login
		login(getUser(),getPass());	
		break;
	case 'startDay':
		startDay();
		break;
	case 'checkHours':
		checkHours();
		break;
	case 'checkIfDayStarted':
		checkIfDayStarted(getUser());
		break;
	case 'stopDay':
		stopDay(getUser());
		break;
	case 'workedHours':
		checkWorkedHours(getUser());
		break;
	default:
		# code...
		echo 'bad request bitch';
		break;
}
function getUser(){
	return filterOutStuff($_POST['user']);
}
function getPass(){
	return filterOutStuff($_POST['pass']);
}
function filterOutStuff($var){
	$var2 = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($var, ENT_QUOTES));
	return $var2;
}
function checkWorkedHours($user){
	$m = new MongoClient();
	$db = $m->db;
	$test = $db->test;
	$results = $test->find(buildQuery(getUser()),array('id','fulltime'));
	$begin = new DateTime($results[0]['fulltime']);
	$end = new DateTime($results[1]['fulltime']);
	$diff = $begin->diff($end);
	$status = padezi($diff->h,$diff->i);
	echo json_encode(array('vrijeme'=>$status));

}
function stopDay($user){
	//$m = new MongoClient();
	//$db = $m->db;
	/*$test = $m->$db->$test;
	$query = buildQuery(getUser());
	$query['']*/
	startDay();
}
function checkIfDayStarted($user){
	$m = new MongoClient();
	$db = $m->db;
	$test = $db->test;
	$results = $test->find(buildQuery(getUser()),array('id'));
	if(count($results)){
		$output = array('logged'=>'true','loggedOut'=>'false');
		if(count($results)>1){
			$output['loggedOut'] = 'true';
		}
		
		echo json_encode($output);
	}

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
	$user = getUser();
	$date = explode(' ',getTime());
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
	//return date('d F Y H:i');
}
function getFormatedTime(){
	return date('Y-m-d H:i');	
}
function buildQuery($user){
	$time = explode(" ",getTime());
	return array('day'=>$time[0],'month'=>$time[1],'year'=>$time[2],'user'=>$user);
}

function padezi($h,$m){
	$status = '';
	if($h > 0){
		//$h = $diff->h;
		if($h == 1)
			$status .= strval($h).' sat';
		elseif($h <= 4)
			$status .= strval($h).' sata';
		elseif($h > 4)
			$status .= strval($h).' sati';
	}
	if($m > 0){
		//$m = $diff->i;
		if($m >= 5 and $m <= 20)
			$status .= ' i '.strval($m).' minuta...';
		elseif($m%10 == 1)
			$status .= ' i '.strval($m).' minutu...';
		elseif($m%10 <= 4)
			$status .= ' i '.strval($m).' minute...';
		elseif($m%10 > 4 or $m%10 == 0)
			$status .= ' i '.strval($m).' minuta...';
	}
	if($h < 1)
		$status = str_replace(" i ", "", $status);
	return $status;
}

function checkHours(){
	$user = getUser();
	$m = new MongoClient();
	$db = $m->db->test;
	$result = $db->findOne(buildQuery($user));
	$then = new DateTime($result['fulltime']);
	$now = new DateTime(getFormatedTime());
	$diff = $now->diff($then);
	$status = padezi($diff->h,$diff->i);
	//$output = array('status'=>'ok','vrijeme'=>$then->format('l F Y H:i').' '.$now->format('l F Y H:i'));
	//$output = array('status'=>'ok','vrijeme'=>strval($diff->h).' '.strval($diff->i).' status '.$status);
	$output = array('status'=>'ok','vrijeme'=>$status,'fullinfo'=>$then->format('l F Y H:i ').$now->format('l F Y H:i'));
	echo json_encode($output);
		
}
?>