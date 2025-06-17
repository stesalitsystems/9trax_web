
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

			<form action="<?= base_url('devices/uploadsoscallcsv') ?>" method="post" autocomplete="off" id="deviceupload" novalidate="true" class="form-actions" enctype="multipart/form-data">
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
			</form>

		</div>
		
	</div>
</div>

<script>
    var dtble;
    $(document).ready(function() {
        
        var category = "";
        
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
		var category = "";
		//alert(category);
		if(category == 'polldata'){
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
