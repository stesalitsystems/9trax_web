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
            <i class="fa fa-fw fa-bar-chart"></i> <?php echo $page_title; ?>
        </div>
        <div class="card-body">
            <div class="search-panel">
                <h4>Search</h4>
                <form action="<?= base_url('keyman-summary'); ?>" method="get" autocomplete="off" name="frmsearch" id="frmsearch">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="col-md-3">
                            <label for="fromDate">From Date</label>
                            <input type="date" class="form-control" id="fromDate" name="fromDate" 
                                   value="<?= isset($fromDate) ? date('Y-m-d', strtotime($fromDate)) : date('Y-m-d'); ?>" required>
                            
                        </div>
						<div class="col-md-3">
                            <label for="fromTime">From Time</label>
                            <input type="time" class="form-control mt-2" id="fromTime" name="fromTime" 
                                   value="<?= isset($fromTime) ? $fromTime : '00:00'; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="toDate">To Time</label>
                            <input type="time" class="form-control mt-2" id="toTime" name="toTime" 
                                   value="<?= isset($toTime) ? $toTime : '23:59'; ?>" required>
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
            <div class="table-responsive mt-4">
                <table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                           <th colspan="9">
                                    Keyman Summary Report From 
                                    <?= date("d-m-Y", strtotime($fromDate)) . ' ' . date('H:i', strtotime($fromTime)); ?> - <?= date('H:i', strtotime($toTime)); ?>
                                </th>
                                <th>
                                    <a style="width: fit-content; float:right;" href="<?= base_url('keyman-summary') . '?download=xlsx&fromDate=' . date('Y-m-d', strtotime($fromDate)) . '&toDate=' . date('Y-m-d', strtotime($toDate ?? $fromDate)) . '&fromTime=' . $fromTime . '&toTime=' . $toTime . '&usertype=' . ($usertype ?? 'all'); ?>" 
                                    class="btn btn-success btn-block" target="_blank">
                                        <i class="fa fa-download"></i> Download XLSX
                                    </a>
                                </th>
                                <th>
                                    <a style="width: fit-content;" href="<?= base_url('keyman-summary') . '?download=pdf&fromDate=' . date('Y-m-d', strtotime($fromDate)) . '&toDate=' . date('Y-m-d', strtotime($toDate ?? $fromDate)) . '&fromTime=' . $fromTime . '&toTime=' . $toTime . '&usertype=' . ($usertype ?? 'all'); ?>" 
                                    class="btn btn-success btn-block" target="_blank">
                                        <i class="fa fa-download"></i> Download PDF
                                    </a>
                                </th>
                        </tr>

                    </thead>
                    <thead>
                        <tr>
                            <th>PWAY</th>
                            <th>Section</th>
                            <th>Total Devices</th>
                            <th>Working Devices</th>
                            <th>Off GPS Count</th>
                            <th>Off GPS Details</th>
                            <th>Less than 2 Hour Working</th>
                            <th>2 to 4 Hour Working</th>
                            <th>Less than 4 KM Travelling</th>
                            <th>On Device After 5AM</th>
                            <th>Off Device Before 3PM</th>
                        </tr>
                    </thead>
                    <tbody class="reportlists-body">
                        <?php if (count($alldata) > 0): ?>
                            <?php foreach ($alldata as $irow): ?>
                                <tr>
                                    <td><?= !empty($irow->parent_organisation) ? $irow->parent_organisation : '--'; ?></td>
                                    <td><?= !empty($irow->organisation) ? $irow->organisation : 'N/A'; ?></td>
                                    <td><?= !empty($irow->total_devices) ? $irow->total_devices : '0'; ?></td>
                                    <td><?= !empty($irow->working_devices) ? $irow->working_devices : '0'; ?></td>
                                    <td><?= !empty($irow->off_gps_count) ? $irow->off_gps_count : '0'; ?></td>
                                    <td><?= !empty($irow->off_gps_details) ? $irow->off_gps_details : 'N/A'; ?></td>
                                    <td>
                                        <?php if (!empty($irow->less_than_2_hour_working)): ?>
                                            <ul>
                                                <?php foreach (explode(', ', $irow->less_than_2_hour_working) as $device): ?>
                                                    <li><?= $device; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($irow->two_to_four_hour_working)): ?>
                                            <ul>
                                                <?php foreach (explode(', ', $irow->two_to_four_hour_working) as $device): ?>
                                                    <li><?= $device; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($irow->less_than_4_km_travelling)): ?>
                                            <ul>
                                                <?php foreach (explode(', ', $irow->less_than_4_km_travelling) as $device): ?>
                                                    <li><?= $device; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($irow->on_device_after_5am)): ?>
                                            <ul>
                                                <?php foreach (explode(', ', $irow->on_device_after_5am) as $device): ?>
                                                    <li><?= $device; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($irow->off_device_before_3pm)): ?>
                                            <ul>
                                                <?php foreach (explode(', ', $irow->off_device_before_3pm) as $device): ?>
                                                    <li><?= $device; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center">No Records Found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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