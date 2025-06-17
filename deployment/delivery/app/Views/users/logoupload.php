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
            <a href="<?php echo base_url()?>users/lists">Account Management</a>
        </li>
        <li class="breadcrumb-item active">Add Logo</li>
    </ol>
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <?php if(isset($errmsg) && !empty($errmsg)){?>
            <div class="error"><?php echo $errmsg?></div>
            <?php } ?>
        </div>
        <div class="col-xs-12 col-md-12">

            <form id="devicesadd" class="form-actions" action="<?php echo base_url()."users/logoupload"; ?>" method="post" enctype="multipart/form-data">
				<div class="form-row">
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<label for="exampleInputEmail1">Company Name<span class="text-danger">*</span></label>
						<input type="text" class="form-control" placeholder="Company Name" name="company_name" id="company_name" maxlength="200" value="<?php echo $userdata->company_name; ?>" required>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
						<label for="exampleInputEmail1">Company Logo</label>
						<input type="file" class="form-control" placeholder="" name="rfile" id="company_logo"  >
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
						<?php if($userdata->company_name != ""){ ?>
						<img class="pull-right" src="<?php echo base_url()."uploads/users/".$this->sessdata->user_id."/".$userdata->company_logo; ?>" style="width: 100px;">
						<?php } ?>
					</div>
					
				</div>
				<div class="clearfix"></div>
				<div class="form-row">      
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<button type="submit" class="btn btn-primary pull-right" name="add">Submit</button>
						<a href="<?php echo base_url()?>users/lists" class="btn btn-danger">Back</a>
					</div>                
				</div>
			</form>
            
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $("#usersadd").validate({
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
		
		
		$(document).on("change", "#group_id", function(){
			
			var group_id = $(this).val();
			var url = "<?php echo site_url('common/getdrpdwnrole'); ?>";
				
			$.ajax({
				url: url,
				type: "POST",
				data: {group_id: group_id},
				success: function (data)
				{
					//alert(data);
					$("#role_id").html(data);
				}
			});
			
		});
		
		
		//$('#organisation').bind('keyup change',function() {
		//	//alert($(this).val());
		//	var regex = new RegExp("^[a-zA-Z]+$");
		//	//var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
		//	if (regex.test($(this).val())) {
		//		return true;
		//	}
		//	else{
		//		e.preventDefault();
		//		alert('Please Enter Alphabate');
		//		this.value = this.value.slice(0,-1);
		//	}
		//	
		//});
		
		$('#email').bind('keyup change',function() {
			//alert("hi");
			var email = $(this).val();
			$('#username').val(email);
			
		});
		
    });
</script>