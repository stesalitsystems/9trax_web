
<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
	
		<?php if($this->uri->segment(3) == 'add'){ ?>
			<div class="card-header">
				<i class="fa fa-mobile"></i> <?php echo $page_title;?>
			</div>
			<div class="search-panel">
				<div class="col-xs-12 col-md-12">
					<?php if(isset($errmsg) && !empty($errmsg)){?>
					<div class="error"><?php echo $errmsg?></div>
					<?php } ?>
				</div>

				<?php echo form_open("masters/menu/add",array("autocomplete"=>"off","id"=>"deviceupload","novalidate"=>"true","class"=>"form-actions","enctype"=>"multipart/form-data")) ?>

					<div class="form-row">  
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Menu Name<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="modulename" id="modulename"  value="" required/>
						</div>	
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Icon<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="icon" id="icon"  value="" required/>
						</div>	
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Controller<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="controllers" id="controllers"  value="" required/>
						</div>	
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Priority<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="priority" id="priority"  value="" required/>
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
		<?php }else if($this->uri->segment(3) == 'edit'){ ?>
			<div class="card-header">
				<i class="fa fa-mobile"></i> <?php echo $page_title;?>
			</div>
			<div class="search-panel">
				<div class="col-xs-12 col-md-12">
					<?php if(isset($errmsg) && !empty($errmsg)){?>
					<div class="error"><?php echo $errmsg?></div>
					<?php } ?>
				</div>

				<?php echo form_open("masters/menu/edit/".$row->id, array("autocomplete"=>"off","id"=>"deviceupload","novalidate"=>"true","class"=>"form-actions","enctype"=>"multipart/form-data")) ?>
					<div class="form-row">  
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Menu Name<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="modulename" id="modulename"  value="<?php echo $row->modulename; ?>" required/>
						</div>	
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Icon<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="icon" id="icon"  value="<?php echo $row->icon; ?>" required/>
						</div>	
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Controller<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="controllers" id="controllers"  value="<?php echo $row->controllers; ?>" required/>
						</div>	
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Priority<span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="priority" id="priority"  value="<?php echo $row->priority; ?>" required/>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">Status<span class="text-danger">*</span></label>
							<select class="form-control valid" name="active" id="active" required="" >
								<option value="1" <?php echo ($row->active == 1) ? "selected" : "";?>>Active</option>
								<option value="0" <?php echo ($row->active == 0) ? "selected" : "";?>>Inactive</option>                
							</select>
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
		<?php } else{ ?>
			<div class="card-header">
				<span class="card-header-title"><i class="fa fa-mobile"></i>  <?php echo $page_title;?></span>
                <span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?php echo site_url('masters/menu/add'); ?>">Add Menu</a></span>  
            </div>
			<div style="height:30px;"></div>
			<div class="table-responsive">
				<table class="table table-bordered" id="settingslist" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th style="text-align: center;">Menu Name</th>
							<th style="text-align: center;">Priority</th>
							<th style="text-align: center;">Icon</th>
							<th style="text-align: center;">Controller</th>
							<th style="text-align: center !important;">&nbsp;</th>
						</tr>
					</thead>
				</table>
			</div>
		<?php } ?>
	</div>
</div>

<script>
    var dtble;
    $(document).ready(function() {
        
        var category = "<?php echo $category;?>";
        
        buildDataTable();

        $("#search, #res").on('click', function() {
            dtble.destroy();
            setTimeout(function() {
                if (typeof sessionStorage !== 'undefined') {
                    var searchDataObj = {
                        serial_no: $.trim($("#serial_no").val()),
						imei_no: $.trim($("#imei_no").val()),
                        mobile_no: $.trim($("#mobile_no").val()),
                        //assinged_to: $.trim($("#assinged_to option:selected").val()),
                        linked: $.trim($("#linked option:selected").val()),
                        active: $.trim($("#active option:selected").val())
                    }
                    sessionStorage.setItem('searchDataDevice', JSON.stringify(searchDataObj));
                }
                //  dtble.destroy();
                buildDataTable();
            }, 1000);

        });
    
	});

    function buildDataTable() {
		var category = "<?php echo $category;?>";
		//alert(category);
		
			dtble = $('#settingslist').DataTable({
			processing: true,
			serverSide: true,
			bSort: false,
			searching: false,
			stateSave: true,
			ajax: {
				url: "<?php echo site_url('/') ?>masters/getmenu/",
				type: 'POST',
				data: {
					category: $.trim(category),
					name: $.trim($("#name").val())
				},
				error: function() {
					$(".devicelists-error").html("");
				}

			},
			columns: [{
					"data": "name"
				},
				{
					"data": "priority"
				},
				{
					"data": "icon"
				},
				{
					"data": "controllers"
				},
				{
					"data": "action"
				}
			]
		});
		
    }

</script>
