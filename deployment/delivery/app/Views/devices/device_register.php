<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-mobile"></i> Device Management
        </div>
        <div class="card-body">
		<div class="search-panel">
			<form id="devicesadd" action="<?php echo site_url('devices/device_register');?>" method="post">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<fieldset class="fldst">
						<legend>Device</legend>
						<div class="form-row">
							<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
								<label for="exampleInputEmail1">Device ID<span class="text-danger">*</span></label>
								<input type="text" class="form-control" placeholder="Serial No" name="serial_no" id="serial_no" maxlength="30" value="<?php echo set_value('serial_no'); ?>" required>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2" style="margin-top: 30px;">
								<button id="chk_device" type="button" class="btn btn-success" name="check" style="padding:0.47rem 0.75rem;">Check</button>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
								<div id="hidenoti"  style="margin-top: 30px;">
									<div class="error"></div>
								</div>
							</div>							
						</div>
					</fieldset>	
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<fieldset class="fldst">
						<legend>Device Details</legend>
						<div class="form-row">
							<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
								<label for="exampleInputEmail1">IMEI No.<span class="text-danger">*</span></label>
								<input type="text" class="form-control" placeholder="IMEI No" id="imei_no" value="" readonly>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
								<label for="exampleInputEmail1">Warranty Till</label>
								<input type="text" class="form-control dt" placeholder="Warranty Till" id="warranty_date" value="" readonly>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
								<label for="exampleInputEmail1">Target IP<span class="text-danger">*</span></label>
								<input type="text" class="form-control " placeholder="Target IP" id="target_ip" value="" readonly>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
								<label for="exampleInputEmail1">Target Port<span class="text-danger">*</span></label>
								<input type="text" class="form-control " placeholder="Target Port" id="target_port" value="" readonly>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
								<label for="exampleInputEmail1">Time Interval (in sec)<span class="text-danger">*</span></label>
								<input type="text" class="form-control " placeholder="Time Interval" id="duration" value="" readonly>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
								<label for="exampleInputEmail1">GSM Type<span class="text-danger">*</span></label>
								<input type="text" class="form-control " placeholder="GSM Type" id="siminstalled" name="siminstalled" value="" readonly required>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
								<label for="exampleInputEmail1">Device Type<span class="text-danger">*</span></label>
								<input type="text" class="form-control " placeholder="Device Type" id="type" name="type" value="" readonly required>
							</div>
						</div>	
						<div id="presim">
							<div class="form-row">
								<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
									<label for="exampleInputEmail1">Mobile No.</label>
									<input type="text" class="form-control" placeholder="Mobile No" id="mobile_no" value="" readonly>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
									<label for="exampleInputEmail1">APN Name.</label>
									<input type="text" class="form-control" placeholder="APN Name" id="apn_name" value="" readonly>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
									<label for="exampleInputEmail1">APN Username.</label>
									<input type="text" class="form-control" placeholder="APN Username" id="apn_username" value="" readonly>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
									<label for="exampleInputEmail1">APN Password.</label>
									<input type="text" class="form-control" placeholder="APN Password" id="apn_pswd" value="" readonly>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
						<div id="postsim">
							<div class="form-row">
								<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
									<label for="exampleInputEmail1">Mobile No.</label>
									<input type="text" class="form-control numeric" placeholder="Mobile No" id="mobile_no" name="mobile_no" minlength="10" maxlength="10" value="" required>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
									<label for="exampleInputEmail1">APN Name.</label>
									<select class="form-control" id="apn_name1"  class="form-control" name="apn_name" required>
										<option value="" >Select An Option</option>
										<?php foreach($apn as $Key => $apval){ ?>
										<option value="<?php echo $apval->apn_value; ?>" ><?php echo $apval->apn_name; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
									<label for="exampleInputEmail1">APN Username.</label>
									<input type="text" class="form-control" placeholder="APN Username" id="apn_username" name="apn_username" value="" >
								</div>
								<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
									<label for="exampleInputEmail1">APN Password.</label>
									<input type="text" class="form-control" placeholder="APN Password" id="apn_pswd" name="apn_pswd"value="" >
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
						
					</fieldset>
				</div>
				<div class="form-row mb-0">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<button id="addbtn" type="submit" class="btn btn-primary pull-right" name="add">Submit</button>
						<a href="<?php echo site_url('devices/lists') ?>" class="btn btn-danger">Back</a>
					</div>
				</div>
				
			</form>
		</div>
		</div>
	</div>
</div>


<script>
    $(document).ready(function () {
        
		$("#hidenoti").hide();
		$("#addbtn").hide();
		
		//$("#presim").hide();
		$("#postsim").hide();
		
		$(document).on("click", "#chk_device", function(){
			
			
			var serial_no = $("#serial_no").val();
			$.ajax({
				url: '<?php echo site_url('devices/validatedevice'); ?>',
				type: "POST",
				data: {serial_no: serial_no},
				success: function (data)
				{
					//alert(data);
					var obj = jQuery.parseJSON(data);
					if(obj.msg == 'Valid Device'){
						if(obj.siminstalled == 2){
							var siminstalled = "Pre Installed";
							$("#postsim").hide();
							$("#presim").show();
							
						}
						else{
							var siminstalled = "Post Installed";
							$("#presim").hide();
							$("#postsim").show();
							
						}
						if(obj.type == 1){
							var type = "Personal Tracker";
						}
						else{
							var type = "Health Tracker";							
						}
						
						//alert((obj.warranty_date);
						$("#imei_no").val(obj.imei_no);
						$("#duration").val('20');
						$("#target_ip").val(obj.target_ip);
						$("#target_port").val(obj.target_port);
						$("#warranty_date").val(obj.warranty_date);
						$("#siminstalled").val(siminstalled);
						$("#type").val(type);
						$("#apn_name").val(obj.apn_name);
						$("#apn_username").val(obj.apn_username);
						$("#apn_pswd").val(obj.apn_pswd);
						$("#mobile_no").val(obj.mobile_no);
						$("#addbtn").show();
						$("#hidenoti").html("").hide();
					}
					else{
						//alert(obj.msg);
						$("#hidenoti").html(obj.msg);
						$("#hidenoti").show();
						$("#addbtn").hide();
						//$("#hidenoti").show().delay( 50000000 ).hide();
					}
					
					//$("#circle_id").html(data);
					//$('#asset_class').has('option').chosen().trigger("chosen:updated");
				}
			});
			
			
		});
		
		
		
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
		
		
	  
	  
	  
		//$(document).on("keyup", "#imei_no", function(){
		//	
		//	var imei = $(this).val();
		//	$("#serial_no").val(imei);
		//	
		//});
	  
	  
    });
</script>