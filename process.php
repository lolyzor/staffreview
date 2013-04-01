<?php 
if(isset($_POST['user'])){
	$user = mysql_real_escape_string($_POST['user']);
	$pass = mysql_real_escape_string($_POST['pass']);

} 

$m = new MongoClient();
$db = $m->db;
$test = $db->test;
//$test->find("{'title':'prd'}");
$document = ['title'=>'Me and smoto','author'=>'Bill prd'];
$document2 = ['title'=>'Me and smoto2','author'=>'Bill and smrd'];
//$test->insert($document2);
$results = $test->find();
foreach ($results as $document) {
		echo $document['title'];
}


?>