<div class="col-sm-12 col-md-12 col-lg-12">
	<div class="col-xs-12 col-md-12">
		<?php if(isset($msg) && !empty($msg)){?>
		<div class="error"><?php echo $msg?></div>
		<?php } ?>
	</div>

	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<fieldset class="fldst">
			<legend>General Icons</legend>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<?php foreach($common_icons as $com_ico){ ?>
					<img src="<?php echo base_url().$com_ico->icon_path; ?>" title="<?php echo $com_ico->name;?>">
				<?php } ?>
			</div>
		</fieldset>
	</div>
	
	<?php if(!empty($customs_icons)){ ?>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
		<fieldset class="fldst">
			<legend>Custom Icons</legend>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<?php foreach($customs_icons as $cus_ico){ ?>
					<img src="<?php echo base_url().$cus_ico->icon_path; ?>" title="<?php echo $cus_ico->name;?>" >
				<?php } ?>
			</div>
		</fieldset>
	</div>
	<?php } ?>
	
	<form id="imgupld" action="<?php echo site_url('devices/device_icons_save');?>" method="post" enctype="multipart/form-data">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
			<fieldset class="fldst">
				<legend>Upload Image</legend>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					
						<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
							<label for="exampleInputEmail1">Name<span class="text-danger">*</span></label> 
							<input type="text" class="form-control" placeholder="Icon Name" name="name" value="" required>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
							<label for="exampleInputEmail1">Image<span class="text-danger">*</span></label> 
							<input type="file" class="" name="custom_image" id="custom_image" value="" required style="height: 35px;">
						</div>
					
				</div>
			</fieldset>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
			<button type="button" class="btn btn-danger" id="cancel" name="cancel">Cancel</button>
			<button class="btn btn-primary pull-right" type="submit" name="save">Upload</button>
		</div>
	</form>
</div>



