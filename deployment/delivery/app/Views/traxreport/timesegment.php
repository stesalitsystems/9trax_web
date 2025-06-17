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
					<?php echo form_open("traxreport/timesegment",array("autocomplete"=>"off","name"=>"frmsearch","onsubmit"=>"return submit_form();")) ?>
					<div class="form-row">
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="typeofuserdiv">
                            <label>Type Of User<span class="rqurd">*</span></label>
                            <select class="form-control selectpicker select_mfc" name="typeofuser" id="typeofuser" required>
                              	<option value="Keyman" <?php if($typeofuser == "Keyman"){ ?>selected<?php } ?>>Keyman</option>
								<option value="Patrolman" <?php if($typeofuser == "Patrolman"){ ?>selected<?php } ?>>Patrolman</option>
                            </select>
                     </div>
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label>Date<span class="rqurd">*</span></label>
							<input type="input" class="form-control stdt" id="stdt" name="stdt" value="<?php echo $stdt; ?>" placeholder="Date" required readonly>
					</div>
					</div>
					<div class="form-row mb-0">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
							<button type="submit" class="btn btn-primary pull-right" name="search" id="search">Search</button>
							<?php if(!empty($report_data) && count($report_data) > 0){ ?>
							<!--<button type="button" class="btn btn-primary pull-right" name="pdfdownload" id="pdfdownload" style="margin-right: 0.5em;" onclick="return pdfSubmit();">Download PDF</button>
							<button type="button" class="btn btn-primary pull-right" name="print" id="print" style="margin-right: 0.5em;">Print</button>-->
							<button type="button" class="btn btn-primary pull-right" name="exceldownload" id="exceldownload" style="margin-right: 0.5em;" onclick="return excelSubmit();">Download Excel</button>
							<?php } ?>
						</div>
					</div>	
		  			
					</div>
         			</div>   
                <div class="table-responsive">
                    <?php 
					if (!empty($report_data)) { //echo "<pre>";print_r($report_data);echo "</pre>";exit;?>
					<table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
						<thead>
							<tr>
							<th colspan="13">Devices List</th>
							</tr>
						</thead>
						<thead>
							<tr>
							<th>Serial No</th>
							<th>Date</th>
							<th>DeviceNo</th>
							<th>PWI</th>
							<th>Section Name</th>
							<th>Devicename</th>
							<th>Start Date</th>
							<th>Start Time</th>
							<th>End Date</th>
							<th>End Time</th>
							<th>Distance KM</th>
							<th>Avg. Speed</th>
							<th>Max Speed</th>
							</tr>
						</thead>          
						<tbody class="reportlists-body">
							<?php 
							if(count($report_data) > 0) { //echo "<pre>";print_r($report_data);echo "</pre>";exit;
							for($i=0;$i<count($report_data);$i++)
							{
							if($report_data[$i]['device_name'] != '') {
							?>
							<tr>
								<td><?php echo ($i+1);?></td>
								<td><?php echo $stdt;?></td>
								<td><?php echo $report_data[$i]['serial_no'];?></td>
								<td><?php echo $report_data[$i]['pwy'];?></td>
								<td><?php echo $report_data[$i]['section'];?></td>
								<td><?php echo $report_data[$i]['device_name'];?></td>
								<?php $time_segment_details = $report_data[$i]['time_segment_details'];
									  $counter = count($time_segment_details);
									  if($counter > 0) {
									  $distance = 0;
									  	for($z=0;$z<count($time_segment_details);$z++) {
											$distance = $distance + $time_segment_details[$z]['distance_cover'];
											$starttime = $time_segment_details[$z]['starttime'];
											$starttimeArr = explode(" ",$starttime);
											$startdate = $starttimeArr[0];
											$starttime = $starttimeArr[1];
											$endtime = $time_segment_details[$z]['endtime'];
											$endtimeArr = explode(" ",$endtime);
											$enddate = $endtimeArr[0];
											$endtime = $endtimeArr[1];
											if($z == 0) {
								?>
									<td><?php echo $startdate;?></td>
									<td><?php echo $starttime;?></td>
									<td><?php echo $enddate;?></td>
									<td><?php echo $endtime;?></td>
									<td><?php echo number_format($time_segment_details[$z]['distance_cover'],3);?></td>
									<td><?php echo $time_segment_details[$z]['avg_speed'];?></td>
									<td><?php echo $time_segment_details[$z]['max_speed'];?></td>
								<?php } else { ?>
								<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td><?php echo $startdate;?></td>
								<td><?php echo $starttime;?></td>
								<td><?php echo $enddate;?></td>
								<td><?php echo $endtime;?></td>
								<td><?php echo number_format($time_segment_details[$z]['distance_cover'],3);?></td>
								<td><?php echo $time_segment_details[$z]['avg_speed'];?></td>
								<td><?php echo $time_segment_details[$z]['max_speed'];?></td>
								</tr>
								<?php } ?>
								<?php } ?>
								<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>Total</td>
								<td><?php echo number_format($distance,3);?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								</tr>
								<?php } else { ?>
								<td>0</td>
								<td>0</td>
								<td>0</td>
								<td>0</td>
								<td>0</td>
								<td>0</td>
								<td>0</td>
								</tr>
							<?php } ?>
							<?php } } ?>
							<?php } else { ?>
							<tr>
								<td colspan="13">No Records Found</td>
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
				alert('Please select Date');
				chk++;
			}
			if($('#typeofuser').val()==''){				
				alert('Please select Type Of User');
				chk++;
			}
			/*if($('#user').val()){				
			}
			else{				
				alert('Please select PWI');
				chk++;
			}*/
			//alert(chk);
			if(chk == 0){
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
		<?php if($sessdata->group_id ==3){ ?>
		if($('#report_type').val() == 7){
            $("#devicediv").show();
            
        }else{
            $("#devicediv").hide();
        }
		<?php } ?>
        <?php if($report_type == 8 || $report_type == 11){ ?>
		$("#date").datepicker({
             dateFormat: 'dd-mm-yy',
             maxDate: new Date()
         });
		 $("#date_to").datepicker({
             dateFormat: 'dd-mm-yy',
             maxDate: new Date()
         });
		<?php } else { ?>
		$( ".stdt" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
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
            maxDate: '-1'
        });
		$( ".endt" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            maxDate: '-1'
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
				<?php if($sessdata->group_id ==3){ ?>
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
			submitFRM(BASEURL + 'traxreport/timesegmentexcel');
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
						$('#user').val('<?php echo $pwi_id ?>');
					} 
					
				}
			});
		}
    </script>