<!DOCTYPE html>
<html lang="en">
<head>
<title>Forgot Password</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Bootstrap styles -->
<!--    Saheb Wordpress Theme   -->
<link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,500,700,800' rel='stylesheet' type='text/css'>
<!-- Bootstrap and Font Awesome css -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<!-- Css animations  -->
<link href="<?php echo base_url() ?>assets/account/css/animate.css" rel="stylesheet">
<!-- Theme stylesheet, if possible do not edit this stylesheet -->
<link href="<?php echo base_url() ?>assets/account/css/style.default.css" rel="stylesheet" id="theme-stylesheet">
<!-- Custom stylesheet - for your changes -->
<link href="<?php echo base_url() ?>assets/account/css/custom.css" rel="stylesheet">
<!-- Responsivity for older IE -->
<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<!-- Favicon and apple touch icons-->
<link rel="shortcut icon" href="<?php echo base_url() ?>assets/account/img/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" sizes="57x57" href="<?php echo base_url() ?>assets/account/img/apple-touch-icon-57x57.png" />
<!-- owl carousel css -->
<link href="<?php echo base_url() ?>assets/account/css/owl.carousel.css" rel="stylesheet">
<link href="<?php echo base_url() ?>assets/account/css/owl.theme.css" rel="stylesheet">
<!-- Generic page styles -->
<!--<link rel="stylesheet" href="<?php echo base_url() ?>assets/uploader/css/style.css">-->
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="<?php echo base_url() ?>assets/uploader/css/jquery.fileupload.css">
<script>               
	   var BASEURL = "<?php echo site_url('/')?>";
	   var BASEURLIMG = "<?php echo base_url()?>";
	</script>
</head>
<body>
<div id="all">
  <header>
    <!-- *** TOP ***
