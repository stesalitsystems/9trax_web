<style>
    .form-row{
        margin: 10px;
    }
	#btnadd, #btnedit {
		cursor:pointer;
	}
	body{
		background: #3879BB none repeat scroll 0% 0%;
	}
</style>
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
		
		$("#btnadd").on('click',function () {
			var chk_flag = 1;
			var device_type = $('#device_type').val();
			var deviceid = $('#deviceid').val();
			var device_name = $('#device_name').val();
			var icon_details = $('#icon_details').val();
			var images_details = $('#images_details').val();
			var sos1_no = $('#sos1_no').val();
			var sos2_no = $('#sos2_no').val();
			var sos3_no = $('#sos3_no').val();
			var call1_no = $('#call1_no').val();
			var call2_no = $('#call2_no').val();
			var call3_no = $('#call3_no').val();
			var duration = $('#duration').val();
			var apn_name = $('#apn_name').val();
			var apn_username = $('#apn_username').val();
			var apn_pswd = $('#apn_pswd').val();
			var target_ip = $('#target_ip').val();
			var target_port = $('#target_port').val();
			var working_start_time = $('#working_start_time').val();
			var working_end_time = $('#working_end_time').val();
			var switchoffrestrict_start_time = $('#switchoffrestrict_start_time').val();
			var switchoffrestrict_end_time = $('#switchoffrestrict_end_time').val();
			var sms = 'N';
			if($('#SMS').is(':checked')){
				sms = 'Y';
			}
			if(device_name == ''){
				chk_flag = 0;
				$('#device_name').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(sos1_no == '' && sos2_no != ''){
				chk_flag = 0;
				$('#sos1_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(sos1_no == '' && sos3_no != ''){
				chk_flag = 0;
				$('#sos1_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(call1_no == '' && call2_no != ''){
				chk_flag = 0;
				$('#call1_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(call1_no == '' && call3_no != ''){
				chk_flag = 0;
				$('#call1_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(sos2_no == '' && sos3_no != ''){
				chk_flag = 0;
				$('#sos2_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(call2_no == '' && call3_no != ''){
				chk_flag = 0;
				$('#call2_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			/*if(sos3_no == ''){
				chk_flag = 0;
				$('#sos3_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}*/
			/*if(device_type == 'Device'){
				if(apn_name == ''){
					chk_flag = 0;
					$('#apn_name').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
				}
				if(apn_username == ''){
					chk_flag = 0;
					$('#apn_username').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
				}
				if(apn_pswd == ''){
					chk_flag = 0;
					$('#apn_pswd').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
				}
			}*/
			
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
				chk_flag = 0;
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
				chk_flag = 0;
			}
			
			if(chk_flag == 1){
				var url = '<?php echo site_url('devices/submitconfiurationajax'); ?>';
				$.ajax({
				method: "POST",
				url: url,
				data: { deviceid: deviceid, device_name: device_name, icon_details: icon_details, images_details: images_details, sos1_no: sos1_no, sos2_no: sos2_no, sos3_no: sos3_no, call1_no: call1_no, call2_no: call2_no, call3_no: call3_no, duration: duration, apn_name: apn_name, apn_username: apn_username, apn_pswd: apn_pswd, target_ip: target_ip, target_port: target_port, working_start_time: working_start_time, working_end_time: working_end_time, sms: sms, submittype: 'add', device_type: device_type, switchoffrestrict_start_time: switchoffrestrict_start_time, switchoffrestrict_end_time: switchoffrestrict_end_time },
				success: function(msg) {
					alert(msg);
				}
				});
			}
		});
		$("#btnedit").on('click',function () {
			var chk_flag = 1;
			var device_type = $('#device_type').val();
			var deviceid = $('#deviceid').val();
			var device_name = $('#device_name').val();
			var icon_details = $('#icon_details').val();
			var images_details = $('#images_details').val();
			var sos1_no = $('#sos1_no').val();
			var sos2_no = $('#sos2_no').val();
			var sos3_no = $('#sos3_no').val();
			var call1_no = $('#call1_no').val();
			var call2_no = $('#call2_no').val();
			var call3_no = $('#call3_no').val();
			var duration = $('#duration').val();
			var apn_name = $('#apn_name').val();
			var apn_username = $('#apn_username').val();
			var apn_pswd = $('#apn_pswd').val();
			var target_ip = $('#target_ip').val();
			var target_port = $('#target_port').val();
			var working_start_time = $('#working_start_time').val();
			var working_end_time = $('#working_end_time').val();
			var switchoffrestrict_start_time = $('#switchoffrestrict_start_time').val();
			var switchoffrestrict_end_time = $('#switchoffrestrict_end_time').val();
			var sms = 'N';
			if($('#SMS').is(':checked')){
				sms = 'Y';
			}
			if(device_name == ''){
				chk_flag = 0;
				$('#device_name').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(sos1_no == '' && sos2_no != ''){
				chk_flag = 0;
				$('#sos1_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(sos1_no == '' && sos3_no != ''){
				chk_flag = 0;
				$('#sos1_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(call1_no == '' && call2_no != ''){
				chk_flag = 0;
				$('#call1_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(call1_no == '' && call3_no != ''){
				chk_flag = 0;
				$('#call1_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(sos2_no == '' && sos3_no != ''){
				chk_flag = 0;
				$('#sos2_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			if(call2_no == '' && call3_no != ''){
				chk_flag = 0;
				$('#call2_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}
			/*if(sos3_no == ''){
				chk_flag = 0;
				$('#sos3_no').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
			}*/
			/*if(device_type == 'Device'){
				if(apn_name == ''){
					chk_flag = 0;
					$('#apn_name').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
				}
				if(apn_username == ''){
					chk_flag = 0;
					$('#apn_username').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
				}
				if(apn_pswd == ''){
					chk_flag = 0;
					$('#apn_pswd').css({'border-color': '#e7505a','border-style': 'solid','border-width': 'medium'});
				}
			}*/
			
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
				chk_flag = 0;
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
				chk_flag = 0;
			}
			
			if(chk_flag == 1){
				var url = '<?php echo site_url('devices/submitconfiurationajax'); ?>';
				$.ajax({
				method: "POST",
				url: url,
				data: { deviceid: deviceid, device_name: device_name, icon_details: icon_details, images_details: images_details, sos1_no: sos1_no, sos2_no: sos2_no, sos3_no: sos3_no, call1_no: call1_no, call2_no: call2_no, call3_no: call3_no, duration: duration, apn_name: apn_name, apn_username: apn_username, apn_pswd: apn_pswd, target_ip: target_ip, target_port: target_port, working_start_time: working_start_time, working_end_time: working_end_time, sms: sms, submittype: 'edit', device_type: device_type, switchoffrestrict_start_time: switchoffrestrict_start_time, switchoffrestrict_end_time: switchoffrestrict_end_time },
				success: function(msg) {
					alert(msg);
				}
				});
			}
		});
		
		$('#icon_details').change(function(){
			var img = '<?php echo base_url();?>'+$('#icon_details').val();
			$('#imgdiv').html('<img src="'+img+'" style="height: 2.5em;"/>');
		});
	});
	function isNumberKey(evt){
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (charCode > 31 && (charCode < 48 || charCode > 57))
			return false;
		return true;
	}
</script>
<div class="container-fluid newcw">
    <div class="row">
        <input type="hidden" name="deviceid" id="deviceid" value="<?php echo $dev_id;?>" />
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			
			<?php if(!empty($getdev)){ ?>
				<div class="form-row">
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">Device Type<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder="" name="device_type" id="device_type" value="<?php echo ($getddm['typeofdevice'] == 'M')? 'Mobile' : 'Device';?>" readonly>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Device ID</label>
							<input type="text" class="form-control " placeholder="" value="<?php echo $getddm['serial_no'];?>" readonly>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Device Name<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder="" name="device_name" id="device_name"  value="<?php echo $getdev['device_name_new'];?>" required>
						</div>
					</div>
					<div class="col-4">
						<div class="form-group">
							<label for="exampleInputEmail1">Icon</label>
							<select name="icon_details" id="icon_details"  class="form-control">
								<?php foreach($icons as $allicon){ ?>
									<option value="<?php echo $allicon->icon_path; ?>" <?php echo ($getdev['icon_details'] == $allicon->icon_path) ?"selected" : ""; ?>><?php echo $allicon->name; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<?php 
						if(!empty($getdev['icon_details'])){
							$obj_sym_img = '<img src="'.base_url().$getdev['icon_details'].'" style="height: 2.5em;"/>';
						}
						else {
							$obj_sym_img = '';
						}
					?>
					<div class="col-2" id="imgdiv" style="padding-top: 1.5em;padding-left: 1em;"><?php echo $obj_sym_img;?></div>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">Images</label>
							<input type="text" class="form-control " placeholder="" name="images_details" id="images_details"  value="" >
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">SOS - 1 <?php if($getdev['sosflag'] == 1 && $getddm['typeofdevice'] != 'M'){ echo "(NC)";}?></label>
							<input type="text" class="form-control " placeholder="" name="sos1_no" id="sos1_no"  maxlength="10" value="<?php echo ($getddm['typeofdevice'] == 'M')? $getdev['sos1_no'] : $getdev['sos1_no'];?>" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">SOS - 2 <?php if($getdev['sosflag'] == 1 && $getddm['typeofdevice'] != 'M'){ echo "(NC)";}?></label>
							<input type="text" class="form-control " placeholder="" name="sos2_no" id="sos2_no"  maxlength="10" value="<?php echo ($getddm['typeofdevice'] == 'M')? $getdev['sos2_no'] : $getdev['sos2_no'];?>" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">SOS - 3 <?php if($getdev['sosflag'] == 1 && $getddm['typeofdevice'] != 'M'){ echo "(NC)";}?></label>
							<input type="text" class="form-control " placeholder="" name="sos3_no" id="sos3_no"  maxlength="10" value="<?php echo ($getddm['typeofdevice'] == 'M')? $getdev['sos3_no'] : $getdev['sos3_no'];?>" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Time Interval (in sec)<span class="text-danger">*</span> <?php if($getdev['intervalflag'] == 1 && $getddm['typeofdevice'] != 'M'){ echo "(NC)";}?></label>							
							<select name="duration" id="duration"  class="form-control">
								<option value="10" <?php if($getdev['duration'] == 10){?> selected<?php } ?>>10</option>
								<option value="20" <?php if($getdev['duration'] == 20){?> selected<?php } ?>>20</option>
								<option value="30" <?php if($getdev['duration'] == 30){?> selected<?php } ?>>30</option>
								<option value="40" <?php if($getdev['duration'] == 40){?> selected<?php } ?>>40</option>
								<option value="50" <?php if($getdev['duration'] == 50){?> selected<?php } ?>>50</option>
								<option value="60" <?php if($getdev['duration'] == 60){?> selected<?php } ?>>60</option>
								<option value="120" <?php if($getdev['duration'] == 120){?> selected<?php } ?>>120</option>
								<option value="180" <?php if($getdev['duration'] == 180){?> selected<?php } ?>>180</option>
								<option value="240" <?php if($getdev['duration'] == 240){?> selected<?php } ?>>240</option>
								<option value="300" <?php if($getdev['duration'] == 300){?> selected<?php } ?>>300</option>
							</select>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Call - 1 <?php if($getdev['callflag'] == 1 && $getddm['typeofdevice'] != 'M'){ echo "(NC)";}?></label>
							<input type="text" class="form-control " placeholder="" name="call1_no" id="call1_no"  maxlength="10" value="<?php echo ($getddm['typeofdevice'] == 'M')? $getdev['call1_no'] : $getdev['call1_no'];?>" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Call - 2 <?php if($getdev['callflag'] == 1 && $getddm['typeofdevice'] != 'M'){ echo "(NC)";}?></label>
							<input type="text" class="form-control " placeholder="" name="call2_no" id="call2_no"  maxlength="10" value="<?php echo ($getddm['typeofdevice'] == 'M')? $getdev['call2_no'] : $getdev['call2_no'];?>" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Call - 3 <?php if($getdev['callflag'] == 1 && $getddm['typeofdevice'] != 'M'){ echo "(NC)";}?></label>
							<input type="text" class="form-control " placeholder="" name="call3_no" id="call3_no"  maxlength="10" value="<?php echo ($getddm['typeofdevice'] == 'M')? $getdev['call3_no'] : $getdev['call3_no'];?>" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<?php if($getddm['typeofdevice'] != 'M'){ ?>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">APN Name<span class="text-danger">*</span> <?php if($getdev['apnflag'] == 1){ echo "(NC)";}?></label>
							<input type="text" class="form-control " placeholder="" name="apn_name" id="apn_name"  value="<?php echo $getdev['apn_name'];?>" <?php if($getddm['siminstalled'] == 2){ ?>readonly <?php } ?>>
						</div>
					</div>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">APN Username<span class="text-danger">*</span> <?php if($getdev['apnflag'] == 1){ echo "(NC)";}?></label>
							<input type="text" class="form-control " placeholder="" name="apn_username" id="apn_username"  value="<?php echo $getdev['apn_username'];?>" <?php if($getddm['siminstalled'] == 2){ ?>readonly <?php } ?>>
						</div>
					</div>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">APN Password<span class="text-danger">*</span> <?php if($getdev['apnflag'] == 1){ echo "(NC)";}?></label>
							<input type="text" class="form-control " placeholder="" name="apn_pswd" id="apn_pswd"  value="<?php echo $getdev['apn_pswd'];?>" <?php if($getddm['siminstalled'] == 2){ ?>readonly <?php } ?>>
						</div>
					</div>
					<?php } ?>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">Target IP<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder="" name="target_ip" id="target_ip"  value="120.138.8.188" readonly>
						</div>
					</div>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">Target Port<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder="" name="target_port" id="target_port"  value="6991" readonly>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1"><?php echo 'Working Time From';?></label>
							<input type="text" class="form-control " placeholder="" name="working_start_time" id="working_start_time"  value="<?php echo ($getdev['working_start_time'] != '')? $getdev['working_start_time'] : '00:00:00';?>">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1"><?php echo 'Working Time To';?></label>
							<input type="text" class="form-control " placeholder="" name="working_end_time" id="working_end_time"  value="<?php echo ($getdev['working_end_time'] != '')? $getdev['working_end_time'] : '00:00:00';?>">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1"><?php echo 'Switch Off Restriction From';?></label>
							<input type="text" class="form-control " placeholder="" name="switchoffrestrict_start_time" id="switchoffrestrict_start_time"  value="<?php echo ($getdev['switchoffrestrict_start_time'] != '')? $getdev['switchoffrestrict_start_time'] : '00:00:00';?>">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1"><?php echo 'Switch Off Restriction To';?></label>
							<input type="text" class="form-control " placeholder="" name="switchoffrestrict_end_time" id="switchoffrestrict_end_time"  value="<?php echo ($getdev['switchoffrestrict_end_time'] != '')? $getdev['switchoffrestrict_end_time'] : '00:00:00';?>">
						</div>
					</div>
					<?php if($getddm['typeofdevice'] == 'M'){ ?>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Device Activation Code<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder=""  value="<?php echo $getddm['dynamiccode'];?>" readonly>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="clearfix"></div>
				<?php if($getdev['devicemodeflag'] == 2){ ?>
				<?php if($getddm['typeofdevice'] != 'M'){ ?>
				<div class="form-row">
					<div class="col-6">
						<label class="checkbox-inline"><input type="checkbox" name="GPRS" id="GPRS" value="GPRS" checked onclick="return false;"> GPRS Configuration</label>
						<label class="checkbox-inline" <?php if(isset($sessdata['configurationsms']) && $sessdata['configurationsms'] == 'N'){ ?>style="display:none;"<?php } ?>><input type="checkbox" name="SMS" id="SMS" value="SMS"> SMS Configuration</label>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php } ?>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="form-group">
						<button type="submit" class="btn btn-primary pull-right" name="edit" id="btnedit" >Submit</button>
					</div>
				</div>
				<?php } ?>
			<?php }else{ ?>
				<div class="form-row">
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">Device Type<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder="" name="device_type" id="device_type"  value="<?php echo ($getddm['typeofdevice'] == 'M')? 'Mobile' : 'Device';?>" readonly>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Device ID</label>
							<input type="text" class="form-control " placeholder="" value="<?php echo $getddm['serial_no'];?>" readonly>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Device Name<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder="" name="device_name" id="device_name"  value="" required>
						</div>
					</div>
					<div class="col-4">
						<div class="form-group">
							<label for="exampleInputEmail1">Icon</label>
							<select name="icon_details" id="icon_details"  class="form-control">
								<?php foreach($icons as $allicon){ ?>
									<option value="<?php echo $allicon->icon_path; ?>"><?php echo $allicon->name; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-2" id="imgdiv" style="padding-top: 1.5em;padding-left: 1em;"></div>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">Images</label>
							<input type="text" class="form-control " placeholder="" name="images_details" id="images_details"  value="" >
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">SOS - 1</label>
							<input type="text" class="form-control " placeholder="" name="sos1_no" id="sos1_no"  maxlength="10" value="" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">SOS - 2</label>
							<input type="text" class="form-control " placeholder="" name="sos2_no" id="sos2_no"  maxlength="10" value="" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">SOS - 3</label>
							<input type="text" class="form-control " placeholder="" name="sos3_no" id="sos3_no"  maxlength="10" value="" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Time Interval (in sec)<span class="text-danger">*</span></label>
							<!--<input type="text" class="form-control" placeholder="" name="duration"  value="" onkeypress="return isNumberKey(event)">-->
							<select name="duration" id="duration"  class="form-control">
								<option value="10">10</option>
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
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Call - 1</label>
							<input type="text" class="form-control " placeholder="" name="call1_no" id="call1_no"  maxlength="10" value="" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Call - 2</label>
							<input type="text" class="form-control " placeholder="" name="call2_no" id="call2_no"  maxlength="10" value="" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Call - 3</label>
							<input type="text" class="form-control " placeholder="" name="call3_no" id="call3_no"  maxlength="10" value="" onkeypress="return isNumberKey(event)">
						</div>
					</div>
					<?php if($getddm['typeofdevice'] != 'M'){ ?>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">APN Name<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder="" name="apn_name" id="apn_name"  value="" readonly>
						</div>
					</div>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">APN Username<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder="" name="apn_username" id="apn_username"  value="" readonly>
						</div>
					</div>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">APN Password<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder="" name="apn_pswd" id="apn_pswd"  value="" readonly>
						</div>
					</div>
					<?php } ?>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">Target IP<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder="" name="target_ip" id="target_ip"  value="120.138.8.188" readonly>
						</div>
					</div>
					<div class="col-6" style="display:none;">
						<div class="form-group">
							<label for="exampleInputEmail1">Target Port<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder="" name="target_port" id="target_port"  value="6991" readonly>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1"><?php echo 'Working Time From';?></label>
							<input type="text" class="form-control " placeholder="" name="working_start_time" id="working_start_time"  value="00:00:00">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1"><?php echo 'Working Time To';?></label>
							<input type="text" class="form-control " placeholder="" name="working_end_time" id="working_end_time"  value="00:00:00">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1"><?php echo 'Switch Off Restriction From';?></label>
							<input type="text" class="form-control " placeholder="" name="switchoffrestrict_start_time" id="switchoffrestrict_start_time"  value="00:00:00">
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1"><?php echo 'Switch Off Restriction To';?></label>
							<input type="text" class="form-control " placeholder="" name="switchoffrestrict_end_time" id="switchoffrestrict_end_time"  value="00:00:00">
						</div>
					</div>
					<?php if($getddm['typeofdevice'] == 'M'){ ?>
					<div class="col-6">
						<div class="form-group">
							<label for="exampleInputEmail1">Device Activation Code<span class="text-danger">*</span></label>
							<input type="text" class="form-control " placeholder=""  value="<?php echo $getddm['dynamiccode'];?>" readonly>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="clearfix"></div>
				<?php if($getddm['typeofdevice'] != 'M'){ ?>
				<div class="form-row">
					<div class="col-6">
						<label class="checkbox-inline"><input type="checkbox" name="GPRS" id="GPRS" value="GPRS" checked onclick="return false;"> GPRS Configuration</label>
						<label class="checkbox-inline" <?php if(isset($sessdata['configurationsms']) && $sessdata['configurationsms'] == 'N'){ ?>style="display:none;"<?php } ?>><input type="checkbox" name="SMS" id="SMS" value="SMS"> SMS Configuration</label>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php } ?>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="form-group">
						<button type="submit" class="btn btn-primary pull-right" name="add" id="btnadd" >Submit</button>
					</div>
				</div>
			<?php } ?>
			
			
        </div>
    </div>
</div>