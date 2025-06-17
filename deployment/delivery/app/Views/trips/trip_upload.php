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
            <!-- <span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn"  href="<?php // echo base_url();?>downloads/FORMAT EXCEPTION REPORT Details of GPS Tracker 2023-24 in ALL PWAY MERGED.csv" title="Download Sample CSV" dat_ifrheight="270">Download Sample CSV</a></span> -->
            <span class="pull-right" style="margin-right: 0.5em;">
                <a 
                    class="btn btn-primary btn-sm moduleaddbtn"
                    href="<?= base_url(); ?>downloads/FORMAT EXCEPTION REPORT Details of GPS Tracker 2023-24 in ALL PWAY MERGED.csv"
                    title="Download Sample CSV"
                    dat_ifrheight="270"
                    download
                >
                    Download Sample CSV
                </a>
            </span>

        </div>
        <div class="card-body">
            <div class="search-panel">
                    <h2>Upload Trip Schedule (CSV)</h2>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= print_r($error) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= esc($success) ?></div>
                    <?php endif; ?>

                    <form action="<?= site_url('trip-schedule/upload') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="form-row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>Start Date:</label>
                                <input type="input" class="form-control stdt" name="start_date" class="form-control" required>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>End Date:</label>
                                <input type="input" class="form-control endt" name="end_date" class="form-control" required>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>Select CSV File:</label>
                                <input type="file" name="csv_file" class="form-control" required>
                            </div>
                            <div class="form-row" style="margin-left: 20px;margin-top: 26px;">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <!-- <button type="reset" class="btn btn-danger" name="res" id="res">Reset</button> -->
                                    <button type="submit" class="btn btn-primary pull-right" name="search" id="search">Upload</button>
                                </div>
                            </div> 
                        </div>
                        
                    </form>

                    <!-- <br>
                    <a href="<? //= site_url('trip-schedule') ?>" class="btn btn-secondary">Back to Trip List</a> -->
                    
            </div>  
        </div>
    </div>    
</div>

<script>
    /*$(".stdt").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        minDate: 1 // This means tomorrow onwards
    });

    $(".endt").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        minDate: 1 // This means tomorrow onwards
    });*/

    $(function () {
        $(".stdt").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            minDate: 1, // Tomorrow onwards
            onSelect: function (selectedDate) {
                const parts = selectedDate.split("-");
                const selected = new Date(parts[2], parts[1] - 1, parts[0]);

                const maxDate = new Date(selected);
                maxDate.setDate(selected.getDate() + 7);

                $(".endt").datepicker("destroy").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'dd-mm-yy',
                    minDate: selected,
                    maxDate: maxDate
                }).val(""); // Clear previous endt date
            }
        });

        $(".endt").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            minDate: 1 // fallback in case stdt not selected
        });

        $(".stdt, .endt").attr("readonly", true);
    });


</script>