<style>
    .form-row {
        margin: 10px;
    }
    
    table tr th {
        width: auto!important;
    }
    
    .ms-options-wrap {
        width: 100%;
    }
</style>
<div class="container-fluid">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?php echo base_url() ?>">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">Role Management</li>
    </ol>
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-table"></i> Role Management
            <!--            <span class="pull-right"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?php echo base_url() ?>devices/add">Add Device</a></span>-->
        </div>
        <div class="col-xs-12">
            <div class="text-danger">
                <?php echo $this->session->flashdata('errmsg') ?>
            </div>
            <div class="text-success">
                <?php echo $this->session->flashdata('sucmsg') ?>
            </div>
            <div class="container">
                <?php echo form_open("roles/edit", array("novalidate" => true,"id"=>"editform")) ?>
               
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() { 
        $("#addform").validate();
        $(function() {
            $("[name='refunddate']").datepicker({
                dateFormat: 'dd-mm-yy',
            });
        });
    });
</script>