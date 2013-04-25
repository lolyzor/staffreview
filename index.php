<!DOCTYPE HTML>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/jqueryui.css">
    <style>
    .ui-button-text {
	        font-size: .6em;
	    	}
    </style>
</head>
<body>
	<!-- <h1 class="h1login">Login below</h1> -->
	<div id="loginpage">
	<!-- <img src="img/login.png" alt="loginimage" id="loginimage">	 -->
	<div id="tabs2">
		<ul>
			<li><a href="#login">Login</a></li>
			<li><a href="#admin">Admin</a></li>
		</ul>
	<div id="login" loginclass="login" style="margin-left:10px;">
		<img src="img/login.png" alt="loginimage" id="1" style="margin-left:100px;">
		<form id="loginform">
			<label for="username">Username</label>
			<label for="password" style="margin-left:115px;">Password</label>
			<br>
			<input type="text" name="user" id="username">
			<input type="text" name="pass" id="password">
		</form>
		<button id="getResult" style="margin-top:5px;">Login</button>
		<button id="registration" style="margin-top:5px;margin-left:115px;">Registration</button>
		<div id="result"></div>
	</div> <!-- login form -->
	<div id="admin">
		<form id="adminlogin">
			<label for="username">Username</label>
			<label for="password" style="margin-left:115px;">Password</label>
			<br>
			<input type="text" name="user">
			<input type="text" name="pass">
			<br>			
		</form>
		<button style="margin-top:5px;" id="adminLogin">Login</button>
		<button style="margin-top:5px;margin-left:85px;" id="adminReg">Reg</button>
	</div>
</div> <!-- end of tabs2 -->
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
			<form id="logFirme" action="#">
			<div style="">
				<p style="display:inline-block;">Izaberi firmu</p>	
				
				<select name="firma" id="firme" style="">
					<option value="firma1">Firma1</option>
					<option value="firma1">Firma1</option>
					<option value="firma1">Firma1</option>
				</select>				
			</div>
			<div>
				<p style="display:inline-block;">Sati provedeno na servisu</p>
				<input type="numeric" name="sati" id="servisSati" style="display:inline-block;">
				<p style="display:inline-block;">i minuta</p>
				<input type="numeric" name="minuta" id="servisMinuta">
				<button id="dodajVrijemeFirmi">Update</button>
			</div>			
			</form>
			</div>
			<div id="tabs-3">
			<p>Informacije</p>
			<div>
				<label for="email" style="display:inline-block;">Email</label>
				<input type="text" name="email">	
			</div>
			<div style="display:inline-block;">
				<label for="broj" style="display:inline-block;">Broj &nbsp;</label>
				<input type="numeric" name="broj">	
			</div>
			<button id="profilupdate">Update</button>			
			</div> <!-- end of tabs 3 -->
			</div> <!-- end of divs tabs -->
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
			$("#proveo").text("Na poslu "+data.vrijeme);
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
	function listaFirmi(data){
		var html = '';
        $.each(data,function(index,value){
          html+='<option>'+value+'</option>';
        });
		$("#firme").html(html);
	}
	function ajaxBitch(data,action){
		$.ajax({
			url:'process.php',
			data:data,
			type:'POST',
			dataType:'JSON'
		}).done(function(data){
			if(action="listafirmi")
				listaFirmi(data);
			console.log(data);
		});
	}
	$(function(){
		$("#tabs2").tabs();
		//$.user=$("#username").val();
		$("#workerpage").toggle(false);
		$("#loginimage").css('position','fixed');
		$("#loginimage").css('top','20%');
		$("#loginimage").css('left','40%');
		$(".login").css('position','fixed');
		$(".login").css('top','60%');
		$(".login").css('left','35%');
		$("body").css('background','black');		
		$("#servisSati").spinner();
		$("#servisMinuta").spinner();
		$("#dodajVrijemeFirmi").button({icons:{primary:"ui-icon-gear"}});
		$("#profilupdate").button({icons:{primary:"ui-icon-gear"}});
		$("#logFirme").on('submit',function(){
			//console.log();
			return false;
		});
	});
	$('#getResult').on('click',function(){
		console.log($("#loginform").serialize());
		$.user = $("#username").val();
		$.pass = $("#password").val();
		if(!$.user || !$.pass){
			$("#loginform").effect('shake',{},500);
			return false;
		}
		$.ajax({
			type: "POST",
			url: "process.php",
			data: $("#loginform").serialize()+'&action=login'
		}).done(function(data){
			if(data != 'success'){
				$("#loginform").effect('shake',{},500);
				return false;
			}
				
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
			url: "process.php",
			data: $("#loginform").serialize()+'&action=registration',
			dataType:"json"
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
			//$.user = 'lol';
			$.ajax({
				url:'process.php',
				data:'action=startDay&user='+$.user,
				type:'POST',
				dataType:'json'
			}).done(function(data){
				console.log(data);
				//return false;
				$("#proveo").toggle(true);
				$("#proveo").val('Na poslu: '+data.vrijeme);
				provjeri();
				console.log(data.status);
			});
		$("#kreni").attr('disabled','disabled');
		});
	$("#adminLogin").on('click',function(){
		//console.log();
		$.ajax({
			url:'process.php',
			data: $("#adminlogin").serialize()+'&action=adminlogin',
			type:'POST',
			dataType:'json'
		}).done(function(data){
			console.log(data);
			if(data.login == 'success'){
				window.location.href='adminpanel.html';
			}
			else{
				$("#adminLogin").effect('shake',{},500);
			}
		});
	});
	$("#dodajVrijemeFirmi").on('click',function(){
		//console.log();
		data = $("#logFirme").serialize()+'&action=logFirme'+'&user='+$.user;
		ajaxBitch(data,'logFirme');
	});
	$("#workerpage").on('tabsactivate',function(event,ui){
      //console.log(event);
      //console.log(ui.newTab[0].textContent);
      var tabName = ui.newTab[0].textContent;
      console.log(tabName);
      //return false;
      if (tabName == 'Servis')
      	ajaxBitch('action=listaFirmi','listafirmi');
    });
</script>
</html>