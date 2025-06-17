<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-user"></i> Account Management
        </div>

		<div class="search-panel">
			<div class="col-xs-12 col-md-12">
				<?php if(isset($errmsg) && !empty($errmsg)){?>
				<div class="error"><?php echo $errmsg?></div>
				<?php } ?>
			</div>
			
			<?php echo form_open("users/edit/".$userid,array("autocomplete"=>"off","id"=>"usersedit","novalidate"=>"true")) ?>
				<?= csrf_field() ?>
				<div class="form-row">
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<label for="exampleInputEmail1">Account Type</label>
						<select class="form-control" name="group_id" id="group_id" disabled>
							<option value="">Select</option>     
							 <?php if(isset($groupdd) && !empty($groupdd)){  foreach($groupdd as $row){ ?>
								 <option value="<?php echo $row->id?>" <?php echo ($userdata->group_id == $row->id) ? "selected" : ""; ?>><?php echo $row->name_e; ?></option>
							<?php } } ?> 
						</select>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="display:none;">
						<label for="exampleInputEmail1">Role</label>
						<select class="form-control" name="role_id" id="role_id" disabled>
							<option value="">Select</option>
							 <?php if(isset($roledd) && !empty($roledd)){ foreach($roledd as $row){ ?>
								 <option value="<?php echo $row->id?>" <?php echo ($userdata->role_id == $row->id)?"selected":"" ?>><?php echo $row->name_e?></option>
							<?php } } ?>                    
						</select>
					</div> 
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<label for="exampleInputEmail1">Account Name<span class="text-danger">*</span></label>
						<input type="text" class="form-control" placeholder="Account Name" name="organisation" id="organisation" maxlength="30"  value="<?php echo  $userdata->organisation; ?>" required>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<label for="exampleInputEmail1">Contact Persons First Name<span class="text-danger">*</span></label>
						<input type="text" class="form-control" placeholder="First name" name="firstname" id="firstname" maxlength="30" value="<?php echo $userdata->firstname; ?>" required>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<label for="exampleInputEmail1">Contact Persons Last Name<span class="text-danger">*</span></label>
						<input type="text" class="form-control" placeholder="Last name" name="lastname" id="lastname" maxlength="30" value="<?php echo $userdata->lastname; ?>"required>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<label for="exampleInputEmail1">Primary Contact<span class="text-danger">*</span></label>
						<input type="text" class="form-control" placeholder="Primary Contact No" name="mobile" id="mobile" maxlength="10" value="<?php echo $userdata->mobile; ?>"required>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<label for="exampleInputEmail1">Secondary Contact</label>
						<input type="text" class="form-control" placeholder="Secondary Contact No" name="phone" id="phone" value="<?php echo $userdata->phone; ?>">
					</div>                
							   
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<label for="exampleInputEmail1">Email<span class="text-danger">*</span></label> 
						<input type="email" class="form-control" placeholder="Email" name="email" id="email" value="<?php echo $userdata->email; ?>" readonly>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<label for="exampleInputEmail1">Address</label>
						<textarea class="form-control" rows="2" maxlength="200" name="address" id="address" placeholder="Address"><?php echo $userdata->address; ?></textarea>
					</div>  
							   
					<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
						<label for="exampleInputEmail1">Pin</label>
						<input type="text" class="form-control" placeholder="Pin" name="pincode" id="pincode" value="<?php echo $userdata->pincode; ?>">
					</div>
					<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
						<label for="exampleInputEmail1">State Name</label>
						<input type="text" class="form-control" placeholder="State" name="state_name" id="state_name" value="<?php echo $userdata->state_name; ?>">
					</div>
					<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
						<label for="exampleInputEmail1">Country</label>
						<input type="text" class="form-control" placeholder="Country" name="country" id="country" value="<?php echo $userdata->country; ?>">
					</div>
							  
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<label for="exampleInputEmail1">Username</label>
						<input type="text" class="form-control" placeholder="Username" value="<?php echo $userdata->username; ?>" readonly>
					</div>
					
					<?php  if($sessdata['group_id'] == 1){ ?>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<label for="exampleInputEmail1">Subscription<span class="text-danger">*</span></label>
					   <select class="form-control" name="status" id="status" required>
						   <option value="">Select</option>
							<option value="1" <?php echo ($userdata->status == 1)?"selected":"" ?>>Active</option>
							<option value="2" <?php echo ($userdata->status == 2)?"selected":"" ?>>Inactive</option>                      
						</select>
					</div> 
					<?php } ?>
							  
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<label for="exampleInputEmail1">Permission<span class="text-danger">*</span></label>
						<div class="clearfix"></div>
						<div class="permissiongroup">
							<label class="checkbox-inline">
								 <input type="checkbox" name="prev_view" id="prev_view" value="1" <?php echo ($userdata->prev_view == 1)?"checked":"" ?>> View
							</label>
							<label class="checkbox-inline">
								 <input type="checkbox" name="prev_download" id="prev_download" value="1" <?php echo ($userdata->prev_download == 1)?"checked":"" ?>> Download
							</label>
						</div>
					</div>
					<?php if($sessdata['group_id'] == 3) { ?>
					<input type="hidden" class="form-control" name="accountid" id="accountid" required value="<?php echo $userdata->accountid; ?>">
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Number of Device<span class="text-danger">*</span></label>                    
						<input type="text" class="form-control" placeholder="Number of Device" name="numberofdevice" id="numberofdevice" required value="<?php echo $userdata->numberofdevice; ?>" onkeypress="return isNumberKey(event);" onkeyup="devicecountcheck();"> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Expiry Date<span class="text-danger">*</span></label>                    
						<input type="text" class="form-control" placeholder="Number of Device" name="expirydate" id="expirydate" required value="<?php echo date("d-m-Y",strtotime($userdata->expirydate)); ?>"> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Configuration-SMS ?<span class="text-danger">*</span></label>                    
						<select class="form-control" name="configurationsms" id="configurationsms" required>                        
							<option value="N" <?php echo ($userdata->configurationsms == 'N')?"selected":"" ?>>No</option>
							<option value="Y" <?php echo ($userdata->configurationsms == 'Y')?"selected":"" ?>>Yes</option>
						</select> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Notification-Whatsapp ?<span class="text-danger">*</span></label>                    
						<select class="form-control" name="notificationsms" id="notificationsms" required>
							<option value="N" <?php echo ($userdata->notificationsms == 'N')?"selected":"" ?>>No</option>
							<option value="Y" <?php echo ($userdata->notificationsms == 'Y')?"selected":"" ?>>Yes</option>                
						</select> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Notification-Email ?<span class="text-danger">*</span></label>                    
						<select class="form-control" name="neotificationemail" id="neotificationemail" required>
							<option value="N" <?php echo ($userdata->neotificationemail == 'N')?"selected":"" ?>>No</option>
							<option value="Y" <?php echo ($userdata->neotificationemail == 'Y')?"selected":"" ?>>Yes</option>                
						</select> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Free Fall Alert ?<span class="text-danger">*</span></label>                    
						<select class="form-control" name="freefallalert" id="freefallalert" required>
							<option value="N" <?php echo ($userdata->freefallalert == 'N')?"selected":"" ?>>No</option>
							<option value="Y" <?php echo ($userdata->freefallalert == 'Y')?"selected":"" ?>>Yes</option>                
						</select> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Network Location ?<span class="text-danger">*</span></label>                    
						<select class="form-control" name="networklocation" id="networklocation" required>
							<option value="N" <?php echo ($userdata->networklocation == 'N')?"selected":"" ?>>No</option>
							<option value="Y" <?php echo ($userdata->networklocation == 'Y')?"selected":"" ?>>Yes</option>                
						</select> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="display:none;">                   
						<label for="exampleInputEmail1">Number of Whatsapp<span class="text-danger">*</span></label>                    
						<input type="text" class="form-control" placeholder="Number of Whatsapp" name="notificationtotalsms" id="notificationtotalsms" required value="<?php echo $userdata->notificationtotalsms; ?>" onkeypress="return isNumberKey(event)">
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Number of POI<span class="text-danger">*</span> <input type="checkbox" name="poi_unlimited" id="poi_unlimited" value="-1" <?php echo ($userdata->numberofpoi == -1)?"checked":"" ?>> Unlimited</label>                    
						<input type="text" class="form-control" placeholder="Number of POI" name="numberofpoi" id="numberofpoi" required value="<?php echo $userdata->numberofpoi; ?>" onkeypress="return isNumberKey(event)" <?php echo ($userdata->numberofpoi == -1)?"readonly":"" ?>> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Number of Route<span class="text-danger">*</span> <input type="checkbox" name="route_unlimited" id="route_unlimited" value="-1" <?php echo ($userdata->numberofroute == -1)?"checked":"" ?>> Unlimited</label>                    
						<input type="text" class="form-control" placeholder="Number of Route" name="numberofroute" id="numberofroute" required value="<?php echo $userdata->numberofroute; ?>" onkeypress="return isNumberKey(event)" <?php echo ($userdata->numberofroute == -1)?"readonly":"" ?>> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Number of Geo-fence<span class="text-danger">*</span> <input type="checkbox" name="geofence_unlimited" id="geofence_unlimited" value="-1" <?php echo ($userdata->numberofgeofence == -1)?"checked":"" ?>> Unlimited</label>                    
						<input type="text" class="form-control" placeholder="Number of Geo-fence" name="numberofgeofence" id="numberofgeofence" required value="<?php echo set_value('numberofgeofence',1); ?>" onkeypress="return isNumberKey(event)" <?php echo ($userdata->numberofgeofence == -1)?"readonly":"" ?>> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="display:none;">                   
						<label for="exampleInputEmail1">Number of Admin<span class="text-danger">*</span></label>                    
						<input type="text" class="form-control" placeholder="Number of Admin" name="numberofadmin" id="numberofadmin" required value="<?php echo $userdata->numberofadmin; ?>" onkeypress="return isNumberKey(event)"> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
						<label for="exampleInputEmail1">Allowed to create User ?<span class="text-danger">*</span></label>                    
						<select class="form-control" name="allowedtocreateuser" id="allowedtocreateuser" required>
							<option value="N" <?php echo ($userdata->allowedtocreateuser == 'N')?"selected":"" ?>>No</option>
							<option value="Y" <?php echo ($userdata->allowedtocreateuser == 'Y')?"selected":"" ?>>Yes</option>                
						</select> 
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" <?php if($userdata->allowedtocreateuser == 'N'){ ?>style="display:none;"<?php } ?> id="numberofuserdiv">                   
						<label for="exampleInputEmail1">Number of User<span class="text-danger">*</span></label>                    
						<input type="text" class="form-control" placeholder="Number of User" name="numberofuser" id="numberofuser" required value="<?php echo $userdata->numberofuser; ?>" onkeypress="return isNumberKey(event)"> 
					</div>
					<?php } ?>      
				   
				</div>
				<div class="clearfix"></div>
				<div class="form-row">    
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<button type="submit" class="btn btn-primary pull-right" name="edit">Update</button>
					<a href="<?php echo  site_url('/')?>users/lists" class="btn btn-danger">Back</a>
					</div>
				</div>
			<?php echo form_close(); ?>
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
		$("#allowedtocreateuser").change(function() {
		  if($("#allowedtocreateuser").val() == 'Y'){
			  $("#numberofuserdiv").show();
		  }
		  else{
			  $("#numberofuserdiv").hide();
			  $("#numberofuser").val(0);
		  }
		});
		$("#poi_unlimited").click(function() {
			if($("#poi_unlimited").prop('checked') == true){
				$("#numberofpoi").val(-1);
				$("#numberofpoi").prop("readonly", true);
			}
			else {
				$("#numberofpoi").val(0);
				$("#numberofpoi").prop("readonly", false);
			}
		});
		$("#route_unlimited").click(function() {
			if($("#route_unlimited").prop('checked') == true){
				$("#numberofroute").val(-1);
				$("#numberofroute").prop("readonly", true);
			}
			else {
				$("#numberofroute").val(0);
				$("#numberofroute").prop("readonly", false);
			}
		});
		$("#geofence_unlimited").click(function() {
			if($("#geofence_unlimited").prop('checked') == true){
				$("#numberofgeofence").val(-1);
				$("#numberofgeofence").prop("readonly", true);
			}
			else {
				$("#numberofgeofence").val(0);
				$("#numberofgeofence").prop("readonly", false);
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
	function isNumberKey(evt){
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (charCode > 31 && (charCode < 48 || charCode > 57))
			return false;
		return true;
	}
	function devicecountcheck(){
		if($("#numberofdevice").val() == 0){
			$("#numberofdevice").val('');
		}
	}
</script>