<!DOCTYPE HTML>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/jqueryui.css">
</head>
<body>
	<!-- <h1 class="h1login">Login below</h1> -->
	<div id="loginpage">
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
	</div> <!-- login form -->
	</div> <!-- end of login page -->
	<div id="workerpage">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1">Radni dan</a></li>
				<li><a href="#tabs-2">Servis</a></li>
				<li><a href="#tabs-3">Profil</a></li>
			</ul>
			<div id="tabs-1">
			<p id="date">Danas je &nbsp</p>
			<form id="zapocniPoso">
				<button id="kreni">Pocni dan</button>
				<button id="stani">Kraj</button>
			</form>
			</div>
			<div id="tabs-2">
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eaque consequatur repellendus corrupti tempore? Dolore, reprehenderit, dolor laboriosam exercitationem odit ad harum quo similique obcaecati eos ipsam dolorum consequatur deserunt perferendis.</p>
			</div>
			<div id="tabs-3">
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Hic, pariatur, totam, ipsa, perferendis inventore ducimus similique maxime iure quos officia nulla ad soluta ipsam aliquid quibusdam ea in voluptas reprehenderit.</p>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Itaque, provident, officia est incidunt ratione quia expedita eligendi voluptatibus voluptate quod rem numquam obcaecati facere neque sint vel nulla laborum accusantium.</p>
			</div>
			</div>
	</div>
	<?php //echo phpinfo(); ?>
	</body>
<script src="js/jquery.js"></script>
<script src="js/jqueryui.js"></script>
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
	function skipLogin(){
		$("#loginpage").toggle(false);
		//$("#workerpage").toggle(false);
		$("#workerpage").tabs();
		var today = new Date();
		$("#date").append(today);
		$("#zapocniPoso").on('submit',function(){
			return false;
		});

	}
	$(function(){
		skipLogin();
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
			$("#loginpage").toggle();
			$("#workerpage").toggle();
			$("#tabs").tabs();
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