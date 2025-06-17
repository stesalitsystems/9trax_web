<style>
    .form-row{
        margin: 10px;
    }    
</style>
<div class="container-fluid">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="javascript:void(0)">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?php echo site_url('/') ?>devices/lists">Device Management</a>
        </li>
        <li class="breadcrumb-item active">Add Device</li>
    </ol>
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <?php if (isset($errmsg) && !empty($errmsg)) { ?>
                <div class="error"><?php echo $errmsg ?></div>
            <?php } ?>
        </div>
		
		<?php if($this->uri->segment(3) == 'add'){?>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<form id="devicesadd" class="form-actions" action="<?= base_url('devices/add');?>" method="post">
					<?= csrf_field() ?>
					<div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Device ID<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="Serial No" name="serial_no" id="serial_no" maxlength="30" value="<?php echo set_value('serial_no'); ?>" required>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">IMEI No.<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="IMEI No" id="imei_no" name="imei_no" maxlength="20" value="<?php echo set_value('imei_no'); ?>" required>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Warranty Till</label>
								<input type="text" class="form-control " placeholder="" name="warranty_date"  id="warranty_date" value="">
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Target IP</label>
								<input type="text" class="form-control " placeholder="" name="target_ip"  id="target_ip" value="103.233.79.35" readonly>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Target Port</label>
								<input type="text" class="form-control " placeholder="" name="target_port"  id="target_port" value="7007" readonly>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Time Interval (in sec)</label>
								<input type="text" class="form-control " placeholder="" name="duration"  id="duration" value="20" readonly>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">GSM Type</label>
							<select name="siminstalled" id="siminstalled"  class="form-control">
								<option value="2" selected>Preloaded</option>
								<option value="1" >Postloaded</option>
							</select>
						</div>
						
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="inact">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<label for="exampleInputEmail1">Mobile No.</label>
									<input type="text" class="form-control" placeholder="Mobile No" id="mobile_no" name="mobile_no" maxlength="10" value="<?php echo set_value('mobile_no'); ?>">
								</div>
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="exampleInputEmail1">APN Name</label>
										<input type="text" class="form-control " placeholder="" name="apn_name"  id="apn_name" value="" >
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="exampleInputEmail1">APN Username</label>
										<input type="text" class="form-control " placeholder="" name="apn_username"  id="apn_username" value="" >
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="exampleInputEmail1">APN Password</label>
										<input type="text" class="form-control " placeholder="" name="apn_pswd"  id="apn_pswd" value="" >
									</div>
								</div>
							</div>
						</div> 
						
						
						
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<button type="submit" class="btn btn-primary pull-right" name="add">Submit</button>
								<a href="<?php echo site_url('/') ?>devices/lists" class="btn btn-danger">Back</a>
								<div class="clearfix"></div>
							</div>
						
					</div>
				</form>
			</div>
		<?php }else{ ?>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<form id="devicesadd" class="form-actions" action="<?php echo site_url('devices/edit');?>" method="post">
					<div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Device ID<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="Serial No" name="serial_no" id="serial_no" maxlength="30" value="<?php echo set_value('serial_no'); ?>" required>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">IMEI No.<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="IMEI No" id="imei_no" name="imei_no" maxlength="20" value="<?php echo set_value('imei_no'); ?>" required>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Warranty Till</label>
								<input type="text" class="form-control " placeholder="" name="warranty_date"  id="warranty_date" value="">
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Target IP</label>
								<input type="text" class="form-control " placeholder="" name="target_ip"  id="target_ip" value="103.233.79.35" readonly>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Target Port</label>
								<input type="text" class="form-control " placeholder="" name="target_port"  id="target_port" value="7007" readonly>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Time Interval (in sec)</label>
								<input type="text" class="form-control " placeholder="" name="duration"  id="duration" value="20" readonly>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">GSM Type</label>
							<select name="siminstalled" id="siminstalled"  class="form-control">
								<option value="2" selected>Preloaded</option>
								<option value="1" >Postloaded</option>
							</select>
						</div>
						
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="inact">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<label for="exampleInputEmail1">Mobile No.</label>
									<input type="text" class="form-control" placeholder="Mobile No" id="mobile_no" name="mobile_no" maxlength="10" value="<?php echo set_value('mobile_no'); ?>">
								</div>
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="exampleInputEmail1">APN Name</label>
										<input type="text" class="form-control " placeholder="" name="apn_name"  id="apn_name" value="" >
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="exampleInputEmail1">APN Username</label>
										<input type="text" class="form-control " placeholder="" name="apn_username"  id="apn_username" value="" >
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="exampleInputEmail1">APN Password</label>
										<input type="text" class="form-control " placeholder="" name="apn_pswd"  id="apn_pswd" value="" >
									</div>
								</div>
							</div>
						</div> 
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<button type="submit" class="btn btn-primary pull-right" name="add">Submit</button>
							<a href="<?php echo site_url('/') ?>devices/lists" class="btn btn-danger">Back</a>
							<div class="clearfix"></div>
						</div>
					</div>
				</form>
			</div>
		
		<?php } ?>
	</div>
</div>
<script>
    $(document).ready(function () {
        
		$("#inact").hide();
		var siminstalled = $("#siminstalled").val();
		
		if(siminstalled == 2){
			$("#inact").show();
		}
		else{
			$("#inact").hide();
		}
		
		$(document).on("change", "#siminstalled", function(){
			var simval = $(this).val();
			if(simval == 2){
				$("#inact").show();
			}
			else{
				$("#inact").hide();
			}
			
		});
		
		
		//
		//
		//$(document).on("click", "#act", function(){
		//	
		//	var serial_no = $("#serial_no").val();
		//	$.ajax({
		//		url: '<?php echo site_url('devices/validatedevice'); ?>',
		//		type: "POST",
		//		data: {serial_no: serial_no},
		//		success: function (data)
		//		{
		//			var obj = jQuery.parseJSON(data);
		//			//console.log(obj.mobile_no);
		//			//alert(data);
		//			
		//			$("#imei_no").val(obj.imei_no);
		//			$("#mobile_no").val(obj.mobile_no);
		//			$("#duration").val('20');
		//			$("#apn_name").val(obj.apn_name);
		//			$("#apn_username").val(obj.apn_username);
		//			$("#apn_pswd").val(obj.apn_pswd);
		//			$("#target_ip").val(obj.target_ip);
		//			$("#target_port").val(obj.target_port);
		//			$("#inact").show();
		//			
		//			
		//			//$("#circle_id").html(data);
		//			//$('#asset_class').has('option').chosen().trigger("chosen:updated");
		//		}
		//	});
		//	
		//	//$(this).hide();
		//	//$("#inact").show();
		//	
		//});
		
		
		
		$("#devicesadd").validate({
            rules: {
                mobile_no: {
                    digits: true,
                    minlength: 10,
                    maxlength: 15
                },
                 imei_no: {
                    digits: true
                }
            }
        });
		
		<?php //if ($sessdata->group_id == 1) { ?>  
        $(function () {
            $("[name='warranty_date']").datepicker({
                dateFormat: 'dd-mm-yy',
            });
        });
		<?php //} ?>
	  
	  
	  
		//$(document).on("keyup", "#imei_no", function(){
		//	
		//	var imei = $(this).val();
		//	$("#serial_no").val(imei);
		//	
		//});
	  
	  
    });
</script>