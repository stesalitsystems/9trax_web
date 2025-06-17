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
            <a href="<?php echo site_url('/') ?>devices/lists">Device Management</a>
        </li>
        <li class="breadcrumb-item active">Edit Device</li>
    </ol>
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <?php if(isset($errmsg) && !empty($errmsg)){?>
            <div class="error"><?php echo $errmsg?></div>
            <?php } ?>
        </div>
        <div class="col-xs-12 col-md-12">

            <?php echo form_open("devices/edit/".$deviceid,array("autocomplete"=>"off","id"=>"deviceedit","novalidate"=>"true","class"=>"form-actions")) ?>
            <div class="form-row">
            <?php if($sessdata->group_id == 1){?>            
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <label for="exampleInputEmail1">Serial No.<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Serial No" name="serial_no" maxlength="30" value="<?php echo $userdata->serial_no; ?>" required >
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <label for="exampleInputEmail1">IMEI No.<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="IMEI No" name="imei_no" value="<?php echo$userdata->imei_no; ?>" required>
                </div>
            <?php }else{ ?>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<label for="exampleInputEmail1">Serial No.</label>
					<input type="text" class="form-control" placeholder="Serial No" value="<?php echo $userdata->serial_no; ?>" readonly>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
					<label for="exampleInputEmail1">IMEI No.</label>
					<input type="text" class="form-control" placeholder="IMEI No" value="<?php echo$userdata->imei_no; ?>" readonly>                   
                </div>
            <?php } ?>
            
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                     <label for="exampleInputEmail1">Mobile No.</label>
                    <input type="text" class="form-control" placeholder="Mobile No" name="mobile_no" value="<?php echo $userdata->mobile_no; ?>" maxlength="10">
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                     <label for="exampleInputEmail1">Warranty Till</label>
                    <input type="text" class="form-control" placeholder="Warranty Till" name="warranty_date" value="<?php echo date("d-m-Y",strtotime($userdata->warranty_date)); ?>">
                </div>  
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                     <label for="exampleInputEmail1">Status<span class="text-danger">*</span></label>
                    <select class="form-control" name="active" required>
                        <option value="">Select</option>
                        <option value="1" <?php echo ($userdata->active == 1)?"selected":"" ?>>Active</option>
                        <option value="2" <?php echo ($userdata->active == 2)?"selected":"" ?>>Inactive</option>                      
                    </select>
                </div>
                <!--<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                     <label for="exampleInputEmail1">Linked Status</label>
                    <select class="form-control" name="linked" disabled>
                        <option value="">Select</option>
                        <option value="1" <?php echo ($userdata->linked == 1)?"selected":"" ?>>Linked</option>
                        <option value="2" <?php echo ($userdata->linked == 2)?"selected":"" ?>>Not Linked</option>                      
                    </select>
                </div>-->
            </div>
            <div class="form-row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">                   
                <button type="submit" class="btn btn-primary pull-right" name="edit">Update</button>
                <a href="<?php echo site_url('/')?>devices/lists" class="btn btn-danger">Back</a>
            </div>
            </div>                
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
       $("#deviceedit").validate({
            rules: {
                mobile_no: {
                    digits: true,                   
                    minlength: 10,
                    maxlength: 10
                },
                sim_icc_id:{
                    required:<?php echo ($sessdata->group_id == 1)?"false":"true"?>,
                    maxlength:25
                }
            }
        });
        
         <?php if ($sessdata->group_id == 1) { ?>  
         $( function() {
        $("[name='warranty_date']").datepicker({
            dateFormat:'dd-mm-yy',
        });
      } );
         <?php } ?>
    });
</script>