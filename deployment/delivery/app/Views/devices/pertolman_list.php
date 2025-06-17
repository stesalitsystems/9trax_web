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
            
            <!-- <h4>Search</h4>              
            <form action="<?= base_url('device-status-list')?>" method="get" autocomplete="off" name="frmsearch" id="frmsearch">
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
                    </div>
                </div>                    
            </form> -->
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
            <?php if (!empty($petrolman) && count($petrolman) > 0) : ?>
                <?php foreach ($petrolman as $detail): ?>
                <tr>
                    <td><?= esc($detail['currentdate']) ?></td>
                    <td><?= esc($detail['currenttime']) ?></td>
                    <td><?= esc($detail['latitude']) ?></td>
                    <td><?= esc($detail['longitude']) ?></td>
                    <td><?= esc($detail['trakerspeed']) ?></td>
                    <td><?= esc($detail['misc']) ?></td>
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
    <?php if(!empty($pager)){ $pager->links(); } ?>
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
        });
		
		// function submitFRM(url){
		// 	var prevURL = document.frmsearch.action;
		// 	document.frmsearch.action = url;
		// 	document.frmsearch.submit();
		// 	document.frmsearch.action = prevURL;
		// }
    
</script>