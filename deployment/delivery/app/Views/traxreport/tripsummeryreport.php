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
                <?php
				$request = \Config\Services::request();
                $notification['msg'] = session()->getFlashdata('msg');
                if (!empty($notification['msg'])) { ?>
                    <?= view('listpagenotification', $notification); ?>
                <?php }
                ?>
                
                <h4>Search</h4>              
                <form action="<?= base_url()?>traxreport/tripSummaryReport" method="post" autocomplete="off" name="frmsearch" id="frmsearch">
					<?= csrf_field() ?>
					<div class="form-row">
						<input type="hidden" name="report_type" id="report_type" value="7" />
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="datefrmdiv">
                            <label for="exampleInputEmail1">Date<span class="text-danger">*</span></label>
                            <input type="input" class="form-control stdt" id="dt" name="dt" value="<?php if(isset($dtt)) echo $dtt; ?>" placeholder="Date" readonly>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="datefrmdiv">
                            <label for="exampleInputEmail1">User Type<span class="text-danger">*</span></label>
                            <select name="usertype" id="usertype" class="form-control usertype">
								<option value="">Select</option>
								<option value="Patrolman" 
								    <?php 
								       if($usertype=="Patrolman") {
										echo "selected"; 
									   }
									 ?>>Patrolman</option>
								<option value="Keyman" 
								    <?php 
								       if($usertype=="Keyman") {
										echo "selected"; 
									   }
									      
									 ?>>Keyman</option>
							</select> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
                            <button type="button" class="btn btn-primary pull-right" name="search" id="search">Search</button>
                            <?php if(!empty($alldata)){ ?>
                            <!--<button type="button" class="btn btn-primary pull-right" name="pdfdownload" id="pdfdownload" style="margin-right: 0.5em;" onclick="return pdfSubmit();">Download PDF</button>-->
                            <button type="button" class="btn btn-primary pull-right" name="exceldownload" id="exceldownload" style="margin-right: 0.5em;" onclick="return excelSubmit();">Download Excel</button>
                            <!--<button type="button" class="btn btn-primary pull-right" name="savereport" id="savereport" style="margin-right: 0.5em;">Save</button>-->
                            <?php } ?>
                        </div>
                    </div>                    
                </form>
            </div>  
        </div>
            
                <div class="table-responsive">
                    <?php if ($request->getMethod() == 'POST') { ?>
					<table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
						<thead>
							<tr>
							<th colspan="20"> <?php if(count($alldata) > 0) { echo $alldata[0]->report_name . ' - ' . date("d-m-Y", strtotime($dtt)); }?> </th>
							</tr>
						</thead>
						<thead>
							<tr>
								<th>SL No.</th>
								<th>Time</th>
								<th>SSE/PWY</th>
								<th>Section</th>
								<th>Off Device</th>
								<th>Beat Covered</th>
								<th>Beat Not Covered</th>
								<th>Over Speed</th>
							</tr>
						</thead>          
						<tbody class="reportlists-body">
							<?php 
							if(count($alldata) > 0) {
								$i = 1;
								$timetravelled = 0;
								// Grouping Data by Device ID
								$groupedData = [];
								foreach ($alldata as $irow) {
									
									echo '<tr>
										<td>' . $i . '</td>
										<td>' . $irow->dttime . '</td>
										<td>' . $irow->pway . '</td>
										<td>' . $irow->section . '</td>
										<td>' . $irow->device_off . '</td>
										<td>' . $irow->beats_covered . '</td>
										<td>' . $irow->beats_not_covered . '</td>
										<td>' . $irow->overspeed . '</td>
									</tr>';
									$i++;
								}
							} else { ?>
							<tr>
								<td colspan="18">No Records Found</td>
							</tr>
							<?php } ?>
						</tbody>
                    </table>
					<?php } else { ?>
					<table><tr><td style="padding-left: 1.5em;">Search To Generate Report</td></tr></table>
					<?php } ?>
                </div>
            </div>
    </div>
	
    <script>
        $("#search").click(function(e){			
			var chk = 0;			
			if($('#dt').val()==''){				
				alert('Please select Date From');
				chk++;
			}
			
			if($('#usertype').val()==''){				
				alert('Please select User Type');
				chk++;
			}
			if(chk == 0){
				e.preventDefault();
				form = $('#frmsearch');
				form.submit();
			}
		});
		
		$("#res").on('click', function() {
			window.location.href = BASEURL + 'traxreport/tripSummaryReport';
		});
		
		$( ".stdt" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
			maxDate: 0
            <!--maxDate: '-1'-->
        });
		$( ".endt" ).datetimepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
			maxDate: 0
            <!--maxDate: '-1'-->
        });
		
        $(document).ready(function () {
			<?php if(!empty($alldata)){ ?>
			$('#lists').DataTable({
				searching: false,
				bSort: false,
				scrollX: true,
				scrollY: false,
				lengthChange: false,
				scrollCollapse: true
			});
			<?php } ?>
        });
		
		function submitFRM(url){
			var prevURL = document.frmsearch.action;
			document.frmsearch.action = url;
			document.frmsearch.submit();
			document.frmsearch.action = prevURL;
		}
		
		function excelSubmit(){
			submitFRM(BASEURL + 'traxreport/tripSummaryReportExcel');
		}		

		

	
    </script>