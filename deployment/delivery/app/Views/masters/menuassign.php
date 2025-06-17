
<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
	
		<div class="card-header">
			<i class="fa fa-mobile"></i> <?php echo $page_title;?>
		</div>
		<div class="search-panel">
			<div class="col-xs-12 col-md-12">
				<?php if(isset($errmsg) && !empty($errmsg)){?>
				<div class="error"><?php echo $errmsg?></div>
				<?php } ?>
			</div>

			<?php echo form_open("masters/menuassign",array("autocomplete"=>"off","id"=>"deviceupload","novalidate"=>"true","class"=>"form-actions","enctype"=>"multipart/form-data")) ?>

				<div class="form-row">
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="exampleInputEmail1">Group<span class="text-danger">*</span></label>
                        <select class="form-control valid" name="groupid" id="groupid">
                            <?php echo $grp; ?>               
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="exampleInputEmail1">Role<span class="text-danger">*</span></label>
                        <select class="form-control valid" name="roleid" id="roleid">
                            <?php echo $rol; ?>                       
                        </select>
                    </div>	
					<div id="menu_container">
                        <?php echo $menu_cont; ?>
                    </div>
				</div>
				<div class="clearfix"></div>
				<div class="form-row">      
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<button type="submit" class="btn btn-primary pull-right" name="save" value="save">Submit</button>
						<a href="<?php echo site_url('masters/menu')?>" class="btn btn-danger">Back</a>
					</div>                
				</div>				
			<?php echo form_close(); ?>

		</div>
	
	</div>
</div>

<style>
    .pmenu{font-weight:bold;font-size: 14px;color: #0e29bd;}
    .smenu{font-weight:bold;font-size: 12px;color: #18d057;}
</style>

<script>
    
    $(document).ready(function() {
        
        $(document).on("change", "#groupid", function(){
            var groupid = $(this).val();
            //alert(userentitytype);
            if(groupid != ""){
                $.ajax({
                    url: "<?php echo site_url('masters/getroles'); ?>",
                    type: "POST",
                    data: {groupid: groupid},
                    success: function (data){
                        //alert(data);
                        $("#roleid").html(data);   
                    }
                });
            }
            else{
                alert("Please select an Option...");
            }
            
        });
        
        $(document).on("change", "#roleid", function(){
			
            var groupid = $("#groupid").val();
			var roleid = $(this).val();
			//alert(role_id);
			
			window.location.href = "<?php echo site_url('masters/getrolemenuassign'); ?>/"+groupid+"/"+roleid;
			 
		});
    
	});

</script>
