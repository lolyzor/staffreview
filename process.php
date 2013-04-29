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
		checkHours(getUser());
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
	case 'registration':
		regajUsera(getUser(),getPass());
		break;
	case 'dodajFirmu':
		dodajFirmu(getFirm());
		break;
	case 'logujFirmu':
		logujFirmu(getFirm());
		break;
	case 'listaFirmi':
		listaFirmi();
		break;
	case 'logFirme':
		logFirme(getUser());
		break;
	default:
		# code...
		echo 'bad request bitch';
		break;
}
function getFirm(){
	return filterOutStuff($_POST['firma']);
}
function getFirmTime(){
	return filterOutStuff($_POST['vrijeme']);	
}
function getUser(){
	return filterOutStuff($_POST['user']);
}
function getPass(){
	return filterOutStuff($_POST['pass']);
}
function filterOutStuff($var){
	$var = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($var, ENT_QUOTES));
	return $var;	
}
function filterArray($array){
	$newArray = array();
	foreach($array as $elem){
		array_push($newArray,filterOutStuff($elem));
	}
	return $newArray;
}
function returnOutput($array){
	echo json_encode($array);
}
function logFirme($user){
    //koja je razlika izmedju logFirme i logujFirme ? wtf is this
	list($firma,$sati,$minuta) = filterArray([$_POST['firma'],$_POST['sati'],$_POST['minuta']]); 
	$m = new MongoClient();
	$db = $m->db;
	$logfirme = $db->firmelogs;
    $date = getFormatedTime();
    $seperatedDate = getTime();
    list($day,$month,$year) = explode(" ",$seperatedDate);
	$logfirme->insert(['firma'=>$firma,'sati'=>$sati,'minuta'=>$minuta,'user'=>$user,'day'=>$day,'month'=>$month,'year'=>$year,'fullDate'=>$date]);
	$output = ['stats'=>'inserted'];
	returnOutput($output);
}
function logUser($user,$hours){
    //log usera, get worked hours and insert it !
    //this should be called when he logges out ! two ajax requests ?
    //$time = explode(" ",getTime());
	//if($user)
	//	return array('day'=>$time[0],'month'=>$time[1],'year'=>$time[2],'user'=>$user);
    $m = new MongoClient();
	$db = $m->db;
	$logfirme = $db->userlogs;
	$date = getFormatedTime();
    $seperatedDate = getTime();
    list($day,$month,$year) = explode(" ",$seperatedDate);
	$userlogs->insert(['user'=>$user,'sati'=>$sati,'minuta'=>$minuta,'day'=>$day,'month'=>$month,'year'=>$year,'fullDate'=>$date]);
	$output = ['stats'=>'inserted'];
	returnOutput($output);

	}
function listaFirmi(){
	$m = new MongoClient();
	$db = $m->db;
	$firme = $db->firme;
	$cursor = $firme->find([],['_id'=>0]);
	$firme = [];
	foreach($cursor as $firma){
		if($firma['ime']!='undefined')
			array_push($firme, $firma['ime']);
	}
	returnOutput($firme);
}
function dodajFirmu($firma){
	$m = new MongoClient();
	$db = $m->db;
	$firme = $db->firme;
	$firme->insert(['ime'=>$firma]);
	returnOutput(['status'=>'inserted']);
}
function logujFirmu($firma,$sati,$datum=NULL){
	$m = new MongoClient();
	$db = $m->db;
	$firmelog = $db->firmelogs;
	if($datum===NULL){
		$datum = getFormatedTime();
	}
	$firmelog->insert(['firma'=>$firma,'sati'=>$sati,'datum'=>$datum]);
	returnOutput(['status'=>'inserted']);
}
function regajUsera($user,$pass){
	$m = new MongoClient();
	$db = $m->db;
	$users = $db->users;
	$users->insert(['user'=>$user,'pass'=>$pass]);
	echo json_encode(['statsu'=>'inserted']);
}

function login($user,$pass){
	$m = new MongoClient();
	$db = $m->db;
	$users = $db->users;
	//$query = array("user"=>array('$eq'=>$user),"pass"=>array('$eq'=>$pass));
	$query = array("user"=>$user,"pass"=>$pass);
	$results = $users->find(['user'=>$user,'pass'=>$pass],['id']);
	if($results->hasNext()){
		echo 'success';
	}
	else{
		//echo 'failed '.$results->count().' '.json_encode($user->find()).' '.json_encode($query);
		echo 'failed ';
	}
}
function kolkoNaPoslu(){
	$m = new MongoClient();
	$db = $m->db;
	$userlogs = $db->userlogs;
	$result = $userlogs->find(buildQuery(),['id','user']);
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
		array_push($times, checkHours($user,true));
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
	$userlogs = $db->userlogs;
	$results = $userlogs->find(buildQuery($user),array('id','fulltime'));
	//$results = $results->();
    if($results->count()<2){
        $status = 8;
    }
    //check if this works, the log out is a problem 
    else{
        $tmp1 = $results->getNext();
        $tmp2 = $results->getNext();
        $begin = new DateTime($tmp1['fulltime']);
        $end = new DateTime($tmp2['fulltime']);
        $diff = $begin->diff($end);
        $status = padezi($diff->h,$diff->i);
    }
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
	$userlogs = $db->userlogs;
	$results = $userlogs->find(buildQuery($user),array('id'));
	//$results = $test->find(buildQuery(getUser()),array('id')).count(false);
	//$result = $results->toArray();
	$output = array('lol'=>$user);
	if($results->hasNext()){
		$output = array('logged'=>'true','loggedOut'=>'false');
		$output['count'] = $results->count();
		if($results->count()>1){
			$output['loggedOut'] = 'true';
			$output['user'] = buildQuery($user);
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

function startDay(){
	$user = getUser();
	$date = explode(' ',getTime());
	$m = new MongoClient();
	$db = $m->db;
	$userlogs = $db->userlogs;
	$query = buildQuery($user);
	$fulltime = getFormatedTime();
	$query['fulltime'] = $fulltime;
	$userlogs->insert($query);
	$vrijeme = 'upravo poceo..';
	$reply = array('status'=>'insreted '.$user,'vrijeme'=>$vrijeme);
	echo json_encode($reply);
}
function getTime(){
	return date('j F Y H:i');
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
		if($m >= 5 and $m <= 20 or $m%10 == 0)
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
		$status = 'manje od minutu...';
	return $status;
}

function checkHours($user,$mod=NULL){
    //$user = getUser();
	$m = new MongoClient();
	$db = $m->db->userlogs;
	$result = $db->findOne(buildQuery($user));
	$then = new DateTime($result['fulltime']);
	$now = new DateTime(getFormatedTime());
	$diff = $now->diff($then);
	$status = padezi($diff->h,$diff->i);
	//$output = array('status'=>'ok','vrijeme'=>$then->format('l F Y H:i').' '.$now->format('l F Y H:i'));
	//$output = array('status'=>'ok','vrijeme'=>strval($diff->h).' '.strval($diff->i).' status '.$status);
	$output = array('status'=>'ok','vrijeme'=>$status,'fullinfo'=>$then->format('l F Y H:i ').$now->format('l F Y H:i'));
    if($mod){
        return $status;
    }
    else{
        echo json_encode($output);
    }
}
?>
