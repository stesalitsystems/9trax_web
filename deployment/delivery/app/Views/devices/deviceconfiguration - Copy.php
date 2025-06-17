<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-mobile"></i> Device Management
        </div>

		<div class="search-panel">
			<div class="col-xs-12 col-md-12">
				<?php echo form_open("devices/deviceconfiguration/".$dev_id, array("autocomplete" => "off", "id" => "devicesconf", "novalidate" => "true" )) ?>
		
					<?php if(!empty($getdev)){ ?>
						<div class="form-row">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<fieldset class="fldst">
									<legend>Basic Configuration</legend>
									<div class="form-row">
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Device Name<span class="text-danger">*</span></label>
											<input type="text" class="form-control " placeholder="" name="device_name"  value="<?php echo $getdev->device_name;?>" required>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Icon</label>
											
												<select name="icon_details" id="icon_details"  class="form-control" style="width: 220px !important;">
													<option value="tablet.png">Select</option>
													<option value="human.png" <?php if($getdev->icon_details == 'human.png'){?> selected<?php } ?>>Human</option>
													<option value="male.png" <?php if($getdev->icon_details == 'male.png'){?> selected<?php } ?>>Male1</option>
													<option value="male-walking.png" <?php if($getdev->icon_details == 'male-walking.png'){?> selected<?php } ?>>Male2</option>
													<option value="female-walking.png" <?php if($getdev->icon_details == 'female-walking.png'){?> selected<?php } ?>>Female</option>
													<option value="bike.png" <?php if($getdev->icon_details == 'bike.png'){?> selected<?php } ?>>Bike</option>
													<option value="car_v2.png" <?php if($getdev->icon_details == 'car_v2.png'){?> selected<?php } ?>>Car</option>
													<option value="bus.png" <?php if($getdev->icon_details == 'bus.png'){?> selected<?php } ?>>Bus1</option>
													<option value="bus2_v1.png" <?php if($getdev->icon_details == 'bus2_v1.png'){?> selected<?php } ?>>Bus2</option>
												</select>
												<?php 
													if(!empty($getdev->icon_details)){
														$obj_sym_img = '<img src="'.base_url().'assets/iconset/'.$getdev->icon_details.'"/>';
													}
													else {
														$obj_sym_img = '';
													}
												?>
												<span id="imgdiv" style="position: absolute;right: 0;top: 32px;"><?php echo $obj_sym_img; ?></span>
											</div>
											
										
										
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Time Interval (in sec)<span class="text-danger">*</span></label>
											<select name="duration" id="duration"  class="form-control">
												<option value="20" <?php if($getdev->duration == 20){?> selected<?php } ?>>20</option>
												<option value="30" <?php if($getdev->duration == 30){?> selected<?php } ?>>30</option>
												<option value="40" <?php if($getdev->duration == 40){?> selected<?php } ?>>40</option>
												<option value="50" <?php if($getdev->duration == 50){?> selected<?php } ?>>50</option>
												<option value="60" <?php if($getdev->duration == 60){?> selected<?php } ?>>60</option>
												<option value="120" <?php if($getdev->duration == 120){?> selected<?php } ?>>120</option>
												<option value="180" <?php if($getdev->duration == 180){?> selected<?php } ?>>180</option>
												<option value="240" <?php if($getdev->duration == 240){?> selected<?php } ?>>240</option>
												<option value="300" <?php if($getdev->duration == 300){?> selected<?php } ?>>300</option>
											</select>
										</div>
										<?php if($getddm->siminstalled == 2){ ?>
											<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
												<label for="exampleInputEmail1">Mobile<span class="text-danger">*</span></label> 
												<input type="text" class="form-control numeric" placeholder="Mobile No." minlength="10" maxlength="10" name="mobile_no"  value="<?php echo $getddm->mobile_no;?>" readonly>
											</div>
											<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
												<label for="exampleInputEmail1">APN Name<span class="text-danger">*</span></label>
												<input type="text" class="form-control " placeholder="APN Name" name="apn_name"  value="<?php echo $getdev->apn_name;?>" readonly>
											</div>
											<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
												<label for="exampleInputEmail1">APN Username</label> 
												<input type="text" class="form-control " placeholder="APN Username" name="apn_username"  value="<?php echo $getdev->apn_username;?>" readonly>
											</div>
											<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
												<label for="exampleInputEmail1">APN Password</label> 
												<input type="text" class="form-control " placeholder="APN Password" name="apn_pswd"  value="<?php echo $getdev->apn_pswd;?>" readonly>
											</div>
										<?php }else{ ?>
											<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
												<label for="exampleInputEmail1">Mobile<span class="text-danger">*</span></label> 
												<input type="text" class="form-control numeric" placeholder="Mobile No." minlength="10" maxlength="10" name="mobile_no"  value="<?php echo $getddm->mobile_no;?>" required>
											</div>
											<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
												<label for="exampleInputEmail1">APN Name<span class="text-danger">*</span></label> 
												<select class="form-control" id="apn_name"  class="form-control" name="apn_name" required>
													<option value="" >Select An Option</option>
													<?php foreach($apn as $Key => $apval){ ?>
													<option value="<?php echo $apval->apn_value; ?>" <?php echo ($apval->apn_value == $getdev->apn_name) ? "selected" : ""; ?>><?php echo $apval->apn_name; ?></option>
													<?php } ?>
												</select>
											</div>
											<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
												<label for="exampleInputEmail1">APN Username</label> 
												<input type="text" class="form-control " placeholder="APN Username" name="apn_username"  value="<?php echo $getdev->apn_username;?>" >
											</div>
											<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
												<label for="exampleInputEmail1">APN Password</label> 
												<input type="text" class="form-control " placeholder="APN Password" name="apn_pswd"  value="<?php echo $getdev->apn_pswd;?>" >
											</div>
										<?php } ?>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Target IP<span class="text-danger">*</span></label> 
											<input type="text" class="form-control " placeholder="" name="target_ip"  value="<?php echo $getdev->target_ip;?>" readonly>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Target Port<span class="text-danger">*</span></label> 
											<input type="text" class="form-control " placeholder="" name="target_port"  value="<?php echo $getdev->target_port;?>" readonly>
										</div>
									</div>
								</fieldset>
							</div>
						
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<fieldset class="fldst">
									<legend>Advanced Configuration</legend>
									<div class="form-row">
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">SOS - 1</label>
											<input type="text" class="form-control numeric" placeholder="" name="sos1_no" id="sos1_no"  minlength="10" maxlength="10" value="<?php echo substr($getdev->sos1_no,1); ?>" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">SOS - 2</label>
											<input type="text" class="form-control numeric" placeholder="" name="sos2_no" id="sos2_no"  minlength="10" maxlength="10" value="<?php echo substr($getdev->sos2_no,1); ?>" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">SOS - 3</label>
											<input type="text" class="form-control numeric" placeholder="" name="sos3_no" id="sos3_no"  minlength="10" maxlength="10" value="<?php echo substr($getdev->sos3_no,1); ?>" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Call - 1</label>
											<input type="text" class="form-control numeric" placeholder="" name="call1_no" id="call1_no"  minlength="10" maxlength="10" value="<?php echo substr($getdev->call1_no,1); ?>" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Call - 2</label>
											<input type="text" class="form-control numeric" placeholder="" name="call2_no" id="call2_no"  minlength="10" maxlength="10" value="<?php echo substr($getdev->call2_no,1); ?>" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Call - 3</label>
											<input type="text" class="form-control numeric" placeholder="" name="call3_no" id="call3_no"  minlength="10" maxlength="10" value="<?php echo substr($getdev->call3_no,1); ?>" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Working Time From</label>
											<input type="text" class="form-control " placeholder="" name="working_start_time" id="working_start_time"  value="<?php echo $getdev->working_start_time; ?>">
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Working Time To</label>
											<input type="text" class="form-control " placeholder="" name="working_end_time" id="working_end_time"  value="<?php echo $getdev->working_end_time; ?>">
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Switch Off Restriction From</label>
											<input type="text" class="form-control " placeholder="" name="switchoffrestrict_start_time" id="switchoffrestrict_start_time"  value="<?php echo $getdev->switchoffrestrict_start_time; ?>">
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Switch Off Restriction To</label>
											<input type="text" class="form-control " placeholder="" name="switchoffrestrict_end_time" id="switchoffrestrict_end_time"  value="<?php echo $getdev->switchoffrestrict_end_time; ?>">
										</div>
									</div>
								</fieldset>
							</div>
							
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<label for="exampleInputEmail1">Configuration Method<span class="text-danger">*</span></label>
								<div class="permissiongroup">
									<label class="checkbox-inline">
										<input type="checkbox" name="GPRS" value="GPRS" checked onclick="return false;"> GPRS Configuration
									</label>
									<label class="checkbox-inline" <?php if(isset($this->session->userdata('login_sess_data')->configurationsms) && $this->session->userdata('login_sess_data')->configurationsms == 'N'){ ?>style="display:none;"<?php } ?>>
										<input type="checkbox" name="SMS" value="SMS"> SMS Configuration
									</label>
								</div>
							</div>
							
						</div>
						<div class="clearfix"></div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="form-group">
								<a href="<?php echo site_url('/') ?>devices/lists" class="btn btn-danger">Back</a>
								<button type="submit" class="btn btn-primary pull-right" name="add">Submit</button>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }else{ ?>
						<div class="form-row">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<fieldset class="fldst">
									<legend>Basic Configuration</legend>
									<div class="form-row">
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Device Name<span class="text-danger">*</span></label>
											<input type="text" class="form-control " placeholder="" name="device_name"  value="" required>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Icon</label>
											<select name="icon_details" id="icon_details"  class="form-control" style="width: 220px !important;">
												<option value="tablet.png">Select</option>
												<option value="human.png">Human</option>
												<option value="male.png">Male1</option>
												<option value="male-walking.png">Male2</option>
												<option value="female-walking.png">Female</option>
												<option value="bike.png">Bike</option>
												<option value="car_v2.png">Car</option>
												<option value="bus.png">Bus1</option>
												<option value="bus2_v1.png">Bus2</option>
											</select>
											<span id="imgdiv" style="style="position: absolute;right: 0;top: 32px;""></span>
										</div>
										
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Time Interval (in sec)<span class="text-danger">*</span></label>
											<select name="duration" id="duration"  class="form-control">
												<option value="20">20</option>
												<option value="30">30</option>
												<option value="40">40</option>
												<option value="50">50</option>
												<option value="60">60</option>
												<option value="120">120</option>
												<option value="180">180</option>
												<option value="240">240</option>
												<option value="300">300</option>
											</select>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Mobile<span class="text-danger">*</span></label> 
											<input type="text" class="form-control numeric" placeholder="Mobile No." minlength="10" maxlength="10" name="mobile_no"  value="" required>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">APN Name<span class="text-danger">*</span></label> 
											<select class="form-control" id="apn_name"  class="form-control" name="apn_name" required>
												<option value="" >Select An Option</option>
												<?php foreach($apn as $Key => $apval){ ?>
												<option value="<?php echo $apval->apn_value; ?>" ><?php echo $apval->apn_name; ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">APN Username</label> 
											<input type="text" class="form-control " placeholder="APN Username" name="apn_username"  value="" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">APN Password</label> 
											<input type="text" class="form-control " placeholder="APN Password" name="apn_pswd"  value="" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Target IP<span class="text-danger">*</span></label> 
											<input type="text" class="form-control " placeholder="" name="target_ip"  value="120.138.8.188" readonly>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Target Port<span class="text-danger">*</span></label> 
											<input type="text" class="form-control " placeholder="" name="target_port"  value="6991" readonly>
										</div>
									</div>
								</fieldset>
							</div>
						
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<fieldset class="fldst">
									<legend>Advanced Configuration</legend>
									<div class="form-row">
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">SOS - 1</label>
											<input type="text" class="form-control numeric" placeholder="" name="sos1_no" id="sos1_no"  minlength="10" maxlength="10" value="" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">SOS - 2</label>
											<input type="text" class="form-control numeric" placeholder="" name="sos2_no" id="sos2_no"  minlength="10" maxlength="10" value="" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">SOS - 3</label>
											<input type="text" class="form-control numeric" placeholder="" name="sos3_no" id="sos3_no"  minlength="10" maxlength="10" value="" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Call - 1</label>
											<input type="text" class="form-control numeric" placeholder="" name="call1_no" id="call1_no"  minlength="10" maxlength="10" value="" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Call - 2</label>
											<input type="text" class="form-control numeric" placeholder="" name="call2_no" id="call2_no"  minlength="10" maxlength="10" value="" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Call - 3</label>
											<input type="text" class="form-control numeric" placeholder="" name="call3_no" id="call3_no"  minlength="10" maxlength="10" value="" >
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Working Time From</label>
											<input type="text" class="form-control " placeholder="" name="working_start_time" id="working_start_time"  value="00:00:00">
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Working Time To</label>
											<input type="text" class="form-control " placeholder="" name="working_end_time" id="working_end_time"  value="00:00:00">
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Switch Off Restriction From</label>
											<input type="text" class="form-control " placeholder="" name="switchoffrestrict_start_time" id="switchoffrestrict_start_time"  value="00:00:00">
										</div>
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">Switch Off Restriction To</label>
											<input type="text" class="form-control " placeholder="" name="switchoffrestrict_end_time" id="switchoffrestrict_end_time"  value="00:00:00">
										</div>
									</div>
								</fieldset>
							</div>
							
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<label for="exampleInputEmail1">Configuration Method<span class="text-danger">*</span></label>
								<div class="permissiongroup">
									<label class="checkbox-inline">
										<input type="checkbox" name="GPRS" value="GPRS" checked onclick="return false;"> GPRS Configuration
									</label>
									<label class="checkbox-inline" <?php if(isset($this->session->userdata('login_sess_data')->configurationsms) && $this->session->userdata('login_sess_data')->configurationsms == 'N'){ ?>style="display:none;"<?php } ?>>
										<input type="checkbox" name="SMS" value="SMS"> SMS Configuration
									</label>
								</div>
							</div>
							
						</div>
						<div class="clearfix"></div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="form-group">
								<a href="<?php echo site_url('/') ?>devices/lists" class="btn btn-danger">Back</a>
								<button type="submit" class="btn btn-primary pull-right" name="add">Submit</button>
							</div>
						</div>
					<?php } ?>
					
				<?php echo form_close(); ?>
			</div>
		</div>
	
	</div>
</div>
		

<script>
$(document).ready(function () {
	$('#working_start_time').timepicker();
	$('#working_end_time').timepicker();
	$( "#working_start_time" ).keydown(function() {
		return false;
	});
	$('#working_end_time').keydown(function() {
		return false;
	});
	$('#working_start_time').change(function(){
		var clockval = this.value+':00';
		$("#working_start_time").val(clockval);
		/*if($("#working_end_time").val()!='' && $("#working_end_time").val()!='00:00:00'){
		var starttime_arr = $("#working_start_time").val().split(':');
		var start_h = starttime_arr[0];
		var start_m = starttime_arr[1];
		var endtime_arr = $("#working_end_time").val().split(':');
		var end_h = endtime_arr[0];
		var end_m = endtime_arr[1];
		var d1 = new Date(2000, 0, 1,  start_h, start_m);
		var d2 = new Date(2000, 0, 1,  end_h, end_m);
		if(d1>d2){
			alert('End Time must be greater than Start Time');
			$("#working_start_time").val('00:00:00');
		}
		}*/
	});
	
	$('#working_end_time').change(function(){
		var clockval = this.value+':00';
		$("#working_end_time").val(clockval);
		/*if($("#working_start_time").val()!='' && $("#working_start_time").val()!='00:00:00'){
		var starttime_arr = $("#working_start_time").val().split(':');
		var start_h = starttime_arr[0];
		var start_m = starttime_arr[1];
		var endtime_arr = $("#working_end_time").val().split(':');
		var end_h = endtime_arr[0];
		var end_m = endtime_arr[1];
		var d1 = new Date(2000, 0, 1,  start_h, start_m);
		var d2 = new Date(2000, 0, 1,  end_h, end_m);
		if(d1>d2){
			alert('End Time must be greater than Start Time');
			$("#working_end_time").val('00:00:00');
		}
		}*/
	});
	
	$('#switchoffrestrict_start_time').timepicker();
	$('#switchoffrestrict_end_time').timepicker();
	$( "#switchoffrestrict_start_time" ).keydown(function() {
		return false;
	});
	$('#switchoffrestrict_end_time').keydown(function() {
		return false;
	});
	$('#switchoffrestrict_start_time').change(function(){
		var clockval = this.value+':00';
		$("#switchoffrestrict_start_time").val(clockval);
		/*if($("#switchoffrestrict_end_time").val()!='' && $("#switchoffrestrict_end_time").val()!='00:00:00'){
		var starttime_arr = $("#switchoffrestrict_start_time").val().split(':');
		var start_h = starttime_arr[0];
		var start_m = starttime_arr[1];
		var endtime_arr = $("#switchoffrestrict_end_time").val().split(':');
		var end_h = endtime_arr[0];
		var end_m = endtime_arr[1];
		var d1 = new Date(2000, 0, 1,  start_h, start_m);
		var d2 = new Date(2000, 0, 1,  end_h, end_m);
		if(d1>d2){
			alert('End Time must be greater than Start Time');
			$("#switchoffrestrict_start_time").val('00:00:00');
		}
		}*/
	});
	
	$('#switchoffrestrict_end_time').change(function(){
		var clockval = this.value+':00';
		$("#switchoffrestrict_end_time").val(clockval);
		/*if($("#switchoffrestrict_start_time").val()!='' && $("#switchoffrestrict_start_time").val()!='00:00:00'){
		var starttime_arr = $("#switchoffrestrict_start_time").val().split(':');
		var start_h = starttime_arr[0];
		var start_m = starttime_arr[1];
		var endtime_arr = $("#switchoffrestrict_end_time").val().split(':');
		var end_h = endtime_arr[0];
		var end_m = endtime_arr[1];
		var d1 = new Date(2000, 0, 1,  start_h, start_m);
		var d2 = new Date(2000, 0, 1,  end_h, end_m);
		if(d1>d2){
			alert('End Time must be greater than Start Time');
			$("#switchoffrestrict_end_time").val('00:00:00');
		}
		}*/
	});
	
	$("#devicesconf").validate({
		submitHandler: function(form) {
			var sos1_no = $("#sos1_no").val();
			var sos2_no = $("#sos2_no").val();
			var sos3_no = $("#sos3_no").val();
			var call1_no = $("#call1_no").val();
			var call2_no = $("#call2_no").val();
			var call3_no = $("#call3_no").val();
			var err_flg = 0;
			if(sos1_no == '' && sos2_no != ''){
				alert('Please enter SOS - 1');
				err_flg++;
			}
			if(sos1_no == '' && sos3_no != ''){
				alert('Please enter SOS - 1');
				err_flg++;
			}
			if(sos2_no == '' && sos3_no != ''){
				alert('Please enter SOS - 2');
				err_flg++;
			}
			if(call1_no == '' && call2_no != ''){
				alert('Please enter Call - 1');
				err_flg++;
			}
			if(call1_no == '' && call3_no != ''){
				alert('Please enter Call - 1');
				err_flg++;
			}
			if(call2_no == '' && call3_no != ''){
				alert('Please enter Call - 2');
				err_flg++;
			}
			var starttime_arr = $("#working_start_time").val().split(':');
			var start_h = starttime_arr[0];
			var start_m = starttime_arr[1];
			var endtime_arr = $("#working_end_time").val().split(':');
			var end_h = endtime_arr[0];
			var end_m = endtime_arr[1];
			var d1 = new Date(2000, 0, 1,  start_h, start_m);
			var d2 = new Date(2000, 0, 1,  end_h, end_m);
			if(d1>d2){
				alert('Working End Time must be greater than Start Time');
				$("#working_start_time").val('00:00:00');
				$("#working_end_time").val('00:00:00');
				err_flg++;
			}			
			
			var switchstarttime_arr = $("#switchoffrestrict_start_time").val().split(':');
			var switchstart_h = switchstarttime_arr[0];
			var switchstart_m = switchstarttime_arr[1];
			var switchendtime_arr = $("#switchoffrestrict_end_time").val().split(':');
			var switchend_h = switchendtime_arr[0];
			var switchend_m = switchendtime_arr[1];
			var switchd1 = new Date(2000, 0, 1,  switchstart_h, switchstart_m);
			var switchd2 = new Date(2000, 0, 1,  switchend_h, switchend_m);
			if(switchd1>switchd2){
				alert('Switch Off End Time must be greater than Start Time');
				$("#switchoffrestrict_start_time").val('00:00:00');
				$("#switchoffrestrict_end_time").val('00:00:00');
				err_flg++;
			}
			if(err_flg == 0){
				form.submit();
			}
		}
	});
	
	$('#icon_details').change(function(){
		var img = '<?php echo base_url();?>'+'assets/iconset/'+$('#icon_details').val();
		$('#imgdiv').html('<img src="'+img+'" />');
	});
	
});
function isNumberKey(evt){
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
}
</script>