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
		<style>
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
		</style>		
		<script>               
		   var BASEURL = "<?php echo base_url()?>";
		   $(document).ajaxStart(function(){
				 $(".loader-div").show();
			}).ajaxStop(function(){
				 $(".loader-div").hide();
			});
		</script>
        
    </head>

    <body>
        <div class="content-wrapper" style="margin-left: 0px; background: #3879BB none repeat scroll 0% 0%;">
            
            <?php echo $middle ?>
            <div class="container-fluid loader-div"><img src="<?php echo base_url()?>assets/images/preloader.gif" /></div>
        </div>
    </body>
	<!-- Custom scripts for all pages-->
    <script src="<?php echo base_url() ?>assets/js/sb-admin.min.js"></script>
</html>
