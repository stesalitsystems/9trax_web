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
                <form action="<?= base_url()?>traxreport/beatcompletionreport" method="post" autocomplete="off" name="frmsearch" id="frmsearch">
					<?= csrf_field() ?>
					<div class="form-row">
						<input type="hidden" name="report_type" id="report_type" value="7" />
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="typeofuserdiv">
                            <label>Type Of User</label>
                            <select class="form-control selectpicker select_mfc" name="typeofuser" id="typeofuser">
								<option value="All" <?php if(isset($typeofuser) && $typeofuser == "All"){ ?>selected<?php } ?>>All</option>
                              	<option value="Keyman" <?php if(isset($typeofuser) && $typeofuser == "Keyman"){ ?>selected<?php } ?>>Keyman</option>
								<option value="Patrolman" <?php if(isset($typeofuser) && $typeofuser == "Patrolman"){ ?>selected<?php } ?>>Patrolman</option>
								<option value="Mate" <?php if(isset($typeofuser) && $typeofuser == "Mate"){ ?>selected<?php } ?>>Mate</option>
								<option value="USFD" <?php if(isset($typeofuser) && $typeofuser == "USFD"){ ?>selected<?php } ?>>USFD</option>
								<!--<option value="Others" <?php if(isset($typeofuser) && $typeofuser == "Others"){ ?>selected<?php } ?>>Others</option>-->
                            </select>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label for="exampleInputEmail1">Pway<span class="text-danger">*</span></label>
                            <select name="pway_id" id="pway_id" class="select_mfc" required>
                                <?php if(!empty($pway)){ ?>
                                <option value="">Select</option>
                                <option value="All" <?php if(isset($sse_pwy) && $sse_pwy == 'All'){ ?>selected<?php } ?>>All</option>
                                <?php foreach ($pway as $key=>$row){ ?>
                                  <option value="<?php echo $row->user_id?>" <?php if(isset($sse_pwy) && $sse_pwy == $row->user_id){ ?>selected<?php } ?>><?php echo $row->organisation; ?></option>
                                <?php } } ?>
                            </select>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="secdiv">
                            <label for="exampleInputEmail1">Section<span class="text-danger">*</span></label>
                            <select name="user" id="user" class="form-control" required>
                                <?php if(!empty($usersdd)){ ?>
                                <option value="All" <?php if(isset($pwi_id) && $pwi_id == 'All'){ ?>selected<?php } ?>>All</option>
                                <?php foreach ($usersdd as $key=>$row){ ?>
                                  <option value="<?php echo $row->user_id?>" <?php if(isset($pwi_id) && $pwi_id == $row->user_id){ ?>selected<?php } ?>><?php echo $row->organisation; ?></option>
                                <?php } } ?>
                            </select>
                            <input type="hidden" name="pwi_name" id="pwi_name" value="<?php if(isset($pwi_id)) echo $pwi_name;?>"/>
							<input type="hidden" name="section_id" id="section_id" value="<?php if(isset($pwi_id)) echo $pwi_id;?>"/>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="datefrmdiv">
                            <label for="exampleInputEmail1">Start Date<span class="text-danger">*</span></label>
                            <input type="input" class="form-control stdt" id="stdt" name="stdt" value="<?php if(isset($stdt)) echo $stdt; ?>" placeholder="Date" readonly>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="datefrmdiv">
                            <label for="exampleInputEmail1">End Date<span class="text-danger">*</span></label>
                            <input type="input" class="form-control endt" id="endt" name="endt" value="<?php if(isset($endt)) echo $endt; ?>" placeholder="Date" readonly>
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
                    <?php //echo "<pre>";print_r($data);echo "</pre>";exit;
					if ($request->getMethod() == 'POST') { ?>
					<table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
                        <?php if($report_type == 7){ ?>
						<thead>
							<tr>
							<th colspan="8"><?php echo $typeofuser;?> Work Status Count Report Date <?php echo date("d-m-Y", strtotime($stdt));?> To <?php echo date("d-m-Y", strtotime($endt));?></th>
							</tr>
						</thead>
						<thead>
							<tr>
							<th>Date</th>
							<th>PWI</th>
							<th>Section Name</th>
							<th>Off Device</th>
							<th>Beat Not Covered</th>
							<th>Beat Covered Successfully</th>
							<th>Device Not Alloted</th>
							<th>Total Device</th>
							<!-- <th>Effective Hours Of Working</th>
							<th>Status</th>
							<th>Remarks</th> -->
							</tr>
						</thead>          
						<tbody class="reportlists-body">
							<?php 
							function sum_the_time($time1, $time2)
							{
								$times = array($time1, $time2);
								$seconds = 0;
						
								foreach ($times as $time) {
									list($hour, $minute, $second) = explode(':', $time);
									$seconds += $hour * 3600;
									$seconds += $minute * 60;
									$seconds += $second;
								}
						
								$hours = floor($seconds / 3600);
								$seconds -= $hours * 3600;
								$minutes = floor($seconds / 60);
								$seconds -= $minutes * 60;
						
								// Format the output
								$hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
								$minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
								$seconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);
						
								return "{$hours}:{$minutes}:{$seconds}";
							}
							
							// echo "<pre>";print_r($alldata);echo "</pre>";exit;
							$inactive_device = $beat_not_covered = $beat_covered = $totaldevice = 0;
							$total_not_alloted_devices = 0;
							$total_duration = '00:00:00';
							if(count($alldata) > 0) {
								foreach ($alldata as $irow) {
									$inactive_device += $irow->inactive_device;
									$beat_not_covered += $irow->beat_not_covered;
									$beat_covered += $irow->beat_covered;
							
									if ($typeofuser != 'All') {
										$total_devices = $irow->inactive_device + $irow->active_device;
									} else {
										$total_devices = $irow->total_devices;
									}
							
									$totaldevice += $total_devices;
									$total_not_alloted_devices += $irow->not_alloted_devices;
							
									// Use the helper function sum_the_time from your model or controller
									$total_duration = sum_the_time($total_duration, $irow->duration);
							
									echo '<tr>
										<td>' . esc($irow->date) . '</td>
										<td>' . esc($irow->pwi) . '</td>
										<td>' . esc($irow->organisation) . '</td>
										<td><a href="' . base_url('traxreport/devicedetails/' . $irow->inactive_device . '/' . $irow->date . '/inactivedevicesection/' . $irow->section_id . '/' . $typeofuser) . '" target="_blank">' . esc($irow->inactive_device) . '</a></td>
										<td><a href="' . base_url('traxreport/devicedetails/' . $irow->inactive_device . '/' . $irow->date . '/beatnotcovereddevicesection/' . $irow->section_id . '/' . $typeofuser) . '" target="_blank">' . esc($irow->beat_not_covered) . '</a></td>
										<td><a href="' . base_url('traxreport/devicedetails/' . $irow->inactive_device . '/' . $irow->date . '/beatcovereddevicesection/' . $irow->section_id . '/' . $typeofuser) . '" target="_blank">' . esc($irow->beat_covered) . '</a></td>
										<td><a href="' . base_url('traxreport/devicedetails/' . $irow->inactive_device . '/' . $irow->date . '/notalloteddevicesection/' . $irow->section_id . '/' . $typeofuser) . '" target="_blank">' . esc($irow->not_alloted_devices) . '</a></td>
										<td><a href="' . base_url('traxreport/devicedetails/' . $irow->inactive_device . '/' . $irow->date . '/totaldevicedevicesection/' . $irow->section_id . '/' . $typeofuser) . '" target="_blank">' . esc($total_devices) . '</a></td>
									</tr>';
								} 
								/*
								<td>' . esc($irow->duration) . '</td>
										<td>' . esc($irow->beat_coverage_percentage) . '% Beat Not Covered</td>
										<td>&nbsp;</td>
										*/
							echo '<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>Total</td>
								<td><a href="'.base_url().'traxreport/totaldevicedetails/'.$irow->inactive_device.'/'.$irow->date.'/inactivedevicesection/' . $irow->section_id . '/' . $typeofuser . '" target="_blank">'.$inactive_device.'</a></td>
								<td><a href="'.base_url().'traxreport/totaldevicedetails/'.$irow->inactive_device.'/'.$irow->date.'/beatnotcovereddevicesection/' . $irow->section_id . '/' . $typeofuser . '" target="_blank">'.$beat_not_covered.'</a></td>
								<td><a href="'.base_url().'traxreport/totaldevicedetails/'.$irow->inactive_device.'/'.$irow->date.'/beatcovereddevicesection/' . $irow->section_id . '/' . $typeofuser . '" target="_blank">'.$beat_covered.'</a></td>
								<td><a href="'.base_url().'traxreport/totaldevicedetails/'.$irow->inactive_device.'/'.$irow->date.'/notalloteddevicesection/' . $irow->section_id . '/' . $typeofuser . '" target="_blank">'.$total_not_alloted_devices.'</a></td>
								<td><a href="'.base_url().'traxreport/totaldevicedetails/'.$irow->inactive_device.'/'.$irow->date.'/totaldevicedevicesection/' . $irow->section_id . '/' . $typeofuser . '" target="_blank">'.$totaldevice.'</a></td>
								</tr>';
							} 
							/*
							<td>'.$total_duration.'</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							*/
							else { ?>
							
							<tr>
								<td colspan="11">No Records Found</td>
							</tr>
							<?php } ?>
						</tbody>
						<?php } ?>
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
			if($('#date_from').val()==''){				
				alert('Please select Date From');
				chk++;
			}
			if($('#date_to').val()==''){				
				alert('Please select Date To');
				chk++;
			}
			/*if($('#user').val()){				
			}
			else{				
				alert('Please select PWI');
				chk++;
			}*/
			if(chk == 0){
				e.preventDefault();
				form = $('#frmsearch');
				form.submit();
			}
		});
		var pad = function(val) { var str = val.toString(); return (str.length < 2) ? "0" + str : str};
		function gettodate(d,val) {		
			if($('#report_type').val() == 8 || $('#report_type').val() == 11){
				d = d.split(" ");
				d = d[0].split("-");
				$("[name='date_from']").val(d[0] +'-'+ d[1] +'-'+ d[2] +' '+ '06:00');
				$("[name='date_to']").datetimepicker("disable");
				var dt = new Date(d[2]+'-'+d[1]+'-'+d[0]);
				dt.setDate(dt.getDate() + 1);
				var dty = dt.getFullYear(),
					dtm = dt.getMonth() + 1, // january is month 0 in javascript
					dtd = dt.getDate();
				$("[name='date_to']").val(pad(dtd)+'-'+pad(dtm)+'-'+dty+' '+ '06:00');
			}
			else{
				d = d.split(" ");
				d = d[0].split("-");
				var sunday = new Date(d[2],(d[1]-1),d[0]);
				sunday.setDate(sunday.getDate() + (1 - 1 - sunday.getDay() + 6) % 7 + 1);//next sunday
				sunday.setHours(23);
				var monday = new Date(d[2],(d[1]-1),d[0]);
				monday.setDate(monday.getDate() - (monday.getDay() + 6) % 7);//prev monday
				$("[name='date_to']").val(val);
				$("[name='date_to']").datetimepicker('destroy');
				$("[name='date_to']").datetimepicker( { dateFormat: 'dd-mm-yy', minDate: new Date(d[2],(d[1]-1),d[0]), maxDate: sunday });
			}
		}
		<?php if($sessdata['group_id'] ==3){ ?>
		if($('#report_type').val() == 7){
            $("#devicediv").show();
            
        }else{
            $("#devicediv").hide();
        }
		<?php } ?>
        <?php if(isset($report_type) && ($report_type == 8 || $report_type == 11)){ ?>
		$("#date").datepicker({
             dateFormat: 'dd-mm-yy',
             <!--maxDate: new Date()-->
			 maxDate: '365'
         });
		 $("#date_to").datepicker({
             dateFormat: 'dd-mm-yy',
             <!--maxDate: new Date()-->
			 maxDate: '365'
         });
		<?php } else { ?>
		$("#date").datetimepicker({
             dateFormat: 'dd-mm-yy',
             <!--maxDate: new Date()-->
			 maxDate: '365'
         });
		 $("#date_to").datetimepicker({
             dateFormat: 'dd-mm-yy',
             <!--maxDate: new Date()-->
			 maxDate: '365'
         });
		<?php } ?>
		
		$("#res").on('click', function() {
			window.location.href = BASEURL + 'traxreport/beatcompletionreport';
		});
		<?php /*if(!empty($date_from)){ ?>
		gettodate('<?php echo $date_from;?>','<?php echo $date_to;?>');
		<?php }*/ ?>
		$( ".stdt" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
			maxDate: 0,
            <!--maxDate: '-1'-->
        });
		$( ".endt" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
			maxDate: 0,
            <!--maxDate: '-1'-->
        });
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
		$(document).on("change", "#report_type", function(e){
			$("[name='date_from']").val('');
			if($('#report_type').val() == 8 || $('#report_type').val() == 11){
				$("[name='date_from']").datetimepicker('destroy');
				$("[name='date_from']").datepicker({
					 dateFormat: 'dd-mm-yy',
					 maxDate: new Date()
				 });
				 $("[name='date_to']").datepicker({
					 dateFormat: 'dd-mm-yy',
					 maxDate: new Date()
				 });
                $("#devicediv").hide();
			}
            else if($('#report_type').val() == 7){
				<?php if($sessdata['group_id'] ==3){ ?>
                $("#devicediv").show();
				<?php } ?>
            }
			else{
				$("[name='date_from']").datepicker('destroy');
				$("[name='date_from']").datetimepicker({
					 dateFormat: 'dd-mm-yy',
					 maxDate: new Date()
				 });
				 $("[name='date_to']").datetimepicker({
					 dateFormat: 'dd-mm-yy',
					 maxDate: new Date()
				 });
                $("#devicediv").hide();
			}
			//$("[name='date_to']").datepicker("destroy");
			$("[name='date_to']").val('');
		});
        $(document).ready(function () {
			<?php if(!empty($alldata)){ 
			if($report_type == 8 || $report_type == 9 || $report_type == 10 || $report_type == 11){
			?>
			$('#lists').DataTable({
				searching: false,
				bSort: false,
				lengthChange: false
			});
			<?php
			} else {
			?>
			$('#lists').DataTable({
				searching: false,
				bSort: false,
				scrollX: true,
				scrollY: false,
				lengthChange: false,
				scrollCollapse: true
			});
			<?php } } ?>
        });
		
		function submitFRM(url){
			var prevURL = document.frmsearch.action;
			document.frmsearch.action = url;
			document.frmsearch.submit();
			document.frmsearch.action = prevURL;
		}
		
		function pdfSubmit(){
			submitFRM(BASEURL + 'traxreport/stoppagereportpdf');
		}
		
		function excelSubmit(){
			submitFRM(BASEURL + 'traxreport/beatcompletionreportexcel');
		}		
		
		$("#savereport").click(function(e){
			var report_type = $('#report_type').val();
			var date = $('#date').val();
			var date_to = $('#date_to').val();
			var pwi_id = $('#user').val();
			var pwi_name = $('#pwi_name').val();
			var dataLoad = {};
			var chk = 0;			
			if($('#date_from').val()==''){				
				alert('Please select Date From');
				chk++;
			}
			if($('#date_to').val()==''){				
				alert('Please select Date To');
				chk++;
			}
			
			if(chk == 0){				
				dataLoad.report_type = report_type;
				dataLoad.date_from = date_from;
				dataLoad.date_to = date_to;
				dataLoad.pwi_id = pwi_id;
				dataLoad.pwi_name = pwi_name;
				$.ajax({
					url: '<?php echo site_url('traxreport/savereport'); ?>',
					dataType:'json',
					type:"POST",
					data:dataLoad,
					success: function (resp) {
						alert(resp.msg);
					}
				});
			}
		});
		function change_device()
		{
			var typeofuser = document.getElmentById('typeofuser').value;
			$.ajax({
					url: '<?php echo site_url('traxreport/savereport'); ?>',
					dataType:'json',
					type:"POST",
					data:dataLoad,
					success: function (resp) {
						alert(resp.msg);
					}
				});
		}
		function populateSection(pwayid) {
			$.ajax({
				url: '<?= base_url('traxreport/getUser') ?>',
				data: {
					"user_id": pwayid,
					"pwi_id": '<?php echo isset($pwi_id) ? $pwi_id : '' ?>'
				},
				type: 'POST',
				success: function(data) {
					if(data=='		') {
						var data1 = '<option value="">Select An Option</option><option value="All">All</option>';
						$("#user").html(data1);
					} else if(data!='') {
						$("#user").empty();
						$("#user").html(data);
						$('#user').val('<?php echo isset($pwi_id) ?>');
					} 
					
				}
			});
		}
    </script>