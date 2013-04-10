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
			<p id="proveo">Sati na poslu: &nbsp</p><p id="vrijeme"></p>
			<form id="zapocniPoso">
				<button id="kreni">Pocni dan</button>
				<button id="stani">Zavrsi dan</button>
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
		//console.log('repositioning');
		$("#loginimage").css('position','fixed');
		$("#loginimage").css('top','20%');
		$("#loginimage").css('left','40%');

		$(".login").css('position','fixed');
		$(".login").css('top','60%');
		$(".login").css('left','35%');
		$("body").css('background','black');
	}
	$(window).resize(function(){
		reposition();
	});
	$("#proveo").click(function(){		
		provjeri();
	});
	function skipLogin(){
		$("#loginpage").toggle(false);
		$("#proveo").toggle(true);
		//$("#workerpage").toggle(false);
	}		
	function provjeri(){
		//mafaka broji ga, udri mujoo hohohooh
		if($.odlogovan)
			return false;
		$.ajax({
			url:'process.php',
			data:'action=checkHours&user='+$.user,
			type: 'POST',
			dataType: "json"
		}).done(function(data){
			console.log(data);
			//console.log(data.vrijeme);
			//if(data.vrijeme)
			$("#proveo").text("Na poslu vec "+data.vrijeme);
		});

	}
	function ukupnoVrijeme(){
		$.ajax({
			url:'process.php',
			data:'action=workedHours&user='+$.user,
			type:'POST',
			dataType:'json'
		}).done(function(data){
			console.log(data);
			$("#proveo").text('Odlogovan, ukupno '+data.vrijeme)
		});
	}
	function checkIfDayStarted(){
		$.ajax({
			url:'process.php',
			data:'action=checkIfDayStarted&user='+$.user,
			type:'POST',
			dataType:'json'
		}).done(function(data){
			console.log(data);
			if(data.logged == 'true'){
				$("#kreni").attr('disabled','disabled');
				if(data.loggedOut == 'false'){
					provjeri();
				}
				else{
					$("#stani").attr('disabled','disabled');
					//$("#proveo").attr('disabled','disabled');
					$.odlogovan = true;
					ukupnoVrijeme();
				}					
			}
		});
	}
	$(function(){
		$.user='lol';
		$("#workerpage").toggle(false);
		$("#loginimage").css('position','fixed');
		$("#loginimage").css('top','20%');
		$("#loginimage").css('left','40%');
		$(".login").css('position','fixed');
		$(".login").css('top','60%');
		$(".login").css('left','35%');
		$("body").css('background','black');		
	});
	$('#getResult').on('click',function(){
		console.log($("#loginform").serialize());
		$.ajax({
			type: "POST",
			url: "process.php",
			data: $("#loginform").serialize()+'&action=login'
		}).done(function(data){
			if(data != 'success')
				return false;
			$("#workerpage").tabs();
			document.title = 'Home'
			//$("#date").append(day+hours+minutes);
			$("#date").append($.datepicker.formatDate('dd M yy', new Date()));
			$("#zapocniPoso").on('submit',function(){
				return false;
			});	
			$("#result").html(data);
			$("#loginpage").toggle(false);
			$("#workerpage").toggle(true);
			//$("#tabs").tabs();
			checkIfDayStarted();
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
	$("#stani").on('click',function(){
		//console.log();
		$.ajax({
			url:'process.php',
			data:'action=stopDay&user='+$.user,
			type:'POST',
			dataType:'json'
		}).done(function(data){
			console.log(data.status);
			$("#stani").attr('disabled','disabled');
		});
	});
	$("#kreni").on('click',function(){
			$.user = 'lol';
			$.ajax({
				url:'process.php',
				data:'action=startDay&user='+$.user,
				type:'POST',
				dataType:'json'
			}).done(function(data){
				console.log(data);
				//return false;
				$("#proveo").toggle(true);
				$("#proveo").val('Na poslu vec: '+data.vrijeme);
				console.log(data.status);
			});
		});
</script>
</html>