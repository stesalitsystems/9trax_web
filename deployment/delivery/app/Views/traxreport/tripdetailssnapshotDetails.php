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
            <i class="fa fa-fw fa-bar-chart"></i> <?php echo $page_title .' - '. $type;?>
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
            </div>  
        </div>
					
				<div class="form-row">
					<form action="<?= base_url()?>traxreport/tripDetailsSummaryReportDetails" method="post" autocomplete="off" name="frmsearch" id="frmsearch">
					<?= csrf_field() ?>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<?php if(!empty($alldata)){ ?>
							<button type="button" class="btn btn-primary pull-right" name="exceldownload" id="exceldownload" style="margin-right: 0.5em;" onclick="return excelSubmit();">Download Excel</button>
							<input type="hidden" name="stdt" id="stdt" value="<?php echo $stdt1;?>" />
							<input type="hidden" name="endt" id="endt" value="<?php echo $endt1;?>" />
							<input type="hidden" name="usertype" id="usertype" value="<?php echo $usertype;?>" />
							<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>" />
							<input type="hidden" name="type" id="type" value="<?php echo $type;?>" />
							<?php } ?>
						</div>
					</form>
				</div> 
                <div class="table-responsive">
					<table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
						<thead>
							<tr>
							<th colspan="5"> Trip Summary Device Details Report - <?php echo $type;?> From <?php echo date("d-m-Y H:i", strtotime($stdt));?> To <?php echo date("d-m-Y H:i", strtotime($endt));?></th>
							</tr>
						</thead>
						<thead>
							<tr>
								<th>SL No.</th>
								<!-- <th>Start Time</th>
								<th>End Time</th> -->
								<th>Device No</th>
								<th>PWI</th>
								<th>Section Name</th>
								<th>Device Name</th>
							</tr>
						</thead>          
						<tbody class="reportlists-body">
							<?php
							if(count($alldata) > 0) {
								$i=1;
								foreach ($alldata as $irow) {
									echo '<tr>
										<td>' . $i . '</td>
										
										<td>' . $irow['device_no'] . '</td>
										<td>' . $irow['pwi'] . '</td>
										<td>' . $irow['section_name'] . '</td>
										<td>' . $irow['devicename'] . '</td>
									</tr>';
									/*
									<td>' . $irow['start_date'] . '</td>
										<td>' . $irow['end_date'] . '</td>
										*/
									$i++;
								} 
							} else { ?>
							<tr>
								<td colspan="18">No Records Found</td>
							</tr>
							<?php } ?>
						</tbody>
                    </table>
                </div>
            </div>
    </div>
	
    <script>
		
        $(document).ready(function () {
			<?php if(!empty($alldata)){ ?>
			$('#reportlists').DataTable({
				searching: false,
				bSort: false,
				scrollX: true,
				scrollY: false,
				lengthChange: false,
				scrollCollapse: true
			});
			<?php } ?>
        });
		
		function excelSubmit(){
			var stdt = $('#stdt').val();
			var endt = $('#endt').val();
			var usertype = $('#usertype').val();
			var user_id = $('#user_id').val();
			var type = $('#type').val();
			submitFRM(BASEURL + 'traxreport/tripDetailsSummaryReportDetailsExcel/'+stdt+'/'+endt+'/'+usertype+'/'+user_id+'/'+type);
		}
		
		function submitFRM(url){
			var prevURL = document.frmsearch.action;
			document.frmsearch.action = url;
			document.frmsearch.submit();
			document.frmsearch.action = prevURL;
		}
    </script>