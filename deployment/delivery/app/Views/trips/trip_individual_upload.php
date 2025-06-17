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

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= print_r($error) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= esc($success) ?></div>
                    <?php endif; ?>

                    <form action="<?= site_url('trip/individual-upload') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="newAdd">
                        <div class="form-row add_1">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>Start Date:</label>
                                <input type="input" class="form-control stdt" name="start_date[]" class="form-control" required>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>End Date:</label>
                                <input type="input" class="form-control endt" name="end_date[]" class="form-control" required>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="margin-top: 24px;">
                                <button type="button" class="btn btn-primary" name="res" id="res" onClick="addRow()">+</button>
                                <button type="reset" class="btn btn-danger" name="res" id="res">-</button>
                            </div>
                        </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>Start Pole</label>
                                <input type="input" name="stpole" class="form-control" required>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>End Pole</label>
                                <input type="input" name="enpole" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>Travelled Distance</label>
                                <input type="input" name="travdist" class="form-control" required>
                            </div>
                            <div class="form-row" style="margin-left: 20px;margin-top: 26px;">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
                                    <button type="submit" class="btn btn-primary pull-right" name="search" id="search">Upload</button>
                                </div>
                            </div> 
                        </div>
                        
                    </form>
                    
            </div>  
        </div>
    </div>    
</div>

<script>
    var addcount = 0 ;

    function addRow(){
        var row ;
        addcount = addcount + 1;

        row = `<div class="form-row add_`+addcount+`">
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <label>Start Date:</label>
                <input type="input" class="form-control stdt" name="start_date[]" class="form-control" required>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <label>End Date:</label>
                <input type="input" class="form-control endt" name="end_date[]" class="form-control" required>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="margin-top: 24px;">
                <button type="button" class="btn btn-primary" name="res" id="res" onClick="addRow()">+</button>
                <button type="reset" class="btn btn-danger" name="res" id="res">-</button>
            </div>
        </div>`;
        $(".newAdd").append(row);

        $(".stdt").datetimepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            minDate: 1 // This means tomorrow onwards
        });

        $(".endt").datetimepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            minDate: 1 // This means tomorrow onwards
        });
    }

    $(".stdt").datetimepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        minDate: 1 // This means tomorrow onwards
    });

    $(".endt").datetimepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        minDate: 1 // This means tomorrow onwards
    });

</script>