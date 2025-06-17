<style>
.dataTables_scrollHeadInner { width:100% !important; }
table { width:100% !important; }
.dataTables_scroll .dataTables_scrollHead { display:none !important; }
.dataTables_scroll .dataTables_scrollBody thead tr { height:auto !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th { height:auto !important; padding:8px 12px !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th .dataTables_sizing { height:auto !important; overflow:visible !important; }
</style>
<style>
/* Pagination container styling */
.pagination-links {
    text-align: end;
    margin-top: 20px;
    margin-bottom: 20px;
}

/* Pagination list styling */
.pagination {
    display: inline-flex;
    list-style: none;
    padding: 0;
    margin: 0;
    border-radius: 4px;
}

/* Pagination links styling */
.pagination li a {
    display: block;
    padding: 8px 12px;
    margin: 0 2px;
    text-decoration: none;
    color: #007bff;
    border: 1px solid #ddd;
    border-radius: 4px;
    transition: background-color 0.3s, color 0.3s;
}

/* Hover effect for pagination links */
.pagination li a:hover {
    background-color: #007bff;
    color: #fff;
    border-color: #007bff;
}

/* Active pagination link styling */
.pagination li.active a {
    background-color: #007bff;
    color: #fff;
    border-color: #007bff;
    pointer-events: none;
}

/* Disabled pagination link styling */
.pagination li a[aria-disabled="true"] {
    color: #ccc;
    border-color: #ddd;
    pointer-events: none;
}

/* Next and Last buttons styling */
.pagination li a[aria-label="Next"],
.pagination li a[aria-label="Last"] {
    font-weight: bold;
}
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
           <form method="GET" action="<?= base_url('scheduled-patrolling-report'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Date</label> 
                    <input type="date" name="start_date" value="<?= ($stdt) ? date('Y-m-d', strtotime($stdt)) :  date('Y-m-d'); ?>" class="form-control">
                </div>

                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Search</button>
                </div>

                <div class="col-md-3">
                    <label>&nbsp;</label>
                    
                </div>
            </div>
        </form>
            </div>  
        </div>
    </div>    
<div class="table-responsive">
        <table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th colspan="13">
                        <div class="clearfix">
                            <div class="float-left">
                                Patrolling Report Of 
                                <?= isset($stdt) ? date("d-m-Y", strtotime($stdt)) : 'N/A'; ?>
                            </div>
                            
                        </div>
                    </th>
                    <th>
                        <a style="width: fit-content;" href="<?= base_url('download-patrolling-report-xlsx') . '?start_date=' . ($stdt ? date('Y-m-d', strtotime($stdt)) : date('Y-m-d')); ?>" class="btn btn-success btn-block" target="_blank">
                            <i class="fa fa-download"></i> Download XLSX
                        </a>
                    </th>
                    <th>
                        <a style="width: fit-content;" href="<?= base_url('download-patrolling-report-pdf') . '?start_date=' . ($stdt ? date('Y-m-d', strtotime($stdt)) : date('Y-m-d')); ?>" class="btn btn-success btn-block" target="_blank">
                            <i class="fa fa-download"></i> Download PDF
                        </a>
                    </th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th>SL No.</th>
                    <th>Device</th>
                    <th>Device Name</th>
                    <th>Trip No.</th>
                    <th>Actual Start Time</th>
                    <th>Expected Start Time</th>
                    <th>Actual Start Beat</th>
                    <th>Expected Start Beat</th>
                    <th>Actual End Time</th>
                    <th>Expected End Time</th>
                    <th>Actual End Beat</th>
                    <th>Expected End Beat</th>
                    <th>Total Distance Covered</th>
                    <th>Expected Distance Covered</th>
                    <th>Remarks</th>

                </tr>
            </thead>          
            <tbody class="reportlists-body">
                <?php if (!empty($tripSchedules) && count($tripSchedules) > 0) : ?>
                    <?php foreach ($tripSchedules as $key => $trip) : 

                        ?>
                        <tr>
                            <td><?= $key + 1; ?></td>
                            <td><?= !empty($trip['imeino']) ? esc($trip['imeino']) : 'N/A'; ?></td>
                            <td><?= !empty($trip['devicename']) ? esc($trip['devicename']) : 'N/A'; ?></td>
                            <td><?= !empty($trip['trip_no']) ? esc($trip['trip_no']) : 'N/A'; ?></td>
                            <td><?= !empty($trip['actual_start_datetime']) ? esc($trip['actual_start_datetime']) : 'N/A'; ?></td>
                            <td><?= !empty($trip['expected_start_datetime']) ? esc($trip['expected_start_datetime']) : 'N/A'; ?></td>
                            <td><?= !empty($trip['actual_stpole']) ? esc($trip['actual_stpole']) : 'N/A'; ?></td>
                            <td><?= !empty($trip['expected_stpole'])? esc($trip['expected_stpole']): 'N/A'; ?></td>
                            <td><?= !empty($trip['actual_end_datetime']) ? esc($trip['actual_end_datetime']) : 'N/A'; ?></td>
                            <td><?= !empty($trip['expected_end_datetime'])? esc($trip['expected_end_datetime']) : 'N/A'; ?></td>
                            <td><?= !empty($trip['actual_endpole']) ? esc($trip['actual_endpole']) : 'N/A'; ?></td>
                            <td><?= !empty($trip['expected_endpole'])? esc($trip['expected_endpole']): 'N/A'; ?></td>
                            <td><?= !empty($trip['totaldistancetravel'])? esc($trip['totaldistancetravel']): 'N/A'; ?></td>
                            <td><?= !empty($trip['expected_distance'])? esc($trip['expected_distance']): 'N/A'; ?></td>
                            <td><?= !empty($trip['trip_status']) ? esc($trip['trip_status']) : ''; ?></td>
                           
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="15">No Records Found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- 🔹 Pagination -->
        <div class="pagination-links">
            <?= $pager->links(); ?>
        </div>
</div>
</div>