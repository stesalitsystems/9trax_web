
<div class="table-responsive">
    <table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Start Pole</th>
                <th>Start Lat</th>
                <th>Start Lon</th>
                <th>Start Time</th>
                <th>End Pole</th>
                <th>End Lat</th>
                <th>End Lon</th>
                <th>End Time</th>
                <th>Status</th>
            </tr>
        </thead>          
        <tbody class="reportlists-body">
            <?php if (!empty($tripDetails) && count($tripDetails) > 0) : ?>
                <?php foreach ($tripDetails as $detail): ?>
                <tr>
                    <td><?= esc($detail['expected_stpole']) ?></td>
                    <td><?= esc($detail['expected_stlat']) ?></td>
                    <td><?= esc($detail['expected_stlon']) ?></td>
                    <td><?= esc($detail['expected_start_datetime']) ?></td>
                    <td><?= esc($detail['expected_endpole']) ?></td>
                    <td><?= esc($detail['expected_endlat']) ?></td>
                    <td><?= esc($detail['expected_endlon']) ?></td>
                    <td><?= esc($detail['expected_end_datetime']) ?></td>
                    <td><?= esc($detail['trip_status']) ?></td>
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
    <!-- <div class="pagination-links">
        <? // $pager->links(); ?>
    </div> -->
    <div class="form-row">      
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <a href="<?php echo base_url()?>/trip-schedule" class="btn btn-danger">Back</a>
        </div>                
    </div>
</div>