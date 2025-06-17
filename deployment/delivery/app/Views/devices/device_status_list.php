<style>.pagination-links{text-align:end;margin:20px 0}.pagination{display:inline-flex;list-style:none;padding:0;margin:0;border-radius:4px}.pagination li a{display:block;padding:8px 12px;margin:0 2px;text-decoration:none;color:#007bff;border:1px solid #ddd;border-radius:4px;transition:background-color .3s,color .3s}.pagination li a:hover{background-color:#007bff;color:#fff;border-color:#007bff}.pagination li.active a{background-color:#007bff;color:#fff;border-color:#007bff;pointer-events:none}.pagination li a[aria-disabled=true]{color:#ccc;border-color:#ddd;pointer-events:none}.pagination li a[aria-label=Next],.pagination li a[aria-label=Last]{font-weight:700}</style>
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
        </div> 
        
        <div class="table-responsive">
            <table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="6"> <?php echo $page_title;?> </th>
                        <th>
                            <a style="width: fit-content; float:right;" href="<?= base_url('device-status-list') . '?download=xlsx&type=keyman'; ?>" 
                                class="btn btn-success pull-right mr-2" target="_blank">
                                    <i class="fa fa-download"></i> Download XLSX
                                </a>
                        </th>
                        <th>
                           <a style="width: fit-content; float:left;" href="<?= base_url('device-status-list') . '?download=pdf&type=keyman'; ?>" 
                            class="btn btn-success pull-right mr-2" target="_blank">
                                <i class="fa fa-download"></i> Download PDF
                            </a>
                        </th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>Device</th>
                        <th>Last Update</th>
                        <th>Mobile Number</th>
                        <th>Speed (km/h)</th>
                        <th>Battery (%)</th>
                        <th>View Live</th>
                        <th>View History</th>
                        <th>Address</th>
                    </tr>
                </thead>          
                <tbody class="reportlists-body">
                    <?php if (!empty($keyman) && count($keyman) > 0) : ?>
                        <?php foreach ($keyman as $key => $detail): $nextKKey = !empty($keyman[$key + 1]->devicename) ? $key + 1 : $key; ?>
                        <tr>
                            <td><?= esc($detail->devicename); ?></td>
                            <td><?= esc($detail->currentdate).' '.esc($detail->currenttime); ?></td>
                            <td><?= esc($detail->mobilenumber); ?></td>
                            <td><?= esc($detail->speed); ?></td>
                            <td><?= esc($detail->batterystats); ?></td>
                            <th><a href="<?php echo base_url('index.php/controlcentre/view').'?d='.$detail->deviceid; ?>" target="_blank"> View Live </a></th>
                            <td><a href="<?php echo base_url('controlcentre/view').'/'.$detail->deviceid.'/'.$detail->currentdate.'/'.$detail->currenttime.'/'.$keyman[$nextKKey]->currenttime; ?>"> View History</a></td>
                            <td><?php echo !empty($detail->poleno)? $detail->poleno : '--' ; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8">No Records Found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- 🔹 Pagination -->
            <div class="pagination-links">
                <?= $keymanLinks ?>
            </div>
        </div>
    </div>
</div>


<div class="card mb-3">
    <div class="card-header">
        <i class="fa fa-fw fa-bar-chart"></i> <?php echo $page_titl2;?>
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
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="6"> <?php echo $page_titl2;?> </th>
                        <th>
                            <a style="width: fit-content; float:right;" style="width: fit-content; float:right;" href="<?= base_url('device-status-list') . '?download=xlsx&type=patrolman'; ?>" 
                            class="btn btn-success pull-right mr-2" target="_blank">
                                <i class="fa fa-download"></i> Download XLSX
                            </a>
                        </th>
                        <th>
                           <a style="width: fit-content; float:left;" style="width: fit-content;" href="<?= base_url('device-status-list') . '?download=pdf&type=patrolman'; ?>" 
                            class="btn btn-success pull-right mr-2" target="_blank">
                                <i class="fa fa-download"></i> Download PDF
                            </a>
                        </th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>Device</th>
                        <th>Last Update</th>
                        <th>Mobile Number</th>
                        <th>Speed (km/h)</th>
                        <th>Battery (%)</th>
                        <th>View Live</th>
                        <th>View History</th>
                        <th>Address</th>
                    </tr>
                </thead>          
                <tbody class="reportlists-body">
                    <?php if (!empty($petrolman) && count($petrolman) > 0) : ?>
                        <?php foreach ($petrolman as $key => $detail):  $nextPKey = !empty($petrolman[$key + 1]->devicename) ? $key + 1 : $key; ?>
                        <tr>
                            <td><?= esc($detail->devicename); ?></td>
                            <td><?= esc($detail->currentdate).' '.esc($detail->currenttime); ?></td>
                            <td><?= esc($detail->mobilenumber); ?></td>
                            <td><?= esc($detail->speed); ?></td>
                            <td><?= esc($detail->batterystats); ?></td>
                            <th><a href="<?php echo base_url('index.php/controlcentre/view').'?d='.$detail->deviceid; ?>" target="_blank"> View Live </a></th>
                            <td><a href="<?php echo base_url('controlcentre/view').'/'.$detail->deviceid.'/'.$detail->currentdate.'/'.$detail->currenttime.'/'.$petrolman[$nextPKey]->currenttime; ?>"> View History</a></td>
                            <td><?php echo !empty($detail->poleno)? $detail->poleno : '--' ; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8">No Records Found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
           
            <div class="pagination-links">
                <?= $patrolmanLinks ?>
            </div>
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
</script>