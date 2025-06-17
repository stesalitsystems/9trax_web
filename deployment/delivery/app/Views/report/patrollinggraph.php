<style>
.dataTables_scrollHeadInner { width:100% !important; }
table { width:100% !important; }
.dataTables_scroll .dataTables_scrollHead { display:none !important; }
.dataTables_scroll .dataTables_scrollBody thead tr { height:auto !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th { height:auto !important; padding:8px 12px !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th .dataTables_sizing { height:auto !important; overflow:visible !important; }

* {
	margin: 0;
	padding: 0;
}

#chart-container {
	position: relative;
	height: 100vh;
	overflow: hidden;
}
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
                <form action="<?= base_url()?>report/patrollingGraph" method="post" autocomplete="off" name="frmsearch" id="frmsearch">
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
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label>Device<span class="rqurd text-danger">*</span></label>
							<select class="select_mfc" id="device_id" name="device_id">
							   <option value="">Select</option>
							   <?php
								if (isset($devicedropdown) && !empty($devicedropdown)) {
									foreach ($devicedropdown as $row) {
										?>
										<option value="<?php echo $row->did ?>" <?php if(!empty($device_id) && ($device_id==$row->did)) echo "selected"; ?>><?php echo $row->serial_no.' - '.$row->device_name; ?></option>
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
                        </div>
                    </div>                    
                </form>
            </div>  
        </div>
            
                <div class="table-responsive">
					
                    <?php if ($request->getMethod() == 'POST') { ?>
							<?php 
							if(count($alldata) > 0) {
								
								$transformed = array_map(function($item) {
									return [
										'currentdate' => date('d-m-Y', strtotime($item->currentdate)),
										'currenttime' => $item->currenttime,
										'poleno' => $item->poleno
									];
								}, $alldata);

								// Convert to JSON
								$json = json_encode($transformed, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

								// echo $json
								
								// echo "<pre>";print_r($alldata);exit();
							?>
							<div id="chart-container"></div>
							<script src=https://echarts.apache.org/en/js/vendors/echarts/dist/echarts.min.js></script>
								<script>
									var dom = document.getElementById('chart-container');
									var myChart = echarts.init(dom, null, {
										renderer: 'canvas',
										useDirtyRect: false
									});

									const rawData = <?php echo $json ?>;

									console.log(rawData);

									// 4. transform into [Date, Number] pairs
									const seriesData = rawData.map(({currentdate, currenttime, poleno}) => {
										// parse DD-MM-YYYY
										const [d, m, y] = currentdate.split('-').map(Number);
										const [hh, mm, ss] = currenttime.split(':').map(Number);
										const date = new Date(y, m - 1, d, hh, mm, ss);
										// extract numeric part of "TP-xxx.xx"
										const value = parseFloat(poleno.split('-')[1]);
										return [date, value];
									});

									// 5. init and set options
									const chart = echarts.init(document.getElementById('chart-container'));
									const option = {
										title: { text: 'Patrolling Graph' },
										tooltip: {
											trigger: 'axis',
											formatter: params => {
												const { data } = params[0];
												return echarts.format.formatTime('yyyy-MM-dd hh:mm:ss', data[0])
													+ '  ' + data[1].toFixed(2);
											}
										},

										xAxis: {
											type: 'time',
											name: 'Timestamp',
											axisLabel: { formatter: v => echarts.format.formatTime('dd-MM-yy hh:mm', v) }
										},
										yAxis: {
											type: 'value',
											name: 'poleno',
											axisLabel: { formatter: v => v.toFixed(2) }
										},
										series: [{
											name: 'poleno',
											type: 'line',
											showSymbol: false,
											data: seriesData
										}],
										grid: { left: '10%', right: '10%', bottom: '15%' }
									};

									chart.setOption(option);

									// 6. make it responsive
									window.addEventListener('resize', () => chart.resize());
								</script>
								

							<?php } else { ?>
								No Records Found
							<?php } ?>
					<?php } else { ?>
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
			if($('#device_id').val()==''){				
				alert('Please select Device');
				chk++;
			}
			if(chk == 0){
				e.preventDefault();
				form = $('#frmsearch');
				form.submit();
			}
		});
		
		$("#res").on('click', function() {
			window.location.href = BASEURL + 'report/geofencegroup';
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
				timeFormat: "HH:mm"
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
			submitFRM(BASEURL + 'report/geofencegroupExcel');
		}		

		$(document).on("change", "#user", function(e){
			$("#pwi_name").val($("#user :selected").text());
		});

    </script>