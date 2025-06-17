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
                <form action="<?= base_url('index.php/exception-report-device')?>" method="get" autocomplete="off" name="frmsearch" id="frmsearch">
					<?= csrf_field() ?>
					<div class="form-row">
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label for="exampleInputEmail1">Start Date<span class="text-danger">*</span></label>
                            <input type="input" class="form-control stdt" id="stdt" name="stdt" value="<?php if(isset($stdt)) echo $stdt; ?>" placeholder="Date" readonly>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label for="exampleInputEmail1">End Date<span class="text-danger">*</span></label>
                            <input type="input" class="form-control endt" id="endt" name="endt" value="<?php if(isset($endt)) echo $endt; ?>" placeholder="Date" readonly>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label for="exampleInputEmail1">Category Selection<span class="text-danger">*</span></label>
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
                            <label for="exampleInputEmail1">Minimum Distance<span class="text-danger">*</span></label>
                            <select name="distance_range" id="distance_range" class="form-control" required>
                                <option value="">Select</option>
                                <?php
                                    // Generate options dynamically
                                    for ($i = 1; $i <= 100; $i++) { // Adjust the range as needed
                                        $selected = (isset($distance_range) && $distance_range == $i) ? 'selected' : '';
                                        echo "<option value=\"$i\" $selected>$i km</option>";
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
                   
					<table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
						<thead>
							<tr>
							<th colspan="8"> Trip Exception Report From <?php echo date("d-m-Y H:i", strtotime($stdt));?> To <?php echo date("d-m-Y H:i", strtotime($endt));?></th>
							</tr>
						</thead>
						<thead>
							<tr>
								<th>Section</th>
								<th>Total Device</th>
								<th>Petrolling Done</th>
								<th>Petrolling Not Done</th>
								<th>Off Device</th>
								<th>Start Low Battery</th>
                                <th>Over Speed</th>
                                <th>Defaulter</th>
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
                                $defaulter_count = 0;
                                $over_speed_count = 0;
                                $low_battery_count = 0;
								foreach ($alldata as $irow) {
									echo '<tr>

										<td>' . $irow->organisation . '</td>
                                        <td>' . ($irow->beats_not_covered_count+$irow->device_off_count+$irow->beats_covered_count+$irow->not_allocated_count ) . '</td>
                                        <td><a href="' . base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/' . $irow->user_id . '/beatCovered') . '" target="_blank">' . $irow->beats_covered_count . '</a></td>
    									<td><a href="' . base_url('traxreport/tripDetailsSummaryReportDetails/' . $stdt1 . '/' . $endt1 . '/' . $usertype . '/' . $irow->user_id . '/beatNotCovered') . '" target="_blank">' . $irow->beats_not_covered_count . '</a></td>
    									<td>' . $irow->device_off_count . '</td>
										<td>' . $irow->low_battery_count . '</td>
										
                                        <td>' . $irow->over_speed_count . '</td>
                                        <td>' . $irow->defaulter_count .'</td>
									</tr>';
 
									$i++;
					$device_off_count = $irow->device_off_count + $device_off_count;
					$beats_covered_count = $irow->beats_covered_count + $beats_covered_count;
					$beats_not_covered_count = $irow->beats_not_covered_count + $beats_not_covered_count;
					$not_allocated_count = $irow->not_allocated_count + $not_allocated_count;
                    $defaulter_count = $irow->defaulter_count + $defaulter_count; 
                    $over_speed_count = $irow->over_speed_count + $over_speed_count;
                    $low_battery_count = $irow->low_battery_count + $low_battery_count;
								} 
							?>
							 <tr>
								<td align="right">Total</td>
                                <td><?php echo ($not_allocated_count+$device_off_count+$beats_covered_count+$beats_not_covered_count); ?></td>
                                <td><?php echo $beats_covered_count; ?></td>
								<td><?php echo $beats_not_covered_count; ?></td>
								<td><?php echo $device_off_count; ?></td>
								<td><?php echo $low_battery_count; ?></td>
                                <td><?php echo $over_speed_count; ?></td>
                                <td><?php echo $defaulter_count; ?></td>
							</tr>
							<?php	
							} else { ?>
							<tr>
								<td colspan="8">No Records Found</td>
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
            alert('Please select Date From');
            chk++;
        }
        if($('#endt').val()==''){				
            alert('Please select Date To');
            chk++;
        }
        if(chk == 0){
            e.preventDefault();
            form = $('#frmsearch');
            form.submit();
        }
    });
    
    // $( ".stdt" ).datetimepicker({
    //     changeMonth: true,
    //     changeYear: true,
    //     dateFormat: 'dd-mm-yy',
    //     maxDate: 0
    //     <!--maxDate: '-1'-->
    // });
    // $( ".endt" ).datetimepicker({
    //     changeMonth: true,
    //     changeYear: true,
    //     dateFormat: 'dd-mm-yy',
    //     maxDate: 0
    //     <!--maxDate: '-1'-->
    // });
    

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

</script>

<script>
    $(document).ready(function () {
        const startDateInput = $('#stdt');
        const endDateInput = $('#endt');
        const distanceRangeSelect = $('#distance_range');

        // Function to parse dd-mm-yyyy HH:mm to a valid Date object
        function parseDate(input) {
            const [datePart, timePart] = input.split(' '); // Split into date and time
            const [day, month, year] = datePart.split('-'); // Split date into day, month, year
            const [hours, minutes] = timePart ? timePart.split(':') : [0, 0]; // Split time into hours and minutes
            return new Date(year, month - 1, day, hours, minutes); // Create a new Date object
        }

        // Function to calculate and populate the distance range
        function populateDistanceRange() {
            const startDateValue = startDateInput.val(); // dd-mm-yyyy HH:mm format
            const endDateValue = endDateInput.val(); // dd-mm-yyyy HH:mm format
            const selectedDistance = <?= isset($distance_range) ? $distance_range : 'null'; ?>;

            console.log('Raw Input Dates:', startDateValue, endDateValue); // Debugging: Check the input values

            // Parse dates in dd-mm-yyyy HH:mm format
            const startDate = startDateValue ? parseDate(startDateValue) : null;
            const endDate = endDateValue ? parseDate(endDateValue) : null;

            console.log('Parsed Dates:', startDate, endDate); // Debugging: Check the parsed dates

            // Clear existing options
            distanceRangeSelect.empty();
            distanceRangeSelect.append('<option value="">--Select--</option>');

            if (startDate && endDate && !isNaN(startDate) && !isNaN(endDate)) {
                // Calculate the difference in days
                const timeDiff = endDate.getTime() - startDate.getTime();
                const dayDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1; // Include the start date

                console.log('Day Difference:', dayDiff); // Debugging: Check the day difference

                // Generate options based on the date range
                const maxDistance = dayDiff * 10; // 10km per day
                for (let i = 1; i <= maxDistance; i ++) {
                    const isSelected = selectedDistance == i ? 'selected' : '';
                    const option = `<option value="${i}" ${isSelected}>${i} km</option>`;
                    distanceRangeSelect.append(option);
                }
            }
        }

        // Event listeners for date inputs
        startDateInput.on('change', populateDistanceRange);
        endDateInput.on('change', populateDistanceRange);

        // Populate the select box on page load if dates are already set
        if (startDateInput.val() && endDateInput.val()) {
            populateDistanceRange();
        }

        $('#res').on('click', function() {
            window.location.href = BASEURL + 'exception-report-device';
        });
    });
</script>