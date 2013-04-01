<!DOCTYPE HTML>
<html>
<head>
    <title>Login</title>
</head>
<body>
	<!-- <h1 class="h1login">Login below</h1> -->
	<img src="img/login.png" alt="loginimage" id="loginimage">	
	<div class="login">
		<form id="loginform">
			<label for="username">Username</label>
			<label for="password" style="margin-left:85px;">Password</label>
			<br>
			<input type="text" name="user">
			<input type="text" name="pass">
		</form>
		<button id="getResult" style="margin-top:5px;">Login</button>
		<button id="registration" style="margin-top:5px;margin-left:85px;">Registration</button>
		<div id="result"></div>
	</div>
	<?php //echo phpinfo(); ?>
	</body>
<script src="js/jquery.js"></script>
<script>
	jQuery.fn.center = function () {
	    this.css("position","absolute");
	    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + 
	                                                $(window).scrollTop()) + "px");
	    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + 
	                                                $(window).scrollLeft()) + "px");
	    return this;
	}
	function reposition(){
		console.log('repositioning');
		$("#loginimage").css('position','fixed');
		$("#loginimage").css('top','20%');
		$("#loginimage").css('left','40%');

		$(".login").css('position','fixed');
		$(".login").css('top','60%');
		$(".login").css('left','35%');
		$("body").css('background','white');
	}
	$(window).resize(function(){
		reposition();
	});
	$(function(){
		console.log('ready');
		console.log($(window).width());
		console.log($(document).width());
		//$(".h1login").center();
		$("#loginimage").css('position','fixed');
		$("#loginimage").css('top','20%');
		$("#loginimage").css('left','40%');

		$(".login").css('position','fixed');
		$(".login").css('top','60%');
		$(".login").css('left','35%');
		$("body").css('background','white');
	});
	$('#getResult').on('click',function(){
		console.log($("#loginform").serialize());
		$.ajax({
			type: "POST",
			url: "process.php",
			data: $("#loginform").serialize()
		}).done(function(data){
			$("#result").html(data);
		});
	});
	$('#registration').on('click',function(){
		$.ajax({
			type: "POST",
			url: "registration.php",
			data: $("#loginform").serialize()
		}).done(function(data){
			$("#result").html(data);
		});
	});
</script>
</html>