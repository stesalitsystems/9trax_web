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
                <form action="<?= base_url('scheduled-patrolling-summery'); ?>" method="get" autocomplete="off" name="frmsearch" id="frmsearch">
					<?= csrf_field() ?>
					<div class="form-row">
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label for="exampleInputEmail1">Date<span class="text-danger">*</span></label>
                            <input type="date" class="form-control stdt" id="stdt" name="stdt" 
                                   value="<?= isset($stdt) ? date('Y-m-d', strtotime($stdt)) : date('Y-m-d'); ?>" 
                                   placeholder="Date" required>
                        </div>

                    </div>
                    <div class="form-row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
                            <button type="button" class="btn btn-primary pull-right" name="search" id="search">Search</button>
                        </div>
                    </div>                    
                </form>
            </div>  
        </div>
            
                <div class="table-responsive">
                    
					<table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
						<thead>
							<tr>
							<th colspan="8"> Scheduled Patrolling on <?php echo date("d-m-Y H:i", strtotime($stdt));?> </th>
							<th>
								<a style="width: fit-content;" href="<?= base_url('scheduled-patrolling-summery') . '?download=xlsx&start_date=' . ($stdt ? date('Y-m-d', strtotime($stdt)) : date('Y-m-d')); ?>" class="btn btn-success btn-block" target="_blank">
									<i class="fa fa-download"></i> Download XLSX
								</a>
							</th>
							<th>
								<a style="width: fit-content;" href="<?= base_url('scheduled-patrolling-summery') . '?download=pdf&start_date=' . ($stdt ? date('Y-m-d', strtotime($stdt)) : date('Y-m-d')); ?>" class="btn btn-success btn-block" target="_blank">
									<i class="fa fa-download"></i> Download PDF
								</a>
							</th>
							</tr>
						</thead>
						<thead>
							<tr>
								<th>PWay</th>
								<th>Section</th>
                                <th>Total Device</th>
								<th>Inactive Devices</th>
                                <th>Active Devices</th>
								<th>Patrolling Completed in all respect</th>
								<th>Incompleted Beat</th>
								<th>Not Allocated</th>
                                <th>Delay</th>
                                <th>Overspeed</th>
								
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
								$over_speed_count = 0;
								$delayed_start_count = 0;
								foreach ($alldata as $irow) {
									echo '<tr>
										<td>' . $irow->parent_organisation . '</td>
										<td>' . $irow->organisation . '</td>
                                        <td>' . ($irow->beats_not_covered_count+$irow->device_off_count+$irow->beats_covered_count+$irow->not_allocated_count ) . '</td>
										<td>' . $irow->device_off_count . '</td>
                                        <td>' . ($irow->beats_not_covered_count+$irow->beats_covered_count ) . '</td>
										<td>' . $irow->beats_covered_count . '</td>
										<td>' . $irow->beats_not_covered_count . '</td>
										<td>' . $irow->not_allocated_count . '</td>
										<td>' . $irow->delayed_start_count . '</td>
										<td>' . $irow->over_speed_count . '</td>
									</tr>';
 
									$i++;
                                    $device_off_count = $irow->device_off_count + $device_off_count;
                                    $beats_covered_count = $irow->beats_covered_count + $beats_covered_count;
                                    $beats_not_covered_count = $irow->beats_not_covered_count + $beats_not_covered_count;
                                    $not_allocated_count = $irow->not_allocated_count + $not_allocated_count;
									$over_speed_count = $irow->over_speed_count + $over_speed_count;
									$delayed_start_count = $irow->delayed_start_count + $delayed_start_count;
								} 
							?>
							 <tr>
								<td colspan="2" align="right">Total Summery</td>
								
								<td><?php echo ($not_allocated_count+$device_off_count+$beats_covered_count+$beats_not_covered_count); ?></td>

								

								<td><?php echo $device_off_count; ?></td>
								<td><?php echo ($beats_covered_count+$beats_not_covered_count); ?></td>
								
								<td><?php echo $beats_covered_count; ?></td>
								
								<td><?php echo $beats_not_covered_count; ?></td>
								
								<td></td>
								<td><?php echo $delayed_start_count; ?></td>
								<td><?php echo $over_speed_count; ?></td>

							</tr>
							<?php	
							} else { ?>
							<tr>
								<td colspan="10">No Records Found</td>
							</tr>
							<?php } ?>
						</tbody>
                    </table>

                </div>
            </div>
    </div>
	
    <script>
        $("#search").click(function(e){			
			var chk = 0;			
			if($('#stdt').val()==''){				
				alert('Please select Date');
				chk++;
			}
			
			if(chk == 0){
				e.preventDefault();
				form = $('#frmsearch');
				form.submit();
			}
		});
		
		$("#res").on('click', function() {
			window.location.href = BASEURL + 'scheduled-patrolling-summery';
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
		
    </script>