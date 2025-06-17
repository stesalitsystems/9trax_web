<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-mobile"></i> Device Management
            <?php if($sessdata['group_id'] == 1){?>
				<span class="pull-right"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?= base_url('devices/devicesAdmin/add') ?>">Add Device</a></span>
				<span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?= base_url('devices/uploadDeviceCsv') ?>">Upload Bulk CSV</a></span>
				<span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?= base_url().'assets/csv/add_device_list.csv' ?>" download>Download Bulk CSV Format</a></span>
                <?php if($sessdata['group_id'] == 8){ ?>
				<span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?= base_url('devices/assigndevicetousercsv') ?>">Assign Device</a></span>
				<span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?= base_url().'assets/csv/assign_device.csv' ?>" download>Download Bulk CSV Format For Assignment</a></span>
            <?php } } ?>
        </div>
        <div class="col-xs-12 search-panel">
            <?php
                $notification['msg'] = session()->getFlashdata('msg');
                if(!empty($notification['msg'])){
            ?>
                    <?= view('listpagenotification',$notification); ?>
            <?php 
                }
            ?>
                <div class="container">
                    <h4>Search</h4>
                    <form onsubmit="return false;">
                        <div class="form-row">
                           
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>Serial No.</label>
                                <input type="text" class="form-control" placeholder="Serial No" name="serial_no" id="serial_no" maxlength="60">
                            </div>
							<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>IMEI No.</label>
                                <input type="text" class="form-control" placeholder="IMEI No" name="imei_no" id="imei_no" maxlength="30">
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>Mobile No.</label>
                                <input type="text" class="form-control" placeholder="Mobile No" name="mobile_no" id="mobile_no" maxlength="15">
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                <label>Status</label>
                                <select class="form-control" name="active" id="active">
                                <option value="">Select</option>
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>                      
                            </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
								<button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
                                <button type="submit" class="btn btn-primary pull-right" name="search" id="search">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="devicelists" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Device ID</th>
                            <th style="text-align: center;">IMEI No</th>
                            <th style="text-align: center;">Mobile No</th>
                            <th style="text-align: center;">Assigned To</th>
                            <th style="text-align: center;">Warranty Date</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;"></th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
</div>

<script>
    var dtble;
    $(document).ready(function() {
        // console.log(openAlert)
        if (typeof sessionStorage !== 'undefined') {
            var dataStored = sessionStorage.getItem('searchDataDevice');
            if (typeof dataStored !== 'undefined' && dataStored != null) {
                dataStored = JSON.parse(dataStored);

                $("#serial_no").val($.trim(dataStored.serial_no));
				$("#imei_no").val($.trim(dataStored.imei_no));
                $("#mobile_no").val($.trim(dataStored.mobile_no));
                $("#assinged_to").val($.trim(dataStored.assinged_to));
                $("#linked").val($.trim(dataStored.linked));
                $("#active").val($.trim(dataStored.active));
            }
        }
        buildDataTable();

        $("#search, #res").on('click', function() {
            dtble.destroy();
            setTimeout(function() {
                if (typeof sessionStorage !== 'undefined') {
                    var searchDataObj = {
                        serial_no: $.trim($("#serial_no").val()),
						imei_no: $.trim($("#imei_no").val()),
                        mobile_no: $.trim($("#mobile_no").val()),
                        //assinged_to: $.trim($("#assinged_to option:selected").val()),
                        linked: $.trim($("#linked option:selected").val()),
                        active: $.trim($("#active option:selected").val())
                    }
                    sessionStorage.setItem('searchDataDevice', JSON.stringify(searchDataObj));
                }
                //  dtble.destroy();
                buildDataTable();
            }, 1000);

        });
    
		$(document).on("click", ".statuschange", function(e){
			e.preventDefault();
			var link = $(this).attr('href');
			//alert(link);
			$.ajax({
				url: link,
				type: "POST",
				data: {},
				success: function (data)
				{
					// var recvdJson = JSON.parse(data);
					if(data.suc == 1){
						openAlert('alert-success', "Unassigned Successfully...");
					}
					else {
						openAlert('alert-danger', "Failed to Unassign....");
					}
					dtble.destroy();
					buildDataTable();
					
				},
				error: function (data){
					openAlert('alert-danger', "Failed to Change Status....");
				}
			});
		});
	
	
	});

    function unlink(id) {
        var URL = "<?php echo base_url()?>deviceslinked/delete";
        $.ajax({
            url: URL,
            method: "POST",
            data: {
                id: id
            }
        }).done(function(data) {
            var recvdJson = JSON.parse(data);
            openAlert('alert-success', "Unlinked successfully");
            dtble.destroy();
            buildDataTable();
        }).fail(function() {
            openAlert('alert-danger', "Failed to Unlink");
        });
    }

    function buildDataTable() {
        dtble = $('#devicelists').DataTable({
            processing: true,
            serverSide: true,
            bSort: false,
            searching: false,
            stateSave: true,
            ajax: {
                url: "<?php echo base_url('devices/getAllDevicesAdmin') ?>",
                type: 'POST',
                data: {
                    serial_no: $.trim($("#serial_no").val()),
					imei_no: $.trim($("#imei_no").val()),
                    mobile_no: $.trim($("#mobile_no").val()),
                    //assinged_to: $.trim($("#assinged_to option:selected").val()),
                    linked: $.trim($("#linked option:selected").val()),
                    active: $.trim($("#active option:selected").val())
                },
                error: function() {
                    $(".devicelists-error").html("");
                }

            },
            columns: [
						{"data": "serial_no"},
						{"data": "imei_no"},
						{"data": "mobile_no"},
						{"data": "assigned_user"},
						{"data": "warranty_date"},
						{"data": "active"},
						{"data": "action"}
					]
        });
    }

</script>
