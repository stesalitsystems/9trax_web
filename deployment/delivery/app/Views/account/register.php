<!DOCTYPE html>
<html lang="en">
<head>
<title>Registration</title>
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
	   var BASEURL = "<?php echo base_url()?>";
	   var BASEURLIMG = "<?php echo base_url()?>";
</script>

<style>
span.tt:before {
	content: "";
	border-style: solid;
	border-width: 0 15px 15px 15px;
	border-color:  transparent transparent rgba(0,102,255,.5) transparent;
	height: 0;
	position: absolute;
	top: -17px;
	width: 0;
}

span.tt {
	   border: 2px solid rgba(0, 102, 255, 0.5);
		border-radius: 10px;
		color: #807878;
		display: none;
		position: relative;
		background-color: #FFF;
		padding: 6px;

}

a.cl {
	display: inline-block
}

a.cl:hover + span {
	display: inline-block;
	margin: 0px;
	z-index: 99;
	position: absolute;
	left: 92px;
	top: 31px;
	font-size: 10px;
}
span.tt2:before {
	content: "";
	border-style: solid;
	border-width: 0 15px 15px 15px;
	border-color:  transparent transparent rgba(0,102,255,.5) transparent;
	height: 0;
	position: absolute;
	top: -17px;
	width: 0;
}

span.tt2 {
	   border: 2px solid rgba(0, 102, 255, 0.5);
		border-radius: 10px;
		color: #807878;
		display: none;
		position: relative;
		background-color: #FFF;
		padding: 6px;

}

a.cl2 {
	display: inline-block
}

