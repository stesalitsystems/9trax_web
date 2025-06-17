<link href="<?php echo base_url() ?>assets/css/chosen.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo base_url() ?>assets/js/chosen.jquery.min.js" type="text/javascript"></script>
<style>
.chosen-container-single .chosen-single{
		height: 38px;
	}
</style>
<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-fw fa-bar-chart"></i> <?php echo $page_title;?>
        </div>
        <div class="card-body">
        <div class="search-panel">
            <?php
            $notification['msg'] = $this->session->flashdata('msg');
            if (!empty($notification['msg'])) {
                $this->load->view('listpagenotification', $notification);
            }
            ?>           
                <?php echo form_open("traxreport/alertreport",array("autocomplete"=>"off","name"=>"frmsearch","onsubmit"=>"return submit_form();")) ?>
                    <div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<label>Alert Type<span class="rqurd">*</span></label>
							<select class="select_mfc" id="config_id" name="config_id">
							   <option value="">Select</option>
							   <?php
								if (isset($master_alart_dd) && !empty($master_alart_dd)) {
									foreach ($master_alart_dd as $row) {
										?>
										<option value="<?php echo $row->id ?>" <?php if($config_id==$row->id) echo "selected"; ?>><?php echo $row->description ?></option>
										<?php
									}
								}
								?>
							</select>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<label>Device<span class="rqurd">*</span></label>
							<select class="select_mfc" id="device_id" name="device_id">
							   <option value="">Select</option>
							   <?php
								if (isset($devicedropdown) && !empty($devicedropdown)) {
									foreach ($devicedropdown as $row) {
										?>
										<option value="<?php echo $row->did ?>" <?php if($device_id==$row->did) echo "selected"; ?>><?php echo $row->serial_no.' - '.$row->device_name; ?></option>
										<?php
									}
								}
								?>
							</select>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<label>Date<span class="rqurd">*</span></label>
							<input type="input" class="form-control dt" id="dt" name="dt" value="<?php echo $dt; ?>" placeholder="Date" readonly>
						</div>
					</div>
					<div class="form-row mb-0">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
							<button type="submit" class="btn btn-primary pull-right" name="search" id="search">Search</button>
							<?php if(!empty($report_data) && count($report_data) > 0){ ?>
							<button type="button" class="btn btn-primary pull-right" name="pdfdownload" id="pdfdownload" style="margin-right: 0.5em;" onclick="return pdfSubmit();">Download PDF</button>
							<button type="button" class="btn btn-primary pull-right" name="exceldownload" id="exceldownload" style="margin-right: 0.5em;" onclick="return excelSubmit();">Download Excel</button>
							<?php } ?>
						</div>
					</div>                    
                </form>
            </div>
        </div>                
    </div>

    <div style="height:30px;"></div>


    <div class="table-responsive">
                    <table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
						<thead>
							<tr>
								<th>Alert Type</th>
								<th>Device ID</th>
								<th>Date Time</th>
							</tr>
						</thead>
						<?php if (!empty($_POST) && isset($_POST['search'])) { ?>
						<tbody class="reportlists-body">
							<?php if(!empty($report_data)){
							foreach($report_data as $report_data_each){
								
							?>
							
							<tr>
								<td><?php echo $report_data_each->description; ?></td>
								<td><?php echo $report_data_each->serial_no; ?></td>
								<td><?php echo date('d-m-Y', strtotime($report_data_each->currentdate)).' '.$report_data_each->currenttime; ?></td>
							</tr>
							
							<?php } } else { ?>
							<tr><td colspan="3">No Records Found</td></tr>
							<?php } ?>
						</tbody>
						<?php } else { ?>
						<tbody class="reportlists-body">
							<tr><td colspan="3">Search To Generate Report</td></tr>
						</tbody>
						<?php } ?>
					</table>
                </div>
	
    <script>
    $(document).ready(function() {
		//$('#device_id').chosen();
		<?php if(!empty($report_data)){ ?>
		$('#reportlists').DataTable({
			searching: false,
			bSort: false,
		});
		<?php } ?>
		
		$("#res").on('click', function() {
			window.location.href = BASEURL + 'traxreport/alertreport';
		});
		
		$( ".dt" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            //minDate: new Date('2016/09/01'),
            maxDate: '0'
        });
	
	});
	
	function submit_form(){
		var config_id = $("#config_id").val();
		var device_id = $("#device_id").val();
		var dt = $("#dt").val();
		
		if(config_id == '')
		{
			alert('Please select alert type');
			return false;
		}
		if(device_id == '')
		{
			alert('Please select device');
			return false;
		}
		if(dt == '')
		{
			alert('Please enter date');
			return false;
		}
	}
	
	function submitFRM(url){
		var prevURL = document.frmsearch.action;
		document.frmsearch.action = url;
		document.frmsearch.submit();
		document.frmsearch.action = prevURL;
	}
	
	function pdfSubmit(){
        if($("#config_id").val()!="" && $("#device_id").val()!="" && $("#dt").val()!=""){
            submitFRM(BASEURL + 'traxreport/alertreportpdf');
        }else{
           alert('All search fields are required');
		   return false;
        }
    }
	
	function excelSubmit(){
        if($("#config_id").val()!="" && $("#device_id").val()!="" && $("#dt").val()!=""){
            submitFRM(BASEURL + 'traxreport/alertreportexcel');
        }else{
           alert('All search fields are required');
		   return false;
        }
    }

</script>