_________________________________________________________ -->
    <div id="top">
      <div class="container">
        <div class="row">
          <div class="col-xs-5 contact">
            <p class="hidden-sm hidden-xs">Contact us on +033 6533 0925 or info@9trax.com.</p>
            <p class="hidden-md hidden-lg"><a href="#" data-animate-hover="pulse"><i class="fa fa-phone"></i></a> <a href="#" data-animate-hover="pulse"><i class="fa fa-envelope"></i></a> </p>
          </div>
          <div class="col-xs-7">
            <div class="social"> <a href="#" class="external facebook" data-animate-hover="pulse"><i class="fa fa-facebook"></i></a> <a href="#" class="external gplus" data-animate-hover="pulse"><i class="fa fa-google-plus"></i></a> <a href="#" class="external twitter" data-animate-hover="pulse"><i class="fa fa-twitter"></i></a> <a href="#" class="email" data-animate-hover="pulse"><i class="fa fa-envelope"></i></a> </div>
            <div class="login"><a href="http://live.9trax.com/" target="_blank"><i class="fa fa-sign-in"></i> <span class="hidden-xs text-uppercase">Live Demo</span></a> <a href="https://track.9trax.com/" target="_blank"><i class="fa fa-sign-in"></i> <span class="hidden-xs text-uppercase">Sign in</span></a> </div>
          </div>
        </div>
      </div>
    </div>
    <!-- *** TOP END *** -->
    <!-- *** NAVBAR ***
    _________________________________________________________ -->
    <div class="navbar-affixed-top" data-spy="affix" data-offset-top="200">
      <div class="navbar navbar-default yamm" role="navigation" id="navbar">
        <div class="container">
          <div class="navbar-header"> <a class="navbar-brand home" href="index.html" style="padding:0;"> <img src="<?php echo base_url() ?>assets/account/img/9TRAX_LOGO.svg" alt="" class="hidden-xs hidden-sm"> <img src="<?php echo base_url() ?>assets/account/img/9TRAX_LOGO.svg" alt="" class="visible-xs visible-sm"><span class="sr-only">go to homepage</span> </a>
            <div class="navbar-buttons">
              <button type="button" class="navbar-toggle btn-template-main" data-toggle="collapse" data-target="#navigation"> <span class="sr-only">Toggle navigation</span> <i class="fa fa-align-justify"></i> </button>
            </div>
          </div>
          <!--/.navbar-header -->
          <div class="navbar-collapse collapse" id="navigation">
            <ul class="nav navbar-nav navbar-right">
              <li class="dropdown hactive"> <a href="https://9trax.com/">Home</a> </li>
              <!--<li class="dropdown"> <a href="javascript: void(0)" class="dropdown-toggle" data-toggle="dropdown">How it works <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">How to register</a> </li>
                <li><a href="#">Live Demo</a> </li>
              </ul>
            </li>-->
              <li class="dropdown"> <a target="_blank" href="https://live.9trax.com/">How it works</a></li>
              <li class="dropdown spactive"> <a href="https://9trax.com/specifications/">Specifications</a> </li>
              <!--<li class="dropdown spactive"> <a href="javascript: void(0)" class="dropdown-toggle" data-toggle="dropdown">Specifications <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Tech Specifications</a> </li>
                <li><a href="#">App Specifications</a> </li>
              </ul>
            </li>-->
              <li class="dropdown"> <a target="_blank" href="https://track.9trax.com/index.php/register">Get started</a></li>
              <!--<ul class="dropdown-menu">
                <li><a href="#">Register yourself</a> </li>
                <li><a href="#">Free trial</a> </li>
              </ul>
            </li>-->
              <li class="dropdown"> <a href="https://9trax.com/order-confirmation/">Shop</a> </li>
              <li class="dropdown prcactive"> <a href="https://9trax.com/pricing/">Pricing</a> </li>
              <li class="dropdown cactive"> <a href="https://9trax.com/contact/">Contact</a> </li>
            </ul>
          </div>
          <!--/.nav-collapse -->
          <div class="collapse clearfix" id="search">
            <form class="navbar-form" role="search">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Search">
                <span class="input-group-btn">
                <button type="submit" class="btn btn-template-main"><i class="fa fa-search"></i></button>
                </span> </div>
            </form>
          </div>
          <!--/.nav-collapse -->
        </div>
      </div>
      <!-- /#navbar -->
    </div>
    <!-- *** NAVBAR END *** -->
  </header>
  <div id="heading-breadcrumbs" class="no-mb">
    <div class="container">
      <div class="row">
        <div class="col-md-7">
          <h1>Forgot Password</h1>
        </div>
        <div class="col-md-5">
          <ul class="breadcrumb">
            <li><a href="https://9trax.com/">Home</a> </li>
            <li>Forgot Password</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
  
  <div class="error_msg" style="padding-top: 3em;padding-left: 1em;">
		<span style="color:red" id="error_msg"></span>
	</div>
  
  <div class="col-md-5" style="padding-top:40px;">
  
        
        <div class="form-group">
          <label for="name">User Name</label>
          <input type="text" class="form-control" id="username" placeholder="Enter Username" name="username" required>
        </div>
    
    	 <button type="button" class="btn btn-default" id="forgetpassFormbtn">Submit</button>
  </div>
  
    <div class="clearfix"></div>
   
    <div style="padding: 2em;"></div>
  </div>
  <div id="copyright">
    <div class="container">
      <div class="col-md-12">
        <p class="pull-left">&copy; 2018. 9Trax, <a href="https://9trax.com/terms-and-conditions/">Terms and Conditions</a>, <a href="https://9trax.com/privacy-policy/">Privacy Policy</a></p>
        <p class="pull-right">Powred by <a href="http://stesalitsystems.com">Stesalit</a> &amp; <a href="http://sxtreo.com">Sxtreo</a>
          <!-- Not removing these links is part of the license conditions of the template. Thanks for understanding :) If you want to use the template without the attribution links, you can do so after supporting further themes development at https://bootstrapious.com/donate  -->
        </p>
      </div>
    </div>
  </div>
  <!-- /#copyright -->
  <!-- *** COPYRIGHT END *** -->
</div>
<!-- /#all -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="<?php echo base_url() ?>assets/uploader/js/vendor/jquery.ui.widget.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?php echo base_url() ?>assets/uploader/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="<?php echo base_url() ?>assets/uploader/js/jquery.fileupload.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
/*jslint unparam: true */
/*global window, $ */
$(function () {
    $("#forgetpassFormbtn").on('click',function () {
		var chk_flag = 1;
		var username = $('#username').val();

		if(username == ''){
			chk_flag = 0;
			$('#username').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
		}
		
		if(chk_flag == 1){
			var url = '<?php echo site_url('account/passwordreset'); ?>';
			$.ajax({
			  method: "POST",
			  url: url,
			  data: { username: username },
			  success: function(res) {
          console.log(res.msg);

				// var dataJson = JSON.parse(msg);
				$('#error_msg').html(res.msg);
			  }
			});
		}
	});
});
</script>
</body>
</html>
