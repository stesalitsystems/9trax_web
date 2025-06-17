<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="<?php echo base_url() ?>assets/images/fab1.ico" type="image/x-icon" />
        <title><?php echo $page_title ?></title>
        <!-- Bootstrap core CSS-->
        <link href="<?php echo base_url() ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom fonts for this template-->
        <link href="<?php echo base_url() ?>assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <!-- Page level plugin CSS-->
        <link href="<?php echo base_url() ?>assets/vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
        <!-- Custom styles for this template-->
        <link href="<?php echo base_url() ?>assets/css/sb-admin.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" rel="stylesheet">  
        <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" rel="stylesheet">  
        
        <!-- Bootstrap core JavaScript-->
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="crossorigin="anonymous"></script>
        <script src="<?php echo base_url() ?>assets/vendor/popper/popper.min.js"></script>
		<!-- Core plugin JavaScript-->
		<script src="<?php echo base_url() ?>assets/vendor/jquery-easing/jquery.easing.min.js"></script>
		<!-- Page level plugin JavaScript-->
		<script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.min.js"></script>
		<script src="<?php echo base_url() ?>assets/vendor/datatables/jquery.dataTables.js"></script>
		<script src="<?php echo base_url() ?>assets/vendor/datatables/dataTables.bootstrap4.js"></script>
		<!-- Custom scripts for this page-->
		<script src="<?php echo base_url() ?>assets/js/sb-admin-datatables.min.js"></script>
		<!--<script src="<?php echo base_url() ?>assets/js/sb-admin-charts.min.js"></script>-->
		<script src="<?php echo base_url() ?>assets/vendor/jqueryvalidation/jquery.validate.js" type="text/javascript"></script>
		<script src="<?php echo base_url() ?>assets/vendor/jqueryvalidation/additional-methods.js" type="text/javascript"></script>
	   <script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.js"></script>
		 
		<script>               
		   var BASEURL = "<?php echo site_url('/')?>";
		   var BASEURLIMG = "<?php echo base_url()?>";
		</script>
        <style>
            .btn-primary {
                color: #fff;
                background-color: #3879bb;
                border-color: #3879bb;
            }
            .blue-theme{
                background-color: #3879BB !important;

            }
            .btn{
                cursor: pointer;
            }
            .ui-dialog{
                z-index: 1070 !important;
            }
            .moduleaddbtn{
                color:#fff !important;
                cursor:pointer;
            }
            body{
                font-size: 14px !important;
            }
            .nav-link-text, #mainNav.navbar-dark .navbar-collapse .navbar-sidenav > .nav-item > .nav-link,#mainNav.navbar-dark .navbar-collapse .navbar-sidenav .nav-link-collapse:after,.navbar-dark .navbar-nav .nav-link,#mainNav.fixed-top.navbar-dark .sidenav-toggler a i { 
                color: #fff!important;
            }
            #mainNav.fixed-top.navbar-dark .sidenav-toggler {
                background-color:#3879bb!important;
            }
            .error{
                color:#f00;
            }
            .loader-div{
                position: absolute;
                top: 0px;    
                width: 100%;
                height: 100%;
                z-index: 2000;
                background-color: rgba(191, 189, 189, 0.2);
                display: none;
            }
            .loader-div>img{
                position: fixed;
                top:45%;
                left:45%;
                width:6em;
            }
            .ddaction{
                background-color: transparent;
                color: #007bff;
            }
            .ddaction:focus{               
                box-shadow: none;
                color: #17a2b8;
            }
            .search-panel{
                background-color: rgba(147, 145, 158, 0.61);
                color: #fff;
                padding: 1em;                
                box-shadow: 1px 4px 10px 0px #1d1b1b;  
                margin-top: 2px;
            }
            .search-panel>.container>h4+form{
                border: 1px solid #949185;
            } 
            ul.action-dd li{
                margin: 10px;
            }
            .form-actions{
                background-color: rgba(0, 0, 0, 0.05);
                padding: 25px;
            }
			#mainNav .navbar-collapse .navbar-sidenav > .nav-item.startactive {background: rgb(237, 31, 36) none repeat scroll 0% 0%;border: medium none;}
        
		.withlogobg {background: transparent url("../assets/images/9trax_56_56.png") no-repeat scroll 96% 30%;}
		.withlogobg>.navbar-brand {display:none;}
		.logonm { color: #fff;font-size: 1.5em;font-weight: bold;padding-left: 10px;}
		</style>
    </head>

    <body class="fixed-nav sticky-footer" id="page-top">
		<div class="container-fluid">
			
			<div class="row">
				<div class="col-xs-12 col-md-12">
					<?php if(isset($msg) && !empty($msg)){?>
					<div class="error"><?php echo $msg?></div>
					<?php } ?>
				</div>
				<form id="imgupld" action="<?php echo site_url('users/upload_image');?>" method="post" enctype="multipart/form-data">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<input type="file" class="" name="profile_image" id="profile_image" value="" required>
					</div>
					
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 20px;">
						<button class="btn btn-primary" type="submit" name="save">Upload</button>
					</div>	
				</form>
			</div>
		</div>
	</body>
</html>


