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
			$notification['msg'] = session()->getFlashdata('msg');
			if (!empty($notification['msg'])) { ?>
				<?= view('listpagenotification', $notification); ?>
			<?php }
			?>     
                
                <h4>Search</h4>              
                <form action="<?= base_url('traxreport/devallotreport')?>" method="post" autocomplete="off" name="frmsearch" id="frmsearch">
					<?= csrf_field() ?>
                    <div class="form-row">
						<input type="hidden" name="report_type" id="report_type" value="7" />                                               
						<?php if($sessdata['group_id'] == 3){ ?>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="devicediv">
                            <label>Device</label>
                            <select class="form-control selectpicker select_mfc" name="device_id" id="device_id" >
                                <option value="">Select</option>
                               <?php
                                if (isset($devicedropdown) && !empty($devicedropdown)) {
                                    foreach ($devicedropdown as $row) {
                                        ?>
                                        <option value="<?php echo $row->did ?>" <?php if(isset($device_id) && $device_id==$row->did) echo "selected"; ?>><?php echo $row->serial_no.' - '.$row->device_name; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
						<?php } ?>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="datefrmdiv">
                            <label for="exampleInputEmail1">From<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="" name="date_from" id="date_from" value="<?php echo $date_from; ?>" required readonly >
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="datetodiv">
                            <label for="exampleInputEmail1">To<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="" name="date_to" id="date_to" value="<?php echo $date_to; ?>" required readonly>
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
                                <option value="All" <?php if(isset($pwi_id) && $pwi_id == 'All'){ ?>selected<?php } ?>>All</option>
                                <?php foreach ($usersdd as $key=>$row){ ?>
                                  <option value="<?php echo $row->user_id?>" <?php if(isset($pwi_id) && $pwi_id == $row->user_id){ ?>selected<?php } ?>><?php echo $row->organisation; ?></option>
                                <?php } } ?>
                            </select>
                        </div>
						<input type="hidden" name="pwi_name" id="pwi_name" value="<?php echo isset($pwi_name) ? $pwi_name : old('pwi_name');?>"/>
						<input type="hidden" name="section_id" id="section_id" value="<?php echo isset($pwi_id) ? $pwi_id : old('pwi_id');?>"/>
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
                    <?php if(!empty($date_from)){ ?>
					<table class="table table-bordered" id="lists" width="100%" cellspacing="0">
                        <?php if($report_type == 7){ ?>
							<thead>
							<tr>
							<th>Date</th>
							<th>DeviceName</th>
							<th>Device SerialNo</th>
							<th>SSE/PWAY</th>
							<th>Section</th>
							<th>BIT</th>
							<th>UserType</th>
							</tr>
							</thead>
							<tbody class="reportlists-body">
							<?php 
							// print_r($alldata);exit;
							if(is_array($alldata) && count($alldata) > 0) {
							foreach($alldata as $irow){
							if($irow->deviceid != '') {
							$mdddevicename_arr = explode("/",$irow->mdddevicename);
							if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
								$type = 'Stock';
							}
							else if (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
								$type = 'Keyman';
							}
							else if (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
								$type = 'Patrolman';
							}
							$devicename = $irow->mdddevicename;
							$sectionArr = explode("(",$devicename);	
							if(count($sectionArr) > 1) {
															
								$str = $sectionArr[1];
								$bit = str_replace(')','',$str);
								$sectionArr1 = explode("/",$devicename);
								$usertype = $sectionArr1[0];
							} else {
								$usertype = 'NA';
							}
							
							if($irow->acting_trip != ''){
							?>
								<tr>
								<td><?php echo date("d-m-Y", strtotime($irow->result_date));?></td>
								<td><?php echo $irow->mdddevicename;?></td>
								<td><?php echo $irow->mddserialno;?></td>
								<td><?php echo $irow->pwy ?? '';?></td>
								<td><?php echo $irow->section ?? '';?></td>
								<td><?php echo $irow->bit ?? '';?></td>
								<td><?php echo $usertype ?? '';?></td>
								</tr>
						<?php } else { ?>
								<tr>
								<td>NA</td>
								<td><?php echo $irow->mdddevicename;?></td>
								<td><?php echo $irow->mddserialno;?></td>
								<td><?php echo $irow->pwy;?></td>
								<td><?php echo $irow->section;?></td>
								<td><?php echo $irow->bit;?></td>
								<td><?php echo $usertype;?></td>
								</tr>
						<?php } } } } else { ?>
							<tr>
								<td colspan="7">No Devices Assigned</td>
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
			if($('#pway_id').val()==''){				
				alert('Please select Pway');
				chk++;
			}
			if($('#user').val()==''){				
				alert('Please select Section');
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
		$("#date_from").datepicker({
             dateFormat: 'dd-mm-yy',
             maxDate: new Date()
         });
		 $("#date_to").datepicker({
             dateFormat: 'dd-mm-yy',
             maxDate: new Date()
         });
		<?php } else { ?>
		$("#date_from").datetimepicker({
             dateFormat: 'dd-mm-yy',
             maxDate: new Date()
         });
		 $("#date_to").datetimepicker({
             dateFormat: 'dd-mm-yy',
             maxDate: new Date()
         });
		<?php } ?>
		
		$("#res").on('click', function() {
			window.location.href = BASEURL + 'traxreport/devallotreport';
		});
		<?php /*if(!empty($date_from)){ ?>
		gettodate('<?php echo $date_from;?>','<?php echo $date_to;?>');
		<?php }*/ ?>
		$(document).on("change", "#user", function(e){
			$("#pwi_name").val($("#user :selected").text());
		});
		$("#pway_id").on('change', function() {
				populateSection($(this).val());
			});
			<?php if($_POST) {
			?>
			// populateSection('<?php // echo $pway_id; ?>');
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
			submitFRM(BASEURL + 'traxreport/devallotreportpdf');
		}
		
		function excelSubmit(){
			submitFRM(BASEURL + 'traxreport/devallotreportexcel');
		}		
		
		$("#savereport").click(function(e){
			var report_type = $('#report_type').val();
			var date_from = $('#date_from').val();
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
			if($('#user').val()){				
			}
			else{				
				alert('Please select section');
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
		
		function populateSection(pwayid) {
			$.ajax({
				url: '<?php echo site_url('traxreport/getUser') ?>',
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