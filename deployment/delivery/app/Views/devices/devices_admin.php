<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-mobile"></i> Device Management
        </div>

		<div class="search-panel">
			<div class="col-xs-12 col-md-12">
				<?php if(isset($errmsg) && !empty($errmsg)){?>
				<div class="error"><?= $errmsg?></div>
				<?php } ?>
			</div>
			
			<?php if ($segment == 'add'){?>
				<form id="devicesadd" action="<?= base_url('devices/devicesAdmin/add');?>" method="post">
					<div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Device ID<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="Serial No" name="serial_no" id="serial_no" maxlength="20" value="<?= old('serial_no'); ?>" required>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">IMEI No.<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="IMEI No" id="imei_no" name="imei_no" maxlength="20" value="<?= old('imei_no'); ?>" required>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Warranty Till</label>
								<input type="text" class="form-control " placeholder="" name="warranty_date"  id="warranty_date" value="">
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Target IP<span class="text-danger">*</span></label>
								<input type="text" class="form-control " placeholder="" name="target_ip"  id="target_ip" value="120.138.8.188" readonly>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Target Port<span class="text-danger">*</span></label>
								<input type="text" class="form-control " placeholder="" name="target_port"  id="target_port" value="7007" readonly>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Time Interval (in sec)<span class="text-danger">*</span></label>
								<input type="text" class="form-control " placeholder="" name="duration"  id="duration" value="20" readonly>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Device Type<span class="text-danger">*</span></label>
							<select name="type" id="type"  class="form-control" required>
								<option value="1" selected>Personal Tracker</option>
								<option value="2" >Health Tracker</option>
							</select>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">GSM Type<span class="text-danger">*</span></label>
							<select name="siminstalled" id="siminstalled"  class="form-control" required>
								<option value="2" selected>Preloaded</option>
								<option value="1" >Postloaded</option>
							</select>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="inact">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<label for="exampleInputEmail1">Mobile No.<span class="text-danger">*</span></label>
									<input type="text" class="form-control" placeholder="Mobile No" id="mobile_no" name="mobile_no" maxlength="10" value="<?= old('mobile_no'); ?>" required>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="exampleInputEmail1">APN Name<span class="text-danger">*</span></label>
										<select name="apn_name" id="apn_name"  class="form-control" required>
											<option value="" >Select An Option</option>
											<?php foreach($apn as $Key => $apval){ ?>
											<option value="<?= $apval->apn_value; ?>" ><?= $apval->apn_name; ?></option>
											<?php } ?>
										</select>
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
							<a href="<?= base_url('devices/lists'); ?>" class="btn btn-danger">Back</a>
							<div class="clearfix"></div>
						</div>
						
					</div>
				</form>
			<?php }else if($segment == 'edit'){ ?>
				<form id="devicesadd" action="<?= base_url('devices/devicesAdmin/edit/'.$main->serial_no);?>" method="post">
					<div class="form-row">
						<input type="hidden" class="form-control" name="dynamiccode" id="dynamiccode" value="<?php echo ($main->dynamiccode != "") ? $main->dynamiccode : 0; ?>">
						<input type="hidden" class="form-control" name="active" id="active" value="<?= esc($main->dev_active); ?>">
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Device ID<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="Serial No" name="serial_no" id="serial_no" maxlength="20" value="<?php echo $main->serial_no; ?>" readonly>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">IMEI No.<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="IMEI No" id="imei_no" name="imei_no" maxlength="20" value="<?php echo $main->imei_no; ?>" required>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Warranty Till</label>
								<input type="text" class="form-control " placeholder="" name="warranty_date"  id="warranty_date" value="<?php echo ($main->warranty_date != null) ? date("d-m-Y", strtotime($main->warranty_date)) : ""; ?>">
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Target IP<span class="text-danger">*</span></label>
								<input type="text" class="form-control " placeholder="" name="target_ip"  id="target_ip" value="<?php echo $main->target_ip; ?>" required>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Target Port<span class="text-danger">*</span></label>
								<input type="text" class="form-control " placeholder="" name="target_port"  id="target_port" value="<?php echo $main->target_port; ?>" required>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="exampleInputEmail1">Time Interval (in sec)<span class="text-danger">*</span></label>
								<input type="text" class="form-control " placeholder="" name="duration"  id="duration" value="<?php echo $main->duration; ?>" readonly>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Device Type<span class="text-danger">*</span></label>
							<select name="type" id="type"  class="form-control" required>
								<option value="1" <?php echo ($main->type == "1") ? "selected" : "";?>>Personal Tracker</option>
								<option value="2" <?php echo ($main->type == "2") ? "selected" : "";?>>Health Tracker</option>
							</select>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">GSM Type<span class="text-danger">*</span></label>
							<select name="siminstalled" id="siminstalled"  class="form-control" required>
								<option value="2" <?php echo ($main->siminstalled == "2") ? "selected" : "";?>>Preloaded</option>
								<option value="1" <?php echo ($main->siminstalled == "1") ? "selected" : "";?>>Postloaded</option>
							</select>
						</div>
						
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="inact">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<label for="exampleInputEmail1">Mobile No.<span class="text-danger">*</span></label>
									<input type="text" class="form-control" placeholder="Mobile No" id="mobile_no" name="mobile_no" maxlength="10" value="<?php echo $main->mobile_no; ?>" required>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="exampleInputEmail1">APN Name<span class="text-danger">*</span></label>
										<select name="apn_name" id="apn_name"  class="form-control">
											<option value="" >Select An Option</option>
											<?php foreach($apn as $Key => $apval){ ?>
											<option value="<?php echo $apval->apn_value; ?>" <?php echo ($apval->apn_value == $main->apn_name) ? "selected" : ""; ?> ><?php echo $apval->apn_name; ?></option>
											<?php } ?>
										</select>
										<!--<input type="text" class="form-control " placeholder="" name="apn_name"  id="apn_name" value="<?php echo $main->apn_name; ?>" >-->
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="exampleInputEmail1">APN Username</label>
										<input type="text" class="form-control " placeholder="" name="apn_username"  id="apn_username" value="<?php echo $main->apn_username; ?>" >
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="exampleInputEmail1">APN Password</label>
										<input type="text" class="form-control " placeholder="" name="apn_pswd"  id="apn_pswd" value="<?php echo $main->apn_pswd; ?>" >
									</div>
								</div>
							</div>
						</div> 
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<button type="submit" class="btn btn-primary pull-right" name="edit">Submit</button>
							<a href="<?php echo site_url('devices/lists') ?>" class="btn btn-danger">Back</a>
							<div class="clearfix"></div>
						</div>
					</div>
				</form>
			<?php } else { ?>
				<form id="devicesadd" action="<?= base_url('devices/devicesAdmin/editmode/'.$serial_no);?>" method="post">
					<div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Configuration Mode<span class="text-danger">*</span></label>
							<select name="devicemodeflag" id="devicemodeflag"  class="form-control" required>
								<option value="1" <?php echo ($main->devicemodeflag == "1") ? "selected" : "";?>>Normal Mode</option>
								<option value="2" <?php echo ($main->devicemodeflag == "2") ? "selected" : "";?>>Edit Mode</option>
							</select>
						</div>
					</div>
					<div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<button type="submit" class="btn btn-primary pull-right" name="edit">Submit</button>
							<a href="<?php echo site_url('devices/lists') ?>" class="btn btn-danger">Back</a>
							<div class="clearfix"></div>
						</div>
					</div>
				</form>
			<?php } ?>
		</div>
	
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
		
		$(document).on("change", "#apn_name", function(){
			
			var apn_name = $(this).val();
			if(apn_name != ""){
				$.ajax({
					url: '<?= base_url('devices/getapnsettings'); ?>',
					type: "POST",
					data: {apn_name: apn_name},
					success: function (data)
					{
						// var obj = jQuery.parseJSON(data);
						//alert(data);
						$("#apn_username").val(data.apnusername);
						$("#apn_pswd").val(data.apnpassword);
						
					}
				});
			}
			else{
				alert("Please select an option.");
				$("#apn_username").val("");
				$("#apn_pswd").val("");
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
		
    });
</script>