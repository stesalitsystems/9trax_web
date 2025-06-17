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
                <form action="<?= base_url()?>report/stoppage" method="post" autocomplete="off" name="frmsearch" id="frmsearch">
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
								<!-- <option value="All" 
								    <?php 
								    //    if($usertype=="All") {
									// 	echo "selected"; 
									//    }
									      
									 ?>>All</option> -->
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
                            <button type="button" class="btn btn-primary pull-right" name="pdfdownload" id="pdfdownload" style="margin-right: 0.5em;" onclick="return pdfSubmit();">Download PDF</button>
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
						<th colspan="9"> Stoppage Report From <?php echo date("d-m-Y H:i", strtotime($stdt));?> To <?php echo date("d-m-Y H:i", strtotime($endt));?></th>
						</tr>
					</thead>
					<thead>
						<tr>
							<th>SL No.</th>
							<th>Device No</th>
							<th>Device Name</th>
							<th>Stoppage Start Time</th>
							<th>Stoppage Address</th>
							<th>Halt Time</th>
						</tr>
					</thead>          
					<tbody class="reportlists-body">
						<?php 
						if(count($alldata) > 0) {
							$i=1; 
							$imeino = '';
							$groupOpen = false;
							foreach ($alldata as $irow) {
								if($irow->stoppage_duration != '00:00:00') {
									$stoppage_duration = strtotime($irow->stoppage_duration);
									$seconds = (int) $stoppage_duration; // Ensure it's an integer

									$days = floor($seconds / 86400); 
									$seconds %= 86400; 

									$hours = floor($seconds / 3600);
									$seconds %= 3600;

									$minutes = floor($seconds / 60);
									$seconds %= 60;

									$formatted = "{$hours} hour {$minutes} mins {$seconds} sec";
									
									// Group by device
									if ($irow->imeino != $imeino) {
										if ($groupOpen) {
											echo '</tbody>'; // Close previous group
										}
										echo '<tbody style="border: 2px solid #007bff;">'; // Start new group
										$groupOpen = true;
						
										echo '<tr>
												<td>' . $i . '</td>
												<td>' . $irow->imeino . '</td>
												<td>' . $irow->device_name . '</td>
												<td>' . date("Y-m-d H:i:s", strtotime($irow->stoppage_start)) . '</td>
												<td>' . $irow->pole . '</td>
												<td>' . $formatted . '</td>
											  </tr>';
										$i++;
										$imeino = $irow->imeino;
									} else {
										echo '<tr>
												<td></td>
												<td>' . $irow->imeino . '</td>
												<td>' . $irow->device_name . '</td>
												<td>' . date("Y-m-d H:i:s", strtotime($irow->stoppage_start)) . '</td>
												<td>' . $irow->pole . '</td>
												<td>' . $formatted . '</td>
											  </tr>';
									}
								}
								
							}

							if ($groupOpen) {
								echo '</tbody>'; // Close last group
							}
						} else { ?>
						<tr>
							<td colspan="9">No Records Found</td>
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
			if(chk == 0){
				e.preventDefault();
				var form = $('#frmsearch')[0];
				form.target = '_self';
				form.submit();
			}
		});
		
		$("#res").on('click', function() {
			window.location.href = BASEURL + 'report/stoppage';
		});
		
		/*$( ".stdt" ).datetimepicker({
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
        });*/

		$(function () {
			$(".stdt").datetimepicker({
				dateFormat: "dd-mm-yy",
				maxDate: 0,
				timeFormat: "HH:mm",
				onClose: function (selectedDateTime) {
					if (!selectedDateTime) return;

					const parts = selectedDateTime.split(" ");
					const dateParts = parts[0].split("-");
					const timeParts = parts[1].split(":");

					const selectedDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0], timeParts[0], timeParts[1]);

					const nextDay = new Date(selectedDate);
					nextDay.setDate(selectedDate.getDate() + 1);

					$(".endt").datetimepicker("option", {
						minDate: selectedDate,
						maxDate: nextDay,
						beforeShowDay: function (date) {
							return [
								date.toDateString() === selectedDate.toDateString() ||
								date.toDateString() === nextDay.toDateString(),
								""
							];
						}
					}).val(""); // Clear previous value
				}
			});

			$(".endt").datetimepicker({
				dateFormat: "dd-mm-yy",
				timeFormat: "HH:mm",
				maxDate: 0
			});

			$(".stdt, .endt").attr("readonly", true);
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
			submitFRM(BASEURL + 'report/stoppageExcel');
		}		

		function pdfSubmit() {
			var prevURL = document.frmsearch.action;
			document.frmsearch.target = '_blank';
			document.frmsearch.action = BASEURL + 'report/stoppagePdf';
			document.frmsearch.submit();
			document.frmsearch.action = prevURL;
		}

		$(document).on("change", "#user", function(e){
			$("#pwi_name").val($("#user :selected").text());
		});
    </script>