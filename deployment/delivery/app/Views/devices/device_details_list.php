<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> -->
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
            <form action="<?= base_url('device-details')?>" method="get" autocomplete="off" name="frmsearch" id="frmsearch">
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
                        <label>Device<span class="text-danger">*</span></label>
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
                </div>
                <div class="form-row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
                        <button type="button" class="btn btn-primary pull-right" name="search" id="search">Search</button>
                        
                        <?php if (!empty($getcoordinates)) { ?>
                            <a href="<?= base_url('device-details') . '?download=xlsx&stdt=' . $stdt . '&endt=' . $endt . '&device_id=' . $device_id; ?>" 
                               class="btn btn-success pull-right mr-2" target="_blank">
                                <i class="fa fa-download"></i> Download XLSX
                            </a>
                            <a href="<?= base_url('device-details') . '?download=pdf&stdt=' . $stdt . '&endt=' . $endt . '&device_id=' . $device_id; ?>" 
                               class="btn btn-success pull-right mr-2" target="_blank">
                                <i class="fa fa-download"></i> Download PDF
                            </a>
                        <?php } ?>
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
                <th>Date</th>
                <th>Time</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Speed</th>
                <th>Location</th>
            </tr>
        </thead>          
        <tbody class="reportlists-body">
            <?php if (!empty($getcoordinates) && count($getcoordinates) > 0) : ?>
                <?php foreach ($getcoordinates as $detail): ?>
                <tr>
                    <td><?= esc($detail->currentdate) ?></td>
                    <td><?= esc($detail->currenttime) ?></td>
                    <td><?= esc($detail->latitude) ?></td>
                    <td><?= esc($detail->longitude) ?></td>
                    <td><?= esc($detail->trakerspeed) ?></td>
                    <td><?= esc($detail->poleno) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="9">No Records Found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <!-- 🔹 Pagination -->
    <div class="pagination-links">
    <?php if (!empty($pager)): ?>
        <div class="pagination-links">
            <?= $pager ?>
        </div>
    <?php endif; ?>
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
				alert('Please select a Device');
				chk++;
			}
			if(chk == 0){
				e.preventDefault();
				form = $('#frmsearch');
				form.submit();
			}
		});
		

		$( ".stdt" ).datetimepicker({
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

            $('#res').on('click', function() {
                window.location.href = "<?= base_url('device-details'); ?>";
            });
        });
		
    
</script>

<!-- <script>
    $(document).ready(function () {
        // Initialize DataTable with export buttons
        $('#reportlists').DataTable({
            dom: 'Bfrtip',
            paging: false, // Disable DataTables pagination to use PHP pagination
            searching: false, // Disable search if not needed
            ordering: false, // Disable column sorting if not needed
            buttons: [
                'copy', 
                'excel', 
                'pdf', 
                'print'
            ]
        });
    });
</script> -->