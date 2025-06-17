<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
		<link rel="shortcut icon" href="<?= base_url() ?>assets/images/fab1.ico" type="image/x-icon" />
		<title><?= $page_title; ?></title>
		<link rel="stylesheet" href="//data.16180track.com/resource/css/normalize.css">
		<link rel="stylesheet" href="//data.16180track.com/resource/css/base.css">
		<link rel="stylesheet" href="//data.16180track.com/resource/css/bootstrap.min.css">
		<link rel="stylesheet" href="//data.16180track.com/resource/css/font-awesome.min.css">
		<link rel="stylesheet" href="<?= base_url()?>assets/css/login.css">
		<!--CSS3 Media Queries (for IE 6-8)-->
		<!--[if lte IE 8]>
	<script src="js/respond.min.js"></script>
	<![endif]-->
		<!-- Compatible with HTML5 tags -->
	</head>
	
	<body class="login-bg login-bg-default">
		<div class="login">
			<h1 class="logo-login">
				<img src="<?= base_url()?>assets/images/mgikk-logo.png"  alt="Magikk" style="padding-left: 2.4em;"/>
			</h1>
			<div> 
				<span class="form-tip pa" id="tipsmsg" style="display:inline-block;margin-top: 18em;;">
					<?php if(isset($errmsg) && !empty($errmsg)){?>
					<?= $errmsg;?>
					<?php } ?>
					<?= esc(session()->getFlashdata('msg')) ?>
				</span>
				<form id="userlogin" role="form" method="post" action="<?= base_url('users'); ?>" autocomplete="off" novalidate="true">
					<?= csrf_field() ?>
					<div class="form-group form-username">               
						<input class="form-control username" id="exampleInputEmail1" type="text" aria-describedby="emailHelp" placeholder="Username" required maxlength="50" name="username">
					</div>
					<div class="form-group form-password">
						 <input class="form-control password" id="exampleInputPassword1" type="password" name="password" placeholder="Password" maxlength="20" required>
					</div>
				   	<div class="form-checkbox" style="margin-top: -10px"> 
						<span class="fl"> <a href="<?= base_url('register'); ?>" class="js-retrieve-password" title="Create Account"> Create Account </a> </span>
						<span class="fr"> <a href="<?= base_url('forgetpassword'); ?>" class="js-retrieve-password" title="Forget Password?"> Forgot Password? </a> </span>
						<!-- <span class="checkbox pointer checked">
						<input type="hidden"   id="ispassChang" value="0">
						<input type="checkbox" id="checkbox" name="checkbox">
						</span>
						<label for="checkbox">&nbsp;&nbsp;Remember me</label>-->
					</div>
					<div class="form-group">
						<button type="submit" id="logins"  name="login" class="btn btn-default btn-block">Login</button>
						<!--<button type="button" id="load" disabled="disabled" style="display: none;" class="btn btn-default btn-block"><i class="fa fa-spinner fa-pulse"></i>&nbsp;Login</button>-->
					</div>
				</form>
			</div>
			<span class="mappin"><i class="shenzhen"></i><i class="beijing"></i></i><i class="washington"></i><i class="losangeles"></i><i class="santiago"></i></span> </div>
			<script src="<?= base_url() ?>assets/vendor/jquery/jquery.min.js"></script>        
			<script src="<?= base_url() ?>assets/vendor/jqueryvalidation/jquery.validate.js" type="text/javascript"></script>
			<script src="<?= base_url() ?>assets/vendor/jqueryvalidation/additional-methods.js" type="text/javascript"></script>
			<script>
				$(document).ready(function(){
					$("#userlogin").validate();
				});
			</script>
	</body>
</html>
