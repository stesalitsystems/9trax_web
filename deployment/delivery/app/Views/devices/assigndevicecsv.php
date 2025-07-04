<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-mobile"></i> Upload CSV To Assign Device
        </div>

		<div class="search-panel">
			<div class="col-xs-12 col-md-12">
				<?php if(isset($errmsg) && !empty($errmsg)){?>
				<div class="error"><?php echo $errmsg?></div>
				<?php } ?>
			</div>


            <?php echo form_open("devices/assigndevicecsv",array("autocomplete"=>"off","id"=>"deviceupload","novalidate"=>"true","class"=>"form-actions","enctype"=>"multipart/form-data")) ?>

			<div class="form-row"> 
				<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <label for="exampleInputEmail1">CSV<span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="csvfile" id="csvfile"  align="center" required/>
                </div>				
            </div>
            <div class="clearfix"></div>
            <div class="form-row">      
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <button type="submit" class="btn btn-primary pull-right" name="upload">Upload</button>
					<a href="<?php echo site_url('devices/lists')?>" class="btn btn-danger">Back</a>
                </div>                
            </div>
            <?php echo form_close(); ?>

		</div>
	
	</div>
</div>
<script>
	$(document).ready(function(){
        $("#deviceupload").validate();
    });
	
	function isNumberKey(evt){
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (charCode > 31 && (charCode < 48 || charCode > 57))
			return false;
		return true;
	}
</script>