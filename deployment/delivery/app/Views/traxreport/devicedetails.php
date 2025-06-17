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
		  			<div class="form-row">
					<?php echo form_open("traxreport/beatcompletionreport",array("autocomplete"=>"off","name"=>"frmsearch","id"=>"frmsearch")) ?>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<input type="hidden" name="date" id="date" value="<?php echo $date;?>" />
							<input type="hidden" name="type" id="type" value="<?php echo $type;?>" />
							<input type="hidden" name="section_id" id="section_id" value="<?php echo $section_id;?>" />
							<input type="hidden" name="typeofuser" id="typeofuser" value="<?php echo $typeofuser;?>" />
                            <?php if(!empty($newresult)){ ?>
                            <button type="button" class="btn btn-primary pull-right" name="exceldownload" id="exceldownload" style="margin-right: 0.5em;" onclick="return excelSubmit();">Download Excel</button>
                            <?php } ?>
                        </div>
					</form>
                    </div>
					</div>
         			</div>   
                <div class="table-responsive">
                    <?php //echo "<pre>";print_r($data);echo "</pre>";exit;
					if (!empty($newresult)) { ?>
					<table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
						<thead>
							<tr>
							<th colspan="11"><?php echo $type;?> Devices List</th>
							</tr>
						</thead>
						<thead>
							<tr>
							<th>Device No</th>
							<th>Date</th>
							<th>PWI</th>
							<th>Section Name</th>
							<th>Devicename</th>
							</tr>
						</thead>          
						<tbody class="reportlists-body">
							<?php 
							//echo "<pre>";print_r($newresult);echo "</pre>";//exit;
							if(count($newresult) > 0) {
							for($i=0;$i<count($newresult);$i++)
							{
								echo '<tr>
								<td>'.$newresult[$i]->serial_no.'</td>
								<td>'.$newresult[$i]->date.'</td>
								<td>'.$newresult[$i]->pwy.'</td>
								<td>'.$newresult[$i]->section.'</td>								
								<td>'.$newresult[$i]->mdddevicename.'</td>
								</tr>';
							} 
							} else { ?>
							<tr>
								<td colspan="11">No Records Found</td>
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
             	maxDate: new Date()
         	});
		 	$("#date_to").datepicker({
             	dateFormat: 'dd-mm-yy',
             	maxDate: new Date()
         	});
		<?php } else { ?>
		$("#date").datetimepicker({
             dateFormat: 'dd-mm-yy',
             maxDate: new Date()
         });
		 $("#date_to").datetimepicker({
             dateFormat: 'dd-mm-yy',
             maxDate: new Date()
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
			var date = $('#date').val();
			var type = $('#type').val();
			var section_id = $('#section_id').val();
			var typeofuser = $('#typeofuser').val();
			if(section_id != '')
			{
				submitFRM(BASEURL + 'traxreport/devicedetailsexcel/3/'+date+'/'+type+'/'+section_id+'/'+typeofuser);
			}
			else
			{
				submitFRM(BASEURL + 'traxreport/totaldevicedetailsexcel/3/'+date+'/'+type+'/'+section_id+'/'+typeofuser);
			}
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