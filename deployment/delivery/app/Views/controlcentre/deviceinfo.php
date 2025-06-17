<style>
    .form-row{
        margin: 10px;
    }
	.newcw .form-group {
    margin: 0px;
    padding: 4px 0;
}
</style>
<div class="container-fluid newcw">
    <div class="row">
        <div class="col-xs-12 col-md-12">
			
			<div class="form-row">
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<div class="form-group">
						<label>Serial No.</label> : <label><?php echo $getdev->serial_no;?></label>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<div class="form-group">
						<label>User Name</label> : <label><?php echo $getdev->firstname.' '.$getdev->lastname;?></label>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<div class="form-group">
						<label>Alias Name</label> : <label><?php echo $getdev->device_name;?></label>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<div class="form-group">
						<label>Mobile No.</label> : <label><?php echo $getdev->mobile_no;?></label>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<div class="form-group">
						<label>IMEI No.</label> : <label><?php echo $getdev->imei_no;?></label>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<div class="form-group">
						<label>Data Interval</label> : <label><?php if($getdev->duration != ''){ echo $getdev->duration.' sec.';} else { echo 'NA';}?></label>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<div class="form-group">
						<label>SOS Nos.</label> : <label><?php if($getdev->sos1_no != ''){ echo $getdev->sos1_no.'-'.$getdev->sos2_no.'-'.$getdev->sos3_no;} else { echo 'NA';}?></label>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<div class="form-group">
						<label>Call Nos.</label> : <label><?php if($getdev->sos1_no != ''){ echo $getdev->call1_no.'-'.$getdev->call2_no.'-'.$getdev->call3_no;} else { echo 'NA';}?></label>
					</div>
				</div>
				<?php if($getdev->typeofdevice == 'M'){ ?>
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<div class="form-group">
						<label>Working Time</label> : <label><?php echo $getdev->working_start_time.'-'.$getdev->working_end_time;?></label>
					</div>
				</div>
				<?php } else { ?>
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<div class="form-group">
						<label>Restricted Switch Off Time</label> : <label><?php echo $getdev->working_start_time.'-'.$getdev->working_end_time;?></label>
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="clearfix"></div>		
			
        </div>
    </div>
</div>