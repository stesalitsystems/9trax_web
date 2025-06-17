<?php

	$session = session();
	$sessdata = $session->get('login_sess_data');
	
?>
<style>
    .form-row{
        margin: 10px;
    }    
</style>
<div class="container-fluid">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo site_url('Dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo site_url('users/lists');?>">Account Management</a></li>
        <li class="breadcrumb-item active">Profile</li>
    </ol>
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <?php if(isset($errmsg) && !empty($errmsg)){?>
            <div class="error"><?php echo $errmsg?></div>
            <?php } ?>
        </div>
        <div class="col-xs-12 col-md-12">

			<div class="form-row">
				
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
				<fieldset class="fldst">
					<legend>Account</legend>
					<i class="fa fa-pencil edtdlt shw" id="acct_edt" aria-hidden="true"></i>
					<i class="fa fa-save saveopt hid" id="acct_save" aria-hidden="true"></i>
					<div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="exampleInputEmail1">Account Name<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="Account Name" name="organisation" id="organisation" maxlength="30"  value="<?php echo  $sessdata['organisation']; ?>" readonly>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="exampleInputEmail1">Primary Contact<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="Primary Contact No" name="mobile" id="mobile" maxlength="10" value="<?php echo $sessdata['mobile']; ?>"readonly>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="exampleInputEmail1">Username<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="Username" value="<?php echo $sessdata['username']; ?>" readonly>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="exampleInputEmail1">Email<span class="text-danger">*</span></label> 
							<input type="email" class="form-control" placeholder="Email" name="email" id="email" value="<?php echo $sessdata['email']; ?>" readonly>
						</div>
					</div>
				</fieldset>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
					<fieldset class="fldst" style="height: 205px; !important">
						<legend>Image</legend>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="topgmapop">
								<a class="gb_zb mdBt" dat_link="<?php echo site_url('users/upload_image');?>" title="Upload Profile Image">
									<?php if($sessdata['profile_image'] != ""){ ?>
									<img src="<?php echo base_url()."uploads/users/".$sessdata['user_id']."/".$sessdata['profile_image'];?>" class="gb_Ab">
									<?php }else{ ?>
										<img src="<?php echo base_url()."assets/images/no_image.jpg";?>" class="gb_Ab">
									<?php } ?>
								</a>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="clearfix"></div>
				
				<fieldset class="fldst">
					<legend>Address</legend>
					<i class="fa fa-pencil edtdlt shw" id="add_edt" aria-hidden="true"></i>
					<i class="fa fa-save saveopt hid" id="add_save" aria-hidden="true"></i>
					<div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="exampleInputEmail1">Contact Persons Name<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="First name" name="firstname" id="firstname" maxlength="30" value="<?php echo $sessdata['firstname']; ?>" readonly>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<label for="exampleInputEmail1">Contact Persons Name<span class="text-danger">*</span></label>
							<input type="text" class="form-control" placeholder="First name" name="lastname" id="lastname" maxlength="30" value="<?php echo $sessdata['lastname']; ?>" readonly>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<label for="exampleInputEmail1">Address</label>
							<textarea class="form-control" rows="2" maxlength="200" name="address" id="address" placeholder="Address" readonly><?php echo $sessdata['address']; ?></textarea>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<label for="exampleInputEmail1">Country</label>
							<input type="text" class="form-control" placeholder="Country" name="country" id="country" value="<?php echo $sessdata['country']; ?>" readonly>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<label for="exampleInputEmail1">State Name</label>
							<input type="text" class="form-control" placeholder="State" name="state_name" id="state_name" value="<?php echo $sessdata['state_name']; ?>" readonly>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<label for="exampleInputEmail1">Pin</label>
							<input type="text" class="form-control" placeholder="Pin" name="pincode" id="pincode" value="<?php echo $sessdata['pincode']; ?>" readonly>
						</div>
					</div>
				</fieldset>
                <div class="clearfix"></div>
				
				<?php if($sessdata['group_id'] == 2) { ?>
				<fieldset class="fldst">
					<legend>Configuration</legend>
					<div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
							<label for="exampleInputEmail1">Number of Device</label>                    
							<input type="text" class="form-control" value="<?php echo $userdata->numberofdevice; ?>"  readonly> 
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
							<label for="exampleInputEmail1">Expiry Date</label>                    
							<input type="text" class="form-control" value="<?php echo date("d-m-Y",strtotime($userdata->expirydate)); ?>" readonly> 
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
							<label for="exampleInputEmail1">Configuration-SMS</label>
							<input type="text" class="form-control" value="<?php echo ($userdata->configurationsms == "N") ? "No" : "Yes"; ?>" readonly> 
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
							<label for="exampleInputEmail1">Notification-SMS</label>
							<input type="text" class="form-control" value="<?php echo ($userdata->notificationsms == "N") ? "No" : "Yes"; ?>" readonly> 
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
							<label for="exampleInputEmail1">Notification-Email</label>
							<input type="text" class="form-control" value="<?php echo ($userdata->neotificationemail == "N") ? "No" : "Yes"; ?>" readonly> 
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
							<label for="exampleInputEmail1">Number of SMS</label>                    
							<input type="text" class="form-control" value="<?php echo $userdata->notificationtotalsms; ?>" readonly>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
							<label for="exampleInputEmail1">Number of POI</label>                    
							<input type="text" class="form-control" value="<?php echo ($userdata->numberofpoi == "-1") ? "Unlimited" : $userdata->numberofpoi; ?>" readonly> 
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
							<label for="exampleInputEmail1">Number of Route</label>                    
							<input type="text" class="form-control" value="<?php echo ($userdata->numberofroute == "-1") ? "Unlimited" : $userdata->numberofroute; ?>" readonly> 
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
							<label for="exampleInputEmail1">Number of Geo-fence</label>                    
							<input type="text" class="form-control" value="<?php echo ($userdata->numberofgeofence == "-1") ? "Unlimited" : $userdata->numberofgeofence; ?>" readonly> 
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
							<label for="exampleInputEmail1">Number of Admin</label>                    
							<input type="text" class="form-control" value="<?php echo $userdata->numberofadmin; ?>" readonly> 
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">                   
							<label for="exampleInputEmail1">Allowed to create User</label>
							<input type="text" class="form-control" value="<?php echo ($userdata->allowedtocreateuser == "N") ? "No" : "Yes"; ?>" readonly> 
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" <?php if($userdata->allowedtocreateuser == 'N'){ ?>style="display:none;"<?php } ?> id="numberofuserdiv">                   
							<label for="exampleInputEmail1">Number of User</label>                    
							<input type="text" class="form-control" value="<?php echo $userdata->numberofuser; ?>" readonly> 
						</div>
					</div>
				</fieldset>
				<?php } ?>      
				
            </div>
            
			
		
		
        </div>
    </div>
</div>

<style>
	.shw{
		display: block !important;
	}
	
	.hid{
		display: none !important;
	}
</style>

<script>
    $(document).ready(function(){
        
		//$(".hid").hide();
		var post_url = "<?php echo site_url('users/profile_update');?>";
		
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
		
		
		$(document).on("click", ".edtdlt", function(e){
			
			var btid = $(this).attr("id");

			if(btid == "acct_edt"){
				$("#organisation").removeAttr("readonly");
				$("#organisation").attr("required", "required");
				$("#mobile").removeAttr("readonly");
				$("#mobile").attr("required", "required");
				
				$("#acct_save").removeClass("hid");
				$("#acct_save").addClass("shw");
				
				$("#acct_edt").removeClass("shw");
				$("#acct_edt").addClass("hid");
			}
			else{
				$("#firstname").removeAttr("readonly");
				$("#firstname").attr("required", "required");
				$("#lastname").removeAttr("readonly");
				$("#lastname").attr("required", "required");
				$("#country").removeAttr("readonly");
				$("#country").attr("required", "required");
				$("#state_name").removeAttr("readonly");
				$("#state_name").attr("required", "required");
				$("#pincode").removeAttr("readonly");
				$("#pincode").attr("required", "required");
				$("#address").removeAttr("readonly");
				$("#address").attr("required", "required");
				
				
				$("#add_save").removeClass("hid");
				$("#add_save").addClass("shw");
				
				$("#add_edt").removeClass("shw");
				$("#add_edt").addClass("hid");
				
				
			}
			
		});
		
		
		$(document).on("click", "#acct_save", function(e){
		
			var organisation = $("#organisation").val();
			var mobile = $("#mobile").val();
			var mode = 'act';
			
			$.ajax({
				url: post_url,
				type: 'post',
				data:  {mode: mode, organisation: organisation, mobile: mobile},
				success: function(data){
					//alert(data);
					if(data == 1){
						$("#organisation").removeAttr("required");
						$("#organisation").attr("readonly", "readonly");
						$("#mobile").removeAttr("required");
						$("#mobile").attr("readonly", "readonly");
						
						$("#acct_save").removeClass("shw");
						$("#acct_save").addClass("hid");
						
						$("#acct_edt").removeClass("hid");
						$("#acct_edt").addClass("shw");
						//alert("Updated Successfully...");
						location.reload();
					}
					else{
						alert("Error in updation, Please try again...");
					}
				},
				error: function( jqXhr, textStatus, errorThrown ){
					console.log( errorThrown );
				}
			});
			
		});
				
		
		$(document).on("click", "#add_save", function(e){
		
			var firstname = $("#firstname").val();
			var lastname = $("#lastname").val();
			var address = $("#address").val();
			var country = $("#country").val();
			var state_name = $("#state_name").val();
			var pincode = $("#pincode").val();
			var mode = 'add';
			$.ajax({
				url: post_url,
				type: 'post',
				data:  {mode: mode, firstname: firstname, lastname: lastname, address: address, country: country, state_name: state_name, pincode: pincode},
				
				success: function(data){
					//alert(data);
					if(data == 1){
						$("#firstname").removeAttr("required");
						$("#firstname").attr("readonly", "readonly");
						$("#lastname").removeAttr("required");
						$("#lastname").attr("readonly", "readonly");
						$("#country").removeAttr("required");
						$("#country").attr("readonly", "readonly");
						$("#state_name").removeAttr("required");
						$("#state_name").attr("readonly", "readonly");
						$("#pincode").removeAttr("required");
						$("#pincode").attr("readonly", "readonly");
						$("#address").removeAttr("required");
						$("#address").attr("readonly", "readonly");
						
						$("#add_save").removeClass("shw");
						$("#add_save").addClass("hid");
						
						$("#add_edt").removeClass("hid");
						$("#add_edt").addClass("shw");
						
						//alert("Updated Successfully...");
						location.reload();
					}
					else{
						alert("Error in updation, Please try again...");
					}
				},
				error: function( jqXhr, textStatus, errorThrown ){
					console.log( errorThrown );
				}
			});
			
		});
		
    
		
	
	});
	
	function isNumberKey(evt){
		
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (charCode > 31 && (charCode < 48 || charCode > 57)){
			return false;
		}
		return true;
	
	}
	
</script>