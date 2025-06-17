<style>
.dataTables_scrollHeadInner { width:100% !important; }
table { width:100% !important; }
.dataTables_scroll .dataTables_scrollHead { display:none !important; }
.dataTables_scroll .dataTables_scrollBody thead tr { height:auto !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th { height:auto !important; padding:8px 12px !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th .dataTables_sizing { height:auto !important; overflow:visible !important; }
</style>
<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-fw fa-bar-chart"></i> <?php echo $page_title;?>
        </div>
        <div class="card-body">
            <div class="search-panel">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= print_r($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= esc($success) ?></div>
                <?php endif; ?>

                <form action="<?= base_url()?>trip/checkimei" method="post" autocomplete="off" name="frmsearch" id="frmsearch">
					<?= csrf_field() ?>
					<div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label>Device<span class="rqurd">*</span></label>
							<select class="select_mfc" id="device_id" name="device_id">
							   <option value="">Select</option>
							   <?php
								if (isset($devicedropdown) && !empty($devicedropdown)) {
									foreach ($devicedropdown as $row) {
										?>
										<option value="<?php echo $row->did ?>" <?php if(!empty($device_id) && ($device_id==$row->did)) echo "selected"; ?>><?php echo $row->serial_no.' - '.$row->device_name; ?></option>
										<?php
									}
								}
								?>
							</select>
						</div>
                    </div>
                    <div class="form-row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <button type="reset" class="btn btn-danger" name="res" id="res">Edit</button>
                        </div>
                    </div>                    
                </form>
                        
            </div>  
        </div>
    </div>    
</div>

<script>
    $("#res").on('click', function() {
        var imei = $('#device_id').val();
        if(imei == '') {
            alert('Please select Date To');
            return false;
        }
        window.location.href = BASEURL + 'trip/individual-upload/' + imei ;
    });
</script>