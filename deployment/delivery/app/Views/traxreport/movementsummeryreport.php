
<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-fw fa-bar-chart"></i> <?php echo $page_title;?>
        </div>
        <div class="col-xs-12 search-panel">
            <?php
            $notification['msg'] = $this->session->flashdata('msg');
            if (!empty($notification['msg'])) {
                $this->load->view('listpagenotification', $notification);
            }
            ?>
            <div class="container">
                <h4>Search</h4>              
                <?php echo form_open("traxreport/movementsummeryreport",array("autocomplete"=>"off","name"=>"frmsearch","onsubmit"=>"return submit_form();")) ?>
                    <div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
							<label>Device<span class="rqurd">*</span></label>
							<select class="form-control" id="device_id" name="device_id">
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
					<div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
							<button type="submit" class="btn btn-primary pull-right" name="search" id="search">Search</button>
							<?php if(count($report_data) > 0){ ?>
							<button type="button" class="btn btn-primary pull-right" name="pdfdownload" id="pdfdownload" style="margin-right: 0.5em;" onclick="return pdfSubmit();">Download PDF</button>
							<button type="button" class="btn btn-primary pull-right" name="exceldownload" id="exceldownload" style="margin-right: 0.5em;" onclick="return excelSubmit();">Download Excel</button>
							<?php } ?>
						</div>
					</div>                    
                </form>
            </div>  
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
						<thead>
							<tr>
								<th>Device ID</th>
								<th>Start Date Time</th>
								<th>End Date Time</th>
								<th>Travelled Distance(KM)</th>
							</tr>
						</thead>
						<?php if (!empty($_POST) && isset($_POST['search'])) { ?>
						<tbody class="reportlists-body">
							<?php if(!empty($report_data)){
							foreach($report_data as $report_data_each){
								
							?>
							
							<tr>
								<td><?php echo $report_data_each->serial_no; ?></td>
								<td><?php echo $report_data_each->stsrt; ?></td>
								<td><?php echo $report_data_each->endtime; ?></td>
								<td><?php echo round($report_data_each->length/1000).' km'; ?></td>
							</tr>
							
							<?php } } else { ?>
							<tr><td colspan="4">No Records Found</td></tr>
							<?php } ?>
						</tbody>
						<?php } else { ?>
						<tbody class="reportlists-body">
							<tr><td colspan="4">Search To Generate Report</td></tr>
						</tbody>
						<?php } ?>
					</table>
                </div>
            </div>
    </div>
	
    <script>
    $(document).ready(function() {
		<?php if(!empty($report_data)){ ?>
		$('#reportlists').DataTable({
			searching: false,
			bSort: false,
		});
		<?php } ?>
		
		$("#res").on('click', function() {
			window.location.href = BASEURL + 'traxreport/movementsummeryreport';
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
		var device_id = $("#device_id").val();
		var dt = $("#dt").val();
		
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
        if(&& $("#device_id").val()!="" && $("#dt").val()!=""){
            submitFRM(BASEURL + 'traxreport/movementsummeryreportpdf');
        }else{
           alert('All search fields are required');
		   return false;
        }
    }
	
	function excelSubmit(){
        if($("#device_id").val()!="" && $("#dt").val()!=""){
            submitFRM(BASEURL + 'traxreport/movementsummeryreportexcel');
        }else{
           alert('All search fields are required');
		   return false;
        }
    }

</script>