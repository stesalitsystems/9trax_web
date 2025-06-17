<?php

	$session = session();
	$sessdata = $session->get('login_sess_data');
	
?>
<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-user"></i> Account Management
            <?php //echo "<pre>";print_r($sessdata);
            if ($sessdata['group_id'] == 1 || $sessdata['role_id'] == 1) { ?>
                <span class="pull-right"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?php echo  base_url('/') ?>users/add">Add Account</a></span>
            <?php } else if (isset($sessdata['allowedtocreateuser']) && $sessdata['allowedtocreateuser'] == 'Y'){ ?>
				<span class="pull-right"><a class="btn btn-primary btn-sm moduleaddbtn" href="<?php echo  base_url('/') ?>users/add">Add Account</a></span>
			<?php } ?>
        </div>
        <div class="card-body">
        <div class="search-panel">
            <?php
            $notification['msg'] = session()->getFlashdata('msg');
            if (!empty($notification['msg'])) { ?>
                <?= view('listpagenotification', $notification); ?>
            <?php } ?>            
                <form onsubmit="return false;"> 
                    <div class="form-row">                                               
                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                            <label>First/Last Name</label>
                            <input type="text" class="form-control" placeholder="First/Last name" name="name" id="name"  maxlength="60">
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                            <label>Email</label>
                            <input type="email" class="form-control" placeholder="Email" name="email" id="email"  maxlength="100">
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                            <label>Mobile</label>
                            <input type="text" class="form-control" placeholder="Mobile" name="contact" id="contact"  maxlength="15">
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
							<label>Account Type</label>
							<select class="form-control" name="group" id="group">
								<option value="">Select</option>
								<?php if($sessdata['group_id'] == 1) { ?>
									<option value="3">Distributor</option>
								<?php } ?>
								<?php if($sessdata['group_id'] == 3) { ?>
									<option value="6">Special User</option>
									<option value="2">Individual User</option>
								<?php } ?>
								<?php if($sessdata['group_id'] == 4) { ?>
									<option value="5">Department</option>
									<option value="2">User</option>
								<?php } ?>
								<?php if($sessdata['group_id'] == 5) { ?>
									<option value="2">User</option>
								<?php } ?>
							</select>
						</div>
						
						
						
                        <?php if($sessdata['group_id'] == 1) { ?>
                            <!--<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                <label>Account Type</label>
                                <select class="form-control" name="group" id="group">
                                    <option value="">Select</option>
                                    <?php
										if (isset($groupdd) && !empty($groupdd)) {
											foreach ($groupdd as $row) {
												?>
												<option value="<?php echo $row->id ?>"><?php echo $row->name_e ?></option>
												<?php
											}
										}
                                    ?>    
                                </select>
                            </div>-->
                        <?php } ?>
                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                            <label>Role</label>
                            <select class="form-control" name="role" id="role">
                                <option value="">Select</option>
                                <?php
                                if (isset($roledd) && !empty($roledd)) {
                                    foreach ($roledd as $row) {
                                        ?>
                                        <option value="<?php echo $row->id ?>"><?php echo $row->name_e ?></option>
                                        <?php
                                    }
                                }
                                ?>                          
                            </select>
                        </div>
						
						
						
						
						
                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                            <label>Status</label>
                            <select class="form-control" name="active"  id="active">
                                <option value="">Select</option>
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>                      
                            </select>
                        </div>

                        <?php /*if ($sessdata->group_id == 1) { ?>
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                <label>Subscription</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="">Select</option>
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>                      
                                </select>
                            </div>                       
                        <?php } */?>
                        </div>
                        <div class="form-row mb-0">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
								<button type="submit" class="btn btn-primary pull-right" name="search" id="search">Search</button>
                            </div>
                        </div>                    
                </form>
            </div>
            </div>
    </div>

    <div style="height:30px;"></div>

    <div class="table-responsive">
        <table class="table table-bordered" id="userlists" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Primary Contact</th>
                    <th>Email</th>
                    <th>Account Type</th>           
                    <th>Role</th>
                    <th>Status</th>               
                    <th></th>
                </tr>
            </thead>             

        </table>
    </div>

    <script>
        var dtble;
        $(document).ready(function () {
            if (typeof sessionStorage !== 'undefined') {
                var dataStored = sessionStorage.getItem('searchData');
                if (typeof dataStored !== 'undefined' && dataStored != null) {
                    dataStored = JSON.parse(dataStored);

                    $("#name").val($.trim(dataStored.name));
					$("#contact").val($.trim(dataStored.contact));
                    $("#email").val($.trim(dataStored.email));
					$("#group").val($.trim(dataStored.group));
                    $("#role").val($.trim(dataStored.role));
					$("#active").val($.trim(dataStored.active));
                    $("#action").val($.trim(dataStored.action));

                }
            }
            buildDataTable();

            $("#search, #res").on('click', function () {
                dtble.destroy();
                setTimeout(function () {
                    if (typeof sessionStorage !== 'undefined') {
					<?php if ($sessdata['group_id'] == 1) { ?>
                            var searchDataObj = {
                                name: $.trim($("#name").val()),
                                email: $.trim($("#email").val()),
                                contact: $.trim($("#contact").val()),
                                group: $.trim($("#group option:selected").val()),
                                role: $.trim($("#role option:selected").val()),
                                active: $.trim($("#active option:selected").val()),
                                status: $.trim($("#status option:selected").val())
                            }
					<?php } else { ?>
                            var searchDataObj = {
                                name: $.trim($("#name").val()),
                                email: $.trim($("#email").val()),
                                contact: $.trim($("#contact").val()),
                                role: $.trim($("#role option:selected").val()),
                                active: $.trim($("#active option:selected").val())
                            }
					<?php } ?>

                        sessionStorage.setItem('searchData', JSON.stringify(searchDataObj));
                    }
                    buildDataTable();
                }, 1000);

            });
			
			$(document).on("click", ".statuschange", function(e){
				e.preventDefault();
				var link = $(this).attr('href');
				// alert(link);
				$.ajax({
					url: link,
					type: "POST",
					data: {},
					success: function (data)
					{
						console.log(data);
                        console.log(data.suc);
                        // var recvdJson = JSON.parse(data);
						if(data.suc == 1){
							openAlert('alert-success', data.msg);
						}
						else{
							openAlert('alert-danger', data.msg);
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

        function buildDataTable() {
            var dataLoad = {};
            var coLumns = [];

                dataLoad = {
                    name: $.trim($("#name").val()),
                    email: $.trim($("#email").val()),
                    contact: $.trim($("#contact").val()),
                    group: $.trim($("#group option:selected").val()),
                    role: $.trim($("#role option:selected").val()),
                    active: $.trim($("#active option:selected").val()),
                    status: $.trim($("#status option:selected").val())
                };
                coLumns = [
                    {"data": "name"},
                    {"data": "mobile"},
                    {"data": "email"},
                    {"data": "group"},
                    {"data": "role"},
                    {"data": "active"},
                    {"data": "action"}
                ];

            dtble = $('#userlists').DataTable({
                processing: true,
                serverSide: true,
                bSort: false,
                searching: false,
                stateSave: true,
                ajax: {
                    url: "<?php echo base_url() ?>users/getallusers",
                    type: 'POST',
                    data: dataLoad,
                    error: function () {
                        $(".userlists-error").html("");
    //                        $("#userlists").append('<tbody class="userlists-error"><tr><td colspan="8">No data found in the server</td></tr></tbody>');
                        $("#userlists_processing").css("display", "none");
                    }

                },
                columns: coLumns
            });
        }
    </script>