<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
			
	.panel.with-nav-tabs .panel-heading{
		padding: 5px 5px 0 5px;
	}
	.panel.with-nav-tabs .nav-tabs{
		border-bottom: none;
	}
	.panel.with-nav-tabs .nav-justified{
		margin-bottom: -1px;
	}
	/********************************************************************/
	/*** PANEL DEFAULT ***/
	.with-nav-tabs.panel-default .nav-tabs > li > a,
	.with-nav-tabs.panel-default .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-default .nav-tabs > li > a:focus {
		color: #777;
	}
	.with-nav-tabs.panel-default .nav-tabs > .open > a,
	.with-nav-tabs.panel-default .nav-tabs > .open > a:hover,
	.with-nav-tabs.panel-default .nav-tabs > .open > a:focus,
	.with-nav-tabs.panel-default .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-default .nav-tabs > li > a:focus {
		color: #777;
		background-color: #ddd;
		border-color: transparent;
	}
	.with-nav-tabs.panel-default .nav-tabs > li.active > a,
	.with-nav-tabs.panel-default .nav-tabs > li.active > a:hover,
	.with-nav-tabs.panel-default .nav-tabs > li.active > a:focus {
		color: #555;
		background-color: #fff;
		border-color: #ddd;
		border-bottom-color: transparent;
	}
	.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu {
		background-color: #f5f5f5;
		border-color: #ddd;
	}
	.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a {
		color: #777;   
	}
	.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
	.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
		background-color: #ddd;
	}
	.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a,
	.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
	.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
		color: #fff;
		background-color: #555;
	}
	/********************************************************************/
	/*** PANEL PRIMARY ***/
	.with-nav-tabs.panel-primary .nav-tabs > li > a,
	.with-nav-tabs.panel-primary .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-primary .nav-tabs > li > a:focus {
		color: #fff;
	}
	.with-nav-tabs.panel-primary .nav-tabs > .open > a,
	.with-nav-tabs.panel-primary .nav-tabs > .open > a:hover,
	.with-nav-tabs.panel-primary .nav-tabs > .open > a:focus,
	.with-nav-tabs.panel-primary .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-primary .nav-tabs > li > a:focus {
		color: #fff;
		background-color: #3071a9;
		border-color: transparent;
	}
	.with-nav-tabs.panel-primary .nav-tabs > li.active > a,
	.with-nav-tabs.panel-primary .nav-tabs > li.active > a:hover,
	.with-nav-tabs.panel-primary .nav-tabs > li.active > a:focus {
		color: #428bca;
		background-color: #fff;
		border-color: #428bca;
		border-bottom-color: transparent;
	}
	.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu {
		background-color: #428bca;
		border-color: #3071a9;
	}
	.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a {
		color: #fff;   
	}
	.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
	.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
		background-color: #3071a9;
	}
	.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a,
	.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
	.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
		background-color: #4a9fe9;
	}
	/********************************************************************/
	/*** PANEL SUCCESS ***/
	.with-nav-tabs.panel-success .nav-tabs > li > a,
	.with-nav-tabs.panel-success .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-success .nav-tabs > li > a:focus {
		color: #3c763d;
	}
	.with-nav-tabs.panel-success .nav-tabs > .open > a,
	.with-nav-tabs.panel-success .nav-tabs > .open > a:hover,
	.with-nav-tabs.panel-success .nav-tabs > .open > a:focus,
	.with-nav-tabs.panel-success .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-success .nav-tabs > li > a:focus {
		color: #3c763d;
		background-color: #d6e9c6;
		border-color: transparent;
	}
	.with-nav-tabs.panel-success .nav-tabs > li.active > a,
	.with-nav-tabs.panel-success .nav-tabs > li.active > a:hover,
	.with-nav-tabs.panel-success .nav-tabs > li.active > a:focus {
		color: #3c763d;
		background-color: #fff;
		border-color: #d6e9c6;
		border-bottom-color: transparent;
	}
	.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu {
		background-color: #dff0d8;
		border-color: #d6e9c6;
	}
	.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a {
		color: #3c763d;   
	}
	.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
	.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
		background-color: #d6e9c6;
	}
	.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a,
	.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
	.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
		color: #fff;
		background-color: #3c763d;
	}
	/********************************************************************/
	/*** PANEL INFO ***/
	.with-nav-tabs.panel-info .nav-tabs > li > a,
	.with-nav-tabs.panel-info .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-info .nav-tabs > li > a:focus {
		color: #31708f;
	}
	.with-nav-tabs.panel-info .nav-tabs > .open > a,
	.with-nav-tabs.panel-info .nav-tabs > .open > a:hover,
	.with-nav-tabs.panel-info .nav-tabs > .open > a:focus,
	.with-nav-tabs.panel-info .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-info .nav-tabs > li > a:focus,
	.with-nav-tabs.panel-info .nav-tabs > li > a.active
	{
		color: #31708f;
		background-color: #fff;
		border-color: transparent;
	}
	.with-nav-tabs.panel-info .nav-tabs > li.active > a,
	.with-nav-tabs.panel-info .nav-tabs > li.active > a:hover,
	.with-nav-tabs.panel-info .nav-tabs > li.active > a:focus {
		color: #31708f;
		background-color: #fff;
		border-color: #bce8f1;
		border-bottom-color: transparent;
	}
	.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu {
		background-color: #d9edf7;
		border-color: #bce8f1;
	}
	.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a {
		color: #31708f;   
	}
	.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
	.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
		background-color: #bce8f1;
	}
	.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a,
	.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
	.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
		color: #fff;
		background-color: #31708f;
	}
	/********************************************************************/
	/*** PANEL WARNING ***/
	.with-nav-tabs.panel-warning .nav-tabs > li > a,
	.with-nav-tabs.panel-warning .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-warning .nav-tabs > li > a:focus {
		color: #8a6d3b;
	}
	.with-nav-tabs.panel-warning .nav-tabs > .open > a,
	.with-nav-tabs.panel-warning .nav-tabs > .open > a:hover,
	.with-nav-tabs.panel-warning .nav-tabs > .open > a:focus,
	.with-nav-tabs.panel-warning .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-warning .nav-tabs > li > a:focus {
		color: #8a6d3b;
		background-color: #faebcc;
		border-color: transparent;
	}
	.with-nav-tabs.panel-warning .nav-tabs > li.active > a,
	.with-nav-tabs.panel-warning .nav-tabs > li.active > a:hover,
	.with-nav-tabs.panel-warning .nav-tabs > li.active > a:focus {
		color: #8a6d3b;
		background-color: #fff;
		border-color: #faebcc;
		border-bottom-color: transparent;
	}
	.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu {
		background-color: #fcf8e3;
		border-color: #faebcc;
	}
	.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a {
		color: #8a6d3b; 
	}
	.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
	.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
		background-color: #faebcc;
	}
	.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a,
	.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
	.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
		color: #fff;
		background-color: #8a6d3b;
	}
	/********************************************************************/
	/*** PANEL DANGER ***/
	.with-nav-tabs.panel-danger .nav-tabs > li > a,
	.with-nav-tabs.panel-danger .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-danger .nav-tabs > li > a:focus {
		color: #a94442;
	}
	.with-nav-tabs.panel-danger .nav-tabs > .open > a,
	.with-nav-tabs.panel-danger .nav-tabs > .open > a:hover,
	.with-nav-tabs.panel-danger .nav-tabs > .open > a:focus,
	.with-nav-tabs.panel-danger .nav-tabs > li > a:hover,
	.with-nav-tabs.panel-danger .nav-tabs > li > a:focus {
		color: #a94442;
		background-color: #ebccd1;
		border-color: transparent;
	}
	.with-nav-tabs.panel-danger .nav-tabs > li.active > a,
	.with-nav-tabs.panel-danger .nav-tabs > li.active > a:hover,
	.with-nav-tabs.panel-danger .nav-tabs > li.active > a:focus {
		color: #a94442;
		background-color: #fff;
		border-color: #ebccd1;
		border-bottom-color: transparent;
	}
	.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu {
		background-color: #f2dede; /* bg color */
		border-color: #ebccd1; /* border color */
	}
	.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a {
		color: #a94442; /* normal text color */  
	}
	.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
	.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
		background-color: #ebccd1; /* hover bg color */
	}
	.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a,
	.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
	.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
		color: #fff; /* active text color */
		background-color: #a94442; /* active bg color */
	}

	

	.open_rprt{
		text-decoration: none;
		cursor: pointer;
		
	}
		
	.delete_rprt{
		text-decoration: none;
		cursor: pointer;
		
	}
	
