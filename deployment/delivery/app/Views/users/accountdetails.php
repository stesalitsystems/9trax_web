<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-user"></i> Account Management
        </div>

		<div class="card-body">
			<div class="search-panel">
				<div class="form-row">				
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Number of Device</label>                    
						<input type="text" class="form-control" placeholder="Number of Device" name="numberofdevice" id="numberofdevice" value="<?php if(isset($userdata->numberofdevice)){echo $userdata->numberofdevice;} ?>" readonly> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Expiry Date</label>                    
						<input type="text" class="form-control" placeholder="Number of Device" name="expirydate" id="expirydate" value="<?php if(isset($userdata->expirydate)){echo date("d-m-Y",strtotime($userdata->expirydate));} ?>" readonly> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Configuration-SMS ?</label>                    
						<select class="form-control" name="configurationsms" id="configurationsms" disabled>                        
							<option value="N" <?php if(isset($userdata->configurationsms)){echo ($userdata->configurationsms == 'N')?"selected":"";} ?>>No</option>
							<option value="Y" <?php if(isset($userdata->configurationsms)){echo ($userdata->configurationsms == 'Y')?"selected":"";} ?>>Yes</option>
						</select> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Notification-SMS ?</label>                    
						<select class="form-control" name="notificationsms" id="notificationsms" disabled>
							<option value="N" <?php if(isset($userdata->notificationsms)){echo ($userdata->notificationsms == 'N')?"selected":"";} ?>>No</option>
							<option value="Y" <?php if(isset($userdata->notificationsms)){echo ($userdata->notificationsms == 'Y')?"selected":"";} ?>>Yes</option>                
						</select> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Notification-Email ?</label>                    
						<select class="form-control" name="neotificationemail" id="neotificationemail" disabled>
							<option value="N" <?php if(isset($userdata->neotificationemail)){echo ($userdata->neotificationemail == 'N')?"selected":"";} ?>>No</option>
							<option value="Y" <?php if(isset($userdata->neotificationemail)){echo ($userdata->neotificationemail == 'Y')?"selected":"";} ?>>Yes</option>                
						</select> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Number of SMS</label>                    
						<input type="text" class="form-control" placeholder="Number of SMS" name="notificationtotalsms" id="notificationtotalsms" readonly value="<?php if(isset($userdata->notificationtotalsms)){echo $userdata->notificationtotalsms;} ?>">
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Number of POI</label>                    
						<input type="text" class="form-control" placeholder="Number of POI" name="numberofpoi" id="numberofpoi" readonly value="<?php if(isset($userdata->numberofpoi)){echo $userdata->numberofpoi;} ?>"> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Number of Route</label>                    
						<input type="text" class="form-control" placeholder="Number of Route" name="numberofroute" id="numberofroute" readonly value="<?php if(isset($userdata->numberofroute)){echo $userdata->numberofroute;} ?>"> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Number of Geo-fence</label>                    
						<input type="text" class="form-control" placeholder="Number of Geo-fence" name="numberofgeofence" id="numberofgeofence" readonly value="<?php if(isset($userdata->numberofgeofence)){echo $userdata->numberofgeofence;} ?>"> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Number of Admin</label>                    
						<input type="text" class="form-control" placeholder="Number of Admin" name="numberofadmin" id="numberofadmin" readonly value="<?php if(isset($userdata->numberofadmin)){echo $userdata->numberofadmin;} ?>"> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Allowed to create User ?</label>                    
						<select class="form-control" name="allowedtocreateuser" id="allowedtocreateuser" disabled>
							<option value="N" <?php if(isset($userdata->allowedtocreateuser)){echo ($userdata->allowedtocreateuser == 'N')?"selected":"";} ?>>No</option>
							<option value="Y" <?php if(isset($userdata->allowedtocreateuser)){echo ($userdata->allowedtocreateuser == 'Y')?"selected":"";} ?>>Yes</option>                
						</select> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Number of User</label>                    
						<input type="text" class="form-control" placeholder="Number of User" name="numberofuser" id="numberofuser" readonly value="<?php if(isset($userdata->numberofuser)){echo $userdata->numberofuser;} ?>"> 
					</div>   
				   
				</div>
				<div class="form-row">    
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<a href="<?php echo  site_url('/')?>users/lists" class="btn btn-danger">Back</a>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>


<script>
    $(document).ready(function(){
        $("#usersedit").validate({
            rules: {
                mobile: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 15
                },
                phone: {                  
                    digits: true,
                    minlength: 10,
                    maxlength: 15
                }
            }
        });
		
		<?php if ($sessdata['group_id'] == 3) { ?>  
        $(function () {
            $("[name='expirydate']").datepicker({
                dateFormat: 'dd-mm-yy',
            });
        });
		<?php } ?>
    });
</script>