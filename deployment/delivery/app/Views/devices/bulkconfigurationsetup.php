<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-mobile"></i> Bulk Configuration Setup
        </div>
        <div class="card-body">
		<div class="search-panel">
			<div class="col-xs-12 col-md-12">
				<?php echo form_open("devices/bulkconfigurationsetup/", array("autocomplete" => "off", "id" => "devicesconf", "novalidate" => "true" )) ?>
						<div class="form-row">						
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<fieldset class="fldst">
									<legend>Advanced Configuration</legend>
									<div class="form-row">
										<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
											<label for="exampleInputEmail1">SOS - 1<span class="text-danger">*</span></label>
											<input type="text" class="form-control numeric" placeholder="" name="sos1_no" id="sos1_no"  minlength="10" maxlength="10" value="" required>
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
											<label for="exampleInputEmail1">Call - 1<span class="text-danger">*</span></label>
											<input type="text" class="form-control numeric" placeholder="" name="call1_no" id="call1_no"  minlength="10" maxlength="10" value="" required>
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
					
				<?php echo form_close(); ?>
			</div>
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
	
	$('#icon_details').change(function(){
		var img = '<?php echo base_url();?>'+$('#icon_details').val();
		$('#imgdiv').html('<img src="'+img+'"/>');
	});
	
});
function isNumberKey(evt){
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
}
</script>