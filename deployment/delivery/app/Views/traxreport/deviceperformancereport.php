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
                <form action="<?= base_url()?>traxreport/deviceperformancereport" method="post" autocomplete="off" name="frmsearch" id="frmsearch">
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
                            <label for="exampleInputEmail1">Section<span class="text-danger">*</span></label>
                            <select name="user" id="user" class="form-control" required>
								<option value="">Select</option>
                                <?php if(!empty($usersdd)){ ?>
                                <option value="All" <?php if($pwi_id == 'All'){ ?>selected<?php } ?>>All</option>
                                <?php foreach ($usersdd as $key=>$row){ ?>
                                  <option value="<?php echo $row->user_id?>" <?php if($pwi_id == $row->user_id){ ?>selected<?php } ?>><?php echo $row->organisation; ?></option>
                                <?php } } ?>
                            </select>
                            <!-- <input type="hidden" name="pwi_name" id="pwi_name" value="<?php // echo $pwi_name;?>"/>
							<input type="hidden" name="section_id" id="section_id" value="<?php // echo $pwi_id;?>"/>
							<input type="hidden" name="map_device_id" id="map_device_id" value="<?php // if(isset($map_device_id)) echo $map_device_id;?>" />
							<input type="hidden" name="map_start_date" id="map_start_date" value="<?php // if(isset($map_start_date)) echo $map_start_date;?>" />
							<input type="hidden" name="map_start_time" id="map_start_time" value="<?php // if(isset($map_start_time)) echo $map_start_time;?>" />
							<input type="hidden" name="map_end_date" id="map_end_date" value="<?php // if(isset($map_end_date)) echo $map_end_date;?>" />
							<input type="hidden" name="map_end_time" id="map_end_time" value="<?php // if(isset($map_end_time)) echo $map_end_time;?>" /> -->
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label>Device<span class="text-danger">*</span></label>
							<select class="select_mfc" id="device_id" name="device_id" required>
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
                    </div>
                    <div class="form-row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
                            <button type="button" class="btn btn-primary pull-right" name="search" id="search">Search</button>
                            <?php if(!empty($alldata)){ ?>
                            <!--<button type="button" class="btn btn-primary pull-right" name="pdfdownload" id="pdfdownload" style="margin-right: 0.5em;" onclick="return pdfSubmit();">Download PDF</button>-->
                            <!-- <button type="button" class="btn btn-primary pull-right" name="exceldownload" id="exceldownload" style="margin-right: 0.5em;" onclick="return excelSubmit();">Download Excel</button> -->
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
							<th colspan="20"> Device Performence Report From <?php echo date("d-m-Y H:i", strtotime($stdt));?> To <?php echo date("d-m-Y H:i", strtotime($endt));?></th>
							</tr>
						</thead>
						<thead>
							<tr>
								<th>SL No.</th>
								<th>Device ID</th>
								<th>BIT</th>
								<th>SSE/PWY</th>
								<th>Section</th>
								<th>User Type</th>
								<th>Start Date Time</th>
								<th>Start Battery Percentage</th>
								<th>End Date Time</th>
								<th>End Battery Percentage</th>
								<th>Travelled Distance(KM)</th>
								<th>Travelled Time</th>
								<th>Show History</th>
							</tr>
						</thead>          
						<tbody class="reportlists-body">
							<?php 
							if(count($alldata) > 0) {
								$i = 1; 
								foreach ($alldata as $row) {
									$seconds = strtotime($row->timetravelled); // Ensure it's an integer

									$days = floor($seconds / 86400); 
									$seconds %= 86400; 

									$hours = floor($seconds / 3600);
									$seconds %= 3600;

									$minutes = floor($seconds / 60);
									$seconds %= 60;

									$formatted = "{$hours} hours {$minutes} mins {$seconds} sec";

									echo '<tr>
										<td>' . $i . '</td>
										<td>' . $row->imeino . '</td>
										<td>' . ($row->expected_stpole ?? 'NaN') . '-' . ($row->expected_endpole ?? 'NaN') . '</td>
										<td>' . $row->parent_organisation . '</td>
										<td>' . $row->organisation . '</td>
										<td>' . $row->usertype . '</td>
										<td>' . date("Y-m-d H:i:s", strtotime($row->actual_starttime)) . '</td>
										<td>' . $row->startbattery . '</td>
										<td>' . date("Y-m-d H:i:s", strtotime($row->actual_endtime)) . '</td>
										<td>' . $row->endbattery . '</td>
										<td>' . number_format($row->totaldistancetravel, 4) . ' KM</td>
										<td>' . $formatted . '</td>
										<td>' . 'History' . '</td>
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
			if($('#device_id').val()==''){				
				alert('Please select a Device');
				chk++;
			}
			if(chk == 0){
				e.preventDefault();
				form = $('#frmsearch');
				form.submit();
			}
		});
		
		$("#res").on('click', function() {
			window.location.href = BASEURL + 'traxreport/deviceperformancereport';
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

		$(document).on("change", "#user", function(e){
			$("#pwi_name").val($("#user :selected").text());
		});

		$("#pway_id").on('change', function() {
			populateSection($(this).val());
		});
		<?php if($_POST) {
		?>
		populateSection('<?php echo $sse_pwy; ?>');
		<?php
		}
		?>

	function populateSection(pwayid) {
		$.ajax({
			url: '<?php echo site_url('traxreport/getUser') ?>',
			data: {
				"user_id": pwayid,
				"pwi_id": '<?php echo $pwi_id ? $pwi_id : '' ?>'
			},
			type: 'POST',
			success: function(data) {
				if(data=='		') {
					var data1 = '<option value="">Select An Option</option><option value="All">All</option>';
					$("#user").html(data1);
				} else if(data!='') {
					$("#user").empty();
					$("#user").html(data);
					$('#user').val('<?php echo $pwi_id ? $pwi_id : '' ?>');
				} 
				
			}
		});
	}
    </script>