<?php
    namespace App\Helpers;
	$sessdata = session();
	
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="<?= base_url() ?>assets/images/fab1.ico" type="image/x-icon" />
        <title><?= $page_title ?></title>
        <!-- Bootstrap core CSS-->
        <link href="<?= base_url() ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom fonts for this template-->
        <link href="<?= base_url() ?>assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <!-- Page level plugin CSS-->
        <link href="<?= base_url() ?>assets/vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
        <!-- Custom styles for this template-->
        <link href="<?= base_url() ?>assets/css/sb-admin.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" rel="stylesheet">  
        <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" rel="stylesheet">  
        
        <!-- Bootstrap core JavaScript-->
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="crossorigin="anonymous"></script>
        <script src="<?= base_url() ?>assets/vendor/popper/popper.min.js"></script>
		<!-- Core plugin JavaScript-->
		<script src="<?= base_url() ?>assets/vendor/jquery-easing/jquery.easing.min.js"></script>
		<!-- Page level plugin JavaScript-->
		<script src="<?= base_url() ?>assets/vendor/chart.js/Chart.min.js"></script>
		<script src="<?= base_url() ?>assets/vendor/datatables/jquery.dataTables.js"></script>
		<script src="<?= base_url() ?>assets/vendor/datatables/dataTables.bootstrap4.js"></script>
		<!-- Custom scripts for this page-->
		<script src="<?= base_url() ?>assets/js/sb-admin-datatables.min.js"></script>
		<!--<script src="<?= base_url() ?>assets/js/sb-admin-charts.min.js"></script>-->
		<script src="<?= base_url() ?>assets/vendor/jqueryvalidation/jquery.validate.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>assets/vendor/jqueryvalidation/additional-methods.js" type="text/javascript"></script>
	   <script src="<?= base_url() ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.js"></script>
		
		<script src="<?= base_url() ?>assets/js/chosen.jquery.js"></script>
		
		
		
		<link href="<?= base_url();?>assets/vendor/fselect/fSelect.css" rel="stylesheet">
		<script src="<?= base_url();?>assets/vendor/fselect/fSelect.js"></script>
		
		 
		<script>               
		    var BASEURL = "<?= site_url('/')?>";
		    var BASEURLIMG = "<?= base_url()?>";
		   
			$(document).ready(function() {
			   
				$('.searchable_select').chosen();
			
				$('.multi_select').chosen();
				
				$('.select_mfc').fSelect();
				
			});
			
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
                /*background-color: rgba(147, 145, 158, 0.61);*/
				
                /*color: #fff;*/
                padding: 1em;                
                box-shadow: 1px 4px 10px 0px #1d1b1b;  
                /*margin-top: 2px;*/
				width: 94%;
				margin: 30px 36px 30px;
				border-radius: 2px;
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
			
			.mdBt{text-decoration: none;cursor: pointer !important;}
			
			.set_header_user_cont {right: -46px !important;left: auto !important;}
			
			.form-row{margin: 10px;}

            .modal-dialog-centered {
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                -webkit-box-align: center;
                -ms-flex-align: center;
                align-items: center;
                min-height: calc(100% - (.5rem * 2));
            }
            @media (min-width: 576px) {
                .modal-dialog-centered {
                    min-height: calc(100% - (1.75rem * 2));
                }
            }

            .logutgmailpop { background:#de232d !important; color:#fff !important; float:none; display:block; text-align:center; }
		</style>

        <link href="<?= base_url();?>assets/css/admin.css" rel="stylesheet">
    </head>

    <body class="fixed-nav sticky-footer" id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top blue-theme <?= ($sessdata->get('login_sess_data')['group_id'] == 4 && $sessdata->get('login_sess_data')['company_logo'] != "") ? "withlogobg" : "";?>" id="mainNav">
           	
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
			
				<a class="navbar-brand" href="<?= base_url();?>">
					<img src="<?= base_url('assets/images/9trax_56_56.png') ?>" style="height: 2.3em;"/>
				</a>
			
                <ul class="navbar-nav navbar-sidenav blue-theme" id="exampleAccordion">
                    <?php					
						helper('menu');
						echo menuGenerate();
					?>
                </ul>
                <ul class="navbar-nav sidenav-toggler">
                    <li class="nav-item" style="background: #1f1f1f;">
                        <a class="nav-link text-center" id="sidenavToggler">
                            <i class="fa fa-fw fa-angle-left"></i>
                        </a>
                    </li>
                </ul>
				
                <ul class="navbar-nav ml-auto">		

				<li style="<?= (service('router')->controllerName() == "\App\Controllers\ControlCentre" && service('router')->methodName() == "view") ? "display:block;" : "display:none;" ?>">
                    <div class="dropdown" style="z-index: 1;">    
                        <a href="javascript:void(0)" data-toggle="dropdown" title="Tools"><i class="fa fa-wrench" aria-hidden="true" style="color:#fff;font-size:18px;line-height: 40px;"></i></a>
                        <ul class="dropdown-menu poiul" style="right:0;left: auto;line-height: 27px; padding: 5px 0px; font-size: 12px;background: rgb(53, 145, 236) none repeat scroll 0% 0%;width:150px;">
                            <li><a href="javascript:void(0)" id="createpoi" class="tools-li-a"><i class="fa fa-map-marker tools-li-a-i" aria-hidden="true"></i>POI</a></li>
                            <li><a href="javascript:void(0)" id="createroute" class="tools-li-a"><i class="fa fa-road tools-li-a-i" aria-hidden="true"></i>Routes</a></li>
                            <li><a href="javascript:void(0)" id="creategeof" class="tools-li-a"><i class="fa fa-object-ungroup tools-li-a-i" aria-hidden="true"></i>Geo-Fence</a></li>
                            <li><a href="javascript:void(0)" id="placesearch" class="tools-li-a"><i class="fa fa-home tools-li-a-i" aria-hidden="true"></i>Place Search</a></li>
                            <li><a href="javascript:void(0)" id="ruler" class="tools-li-a"><i class="fa fa-minus tools-li-a-i" aria-hidden="true"></i>Ruler</a></li>
							<li><a href="javascript:void(0)" id="hplbck" class="tools-li-a"><i class="fa fa-play-circle-o tools-li-a-i" aria-hidden="true"></i>live Tracking / History Playback</a></li>
                            <!--<li><a class="tools-li-a nav-link gb_zb mdBt1" dat_link="<?= site_url('report/index'); ?>" dat_ifrwidth="1000" dat_ifrheight="500" id="report" style="cursor: pointer;"><i class="fa fa-bar-chart tools-li-a-i" aria-hidden="true"></i>Report</a></li>-->
							<!--<li><a href="javascript:void(0)" id="sumrept" class="tools-li-a"><i class="fa fa-area-chart tools-li-a-i" aria-hidden="true"></i>Summery Report</a></li>
							<li><a class="tools-li-a nav-link gb_zb mdBt1" dat_link="<?= site_url('devices/device_icons'); ?>" dat_ifrwidth="770" dat_ifrheight="500" id="report" style="cursor: pointer;"><i class="fa fa-plus-square tools-li-a-i" aria-hidden="true"></i>Icon</a></li>-->
                            <li><a href="javascript:void(0)" id="cancelallinteraction" class="tools-li-a"><i class="fa fa-times tools-li-a-i" aria-hidden="true"></i>Clear Map Object</a></li>
                        </ul>
                    </div>
                    </li>
                    <li class="nav-item dropdown iopafetr">
                        <a class="nav-link mr-lg-2" id="alertsDropdown" href="javascript:void(0)" aria-haspopup="true" aria-expanded="false" style="<?= (service('router')->controllerName() == "\App\Controllers\ControlCentre" && service('router')->methodName() == "view")?"display:block;padding-right:0px;":"display:none;padding-right:0px;"?>">
                            <i class="fa fa-fw fa-bell" id="bell"></i>
                            <img src="<?= base_url()?>assets/images/bell.gif" id="notificationbell" style="display:none;   width: 22px;"/>
                            <span class="d-lg-none">Alerts</span>
                        </a>
                    </li>
					
                    
					<li class="nav-item">
						<a class="nav-link" href="<?= base_url().'downloads/manual/Magikk Web User Manual.pdf'; ?>" download title="Download Manual">
						<i class="fa fa-download" aria-hidden="true" style="padding: 0 5px;color: #fff;"></i></a>
					</li>
                    <li>
						<a class="gb_zb2" href="#" id="profile_menu" >
							<?php if($sessdata->get('login_sess_data')['profile_image'] != ""){ ?>
								<img src="<?= base_url().'uploads/users/'.$sessdata->get('login_sess_data')['user_id'].'/'.$sessdata->get('login_sess_data')['profile_image'];?>" class="gb_Ab2">
							<?php }else{ ?>
								<img src="<?= base_url().'assets/images/no_image.jpg';?>" class="gb_Ab2">
							<?php } ?>
						</a>
                	</li>
                    
                </ul>
			</div>
        </nav>
		<div id="profile_container" class="de_active" >
			<i class="fa fa-caret-up gpoparrow"></i>
            <div class="topgmapop">
            	<a class="gb_zb" href="#">
					<?php if($sessdata->get('login_sess_data')['profile_image'] != ""){ ?>
                	<img src="<?= base_url().'uploads/users/'.$sessdata->get('login_sess_data')['user_id'].'/'.$sessdata->get('login_sess_data')['profile_image'];?>" class="gb_Ab">
					<?php }else{ ?>
					<img src="<?= base_url().'assets/images/no_image.jpg';?>" class="gb_Ab">
					<?php } ?>
                	<!--<p class="gb_lb"><i class="fa fa-camera" aria-hidden="true"></i></p>-->
                </a>
                <div class="topgmapopinfo">
                <p><?= $sessdata->get('login_sess_data')['firstname']." ".$sessdata->get('login_sess_data')['lastname']; ?></p>
                <p><?= $sessdata->get('login_sess_data')['email']; ?></p>
                <p><?= $sessdata->get('login_sess_data')['mobile']; ?></p>
               </div> 
            </div>
            
            <div>                  
                <div><a class="nav-link myprogpop" href="<?= base_url('users/profile');?>"><i class="fa fa-fw fa-user"></i>My Profile</a></div>
                <div class="clearfix">
                    <a class="nav-link forgpgpop" style="float:left; width:50%; text-align:center; padding:.5rem 0.35rem;" href="<?php echo base_url();?>cron/distanc_time_insert.php" target="_blank" title="Upload Exception data" dat_ifrheight="270">Upload Exception Data</a>
                    <a class="nav-link forgpgpop" style="float:left; width:50%; text-align:center; padding:.5rem 0.35rem;" href="<?php echo base_url();?>downloads/FORMAT EXCEPTION REPORT Details of GPS Tracker 2023-24 in ALL PWAY MERGED.xlsx" title="Download Exception data" dat_ifrheight="270">Download Exception Data</a>
                </div>
                <div><a class="nav-link logutgmailpop" data-toggle="modal" data-target="#exampleModal" style="cursor: pointer;">Logout</a></div>
            </div>
			
		</div>
        <div class="content-wrapper" style="background-color:#fff;">
            <div class="alert alert-dismissible" id="myAlert" style="display:none;">
                <a href="javascript:void(0);" class="close" onClick="return $('#myAlert').hide();">&times;</a>
                <strong id="msgbody"></strong>
            </div>
            <?= $middle ?>
            <!-- /.container-fluid-->
            <!-- /.content-wrapper-->
            <footer class="sticky-footer">
                <div class="container">
                    <div class="text-center">
                        <small>Copyright © Stesalit  <?= date('Y') ?></small>
                    </div>
                </div>
            </footer>
            <!-- Scroll to Top Button-->
			<!--<a class="scroll-to-top rounded" href="#page-top">
                <i class="fa fa-angle-up"></i>
            </a>-->
            <!-- Modal-->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                            <a class="btn btn-primary" href="<?= base_url('users/logout') ?>">Logout</a>
                        </div>
                    </div>
                </div>
            </div> 
            <div id="dialog_confirm" title="" style="display:none;">
                <p>
                    <span class="ui-icon ui-icon-alert"></span>
                    <span id="alert_msg"></span>
                </p>
            </div>
            
			
			<div class="container-fluid loader-div"><img src="<?= base_url('assets/images/preloader.gif')?>" /></div>
			
			
			<div id="myModal1" class="modal fade" role="dialog">
				<div class="modal-dialog modal-sm modal-megamenu">
			
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title"></h4>
							<button type="button" class="close atst pull-right" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body">
							<iframe class="ifrmod" src="" frameborder="0" width="100%"></iframe>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default atst" data-dismiss="modal">Close</button>
						</div>
					</div>
			
				</div>
			</div>
			
			
			<div id="myModal2" class="modal fade" role="dialog">
				<div class="modal-dialog modal-lg modal-megamenu" style="max-width: 77em;!important">
			
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title"></h4>
							<button type="button" class="close atst pull-right" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body">
							<iframe class="ifrmod" src="" frameborder="0"></iframe>
						</div>
						<!--<div class="modal-footer">
							<button type="button" class="btn btn-default atst" data-dismiss="modal">Close</button>
						</div>-->
					</div>
			
				</div>
			</div>
			
			<!-- Modal-->
			
        </div>
	
		<style>
			.active{
				display: block !important;
			}
			
			.de_active{
				display: none !important;
			}
		</style>

	
        <script>
			$(document).ready(function(){
			   
				//$("#profile_container").css("display", "none");
			   
				closeSideNav(); 
				//fetchNotificationsListing();
			  
			  
				$(document).on("click", "#profile_menu", function(e){
				
					e.preventDefault();
					//alert("Hi");
					
					//alert($("#profile_container").attr("class"));
					
					
					if($("#profile_container").attr("class") == "de_active"){
						
						$("#profile_container").removeClass("de_active");
						$("#profile_container").addClass("active");
					}
					else{
						
						$("#profile_container").removeClass("active");
						$("#profile_container").addClass("de_active");
					}
					
					
				
				});
			  
				
				$('body').click(function(){
					
					if($("#profile_container").attr("class") == "active"){
						$("#profile_container").removeClass("active");
						$("#profile_container").addClass("de_active");
					}
					
				});
				
				
				
				// iframe modal
				$(document).on("click", ".mdBt", function() {
				
					var link = $(this).attr("dat_link");
					var title = $(this).attr("title");
					var ifr_height = 200;
					var iframe_height = $(this).attr("dat_ifrheight");
					if($(this).is("[dat_ifrheight]")){
						ifr_height = $(this).attr("dat_ifrheight");
					}
					else{
						ifr_height = 200;
						
					}
					
					var ifr_width = 200;
					
					//alert(iframe_height);
					
					//alert(title);
					$("#myModal1").find('iframe').attr('src',link);
					$("#myModal1").find('iframe').height(ifr_height);
					$("#myModal1").find('.modal-title').text(title);
					
					$("#myModal1").modal("show");
				});
				
				$(document).on("click", ".mdBt1", function() {
				
					var link = $(this).attr("dat_link");
					var title = $(this).attr("title");
					var ifr_height = 200;
					var iframe_height = $(this).attr("dat_ifrheight");
					if($(this).is("[dat_ifrheight]")){
						ifr_height = $(this).attr("dat_ifrheight");
					}
					else{
						ifr_height = 200;
						
					}
					
					var iframe_height = $(this).attr("dat_ifrwidth");
					if($(this).is("[dat_ifrwidth]")){
						ifr_width = $(this).attr("dat_ifrwidth");
					}
					else{
						ifr_width = 200;
						
					}
					
					//alert(iframe_height);
					
					//alert(title);
					$("#myModal2").find('iframe').attr('src',link);
					$("#myModal2").find('iframe').width(ifr_width);
					$("#myModal2").find('iframe').height(ifr_height);
					$("#myModal2").find('.modal-title').text(title);
					
					$("#myModal2").modal("show");
				});
				
				// iframe modal
			  
			  
				$('#myModal1').on('hidden.bs.modal', function () { 
				
					$("#myModal1").find('iframe').attr('src','');
					window.location=window.location;
				});
				
				$('#myModal2').on('hidden.bs.modal', function () { 
				
					$("#myModal1").find('iframe').attr('src','');
					//window.location=window.location;
				});
				
				window.closeModal = function(){
					$('#myModal2').modal('hide');
				};
				
				$(".dt").datepicker({
					dateFormat: 'dd-mm-yy',
					maxDate: new Date()
				});
				
				
				// Numeric Validation
			
				$(document).on('keyup', '.numeric', function(event) {
					var v = this.value;
					if($.isNumeric(v) === false) {
						this.value = this.value.slice(0,-1);
					}
				});
				
				// Alphanumeric & space Validation
				$('.alpnumspc').bind('keyup change', function (e) {
					//alert($(this).val());
					if ($(this).val().length == 0) {
						if (e.which == 32) { 
							e.preventDefault();
						}
					}
					else {
						$(this).val($(this).val().replace(/[^A-Za-z0-9\s]/, ''))
					}
				});
				
				
			  
			});
			
			
			$(document).ajaxStart(function(){
				 $(".loader-div").show();
			}).ajaxStop(function(){
				 $(".loader-div").hide();
			});
			function openAlert(myclass,msgbody){
				$('#myAlert').show(function(){
					 $(this).attr('class','alert alert-dismissible '+ myclass);
				   // $(this).addClass(myclass);
					$('#myAlert strong#msgbody').html('').html(msgbody);
				});
				setTimeout(function(){
					 $('#myAlert').hide();
				},5000);
			}
			function closeSideNav(){            
				$("body").toggleClass("sidenav-toggled");
				$(".navbar-sidenav .nav-link-collapse").addClass("collapsed");
				$(".navbar-sidenav .sidenav-second-level, .navbar-sidenav .sidenav-third-level").removeClass("show");
				<?php if(service('router')->controllerName() == "ControlCentre" && service('router')->method() == "view"){ ?>
				if(typeof map !== 'undefined'){
					 map.render();
				   map.updateSize();            
				}
				<?php } ?>
			}

			function checkNumberTypeField(evt){
				var charCode = evt.keyCode || evt.which;
				var x = String.fromCharCode(charCode);
				var regex = /^[0-9]+$/;           
				if (!x.match(regex))
				{            
				 return false;
				}
				return true;
			}
			function showDialogs(titleText,paraText,buttons,closeFn){
				$("#dialog_confirm").attr('title', titleText);
				$("#alert_msg").html(paraText);
				$("#dialog_confirm").dialog({
							resizable: false,
							height: "auto",
							width: 400,
							modal: true,
							buttons: buttons,
							close: closeFn
						});
			}
			
		</script>
    </body>
	<!-- Custom scripts for all pages-->
    <script src="<?= base_url('assets/js/sb-admin.min.js') ?>"></script>
</html>
