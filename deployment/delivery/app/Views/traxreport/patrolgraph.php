<script src="<?php echo base_url() ?>assets/js/dygraph-combined.js"></script>
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
					<?php echo form_open("traxreport/patrolgraph",array("autocomplete"=>"off","name"=>"frmsearch","onsubmit"=>"return submit_form();")) ?>
					<div class="form-row">
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label>Device<span class="rqurd">*</span></label>
							<select class="select_mfc" id="device_id" name="device_id">
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
					<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label>Date<span class="rqurd">*</span></label>
							<input type="input" class="form-control stdt" id="stdt" name="stdt" value="<?php echo $stdt; ?>" placeholder="Date" required readonly>
					</div>
					<div class="col-xs-12 col-md-6 col-lg-2">
						<label>Start Time<span class="rqurd">*</span></label>
						<input type="input" class="form-control strt" id="strt" name="strt" value="<?php echo $strt; ?>" placeholder="starttime" readonly required>
					</div>
					<div class="col-xs-12 col-md-6 col-lg-2">
						<label>End Time<span class="rqurd">*</span></label>
						<input type="input" class="form-control endtime" id="endtime" name="endtime" value="<?php echo $endtime; ?>" placeholder="endtime" readonly required>
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
					if (!empty($getcoordinates)) { //echo "<pre>";print_r($getcoordinates);echo "</pre>";
					//echo $string;exit;?>
					<div id="graphdiv" style="width:100%; height:400px;"></div>
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
		var string = '<?php echo $string;?>';
		$( ".stdt" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            maxDate: '365'
        });
		$( ".stdt" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            maxDate: '365'
        });
		$('#strt, #endtime').datetimepicker({
        format: 'HH:mm',
		showDate:false,
		datepicker:false,
        pickDate: false,
        pickSeconds: false,
        pick12HourFormat: false,
		dateFormat: '',
		timeOnly: true           
    });
	var g = new Dygraph(document.getElementById("graphdiv"),"Date,Chainage\n"+string);
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