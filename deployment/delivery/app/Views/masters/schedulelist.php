    <div class="card mb-3">
        <div class="card-header">
            <span class="card-header-title"><i class="fa fa-mobile"></i> <?php echo $page_title; ?></span>
        </div>
        <div class="card-body">
            <div class="search-panel">
                <?php
                    $notification['msg'] = session()->getFlashdata('msg');
                    if(!empty($notification['msg'])){ ?>
                    <?= view('listpagenotification',$notification); ?>
                    <?php
                    }                
                ?>
                <form method="get" action="<?= base_url('/masters/schedulelist') ?>">
                    <div class="form-row">
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label for="imei_no">IMEI Number:</label>
                            <input type="text" name="imei_no" id="imei_no" class="form-control" value="<?= esc($imei_no) ?>">
                        </div>
                    </div>

                    <div class="form-row mb-0">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
                            <button type="reset" class="btn btn-danger" onclick="window.location.href='<?= base_url('/masters/schedulelist') ?>'" >Reset</button>
                            <button type="submit" class="btn btn-primary pull-right" name="search" id="search">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div style="height:30px;"></div>

    <div class="table-responsive">
        <table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>IMEI Number</th>
                    <th>User Type</th>
                    <th>Start Pole</th>
                    <th>Start Lat</th>
                    <th>Start Lon</th>
                    <th>Start Time</th>
                    <th>End Pole</th>
                    <th>End Lat</th>
                    <th>End Lon</th>
                    <th>End Time</th>
                    <th>Distance Travelled</th>
                    <th>Trip</th>
                    <th>Device Name</th>
                    <!-- <th>PWI ID</th>
                    <th>Section ID</th> -->
                    <th>Actions</th>
                </tr>
            </thead>          
            <tbody class="reportlists-body">
                <?php if (!empty($schedules) && count($schedules) > 0) : ?>
                    <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><?= esc($schedule->id) ?></td>
                        <td><?= esc($schedule->imeino) ?></td>
                        <td><?= esc($schedule->usertype) ?></td>
                        <td><?= esc($schedule->stpole) ?></td>
                        <td><?= esc($schedule->stpolelat) ?></td>
                        <td><?= esc($schedule->stpolelon) ?></td>
                        <td><?= esc($schedule->sttime) ?></td>
                        <td><?= esc($schedule->endpole) ?></td>
                        <td><?= esc($schedule->endpolelat) ?></td>
                        <td><?= esc($schedule->endpolelon) ?></td>
                        <td><?= esc($schedule->endtime) ?></td>
                        <td><?= esc($schedule->distance_travelled) ?></td>
                        <td><?= esc($schedule->trip) ?></td>
                        <td><?= esc($schedule->devicename) ?></td>
                        <td>
                            <a href="<?= base_url('/masters/deleteschedule/' . $schedule->id) ?>" onclick="return confirm('Are you sure you want to delete this schedule?');" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="15">No Records Found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- Pagination -->
        <div class="pagination-links">
        <?= $pager->makeLinks($page, $perPage, $total); ?>
        </div>
    </div>
