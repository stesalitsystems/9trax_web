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
		
		<!-- Custom scripts for this page-->
		
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
            
            
			
			body {
				margin: 0px !important;
				font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
				font-size: 1rem !important;
				font-weight: 400 !important;
				font-size: 14px !important;
				color: #212529;
				background-color: #FFF;
			}
			
			body.fixed-nav {
				padding-top: 0 !important;
			}
			
            
            
            
			
		
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
				
				<form id="imgupld" action="<?php echo site_url('users/changepassword');?>" method="post" enctype="multipart/form-data">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<label for="exampleInputEmail1">Current Password<span class="text-danger">*</span></label>
							<input type="password" class="form-control" placeholder="Current Password" name="old_password" id="old_password" minlength="6" maxlength="15"  value="" required>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<label for="exampleInputEmail1">New Password<span class="text-danger">*</span></label>
							<input type="password" class="form-control" placeholder="New Password" name="new_password" id="new_password" minlength="6" maxlength="15"  value="" required>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<label for="exampleInputEmail1">Confirm Password<span class="text-danger">*</span></label>
							<input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password" id="confirm_password" minlength="6" maxlength="15"  value="" required>
						</div>
						
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 20px;">
							<button class="btn btn-primary" type="submit" name="save">Save</button>
						</div>
				</form>
			</div>
		</div>
	</body>
</html>


