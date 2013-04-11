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
	case 'adminlogin':
		adminLogin(getUser());
		break;
	case 'kolkonaposlu':
		kolkoNaPoslu();
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
function kolkoNaPoslu(){
	$m = new MongoClient();
	$db = $m->db;
	$test = $db->test;
	$result = $test->find(buildQuery(),['id','user']);
	$number = $result->count();
	$users = [];
	$times = [];
	$status = [];
	while($result->hasNext()){
		$user = $result->getNext();
		if(!in_array($user['user'],$users)){
			array_push($users, $user['user']);
		}
	}
	foreach ($users as $user) {
		array_push($times, checkWorkedHours($user,true));
		array_push($status, checkIfDayStarted($user,true));
	}

	echo json_encode(['status'=>'ok','number'=>count($users),'users'=>$users,'times'=>$times,'status'=>$status]);
}
function adminLogin($user){
	$result = array('login'=>'success');
	echo json_encode($result);
}
function checkWorkedHours($user,$mode=NULL){
	$m = new MongoClient();
	$db = $m->db;
	$test = $db->test;
	$results = $test->find(buildQuery($user),array('id','fulltime'));
	//$results = $results->();
	$tmp1 = $results->getNext();
	$tmp2 = $results->getNext();
	$begin = new DateTime($tmp1['fulltime']);
	$end = new DateTime($tmp2['fulltime']);
	$diff = $begin->diff($end);
	$status = padezi($diff->h,$diff->i);
	if($mode === NULL)
		echo json_encode(array('vrijeme'=>$status));
	else
		return $status;

}
function stopDay($user){
	startDay();
}
function checkIfDayStarted($user,$mode=NULL){
	$m = new MongoClient();
	$db = $m->db;
	$test = $db->test;
	$results = $test->find(buildQuery($user),array('id'));
	//$results = $test->find(buildQuery(getUser()),array('id')).count(false);
	//$result = $results->toArray();
	if($results->hasNext()){
		$output = array('logged'=>'true','loggedOut'=>'false');
		$output['count'] = $results->count();
		if($results->count()>1){
			$output['loggedOut'] = 'true';
		}
	}
	if($mode === NULL)
		echo json_encode($output);
	else{
		$var = $output['loggedOut'];
		if($var == 'true')
			$var = ' (zavrsio s poslom danas)';
		if($var == 'false')
			$var = ' ';
		return $var;
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
	$reply = array('status'=>'insreted '.$user,'vrijeme'=>$vrijeme);
	echo json_encode($reply);
}
function getTime(){
	return date('l F Y H:i');
	//return date('d F Y H:i');
}
function getFormatedTime(){
	return date('Y-m-d H:i');	
}
function buildQuery($user=""){
	$time = explode(" ",getTime());
	if($user)
		return array('day'=>$time[0],'month'=>$time[1],'year'=>$time[2],'user'=>$user);
	else
		return array('day'=>$time[0],'month'=>$time[1],'year'=>$time[2]);
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
	if($m < 1 and $h < 1)
		$status = 'tek poceo...';
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