</style>

<script>

	$(document).ready(function(){
		
		buildDataTable();
		
		$("#spd").hide();
		$("#alrts").hide();
		$("#geo").hide();
		$("#rots").hide();
		
		$(document).on("click", "#generate", function(e){
			
			e.preventDefault();
			form = $('#rprt');
			form.submit();
			
			// $.ajax({
				// type: 'post',
				// url: '<?php echo site_url('report/generate_report'); ?>',
				// data: $('form').serialize(),
				// success: function (data) {
				  // alert(data);
				// }
			// });
			
		});
		
		$(document).on("click", "#save", function(e){
			
			e.preventDefault();

			$.ajax({
				type: 'post',
				url: '<?php echo site_url('report/save_reposotory'); ?>',
				data: $('form').serialize(),
				success: function (data) {
					alert(data);
					if(data == "Already present"){
						$('#report_tab a[href="#tab2info"]').tab('show');
					}
					// else{
						
					// }
				}
			});
			
		});
		
		$(document).on("click", "#cancel", function(e){
			window.parent.closeModal();
		});
		
		$(document).on("click", "#new", function(e){
			//alert("hi");
			location.reload();
		});
		
		$(document).on("change", "#report_type", function(e){
			
			var opt = $(this).val();
			
			if(opt == '3'){
				
				$("#spd").hide();
				$("#geo").hide();
				$("#rots").hide();
				
				$("#alrts").show();
			}
			else if(opt == '4'){
				
				$("#alrts").hide();
				$("#geo").hide();
				$("#rots").hide();
				
				$("#spd").show();
			}
			else if(opt == '5'){
				
				$("#spd").hide();
				$("#alrts").hide();
				$("#rots").hide();
				
				$("#geo").show();
			}
			else if(opt == '6'){
				
				$("#spd").hide();
				$("#geo").hide();
				$("#alrts").hide();
				
				$("#rots").show();
			}
			else{
				
				$("#spd").hide();
				$("#alrts").hide();
				$("#geo").hide();
				$("#rots").hide();
				
			}
			
		});
		
		function buildDataTable() {
            var dataLoad = {};
            var coLumns = [];

                dataLoad = {
                    ticket_no: $.trim($("#title").val()),
                    issue_date: $.trim($("#report_type").val()),
                    type: $.trim($("#format").val()),
                    description: $.trim($("#devices").val()),
                    closed_status: $.trim($("#is_scheduled").val()),
                    
                };
                coLumns = [
                    {"data": "title"},
                    {"data": "report_type"},
                    {"data": "format"},
                    {"data": "devices"},
                    {"data": "is_scheduled"},
                    {"data": "action"}
                ];

            dtble = $('#reportlists').DataTable({
                processing: true,
                serverSide: true,
                bSort: false,
                searching: false,
                stateSave: true,
				bLengthChange: false,
                ajax: {
                    url: "<?php echo site_url('report/generated_reports'); ?>",
                    type: 'POST',
                    data: dataLoad,
                    error: function () {
    
                    }

                },
                columns: coLumns
            });
        }
		
		$(document).on("click", ".open_rprt", function(e){
			
			//e.preventDefault();
			var rid = $(this).attr('dat-link');
			//alert(rid);
			
			$.ajax({
				url: '<?php echo site_url("report/open_report"); ?>',
				type: 'POST',
				data: {id: rid},
				success: function(data){
					//alert(data);
					$("#chng_tab").html(data);
					
					$('#report_tab a[href="#tab1info"]').tab('show');
					
					// $("#tab2info").removeClass("active show");
					// $("#tab2info").attr("aria-expanded", false);
					// $("#tab1info").addClass("active show");
					// $("#tab1info").attr("aria-expanded", true);
					
				}

			});
			
		});
		
		$(document).on("click", ".delete_rprt", function(e){
			
			//e.preventDefault();
			var rid = $(this).attr('dat-link');
			//alert(rid);
			
			if (confirm("Are you sure, You want delete this report permanently?")) {
				$.ajax({
					url: '<?php echo site_url("report/delete_report"); ?>',
					type: 'POST',
					data: {id: rid},
					success: function(data){
						//alert(data);
						
						if ($.fn.DataTable.isDataTable("#reportlists")) {
						  $('#reportlists').DataTable().clear().destroy();
						  alert("hi");
						  buildDataTable();
						  
						}
						
						//location.reload();
						
					}

				});
			}
			return false;
				
		});
		
		    
	});
	
</script>
	