<style>
.dataTables_scrollHeadInner { width:100% !important; }
table { width:100% !important; }
.dataTables_scroll .dataTables_scrollHead { display:none !important; }
.dataTables_scroll .dataTables_scrollBody thead tr { height:auto !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th { height:auto !important; padding:8px 12px !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th .dataTables_sizing { height:auto !important; overflow:visible !important; }
</style>
<div class="container-fluid">
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success'); ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
<?php endif; ?>

    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-fw fa-bar-chart"></i> <?php echo $page_title;?>
        </div>
        <div class="card-body">
            <div class="search-panel">
            <form method="post" action="<?= base_url('trip-schedule'); ?>" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label>Device ID</label>
                <input type="text" name="deviceid" value="<?= esc($device_id ?? '') ?>" class="form-control" >
            </div>
            <div class="col-md-3">
                <label>Start Date</label>
                <input type="input" name="start_date" value="<?= esc($stdt ?? '') ?>" class="form-control stdt" readonly>
            </div>
            <div class="col-md-3">
                <label>End Date</label>
                <input type="input" name="end_date" value="<?= esc($endt ?? '') ?>" class="form-control endt" readonly>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">Filter</button>
            </div>
        </div>
    </form>
            </div>  
        </div>
    </div>    
    <div class="table-responsive">
    <div class="clearfix">
        <div class="float-left">
            Trip Details Report From 
            <?= isset($stdt) ? date("d-m-Y", strtotime($stdt)) : 'N/A'; ?>
            To 
            <?= isset($endt) ? date("d-m-Y", strtotime($endt)) : 'N/A'; ?>
        </div>
        <div class="float-right">
            <a href="<?= site_url('trip-schedule/upload') ?>" class="btn btn-warning text-white">Upload</a>
        </div>
    </div>
        <table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
            <!-- <thead>
                <tr>
                    <th colspan="19">
                        <div class="clearfix">
                        <div class="float-left">
                            Trip Details Report From 
                            <?= isset($stdt) ? date("d-m-Y", strtotime($stdt)) : 'N/A'; ?>
                            To 
                            <?= isset($endt) ? date("d-m-Y", strtotime($endt)) : 'N/A'; ?>
                        </div>
                        <div class="float-right">
                            <a href="<?= site_url('trip-schedule/upload') ?>" class="btn btn-warning text-white">Upload</a>
                        </div>
                        </div>
                    </th>
                </tr>
            </thead> -->
            <thead>
                <tr>
                <th>SL No.</th>
                    <th>Device ID</th>
                    <th>Device Name</th>
                    <th>User Type</th>
                    <th>Pway</th>  
                    <th>Section</th> 
                    <th> Start Date</th>  
                    <th> Start Time</th>
                    <th> End Date</th>
                    <th> End Time</th>   
                    <!-- <th>Start Pole</th>      
                                 
                    <th>Expected Start Point</th> 
                    <th>End Pole</th>
                                    
                    <th>Expected End Point</th>
                    <th>Distance Travelled</th>
                    <th>Trip Count</th> -->
                    <th>Show History</th>
                    <th>Delete</th>
                </tr>
            </thead>          
            <tbody class="reportlists-body">
                <?php if (!empty($tripSchedules) && count($tripSchedules) > 0) : ?>
                    <?php foreach ($tripSchedules as $key => $trip) : 
                      // echo "<pre>";
                     //  print_r($trip);
                     //  die();
                        ?>
                        <tr>
                            <td><?= $key + 1; ?></td>
                            <td><?= !empty($trip->imeino) ? esc($trip->imeino) : 'N/A'; ?></td>
                            <td><?= !empty($trip->devicename) ? esc($trip->devicename) : 'N/A'; ?></td>
                            <td><?= !empty($trip->device_type) ? esc($trip->device_type) : 'N/A'; ?></td>
                            <td><?= !empty($trip->pwi_name) ? esc($trip->pwi_name) : 'N/A'; ?></td>
                            <td><?= !empty($trip->section_name) ? esc($trip->section_name) : 'N/A'; ?></td>
 
                            <td><?= !empty($trip->expected_start_date) ? esc($trip->expected_start_date) : 'N/A'; ?></td>
                            <td><?= !empty($trip->expected_start_time)? esc($trip->expected_start_time): 'N/A'; ?></td>
                           
                            <td><?= !empty($trip->expected_end_date) ? esc($trip->expected_end_date) : 'N/A'; ?></td>
                            <td><?= !empty($trip->expected_end_time)? esc($trip->expected_end_time) : 'N/A'; ?></td>
                           
                            <td><a href="<?= site_url('trip-schedule/details/' . $trip->schedule_id); ?>">View Details</a></td>
                            <td>
                                <a href="<?= site_url('trip-schedule/delete/' . $trip->schedule_id); ?>" 
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this schedule?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="18">No Records Found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- 🔹 Pagination -->
        <?php if (!empty($pager)): ?>
            <div class="pagination">
                <?= $pager ?>
            </div>
        <?php endif; ?>

</div>
</div>
<script>
    $( ".stdt" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        minDate: 0
        <!--maxDate: '-1'-->
    });
    $( ".endt" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        minDate: 0
        <!--maxDate: '-1'-->
    });

    $('#reportlists').DataTable({
        searching: false,
        bSort: false,
        scrollX: true,
        scrollY: false,
        lengthChange: false,
        scrollCollapse: true
    });
</script>