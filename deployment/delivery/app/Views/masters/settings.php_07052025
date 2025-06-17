
<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
		<?php if($segment == 'upload'){ ?>
			<div class="card-header">
				<i class="fa fa-mobile"></i> <?php echo $page_title;?>
			</div>
			<div class="search-panel">
				<div class="col-xs-12 col-md-12">
					<?php if(isset($errmsg) && !empty($errmsg)){?>
					<div class="error"><?php echo $errmsg?></div>
					<?php } ?>
				</div>

				<form action='<?= base_url("masters/settings/".$category."/upload") ?>' method="post" autocomplete="off" id="deviceupload" novalidate="true" class="form-actions" enctype="multipart/form-data">
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
							<a href="<?php echo site_url('masters/settings/'.$category)?>" class="btn btn-danger">Back</a>
						</div>                
					</div>
				</form>

			</div>
		<?php }else if($segment == 'upload_assignment'){ ?>
			<div class="card-header">
				<i class="fa fa-mobile"></i> <?php echo $page_title;?>
			</div>
			<div class="search-panel">
				<div class="col-xs-12 col-md-12">
					<?php if(isset($errmsg) && !empty($errmsg)){?>
					<div class="error"><?php echo $errmsg?></div>
					<?php } ?>
				</div>

				<?php echo form_open("masters/settings/".$category."/upload_assignment",array("autocomplete"=>"off","id"=>"deviceupload","novalidate"=>"true","class"=>"form-actions","enctype"=>"multipart/form-data")) ?>

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
						<a href="<?php echo site_url('masters/settings/'.$category)?>" class="btn btn-danger">Back</a>
					</div>                
				</div>
				<?php echo form_close(); ?>

			</div>
		
		<?php } else{ ?>
			<div class="card-header">
				<span class="card-header-title"><i class="fa fa-mobile"></i>  <?php echo $page_title;?></span>
                <span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?php echo site_url('masters/settings/'.$category.'/upload'); ?>">Upload Bulk CSV</a></span>
				<!--<span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?php echo ($category == 'polldata') ? base_url().'assets/csv/add_pole_list.csv' : base_url().'assets/csv/add_settings_list.csv'; ?>" download>Download Bulk CSV Format</a></span>-->
                <span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?php echo base_url().'assets/csv/add_'.$category.'_list.csv'; ?>" download>Download Bulk CSV Format</a></span>
                <?php if($category == 'polldata'){ ?>
                <!-- <span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?php // echo site_url('masters/settings/'.$category.'/upload_assignment/'); ?>">Assign Device</a></span>
                <span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?php // echo base_url().'assets/csv/device_pole_assign.csv'; ?>">Download Device Assignment Format</a></span> -->
				<?php } ?>
            </div>
			<div class="card-body">
				<div class="search-panel">
					<?php
						$notification['msg'] = session()->getFlashdata('msg');
						if(!empty($notification['msg'])){ ?>
							<?= view('listpagenotification',$notification); ?>
						<?php }                
					?>
					<form onsubmit="return false;">
						<div class="form-row">
						   
							<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
								<label>Name</label>
								<input type="text" class="form-control" placeholder="Name" name="name" id="name">
							</div>
						</div>

						<div class="form-row mb-0">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
								<button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
								<button type="submit" class="btn btn-primary pull-right" name="search" id="search">Search</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div style="height:30px;"></div>
			<div class="table-responsive">
				<table class="table table-bordered" id="settingslist" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th style="text-align: center;">Name</th>
							<th style="text-align: center;">Latitude</th>
							<th style="text-align: center;">Longitude</th>
							<th style="text-align: center;">Description</th>
							<?php if(trim($segment3) == 'polldata'){ ?>
							<th style="text-align: center;">Pole Description</th>
							<th style="text-align: center;">Parent Pole No.</th>
							<?php } ?>
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
		if(category == 'polldata'){
			dtble = $('#settingslist').DataTable({
				processing: true,
				serverSide: true,
				bSort: false,
				searching: false,
				stateSave: true,
				ajax: {
					url: "<?= base_url() ?>masters/getallsettings/"+category,
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
						"data": "latitude"
					},
					{
						"data": "longitude"
					},
					{
						"data": "description"
					},
					{
						"data": "poledescription"
					},
					{
						"data": "parent_polno"
					}
				]
			});
		}
		else{
				dtble = $('#settingslist').DataTable({
				processing: true,
				serverSide: true,
				bSort: false,
				searching: false,
				stateSave: true,
				ajax: {
					url: "<?php echo site_url('/') ?>masters/getallsettings/"+category,
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
						"data": "latitude"
					},
					{
						"data": "longitude"
					},
					{
						"data": "description"
					}
				]
			});
		}
    }

</script>
