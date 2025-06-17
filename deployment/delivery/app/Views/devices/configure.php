<style>
    .form-row{
        margin: 10px;
    }    
    form small{
        color:#990000;
    }
    .sendtodevice{
        margin-left: 5%;
    }
</style>
<div class="container-fluid">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="javascript:void(0)">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?php echo base_url() ?>users/lists">Device Management</a>
        </li>
        <li class="breadcrumb-item active">Configure Device</li>
    </ol>
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <?php if (isset($errmsg) && !empty($errmsg)) { ?>
                <div class="error"><?php echo $errmsg ?></div>
            <?php } ?>
        </div>
        <div class="col-xs-12 col-md-12">

            <?php echo form_open("devices/devconfig/".$dev_id, array("autocomplete" => "off", "id" => "devicesadd", "novalidate" => "true")) ?>
            <div class="form-group">
                         <label for="exampleInputEmail1">Serial No</label>
                         <input type="text" class="form-control col-xs-12 col-sm-12 col-md-6" placeholder="Serial No" name="serial_no"  maxlength="30" value="<?php echo $get_device->serial_no; ?>" readonly>
                    </div>
            <?php if ($sessdata->usergroupid == 1) { ?>
                               
                    
            <div class="form-group">
                        <label for="exampleInputEmail1">Configure Device ID</label>
                        <input type="text" class="form-control col-xs-12 col-sm-12 col-md-6" placeholder="Configure Device ID" name="configdevserialno" value="<?php echo isset($get_device_settings->configdevserialno)?$get_device_settings->configdevserialno:""; ?>">
                         <small><?php echo isset($get_configs['configdevserialno'])?$get_configs['configdevserialno']->protocol:''?></small><span class="sendtodevice"><input type="checkbox" name="configdevserialno_send">Send to Device</span>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Configure GPRS</label>
                        <input type="text" class="form-control col-xs-12 col-sm-12 col-md-6" placeholder="Configure GPRS" name="configgprs"  maxlength="150" value="<?php echo isset($get_device_settings->configgprs)?$get_device_settings->configgprs:""; ?>" >
                        <small><?php echo isset($get_configs['configgprs'])?$get_configs['configgprs']->protocol:''?></small><span class="sendtodevice"><input type="checkbox" name="configgprs_send">Send to Device</span>
                    </div>
                     

                    <div class="form-group">
                <label for="exampleInputEmail1">Configure Data Transfer Target </label>
                <input type="text" class="form-control col-xs-12 col-sm-12 col-md-6" placeholder="Configure Data Transfer Target" name="configlocation" value="<?php echo isset($get_device_settings->configlocation)?$get_device_settings->configlocation:""; ?>">
                <small><?php echo isset($get_configs['configlocation']) ? $get_configs['configlocation']->protocol : '' ?></small><span class="sendtodevice"><input type="checkbox" name="configlocation_send">Send to Device</span>
            </div> 
               <div class="form-group">
                <label for="exampleInputEmail1">Configure Data Sending Interval (in sec) </label>
                <input type="text" class="form-control col-xs-12 col-sm-12 col-md-6" placeholder="Configure Data Sending Interval" name="configinterval" value="<?php echo isset($get_device_settings->configinterval)?$get_device_settings->configinterval:""; ?>">
                <small><?php echo isset($get_configs['configinterval']) ? $get_configs['configinterval']->protocol : '' ?></small><span class="sendtodevice"><input type="checkbox" name="configinterval_send">Send to Device</span>
            </div> 
            <?php } ?>
           
           <div class="form-group">
                <label for="exampleInputEmail1">Configure SOS Numbers</label>
                <input type="text" class="form-control col-xs-12 col-sm-12 col-md-6" placeholder="Configure SOS Numbers" name="configsos" value="<?php echo isset($get_device_settings->configsos)?$get_device_settings->configsos:""; ?>">
                <small><?php echo isset($get_configs['configsos']) ? $get_configs['configsos']->protocol : '' ?></small><span class="sendtodevice"><input type="checkbox" name="configsos_send">Send to Device</span>
            </div> 
            <div class="form-group">
                <label for="exampleInputEmail1">Configure Alarm</label>
                <input type="text" class="form-control col-xs-12 col-sm-12 col-md-6" placeholder="Configure Alarm" name="configalarm" value="<?php echo isset($get_device_settings->configalarm)?$get_device_settings->configalarm:""; ?>">
                <small><?php echo isset($get_configs['configalarm']) ? $get_configs['configalarm']->protocol : '' ?></small><span class="sendtodevice"><input type="checkbox" name="configalarm_send">Send to Device</span>
            </div> 
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" name="add">Submit</button>
                    <a href="<?php echo base_url() ?>devices/lists" class="btn btn-danger">Back</a>
                </div>
           
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
       
    });
</script>