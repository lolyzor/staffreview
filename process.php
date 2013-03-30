<?php if(isset($_POST['user'])){
	$user = mysql_real_escape_string($_POST['user']);
	$pass = mysql_real_escape_string($_POST['pass']);

} ?>