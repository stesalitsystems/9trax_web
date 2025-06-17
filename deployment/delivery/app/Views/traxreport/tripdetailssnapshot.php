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
                <form action="<?= base_url()?>traxreport/tripDetailsSummaryReport" method="post" autocomplete="off" name="frmsearch" id="frmsearch">
					<?= csrf_field() ?>
					<div class="form-row">
						<input type="hidden" name="report_type" id="report_type" value="7" />
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label for="exampleInputEmail1">Start Date<span class="text-danger">*</span></label>
                            <input type="input" class="form-control stdt" id="stdt" name="stdt" value="<?php if(isset($stdt)) echo $stdt; ?>" placeholder="Date" readonly>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label for="exampleInputEmail1">End Date<span class="text-danger">*</span></label>
                            <input type="input" class="form-control endt" id="endt" name="endt" value="<?php if(isset($endt)) echo $endt; ?>" placeholder="Date" readonly>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
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
                            <!-- <input type="hidden" name="pwi_name" id="pwi_name" value="<?php // if(isset($pwi_id)) echo $pwi_name;?>"/>
							<input type="hidden" name="section_id" id="section_id" value="<?php // if(isset($pwi_id)) echo $pwi_id;?>"/> -->
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
                    <?php //echo "<pre>";print_r($data);echo "</pre>";exit;
					if ($request->getMethod() == 'POST') { ?>
					<table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
						<thead>
							<tr>
							<th colspan="8"> Trip Details Report From <?php echo date("d-m-Y H:i", strtotime($stdt));?> To <?php echo date("d-m-Y H:i", strtotime($endt));?></th>
							</tr>
						</thead>
						<thead>
							<tr>
								<th>SL No.</th>
								<th>PWI</th>
								<th>Section Name</th>
								<th>Off Device</th>
								<th>Beat Covered</th>
								<th>Beat Not Covered</th>
								<th>Not Allocated</th>
								<th>Total Device</th>
							</tr>
						</thead>          
						<tbody class="reportlists-body">
							<?php 
							if(count($alldata) > 0) {
								$i=1;
								$device_off_count = 0;
								$beats_covered_count = 0;
								$beats_not_covered_count = 0;
								$not_allocated_count = 0;
								foreach ($alldata as $irow) {
									echo '<tr>
										<td>' . $i . '</td>
										<td>' . $irow->parent_organisation . '</td>
										<td>' . $irow->organisation . '</td>
										<td><a href="' . base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/' . $irow->user_id . '/offDevice') . '" target="_blank">' . $irow->device_off_count . '</a></td>
										<td><a href="' . base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/' . $irow->user_id . '/beatCovered') . '" target="_blank">' . $irow->beats_covered_count . '</a></td>
										<td><a href="' . base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/' . $irow->user_id . '/beatNotCovered') . '" target="_blank">' . $irow->beats_not_covered_count . '</a></td>
										<td><a href="' . base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/' . $irow->user_id . '/notallocated') . '" target="_blank">' . $irow->not_allocated_count . '</a></td>
										
										<td><a href="' . base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/' . $irow->user_id . '/alltypes') . '" target="_blank">' . ($irow->beats_not_covered_count+$irow->device_off_count+$irow->beats_covered_count+$irow->not_allocated_count ) . '</a></td>
									</tr>';
 
									$i++;
					$device_off_count = $irow->device_off_count + $device_off_count;
					$beats_covered_count = $irow->beats_covered_count + $beats_covered_count;
					$beats_not_covered_count = $irow->beats_not_covered_count + $beats_not_covered_count;
					$not_allocated_count = $irow->not_allocated_count + $not_allocated_count;
								} 
							?>
							 <tr>
								<td colspan="3" align="right">Total</td>
								
								<td><a href="<?php echo base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/All/offDevice') ?>" target="_blank"><?php echo $device_off_count; ?></a></td>
								
								<td><a href="<?php echo base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/All/beatCovered') ?>" target="_blank"><?php echo $beats_covered_count; ?></a></td>
								
								<td><a href="<?php echo base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/All/beatNotCovered') ?>" target="_blank"><?php echo $beats_not_covered_count; ?></a></td>
								
								<td><a href="<?php echo base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/All/notallocated') ?>" target="_blank"><?php echo $not_allocated_count; ?></a></td>

								<td><a href="<?php echo base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/All/alltypes') ?>" target="_blank"><?php echo ($not_allocated_count+$device_off_count+$beats_covered_count+$beats_not_covered_count); ?></a></td>
							</tr>
							<?php	
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
			if($('#usertype').val()==''){				
				alert('Please select User Type');
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
			window.location.href = BASEURL + 'traxreport/tripDetailsSummaryReport';
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
			submitFRM(BASEURL + 'traxreport/tripDetailsSummaryReportExcel');
		}
		
		function pdfSubmit(){
			submitFRM(BASEURL + 'traxreport/tripDetailsSummaryReportPdf');
		}

		$(document).on("change", "#user", function(e){
			$("#pwi_name").val($("#user :selected").text());
		});
		$("#pway_id").on('change', function() {
			populateSection($(this).val());
		});

		function populateSection(pwayid) {
			$.ajax({
				url: '<?= base_url('traxreport/getUser') ?>',
				data: {
					"user_id": pwayid,
					"pwi_id": '<?php echo isset($pwi_id) ? $pwi_id : '' ?>'
				},
				type: 'POST',
				success: function(data) {
					if(data =='		') {
						var data1 = '<option value="">Select An Option</option><option value="All">All</option>';
						$("#user").html(data1);
					} else if(data!='') {
						console.log('<?php echo isset($pwi_id) ?>');
						$("#user").empty();
						$("#user").html(data);
						$('#user').val('<?php echo isset($pwi_id) ? $pwi_id : '' ?>');
					} 
					
				}
			});
		}
    </script>