a.cl2:hover + span {
	display: inline-block;
	margin: 0px;
	z-index: 99;
	position: absolute;
	left: 120px;
	top: 31px;
	font-size: 10px;
}
</style>




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
            <p class="hidden-sm hidden-xs">Contact us on +033 6533 0925 or <a href="mailto:info@9trax.com" style="color: #fff; text-decoration: none;">info@9trax.com</a></p>
            <p class="hidden-md hidden-lg"><a href="#" data-animate-hover="pulse"><i class="fa fa-phone"></i></a> <a href="#" data-animate-hover="pulse"><i class="fa fa-envelope"></i></a> </p>
          </div>
          <div class="col-xs-7">
            <div class="social"><a href="https://www.facebook.com/9trax/" target="_blank" class="external facebook" data-animate-hover="pulse"><i class="fa fa-facebook"></i></a><a href="https://twitter.com/" target="_blank" class="external twitter" data-animate-hover="pulse"><i class="fa fa-twitter"></i></a></div>
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
          <div class="navbar-header"> <a class="navbar-brand home" href="https://9trax.com/"> <img src="<?php echo base_url() ?>assets/account/img/9TRAX_LOGO.svg" alt="" class="hidden-xs hidden-sm"> <img src="<?php echo base_url() ?>assets/account/img/9TRAX_LOGO.svg" alt="" class="visible-xs visible-sm"><span class="sr-only">go to homepage</span> </a>
            <div class="navbar-buttons">
              <button type="button" class="navbar-toggle btn-template-main" data-toggle="collapse" data-target="#navigation"> <span class="sr-only">Toggle navigation</span> <i class="fa fa-align-justify"></i> </button>
            </div>
          </div>
          <!--/.navbar-header -->
          <div class="navbar-collapse collapse" id="navigation">
            <ul class="nav navbar-nav navbar-right">
              <li class="dropdown hactive"><a href="https://9trax.com/">Home</a></li>
             
              <li class="dropdown"><a target="_blank" href="https://9trax.com/how-it-works/">How it works</a></li>
              <li class="dropdown spactive"><a href="https://9trax.com/what-is-9trax/">What is 9trax</a> </li>
              <li class="dropdown spactive"><a href="https://9trax.com/specifications/">Specifications</a></li>
              
              <li class="dropdown"><a href="https://9trax.com/get-started/">Get started</a></li>
              
              <li class="dropdown"><a href="https://9trax.com/shop/">Shop</a></li>
              <?php /* <li class="dropdown prcactive"> <a href="http://9trax.com/pricing/">Pricing</a></li> */ ?>
              <li class="dropdown cactive"><a href="https://9trax.com/contact/">Contact</a></li>
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
          <h1>Registration</h1>
        </div>
        <div class="col-md-5">
          <ul class="breadcrumb">
            <li><a href="https://9trax.com/">Home</a> </li>
            <li>Registration</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
  
  
  
  
  <div class="error_msg" style="padding-top: 3em;padding-left: 1em;"> <span style="color:red" id="error_msg"></span> </div>
  
  <div class="col-md-6" style="padding-top:40px;">
  
        
        <div class="form-group">
          <label for="name">Name <span style="color:red">*</span></label>
          <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" required>
        </div>
        <div class="form-group">
          <label for="mobile">Mobile Number <span style="color:red">*</span></label>
          <input type="text" class="form-control" id="mobile" placeholder="Enter Mobile Number" name="mobile" maxlength="10" required>
        </div>
        <div class="form-group">
          <label for="email">Email <span style="color:red">*</span></label>
          <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" required>
        </div>
        <div class="form-group">
          <label for="address">Address <span style="color:red">*</span></label>
          <textarea class="form-control" rows="5" id="address" name="address" required></textarea>
        </div>
		<div class="form-group">
		  <label for="pin">Pin Number <span style="color:red">*</span></label>
		  <input type="text" class="form-control" id="pin" placeholder="Enter Pin Number" name="pin" maxlength="6" required>
		</div>
    
  </div>
  <div class="col-md-6" style="padding-top:40px;">  

            <div class="form-group">
              <label for="deviceid">Device ID <span style="color:red">*</span></label>
              <input type="text" class="form-control" id="deviceid" placeholder="Enter Device ID" name="deviceid" required>
            </div>
            <div class="form-group">
              <label for="deviceimei">Device IMEI <span style="color:red">*</span></label>
              <input type="text" class="form-control" id="deviceimei" placeholder="Enter Device IMEI" name="deviceimei" required>
            </div>
            <div class="form-group">
              <input type="hidden" name="imgfolder" id="imgfolder" value="<?php echo time();?>"/>
              <label for="deviceimei" style="position:relative;">KYC Document 1 
              
              <a class="cl"><i class="fa fa-question-circle"></i></a>
				<span class="tt">9trax GPS Tracker comes with a preconfigured data-only SIM-card, so that you can start using the tracker immediately after completing the account registration process. 9trax GPS Tracker is send to you with SIM card that works all across India. This way you can track the device when it travels across the country. As per Indian Govt regulations, you are required to upload a valid Identity Proof/Address Proof. </span>
				<a class="cl2"><i class="fa fa-info-circle"></i></a>
								<span class="tt2">List of KYC Documents:<br>
				Voter’s ID Card, Valid Passport, Aadhaar Card, Income Tax PAN, Driving License, NREGA Job Card, Ration Card with Photo of the person.
				</span>
              
              <span style="color:red">*</span> <span style="font-size: 0.8em;vertical-align: middle;">(Please upload jpg,png file within 1MB)</span></label>
              <br>
              <span class="btn btn-success fileinput-button"> <i class="glyphicon glyphicon-plus"></i> <span id="selfile1">Select file1...</span>
              <!-- The file input field used as target for the file upload widget -->
              <input id="fileupload" type="file" name="files">
              <input id="ekyc1" type="hidden" name="ekyc1">
              </span> <br>
              <br>
              <!-- The global progress bar -->
              <div id="progress" class="progress">
                <div class="progress-bar progress-bar-success"></div>
              </div>
              <!-- The container for the uploaded files -->
              <div id="files" class="files"></div>
              <br>
			  <label for="deviceimei" style="position:relative;">KYC Document 2 
              
				<a class="cl"><i class="fa fa-question-circle"></i></a>
				<span class="tt">9trax GPS Tracker comes with a preconfigured data-only SIM-card, so that you can start using the tracker immediately after completing the account registration process. 9trax GPS Tracker is send to you with SIM card that works all across India. This way you can track the device when it travels across the country. As per Indian Govt regulations, you are required to upload a valid Identity Proof/Address Proof.</span>
				<a class="cl2"><i class="fa fa-info-circle"></i></a>
								<span class="tt2">List of KYC Documents:<br>
				Voter’s ID Card, Valid Passport, Aadhaar Card, Income Tax PAN, Driving License, NREGA Job Card, Ration Card with Photo of the person.
				</span>
              <span style="font-size: 0.8em;vertical-align: middle;">(Please upload jpg,png file within 1MB)</span></label>
              <br>
              <span class="btn btn-success fileinput-button"> <i class="glyphicon glyphicon-plus"></i> <span>Select file2...</span>
              <!-- The file input field used as target for the file upload widget -->
              <input id="fileupload2" type="file" name="files2">
              <input id="ekyc2" type="hidden" name="ekyc2">
              </span> <br>
              <br>
              <!-- The global progress bar -->
			  
              <div id="progress2" class="progress">
                <div class="progress-bar progress-bar-success"></div>				
              </div>		  
			 
              <!-- The container for the uploaded files -->
              <div id="files2" class="files"></div>
            </div>
    
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12" style="padding: 21px 0px; margin: 0px auto; text-align: center; font-size: 16px; color: rgb(179, 174, 174);">
	<input type="checkbox" id="chk_term"> I Agree To The <a href="https://9trax.com/terms-and-conditions/" target="_blank">Terms & Conditions</a> And <a href="https://9trax.com/privacy-policy/" target="_blank">Privacy Policy</a></div>
    <button type="button" class="btn btn-default" id="registerFormbtn" style="margin:40px auto;display:block;text-align:center;" >Submit</button>
    <div style="padding: 2em;"></div>
  </div>
  <div id="copyright">
    <div class="container">
      <div class="col-md-12">
        <p class="pull-left"><a href="https://9trax.com">Home</a> | <a href="https://9trax.com/shop/">Shop</a> | <a href="https://9trax.com/contact/">Contact Us</a> | <a href="https://9trax.com/terms-and-conditions/">Terms and Conditions</a> | <a href="https://9trax.com/privacy-policy/">Privacy</a> | <a href="https://9trax.com/sitemap/">Sitemap</a></p>
        <p class="pull-right">&copy; <?php echo date('Y'); ?>. 9trax All Rights Reserved. Powered by <a href="http://stesalitsystems.com/" target="_blank">Stesalit Systems</a></p>
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
    'use strict';
	var imgfolder = $('#imgfolder').val();
    // Change this to the location of your server-side upload handler:
    var url = BASEURL+'account/imageupload1/'+imgfolder;
    $('#fileupload').fileupload({
        url: url,		
        dataType: 'json',
        done: function (e, data) { 
			$('#files').html('');
			if(data.result.succ == 1){
				$('<p/>').text(data.result.filename).appendTo('#files');
				$('#ekyc1').val(data.result.filename);			
			}
			else{
				$('<p/>').text(data.result.filename).appendTo('#files');
				$('#progress .progress-bar').css(
					'width',
					0 + '%'
				);
			}
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');



	var url2 = BASEURL+'account/imageupload2/'+imgfolder;
    $('#fileupload2').fileupload({
        url: url2,		
        dataType: 'json',
        done: function (e, data) {
			$('#files2').html('');
            if(data.result.succ == 1){
				$('<p/>').text(data.result.filename).appendTo('#files2');
				$('#ekyc2').val(data.result.filename);				
			}
			else{
				$('<p/>').text(data.result.filename).appendTo('#files2');
				$('#progress2 .progress-bar').css(
					'width',
					0 + '%'
				);
			}
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress2 .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
		
		
	$("#registerFormbtn").on('click',function () {
		var chk_flag = 1;
		var name = $('#name').val();
		var mobile = $('#mobile').val();
		var email = $('#email').val();
		var address = $('#address').val();
		var pin = $('#pin').val();
		var deviceid = $('#deviceid').val();
		var deviceimei = $('#deviceimei').val();
		var ekyc1 = $('#ekyc1').val();
		var ekyc2 = $('#ekyc2').val();
		var imgfolder = $('#imgfolder').val();
		if(name == ''){
			chk_flag = 0;
			$('#name').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
		}
		if(mobile == ''){
			chk_flag = 0;
			$('#mobile').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
		}
		if(email == ''){
			chk_flag = 0;
			$('#email').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
		}
		if(address == ''){
			chk_flag = 0;
			$('#address').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
		}
		if(pin == ''){
			chk_flag = 0;
			$('#pin').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
		}
		if(deviceid == ''){
			chk_flag = 0;
			$('#deviceid').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
		}
		if(deviceimei == ''){
			chk_flag = 0;
			$('#deviceimei').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
		}
		if(ekyc1 == ''){
			chk_flag = 0;
			$('#selfile1').css({'color': '#e7505a'});
		}
		if($("#chk_term").prop('checked') == false){
			chk_flag = 0;
		}
		
		if(chk_flag == 1){
			var url = '<?php echo base_url('account/newUserRegistration'); ?>';
			$.ajax({
			  method: "POST",
			  url: url,
			  data: { name: name, mobile: mobile, email: email, address: address, pin: pin, deviceid: deviceid, deviceimei: deviceimei, ekyc1: ekyc1, ekyc2: ekyc2, imgfolder: imgfolder },
			  success: function(msg) {
				var dataJson = JSON.parse(msg);
				$('#error_msg').html(dataJson.msg);
				$('html, body').animate({scrollTop: '0px'}, 0);
				$('#name').val('');
				$('#mobile').val('');
				$('#email').val('');
				$('#address').val('');
				$('#pin').val('');
				$('#deviceid').val('');
				$('#deviceimei').val('');
				$('#ekyc1').val('');
				$('#ekyc2').val('');
				$('#imgfolder').val('');
				$('#files').html('');
				$('#files2').html('');
				$('#progress .progress-bar').css(
					'width',
					0 + '%'
				);
				$('#progress2 .progress-bar').css(
					'width',
					0 + '%'
				);
				$('#chk_term').prop('checked', false);
				$('#name').css({'border-color': '','border-style': '','border-width': ''});
				$('#mobile').css({'border-color': '','border-style': '','border-width': ''});
				$('#email').css({'border-color': '','border-style': '','border-width': ''});
				$('#address').css({'border-color': '','border-style': '','border-width': ''});
				$('#pin').css({'border-color': '','border-style': '','border-width': ''});
				$('#deviceid').css({'border-color': '','border-style': '','border-width': ''});
				$('#deviceimei').css({'border-color': '','border-style': '','border-width': ''});
				$('#selfile1').css({'border-color': '','border-style': '','border-width': ''});
			  }
			});
		}
		else{
			$('#error_msg').html('Please fill all required fields and accept terms & conditions and privacy policy');
			$('html, body').animate({scrollTop: '0px'}, 0);
		}
	});
});
</script>



</body>
</html>
