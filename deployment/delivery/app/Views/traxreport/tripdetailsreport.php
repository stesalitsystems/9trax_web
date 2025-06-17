<style>
.dataTables_scrollHeadInner { width:100% !important; }
table { width:100% !important; }
.dataTables_scroll .dataTables_scrollHead { display:none !important; }
.dataTables_scroll .dataTables_scrollBody thead tr { height:auto !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th { height:auto !important; padding:8px 12px !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th .dataTables_sizing { height:auto !important; overflow:visible !important; }

/* Add custom styles for column width and overflow */
.table-responsive {
    overflow-x: auto;
}
/* .table th, .table td {
    white-space: nowrap;
} */
.table td:nth-child(19) {
    min-width: 300px; /* Adjust the width as needed */
    overflow-x: scroll;
	white-space: nowrap;
    max-width: 400px;
}
.table th:nth-child(19){
	min-width: 300px; /* Adjust the width as needed */
    /* overflow-x: scroll; */
	white-space: nowrap;
    max-width: 400px;
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
				$request = \Config\Services::request();
                $notification['msg'] = session()->getFlashdata('msg');
                if (!empty($notification['msg'])) { ?>
                    <?= view('listpagenotification', $notification); ?>
                <?php }
                ?>
                
                <h4>Search</h4>              
                <form action="<?= base_url()?>traxreport/tripdetailsreport" method="post" autocomplete="off" name="frmsearch" id="frmsearch">
					<?= csrf_field() ?>
					<div class="form-row">
						<input type="hidden" name="report_type" id="report_type" value="7" />
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="datefrmdiv">
                            <label for="exampleInputEmail1">Start Date<span class="text-danger">*</span></label>
                            <input type="input" class="form-control stdt" id="stdt" name="stdt" value="<?php if(isset($stdt)) echo $stdt; ?>" placeholder="Date" readonly>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="datefrmdiv">
                            <label for="exampleInputEmail1">End Date<span class="text-danger">*</span></label>
                            <input type="input" class="form-control endt" id="endt" name="endt" value="<?php if(isset($endt)) echo $endt; ?>" placeholder="Date" readonly>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="datefrmdiv">
                            <label for="exampleInputEmail1">User Type<span class="text-danger">*</span></label>
                            <select name="usertype" class="form-control usertype">
								<option value="All" 
								    <?php 
								       if($usertype=="All") {
										echo "selected"; 
									   }
									      
									 ?>>All</option>
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
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label for="exampleInputEmail1">Pway<span class="text-danger">*</span></label>
                            <select name="pway_id" id="pway_id" class="select_mfc" required>
                                <?php if(!empty($pway)){ ?>
                                <option value="">Select</option>
                                <option value="All" <?php if($sse_pwy == 'All'){ ?>selected<?php } ?>>All</option>
                                <?php foreach ($pway as $key=>$row){ ?>
                                  <option value="<?php echo $row->user_id?>" <?php if($sse_pwy == $row->user_id){ ?>selected<?php } ?>><?php echo $row->organisation; ?></option>
                                <?php } } ?>
                            </select>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="secdiv">
                            <label for="exampleInputEmail1">Section<span>*</span></label>
                            <select name="user" id="user" class="form-control" required>
								<option value="">Select</option>
                                <?php if(!empty($usersdd)){ ?>
                                <option value="All" <?php if($pwi_id == 'All'){ ?>selected<?php } ?>>All</option>
                                <?php foreach ($usersdd as $key=>$row){ ?>
                                  <option value="<?php echo $row->user_id?>" <?php if($pwi_id == $row->user_id){ ?>selected<?php } ?>><?php echo $row->organisation; ?></option>
                                <?php } } ?>
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
            
                <div class="table-responsive" >
                    <?php //echo "<pre>";print_r($data);echo "</pre>";exit;
					if ($request->getMethod() == 'POST') { ?>
					<table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
						<thead>
							<tr>
							<th colspan="20"> Trip Details Report From <?php echo date("d-m-Y H:i", strtotime($stdt));?> To <?php echo date("d-m-Y H:i", strtotime($endt));?></th>
							</tr>
						</thead>
						<thead>
							<tr>
								<th>SL No.</th>
								<th>Device ID</th>
								<th>User Type</th>
								<th>PWI</th>
								<th>Section</th>
								<th>Expected Start Pole</th>
								<th>Actual Start Pole</th>
								<th>Expected Start Time</th>
								<th>Actual Start Time</th>
								<th>Start Battery Percentage</th>
								<th>Expected End Pole</th>
								<th>Actual End Pole</th>
								<th>Expected End Time</th>
								<th>Actual End Time</th>
								<th>End Battery Percentage</th>
								<th>Expected Distance Travel</th>
								<th>Actual Distance Travel</th>
								<th>Time Travelled</th>
								<th>Beats Covered</th> 
								<th>Show History</th>
							</tr>
						</thead>          
						<tbody class="reportlists-body">
							<?php 
							if(count($alldata) > 0) {
								$i=0;
								$last_imeino = '';
								// print_r($alldata); die('-----');
								foreach ($alldata as $irow) {
							
									// Convert the string into an array
								    $items = array_map('trim', explode(",", $irow->beats_covered));
										
									// Filter unique values
									$unique_items = array_unique($items);
									
									// Output result as a string
									$beats_covered = implode(", ", $unique_items);
									$beats_covered = $irow->beats_covered;

                                    $timeString = $irow->timetravelled; 

									$formatted = '';

									if (preg_match('/^(?:(\d+)\s+days\s+)?(\d+):(\d+):([\d\.]+)$/', $timeString, $matches)) {
										// If the days portion is not provided, default to 0 days.
										$days    = isset($matches[1]) && $matches[1] !== '' ? (int)$matches[1] : 0;
										$hours   = (int)$matches[2];
										$minutes = (int)$matches[3];
										$seconds = (int) floor($matches[4]);  // Drop fractional seconds
										
										// Format the result as "X Days, Y hours Z minutes and W seconds"
										$formatted = sprintf("%d Days, %d hours %d minutes and %d seconds", $days, $hours, $minutes, $seconds);
									}

									
									if($last_imeino == $irow->imeino){
										//Multiple data for the same range with 3 hours gap.
										//No need to increase serial no. 
										$count = '';
									}else{
										$i++;
										$count = $i;
										$last_imeino = $irow->imeino;
									}

									$actual_stpole = '';
									if(trim($irow->actual_stpole) == ''){
                                        $beats = explode(",",$irow->beats_covered);
									    $beats = array_map('trim', $beats) ;

										if (in_array(trim($irow->expected_stpole), $beats)){
											$irow->actual_stpole = $irow->expected_stpole ;
											
										}
									}

									

									echo '<tr>
										<td>' .$count . '</td>
										<td>' . $irow->imeino . '</td>
										<td>' . $irow->usertype . '</td>
										<td>' . $irow->parent_organisation . '</td>
										<td>' . $irow->organisation . '</td>										
										<td>' . $irow->expected_stpole . '</td>
										<td>' . $irow->actual_stpole . '</td>
										<td>' . date("Y-m-d", strtotime($irow->actual_starttime)) . ' ' . $irow->expected_starttime . '</td>
										<td>' . date("Y-m-d H:i:s", strtotime($irow->actual_starttime)) . '</td>
										<td>' . $irow->startbattery . '</td>
										<td>' . $irow->expected_endpole . '</td>
										<td>' . $irow->actual_endpole . '</td>
										<td>' . date("Y-m-d", strtotime($irow->actual_endtime)) . ' ' . date("H:i:s", strtotime($irow->expected_endtime)) . '</td>
										<td>' . date("Y-m-d H:i:s", strtotime($irow->actual_endtime)) . '</td>
										<td>' . $irow->endbattery . '</td>
										<td>' . number_format($irow->distance_travelled,4) . '</td>
										<td>' . number_format($irow->totaldistancetravel,4) . '</td>
										<td>' . $formatted . '</td>
										<td>' . $irow->beats_covered . '</td>
										<td><a href="'.site_url('controlcentre/view').'/'.$irow->deviceid.'/'.date("d-m-Y", strtotime($irow->actual_starttime)).'/'.date("H:i", strtotime($irow->actual_starttime)).'/'.date("H:i", strtotime($irow->actual_endtime)).'" target="_blank">History</a></td>
									</tr>';

									//<td>' . $irow->beats_covered . '</td>

									//$i++;
								} 
								/* 
								<td>' . $beats_covered .'</td>
								<td>' . $irow->beats_covered . '</td>*/
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
			if($('#stdt').val()==''){				
				alert('Please select Date From');
				chk++;
			}
			if($('#endt').val()==''){				
				alert('Please select Date To');
				chk++;
			}
			if($('#pway_id').val()==''){				
				alert('Please select Pway');
				chk++;
			}
			if($('#user').val()==''){				
				alert('Please select Section');
				chk++;
			}
			if(chk == 0){
				e.preventDefault();
				form = $('#frmsearch');
				form.submit();
			}
		});
		
		$("#res").on('click', function() {
			window.location.href = BASEURL + 'traxreport/tripdetailsreport';
		});
		
		$( ".stdt" ).datetimepicker({
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
			submitFRM(BASEURL + 'traxreport/tripDetailsReportExcel');
		}		
    </script>