<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.12.1/ol.css" type="text/css">
<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/animate.css">
<link href="<?php echo base_url() ?>assets/css/chosen.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ?>assets/css/ol3gm.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo base_url() ?>assets/js/BootSideMenu.js"></script>
<script src="<?php echo base_url() ?>assets/js/chosen.jquery.min.js" type="text/javascript"></script>
<style>
    .map {
        height: 30em;
        width: 100%;
    }
	#map {
		left:0;
		right:0;
		top:5em;
		bottom:0;
	}
    #filter_container{
        position: absolute;
        z-index: 1;
    }
    #customers{
        width: 16em;
        background-color: rgba(58, 53, 53, 0.38);
        color: #fff;
        font-weight: 600;
        border-radius: 5px;        
    }
    .searchdevices{
        width: 15em;
        background-color: rgba(58, 53, 53, 0.38);
        color: #fff;
        border-radius: 5px;
    }

    .actionbuttons-popup{
        width:78px;
        margin-right: 5px;
        cursor: pointer;
    }

    #changediv{height:200px;overflow:hidden;}

    #myposition{
        position: fixed;
        bottom: 34px;
        right: 10px;
        font-weight: bold;
        z-index: 1;
        width: 200px;
        color: #1543c3;
    }
    .focus-required{
        border: 1px solid #c55d5d !important;
    }
    .user-online-offline{
        width: 8px;
		position: absolute;
		right: 7px;
		top: 6px;
    }
    .tools-li-a{
        color:#000;
        text-align: center;
        text-decoration:none !important;
    }
    .tools-li-a-i{
        margin-right: 8px;
        width: 13px;
    }
    .disabledbutton {
        pointer-events: none;
        opacity: 0.4;
    }
    #poidrawingmodeselect{
        z-index: 1;
        float: right;
        position: absolute;
        right: 4em;
        top: 1.8em;
        display: none;
    }
    #showdistance{
        position: absolute;
        padding: 3px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        opacity: 0.7;
        white-space: nowrap;
        font: 10pt sans-serif;
        width: 60px;
        z-index: 1;
        text-align: center;
		right: 20em;
		top: 83px;
		display:none;
		min-height: 1.7em;
    }
	#showruler{
        position: absolute;
        padding: 3px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        opacity: 0.7;
        white-space: nowrap;
        font: 10pt sans-serif;
        width: 60px;
        z-index: 1;
        text-align: center;
		right: 12em;
		top: 83px;
		display:none;
		min-height: 1.7em;
    }
	#showplacesearch{
        position: absolute;
		padding: 1px;
		/*background: rgba(0, 0, 0, 0.2);*/
		color: white;
		opacity: 0.9;
		white-space: nowrap;
		font: 10pt sans-serif;
		width: 165px;
		z-index: 1;
		text-align: center;
		right: 14em;
		top: 5.9em;
		display: none;
		min-height: 1.7em;
    }
    .ui-widget-header{
        border:none;
    }
    .alerttabs{
        height: 280px;
    }
    .activealerts{
        border-bottom: 3px solid #27e46a;
    }
    #myTabsproxy li,#myTabsRoutedeviation li,#myTabsoffroute li{
        text-align: center;
        cursor: pointer;
    }
    #myTabsproxy li a,#myTabsRoutedeviation li a, #myTabsoffroute li a{
        display: block;
        width: 100%;
    }
    .alerttabs .table-responsive{
        height: 200px;
        overflow-y: scroll;
    }
    .frmto{
        width: 70px;
    }
    .devicemenudd{
       	width: 4px;
		height: 14px;
		position: relative;
		left: -6px;
		cursor: pointer;
		top: 1px;
    }
    .deviceconfigmenu{
        font-size: 12px;
        padding: 5px;
		background: rgb(53, 145, 236) none repeat scroll 0% 0%;
		color: #fff;
    }
    .deviceconfigmenu li{
        cursor: pointer;
		padding:0 5px;
    }
    .deviceconfigmenu li:hover{
        cursor: pointer;
        background-color: #ccc; 
		padding:0 5px;
		color:#000;
    }
	.followdiv {
		position: absolute;
		z-index: 9;
		background-color: #f1f1f1;
		text-align: center;
		left: 25em;
		top: 10em;
		border-style: ridge;
		border-color: #3879bb;
		border-width: 1px;
	}
	.followdivheader {
		background: rgb(218, 33, 38) none repeat scroll 0% 0%;
		color: #fff;
		padding: 5px 10px;		
		text-align:left;
		cursor: move;
	}
	.followmap{
		width: 30em;
	}
	#calltab {
		font-size: 12px;
	}
	#alerttab {
		font-size: 12px;
	}
	#sosstab {
		font-size: 12px;
	}	
	
	#exTab3 {
		box-shadow: 5px 3px 14px rgba(0, 0, 0, 0.176);
		border: 1px solid rgba(0, 0, 0, 0.15);
	}
	
	#myInput {
		width: 100%;
		font-size: 11px;
		padding: 3px 3px 3px 3px;
		border: 1px solid #ddd;
		margin-bottom: 10px;
		margin-top: 10px;
	}
	
	#myInputLocation {
		width: 100%;
		font-size: 11px;
		padding: 3px 3px 3px 3px;
		border: 1px solid #ddd;
		margin-bottom: 10px;
	}
	
	.ol-attribution {right: 0.5em!important;}
	.ol-popup {
        position: absolute;
        background-color: white;
        -webkit-filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
        filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
        padding: 0.7em;
        border-radius: 10px;
        border: 1px solid #cccccc;
        bottom: 32px;
        left: -50px;
        min-width: 70px;
      }
      .ol-popup:after, .ol-popup:before {
        top: 100%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
      }
      .ol-popup:after {
        border-top-color: white;
        border-width: 10px;
        left: 48px;
        margin-left: -10px;
      }
      .ol-popup:before {
        border-top-color: #cccccc;
        border-width: 11px;
        left: 48px;
        margin-left: -11px;
      }
      .ol-popup-closer {
        text-decoration: none;
        position: absolute;
        top: 2px;
        right: 8px;
      }
      .ol-popup-closer:after {
        content: "✖";
      }
	  #popup-content {
		text-align: center;
	  }
	  .zoomtoextentdiv{
		right: 5.2em;
		top: 4.64em;
		position: absolute;
		background-color: rgba(255,255,255,0.4);
		border-radius: 4px;
		padding: 2px;
		z-index: 1;
	}
	.zoomtoextentdiv button{
		display: block;
		margin: 1px;
		padding: 0;
		color: white;
		font-size: 1.14em;
		font-weight: bold;
		text-decoration: none;
		text-align: center;
		height: 1.375em;
		width: 1.375em;
		line-height: .4em;
		background-color: rgba(0,60,136,0.5);
		border: none;
		border-radius: 2px;
	}
	.zoomtoextentdiv button:hover,
	.zoomtoextentdiv button:focus {
		text-decoration: none;
		background-color: rgba(0,60,136,0.7);
	}
	#showactivecount{
        position: absolute;
        padding-top: 10px;
        background: #32CD32;
        color: white;
        opacity: 0.7;
        white-space: nowrap;
        font: 10pt sans-serif;
        width: 2.7em;
        z-index: 1;
        text-align: center;
		right: 12em;
		top: 4.7em;
		display:block;
		height: 2.7em;
		cursor: pointer;
		border-radius: 50%;
    }
	#showinactivecount{
        position: absolute;
        padding-top: 10px;
        background: #B22222;
        color: white;
        opacity: 0.7;
        white-space: nowrap;
        font: 10pt sans-serif;
        width: 2.7em;
        z-index: 1;
        text-align: center;
		right: 9em;
		top: 4.7em;
		display:block;
		height: 2.7em;
		cursor: pointer;
		border-radius: 50%;
    }
	.chosen-container-single .chosen-single{
		height: 38px;
	}
	.dataTables_scrollHeadInner { width:100% !important; }
	.table-responsive { overflow-x:scroll; }
table { width:100% !important; }
.dataTables_scroll .dataTables_scrollHead { display:none !important; }
.dataTables_scroll .dataTables_scrollBody thead tr { height:auto !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th { height:auto !important; padding:8px 12px !important; }
.dataTables_scroll .dataTables_scrollBody thead tr th .dataTables_sizing { height:auto !important; overflow:visible !important; }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.12.1/ol.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap-notify.min.js"></script>
<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-fw fa-bar-chart"></i> <?php echo $page_title;?>
        </div>
        <div class="card-body">
        <div class="search-panel">
            <?php
			$notification['msg'] = session()->getFlashdata('msg');
			if (!empty($notification['msg'])) { ?>
				<?= view('listpagenotification', $notification); ?>
			<?php }
			?>        
                <form action="<?= base_url('traxreport/activitysummeryreport') ?>" method="post" autocomplete="off" name="frmsearch" onsubmit="return submit_form();">
					<?= csrf_field() ?>
                    <div class="form-row">
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label for="exampleInputEmail1">Pway<span class="text-danger">*</span></label>
                            <select name="pway_id" id="pway_id" class="select_mfc" required>
                                <?php if(!empty($pway)){ ?>
                                <option value="">Select</option>
                                <option value="All" <?php if($sse_pwy == 'All'){ ?>selected<?php } ?>>All</option>
                                <?php foreach ($pway as $key=>$row){ ?>
                                  <option value="<?php echo $row->user_id?>" <?php if($sse_pwy == $row->user_id){ ?>selected<?php } ?>><?php echo $row->organisation; ?></option>
                                <?php } } ?>
                            </select>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="secdiv">
                            <label for="exampleInputEmail1">Section<span class="text-danger">*</span></label>
                            <select name="user" id="user" class="form-control" required>
								<option value="">Select</option>
                                <?php if(!empty($usersdd)){ ?>
                                <option value="All" <?php if($pwi_id == 'All'){ ?>selected<?php } ?>>All</option>
                                <?php foreach ($usersdd as $key=>$row){ ?>
                                  <option value="<?php echo $row->user_id?>" <?php if($pwi_id == $row->user_id){ ?>selected<?php } ?>><?php echo $row->organisation; ?></option>
                                <?php } } ?>
                            </select>
                            <input type="hidden" name="pwi_name" id="pwi_name" value="<?php echo $pwi_name;?>"/>
							<input type="hidden" name="section_id" id="section_id" value="<?php echo $pwi_id;?>"/>
							<input type="hidden" name="map_device_id" id="map_device_id" value="<?php if(isset($map_device_id)) echo $map_device_id;?>" />
							<input type="hidden" name="map_start_date" id="map_start_date" value="<?php if(isset($map_start_date)) echo $map_start_date;?>" />
							<input type="hidden" name="map_start_time" id="map_start_time" value="<?php if(isset($map_start_time)) echo $map_start_time;?>" />
							<input type="hidden" name="map_end_date" id="map_end_date" value="<?php if(isset($map_end_date)) echo $map_end_date;?>" />
							<input type="hidden" name="map_end_time" id="map_end_time" value="<?php if(isset($map_end_time)) echo $map_end_time;?>" />
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label>Device<span class="rqurd">*</span></label>
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
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="typeofuserdiv">
                            <label>Type Of User</label>
                            <select class="form-control selectpicker select_mfc" name="typeofuser" id="typeofuser">
								<option value="All" <?php if($typeofuser == "All"){ ?>selected<?php } ?>>All</option>
                              	<option value="Keyman" <?php if($typeofuser == "Keyman"){ ?>selected<?php } ?>>Keyman</option>
								<option value="Patrolman" <?php if($typeofuser == "Patrolman"){ ?>selected<?php } ?>>Patrolman</option>
								<option value="Stock" <?php if($typeofuser == "Stock"){ ?>selected<?php } ?>>Stock</option>
								<option value="Mate" <?php if($typeofuser == "Mate"){ ?>selected<?php } ?>>Mate</option>
								<option value="USFD" <?php if($typeofuser == "USFD"){ ?>selected<?php } ?>>USFD</option>
                            </select>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label>Date<span class="rqurd">*</span></label>
							<input type="input" class="form-control dt" id="dt" name="dt" value="<?php if(isset($dt)) echo $dt; ?>" placeholder="Date" readonly>
						</div>
					</div>
					<div class="form-row mb-0">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
							<button type="submit" class="btn btn-primary pull-right" name="search" id="search">Search</button>
							<?php if(!empty($report_data) && count($report_data) > 0){ ?>
							<!--<button type="button" class="btn btn-primary pull-right" name="pdfdownload" id="pdfdownload" style="margin-right: 0.5em;" onclick="return pdfSubmit();">Download PDF</button>-->
							<!-- <button type="button" class="btn btn-primary pull-right" name="print" id="print" style="margin-right: 0.5em;">Print</button> -->
							<button type="button" class="btn btn-primary pull-right" name="exceldownload" id="exceldownload" style="margin-right: 0.5em;" onclick="return excelSubmit();">Download Excel</button>
							<?php } ?>
						</div>
					</div>                    
                </form> 
            </div>
        </div>
    </div>

    <div style="height:30px;"></div>

    <div class="table-responsive">
	<div style="overflow:scroll;">
                    <table class="table table-bordered" id="lists" width="100%" cellspacing="0">
                        <thead>
                            <tr>
								<!--<th>Serial No</th>-->
                                <th>Date</th>
								<th>Device ID</th>
								<th>DeviceName</th>
								<th>BIT</th>
								<th>SSE/PWY</th>
								<th>Section</th>
								<th>User Type</th>
                                <th>Start Date Time</th>
                                <th>End Date Time</th>
                                <th>Travelled Distance(KM)</th>
								<!-- <th>Actual Distance(KM)</th>
								<th>Deviation(KM)</th>
								<th>Distance Exception</th> -->
								<th>Travelled Time</th>
								<!--<th>Actual Travelled Time</th>
								<th>Time Exception</th>-->
                                <!-- <th>Avg. Speed</th>
								<th>Max Speed</th> 
                                <th>Total Call</th>
                                <th>Total SOS</th>-->
                            </tr>
                        </thead>
                        <?php // if ($this->request->getPost()) { ?>
                        <tbody class="reportlists-body">
                            <?php if(!empty($report_data)){
							$b=1;
                            foreach($report_data as $report_data_each){
								$map_device_id = $report_data_each[0]['deviceid'];
								$startdetails = $report_data_each[0]['start_time'];
								$startdetailsarr = explode(' ',$startdetails);				
								$map_start_date = $startdetailsarr[0];
								$starttimedetailsarr = explode(":",$startdetailsarr[1]);
								if(count($starttimedetailsarr)>1) {
									$start_time = $starttimedetailsarr[0].":".$starttimedetailsarr[1];
								} else {
									$start_time = '00:00';
								}
								
								$map_start_time = $start_time;
								$enddetails = $report_data_each[0]['end_time'];
								$enddetailsarr = explode(' ',$enddetails);				
								$map_end_date = $enddetailsarr[0];
								$enddetailsarrarr = explode(":",$enddetailsarr[1]);
								if(count($enddetailsarrarr)>1) {
									$end_time = $enddetailsarrarr[0].":".$enddetailsarrarr[1];
								} else {
									$end_time = '00:00';
								}
								$map_end_time = $end_time;
                            ?>
                            <tr onclick="plotnewtrack('<?php echo $map_device_id;?>','<?php echo $map_start_date;?>','<?php echo $start_time;?>','<?php echo $map_end_date;?>','<?php echo $map_end_time;?>');">
								<!--<td><?php echo $b; ?></td>-->
								<td><?php echo date("d-m-Y", strtotime($report_data_each[0]['result_date'])); ?></td>
                                <td><?php echo $report_data_each[0]['serial_no']; ?></td>
								<td><?php echo $report_data_each[0]['device_name']; ?></td>
								<td><?php if(isset($report_data_each[0]['bit'])) echo $report_data_each[0]['bit']; ?></td>
								<td><?php echo $report_data_each[0]['pwy']; ?></td>
								<td><?php echo $report_data_each[0]['organisation']; ?></td>
								<td><?php echo $report_data_each[0]['user_type']; ?></td>
                                <td><?php echo $report_data_each[0]['start_time']; ?></td>
                                <td><?php echo $report_data_each[0]['end_time']; ?></td>
                                <td><?php echo round($report_data_each[0]['distance_cover']/1000).' km'; ?></td>
                                
								<!-- <td><?php // if(isset($report_data_each[0]['walk_org_distance_out'])) echo $report_data_each[0]['walk_org_distance_out'].' km'; ?></td>
								<td><?php // if(isset($report_data_each[0]['deviation_distance'])) echo round($report_data_each[0]['deviation_distance']/1000).' km'; ?></td>
								<td><?php // if(isset($report_data_each[0]['distance_status'])) echo $report_data_each[0]['distance_status']; ?></td> -->
								
								<td><?php if(isset($report_data_each[0]['duration'])) echo $report_data_each[0]['duration']; ?></td>
								<!--<td><?php //echo $report_data_each[0]['durationorgtime_org_out']; ?></td>
								<td><?php //echo $report_data_each[0]['time_status']; ?></td>-->
								
								<!-- <td><?php // if(isset($report_data_each[0]['avg_speed'])) echo number_format($report_data_each[0]['avg_speed'],2); ?></td>
								<td><?php // if(isset($report_data_each[0]['max_speed'])) echo number_format($report_data_each[0]['max_speed'],2); ?></td> 
								
                                <td><?php echo $report_data_each[0]['call_no']; ?></td>
                                <td><?php echo $report_data_each[0]['sos_no']; ?></td>-->
                            </tr>
                            <?php $b++;} ?>
                            <?php if(!empty($sos_details) && $sos_details != '0'){ ?>
                            <tr>
                                <th colspan="11">SOS Details</th>
                            </tr>
                            <tr>
                                <th>Time</th>
                                <th>Location</th>
                                <th colspan="9">Link</th>
                            </tr>
                            <?php foreach($sos_details as $data_sos_each){
                            ?>
                            <tr>
                                <td><?php echo $data_sos_each->currentdate." ".$data_sos_each->currenttime; ?></td>
                                <td><?php echo $data_sos_each->location; ?></td>
                                <td colspan="5"><a href="<?php echo $data_sos_each->url; ?>" target="_blank"><?php echo $data_sos_each->url; ?></a></td>
                            </tr>
                            <?php } } ?>
                            <?php if(!empty($call_details) && $call_details != '0'){ ?>
                            <tr>
                                <th colspan="11">Call Details</th>
                            </tr>
                            <tr>
                                <th>Time</th>
                                <th>Location</th>
                                <th colspan="9">Link</th>
                            </tr>
                            <?php foreach($call_details as $data_call_each){
                            ?>
                            <tr>
                                <td><?php echo $data_call_each->currentdate." ".$data_call_each->currenttime; ?></td>
                                <td><?php echo $data_call_each->location; ?></td>
                                <td colspan="9"><a href="<?php echo $data_call_each->url; ?>" target="_blank"><?php echo $data_call_each->url; ?></a></td>
                            </tr>
                            <?php } } ?>
                            <?php if(!empty($free_fall_alert_details) && $free_fall_alert_details != '0'){ ?>
                            <tr>
                                <th colspan="11">Free Fall Details</th>
                            </tr>
                            <tr>
                                <th>Time</th>
                                <th>Location</th>
                                <th colspan="9">Link</th>
                            </tr>
                            <?php foreach($free_fall_alert_details as $data_free_fall_each){
                            ?>
                            <tr>
                                <td><?php echo $data_free_fall_each->currentdate." ".$data_free_fall_each->currenttime; ?></td>
                                <td><?php echo $data_free_fall_each->location; ?></td>
                                <td colspan="9"><a href="<?php echo $data_free_fall_each->url; ?>" target="_blank"><?php echo $data_free_fall_each->url; ?></a></td>
                            </tr>
                            <?php } } ?>
                            <?php if(!empty($low_battery_alert_details) && $low_battery_alert_details != '0'){ ?>
                            <tr>
                                <th colspan="11">Low Batery Alert</th>
                            </tr>
                            <tr>
                                <th>Time</th>
                                <th>Location</th>
                                <th colspan="9">Link</th>
                            </tr>
                            <?php foreach($low_battery_alert_details as $data_low_battery_each){
                            ?>
                            <tr>
                                <td><?php echo $data_low_battery_each->currentdate." ".$data_low_battery_each->currenttime; ?></td>
                                <td><?php echo $data_low_battery_each->location; ?></td>
                                <td colspan="9"><a href="<?php echo $data_low_battery_each->url; ?>" target="_blank"><?php echo $data_low_battery_each->url; ?></a></td>
                            </tr>
                            <?php } } ?>
                            <?php if(!empty($no_movement_alert_details) && $no_movement_alert_details != '0'){ ?>
                            <tr>
                                <th colspan="11">No Movement Alert</th>
                            </tr>
                            <tr>
                                <th>Time</th>
                                <th>Location</th>
                                <th colspan="9">Link</th>
                            </tr>
                            <?php foreach($no_movement_alert_details as $data_no_movement_each){
                            ?>
                            <tr>
                                <td><?php echo $data_no_movement_each->currentdate." ".$data_no_movement_each->currenttime; ?></td>
                                <td><?php echo $data_no_movement_each->location; ?></td>
                                <td colspan="9"><a href="<?php echo $data_no_movement_each->url; ?>" target="_blank"><?php echo $data_no_movement_each->url; ?></a></td>
                            </tr>
                            <?php } } ?>
                            <?php if(!empty($overspeed_alert_details) && $overspeed_alert_details != '0'){ ?>
                            <tr>
                                <th colspan="11">Over Speed Alert</th>
                            </tr>
                            <tr>
                                <th>Time</th>
                                <th>Location</th>
                                <th colspan="9">Link</th>
                            </tr>
                            <?php foreach($overspeed_alert_details as $data_over_speed_each){
                            ?>
                            <tr>
                                <td><?php echo $data_over_speed_each->currentdate." ".$data_over_speed_each->currenttime; ?></td>
                                <td><?php echo $data_over_speed_each->location; ?></td>
                                <td colspan="9"><a href="<?php echo $data_over_speed_each->url; ?>" target="_blank"><?php echo $data_over_speed_each->url; ?></a></td>
                            </tr>
                            <?php } } ?>
                            <?php if(!empty($no_data_alert_details) && $no_data_alert_details != '0'){ ?>
                            <tr>
                                <th colspan="11">No Data Alert</th>
                            </tr>
                            <tr>
                                <th>Time</th>
                                <th>Location</th>
                                <th colspan="9">Link</th>
                            </tr>
                            <?php foreach($no_data_alert_details as $data_no_data_each){
                            ?>
                            <tr>
                                <td><?php echo $data_no_data_each->currentdate." ".$data_no_data_each->currenttime; ?></td>
                                <td><?php echo $data_no_data_each->location; ?></td>
                                <td colspan="9"><a href="<?php echo $data_no_data_each->url; ?>" target="_blank"><?php echo $data_no_data_each->url; ?></a></td>
                            </tr>
                            <?php } } ?>
                            <?php if(!empty($zone_in_alert_details) && $zone_in_alert_details != '0'){ ?>
                            <tr>
                                <th colspan="11">Zone In Alert</th>
                            </tr>
                            <tr>
                                <th>Time</th>
                                <th colspan="10">Location</th>
                            </tr>
                            <?php foreach($zone_in_alert_details as $data_zone_in_each){
                            ?>
                            <tr>
                                <td><?php echo $data_zone_in_each->currentdate." ".$data_zone_in_each->currenttime; ?></td>
                                <td colspan="10"><?php echo $data_zone_in_each->geoname; ?></td>
                            </tr>
                            <?php } } ?>
                            <?php if(!empty($zone_out_alert_details) && $zone_out_alert_details != '0'){ ?>
                            <tr>
                                <th colspan="11">Zone Out Alert</th>
                            </tr>
                            <tr>
                                <th>Time</th>
                                <th colspan="10">Location</th>
                            </tr>
                            <?php foreach($zone_out_alert_details as $data_zone_out_each){
                            ?>
                            <tr>
                                <td><?php echo $data_zone_out_each->currentdate." ".$data_zone_out_each->currenttime; ?></td>
                                <td colspan="10"><?php echo $data_zone_out_each->geoname; ?></td>
                            </tr>
                            <?php } } ?>
                            <?php if(!empty($route_deviation_alert_details) && $route_deviation_alert_details != '0'){ ?>
                            <tr>
                                <th colspan="11">Route Deviation Alert</th>
                            </tr>
                            <tr>
                                <th>Time</th>
                                <th colspan="10">Location</th>
                            </tr>
                            <?php foreach($route_deviation_alert_details as $data_route_deviation_each){
                            ?>
                            <tr>
                                <td><?php echo $data_route_deviation_each->currentdate." ".$data_route_deviation_each->currenttime; ?></td>
                                <td colspan="10"><?php echo $data_route_deviation_each->geoname; ?></td>
                            </tr>
                            <?php } } ?>
                            <?php } else { ?>
                            <tr><td colspan="11">No Devices Assigned</td></tr>
                            <?php } ?>
                        </tbody>
                        <?php //} else { ?>
                        <tbody class="reportlists-body">
                            <tr><td colspan="11">Search To Generate Report</td></tr>
                        </tbody>
                        <?php // } ?>
                    </table>
					</div>
                </div>

                <div style="height:30px;"></div>

	<div id="map" class="map" style="display:none;">
		<div id="alertpopup" style="display: none;">  
            <div class="tabbable hide" id="popoverContentalert">
                <div class="sapopup-header">
                    <div class="sapopup-title"><span id="popupheaderalert"></span></div>
                    <ul class="nav nav-tabs nav-default">
                        <li class="tab"><a href="#alerttab1" data-toggle="tab" data-tooltip="tooltip" title="Details"><i class="fa fa-info" aria-hidden="true"></i></a></li> 
                        <li class="tab"><a href="javascript:void(0)" id="closemappopupalert" style="background-color: #bd370f;"><i class="fa fa-times" aria-hidden="true" ></i></a></li>
                    </ul>
                </div>
                <div class="sapopup-body">
                    <div class="tab-content">
                        <div class="tab-pane" id="alerttab1">

                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
<script>
	var map;
		var lat = 23.2599;
		var lon = 77.4126;
		var osmlayer = new ol.layer.Tile({
				type: 'base',
				title: 'OSM',
				visible: true,
				source: new ol.source.OSM()
		});
		var bingLayers = [osmlayer];
		
		var trackSource = new ol.source.Vector({});
		var trackLayer = new ol.layer.Vector({
			source:trackSource
		});
		var vectorSource = new ol.source.Vector({});
		var vectoLayer = new ol.layer.Vector({
			source:vectorSource
		});
		var trackonHPSourcevars = new ol.source.Vector({});
		var trackonHPLayervars = new ol.layer.Vector({
			source:trackonHPSourcevars
		});
		var holdExistingMarkerIdsHistory = [];
		var globalData, globalCounter = 0, globalDataLen;
		var sourcehp;
		var vectorHP;
		var timeoutCounter;
		var csvDownload = [];
		var featureHPvars = {};
		var latlonHPvars = [];
		var HPfeaturevarspole = {};
		var HPlatlonvarspole = {};
		var alert_tooltip = {};
		$(document).ready(function() {
			//$('#device_id').chosen();
			$(document).on("change", "#user", function(e){
				$("#pwi_name").val($("#user :selected").text());
			});
			
			$("#pway_id").on('change', function() {
					populateSection($(this).val());
				});
				<?php if($_POST) {
				?>
				populateSection('<?php echo $sse_pwy; ?>');
				<?php
				}
				?>
			
			<?php if(!empty($report_data)){ ?>		
			// Map Load
			$('#map').show();
			
			
			
			map = new ol.Map({
				target:'map',
				layers:bingLayers.concat(vectoLayer,trackLayer,trackonHPLayervars),
				view:new ol.View({
					center:ol.proj.transform([lon,lat],'EPSG:4326','EPSG:3857'),
					zoom:9,
					minZoom: 2,
					maxZoom: 24
				}),
				interactions: ol.interaction.defaults({mouseWheelZoom:false, dragPan: false}),
				crossOrigin:'anonymous',
				controls: []
			});
			
			$("#closemappopupalert").on('click',function () {
				$("#popupheaderalert").attr('title','');
				$("#popupheaderalert, #alerttab1").html('');
				$("#alertpopup").hide();
			});
			trackonHPLayervars.getSource().clear();
			latlonHPvars = [];
			HPlatlonvarspole['HP1'] = [];
			plottrack();
			<?php } ?>
			$("#res").on('click', function() {
				window.location.href = BASEURL + 'traxreport/activitysummeryreport';
			});
			
			// $( ".stdt" ).datetimepicker({
			// 	// changeMonth: true,
			// 	// changeYear: true,
			// 	dateFormat: 'dd-mm-yy',
			// 	//minDate: new Date('2016/09/01'),
			// 	maxDate: '0'
			// });
			$( ".stdt" ).datepicker({
				// changeMonth: true,
				// changeYear: true,
				dateFormat: 'dd-mm-yy',
				maxDate: '0'
			});
			
			$( "#print" ).on( "click", function() {
				$('#pdfdownload').hide();
				$('#exceldownload').hide();
				$('#search').hide();
				$('#res').hide();
				$('#print').hide();
				window.print();
				$('#pdfdownload').show();
				$('#exceldownload').show();
				$('#search').show();
				$('#res').show();
				$('#print').show();
			});
		
		});
		function addMarker(rowdata,fromWhere) {
			if(parseFloat(rowdata.longitude) != 0 && parseFloat(rowdata.latitude) != 0){
				var geom = new ol.geom.Point(ol.proj.transform([parseFloat(
							rowdata.longitude),parseFloat(rowdata.latitude)],
						'EPSG:4326','EPSG:3857'));

				var sizeArr = [35,35];
				var scale = 0.7;
				
				if (fromWhere == 'track') {
					var feature = new ol.Feature({
						geometry:geom,
						id:"track_" + rowdata.deviceid,
						devid:rowdata.deviceid
					});
					scale = 0.8;
				} else if (fromWhere == 'calls') {
					var feature = new ol.Feature({
						geometry:geom,
						id:"calls_" + rowdata.sosid,
						devid:rowdata.deviceid
					});
				} else if (fromWhere == 'sos') {
					var feature = new ol.Feature({
						geometry:geom,
						id:"sos_" + rowdata.sosid,
						devid:rowdata.deviceid
					});
				} else if (fromWhere == 'alerts') {
					var feature = new ol.Feature({
						geometry:geom,
						id:"alerts_" + rowdata.sosid,
						devid:rowdata.deviceid
					});
				} else if (fromWhere == 'notificationList') {
					var feature = new ol.Feature({
						geometry:geom,
						id:"locateflag_" + rowdata.deviceid,
						devid:rowdata.deviceid
					});
				}
				else if (fromWhere == 'placeSearch') {
					var feature = new ol.Feature({
						geometry:geom
					});
				} else if (fromWhere == 'poi') {
					var feature = new ol.Feature({
						geometry:geom,
						id:"locatepoi_" + rowdata.id
					});
				}else if(fromWhere == 'history'){
					 var feature = new ol.Feature({
						geometry:geom,
						id:"historyplay_" + rowdata.id
					});
				}else if(fromWhere == 'history_calls'){
					 var feature = new ol.Feature({
						geometry:geom,
						id:"historycalls" + rowdata.faetureid
					});
				}else if(fromWhere == 'history_alerts'){
					 var feature = new ol.Feature({
						geometry:geom,
						id:"historyalerts" + rowdata.faetureid
					});
					
				}else if(fromWhere == 'history_sos'){
					 var feature = new ol.Feature({
						geometry:geom,
						id:"historysos" + rowdata.faetureid
					});
				}else if(fromWhere == 'history_stop'){
					 var feature = new ol.Feature({
						geometry:geom,
						id:"historystop" + rowdata.faetureid
					});
				}else if(fromWhere == 'history_start'){
					 var feature = new ol.Feature({
						geometry:geom,
						id:"historystart" + rowdata.faetureid
					});
				}else if(fromWhere == 'history_end'){
					 var feature = new ol.Feature({
						geometry:geom,
						id:"historyend" + rowdata.faetureid
					});
				}

				var iconURL = BASEURLIMG + 'assets/iconset/device.png';
				if (fromWhere == 'alerts' || fromWhere == 'history_alerts') { // alert            
					iconURL = BASEURLIMG + 'assets/images/danger.png'
				} else if (fromWhere == 'calls' || fromWhere == 'history_calls') { // call
					iconURL = BASEURLIMG + 'assets/images/call.png'
				} else if (fromWhere == 'sos' || fromWhere == 'history_sos') { // sos
					iconURL = BASEURLIMG + 'assets/images/sos.png'
				} else if (fromWhere == 'notificationList') { // sos
					iconURL = BASEURLIMG + 'assets/images/blackflag.png'
				} else if (fromWhere == 'placeSearch') { // place search
					iconURL = BASEURLIMG + 'assets/images/bluepin.png'
				} else if (fromWhere == 'poi') { // poi
					iconURL = BASEURLIMG + 'assets/images/blackflag.png'
				} else if (fromWhere == 'history') { 
					iconURL = BASEURLIMG + 'assets/iconset/device.png'
				}else if (fromWhere == 'history_start') { 
					iconURL = BASEURLIMG + 'assets/images/greenflag.png'
				}else if (fromWhere == 'history_end') { 
					iconURL = BASEURLIMG + 'assets/images/redflag.png'
				}else if (fromWhere == 'history_stop') { 
					iconURL = BASEURLIMG + 'assets/images/stop.png'
				}else if (fromWhere == 'track') { 
					if(rowdata.icon_details){
						iconURL = BASEURLIMG + rowdata.icon_details;
					}
					else{
						iconURL = BASEURLIMG + 'assets/iconset/device.png'
					}			
				}
				
				feature.setStyle([
					new ol.style.Style({
						image:new ol.style.Icon(({
							anchor:[0.5,1],
							size:sizeArr,
							scale:scale,
							anchorXUnits:'fraction',
							anchorYUnits:'fraction',
							opacity:1,
							src:iconURL
						}))
					})
				]);

				if (fromWhere == 'track') {
					trackSource.addFeature(feature);
					holdExistingTrackingId.push(rowdata.deviceid);
					feature.setId("track_" + rowdata.deviceid);
					map.getView().setCenter(ol.proj.transform([parseFloat(
								rowdata.longitude),parseFloat(rowdata.latitude)],
							'EPSG:4326','EPSG:3857'));
					trackLayer.setZIndex(11);
				} else if (fromWhere == 'notificationList') {
					if (locateObjStoreArr.indexOf(JSON.stringify(rowdata)) === -1) {
						locateSource.addFeature(feature);
						console.log(rowdata.sosid);
						map.getView().setCenter(ol.proj.transform([parseFloat(
									rowdata.longitude),parseFloat(rowdata.latitude)],
								'EPSG:4326','EPSG:3857'));
						locateObjStoreArr.push(JSON.stringify(rowdata));
						// map.getView().fit(extent, map.getSize());
					}
				} else if (fromWhere == 'placeSearch') {
					sourcePOI.addFeature(feature);
				} else if (fromWhere == 'poi') {
					sourcePOI.addFeature(feature);
					map.getView().setCenter(ol.proj.transform([parseFloat(
								rowdata.longitude),parseFloat(rowdata.latitude)],
							'EPSG:4326','EPSG:3857'));
				}else if(fromWhere == 'history'){
					feature.setId("historyplay_" + rowdata.deviceid);
					sourcehp.addFeature(feature);
					map.getView().setCenter(ol.proj.transform([parseFloat(
								rowdata.longitude),parseFloat(rowdata.latitude)],
							'EPSG:4326','EPSG:3857'));
					holdExistingMarkerIdsHistory.push(rowdata.deviceid);
					map.getView().setZoom(14);
				}else if(fromWhere == 'history_alerts' || fromWhere == 'history_sos' || fromWhere == 'history_calls'){
					sourcehp.addFeature(feature);
				}else if(fromWhere == 'history_start' || fromWhere == 'history_end'){          
					sourcehp.addFeature(feature);
					
				}else if(fromWhere == 'history_stop'){				
					sourcehp.addFeature(feature);
					
					// alert tooltip
					var overlayhtml = '<div class="ol-popup" id="overlayinfo'+rowdata.faetureid+'" style="padding:0.2em;border-radius:4px;border-color: #122ad2aa;border-width: 2px;"></div>';
					$("#map").append(overlayhtml);
					var info =  document.getElementById('overlayinfo'+rowdata.faetureid);
					alert_tooltip[rowdata.faetureid] = new ol.Overlay({
					  element:info
					});
					map.addOverlay(alert_tooltip[rowdata.faetureid]);
					var html = '<div class="ol-popup-content"><div style="height:1.5em;width:6em;font-size:11px;text-align: center;" id="overlayinfo_alert'+rowdata.faetureid+'">'+rowdata.stoptime+'</div></div>';
					info.innerHTML = html;
					var geometry = feature.getGeometry();
					var coord = geometry.getCoordinates();
					alert_tooltip[rowdata.faetureid].setPosition(coord);
				}else {
					sourceAlert.addFeature(feature);

					if (fromWhere == 'calls') {
						feature.setId("calls_" + rowdata.sosid);
						holdExistingCallsDeviceId.push(rowdata.deviceid);
					}
					if (fromWhere == 'sos') {
						feature.setId("sos_" + rowdata.sosid);
						map.getView().setCenter(ol.proj.transform([parseFloat(
								rowdata.longitude),parseFloat(rowdata.latitude)],
							'EPSG:4326','EPSG:3857'));
						holdExistingSosDeviceId.push(rowdata.deviceid);
					}
					if (fromWhere == 'alerts') {
						feature.setId("alerts_" + rowdata.sosid);
						map.getView().setCenter(ol.proj.transform([parseFloat(
								rowdata.longitude),parseFloat(rowdata.latitude)],
							'EPSG:4326','EPSG:3857'));
						//holdExistingAlertDeviceId.push(rowdata.deviceid);

					}

				}
			}
		}
		
		function deleteMarkerById(id,layer) {
			if ((typeof layer !== 'undefined') && (layer == 'track')) {
				var id = trackLayer.getSource().getFeatureById(id);
				trackLayer.getSource().removeFeature(id);
			} else if ((typeof layer !== 'undefined') && (layer == 'locate')) {
				// var id = locateLayer.getSource().getFeatureById(id);
				var featureid = id.get('id');
				var deviceid = featureid.split("_");
				locateObjStoreArr.map(function (row,i) {
					var jsonRow = JSON.parse(row);
					if (deviceid[1] == jsonRow.deviceid) {
						delete locateObjStoreArr[i];
					}
				});
				locateLayer.getSource().removeFeature(id);

			} else if ((typeof layer !== 'undefined') && (layer == 'history')) {
				// var id = locateLayer.getSource().getFeatureById(id);
			   var id = vectorHP.getSource().getFeatureById(id);
				vectorHP.getSource().removeFeature(id);

			} else {
				var id = vectoLayer.getSource().getFeatureById(id);
				vectoLayer.getSource().removeFeature(id);
			}

		}
		
		function removeAllMarkers(layer) {
			if ((typeof layer !== 'undefined') && (layer == 'track')) {
				trackLayer.getSource().clear();
			} else if ((typeof layer !== 'undefined') && (layer == 'notificationList')) {
				locateLayer.getSource().clear();
			} else if ((typeof layer !== 'undefined') && (layer == 'poi')) {
				vectorPOI.getSource().clear();
			} else if ((typeof layer !== 'undefined') && (layer == 'history')) {
				if(typeof vectorHP !== 'undefined'){
				   vectorHP.getSource().clear(); 
				}            
			} 
			else {
				vectoLayer.getSource().clear();
			}

		}

		function moveMarker(rowdata,layer) {
			if (layer == 'track') {
				var feature = trackLayer.getSource().getFeatureById(
						"track_" + rowdata.deviceid);
			} else if (layer == 'track') {
				var feature = trackLayer.getSource().getFeatureById(
						"track_" + rowdata.deviceid);
			} else if (layer == 'alerts') {
				var feature = vectoLayer.getSource().getFeatureById(
						"alerts_" + rowdata.deviceid);
			} else if (layer == 'sos') {
				var feature = vectoLayer.getSource().getFeatureById(
						"sos_" + rowdata.deviceid);
			} else if (layer == 'calls') {
				var feature = vectoLayer.getSource().getFeatureById(
						"calls_" + rowdata.deviceid);
			}else if (layer == 'history') {
				var feature = vectorHP.getSource().getFeatureById("historyplay_" + rowdata.deviceid);
			   
			} 
			
			if (feature != null) {
				feature.setGeometry(new ol.geom.Point(ol.proj.transform([
					parseFloat(rowdata.longitude),parseFloat(rowdata.latitude)],
						'EPSG:4326','EPSG:3857')));
				if(featurevars['trackon' + rowdata.deviceid]){
					trackonLayervars['trackon' + rowdata.deviceid].getSource().clear();
					delete featurevars['trackon' + rowdata.deviceid];
					latlonvars['trackon' + rowdata.deviceid].push([parseFloat(
											rowdata.longitude),parseFloat(
											parseFloat(rowdata.latitude))]);
					console.log(latlonvars['trackon' + rowdata.deviceid]);
					var geom = new ol.geom.LineString(latlonvars['trackon' + rowdata.deviceid]);
					geom.transform('EPSG:4326', 'EPSG:3857');
					//featurevars['trackon' + rowdata.deviceid].setGeometry(geom);
					featurevars['trackon' + rowdata.deviceid] = new ol.Feature({
						geometry: geom
					});
					featurevars['trackon' + rowdata.deviceid].setStyle([
						new ol.style.Style({
							stroke:new ol.style.Stroke({
								color:'green',
								width:1
							})
						})
					]);
					featurevars['trackon' + rowdata.deviceid].setId("trackonroute_" + rowdata.deviceid);
					trackonSourcevars['trackon' + rowdata.deviceid].addFeature(featurevars['trackon' + rowdata.deviceid]);
				}
				if(featureHPvars['trackon' + rowdata.deviceid]){
					trackonHPLayervars.getSource().clear();
					delete featureHPvars['trackon' + rowdata.deviceid];
					latlonHPvars.push([parseFloat(
											rowdata.longitude),parseFloat(
											parseFloat(rowdata.latitude))]);
					console.log(latlonHPvars);
					var geom = new ol.geom.LineString(latlonHPvars);
					geom.transform('EPSG:4326', 'EPSG:3857');
					//featureHPvars['trackon' + rowdata.deviceid].setGeometry(geom);
					featureHPvars['trackon' + rowdata.deviceid] = new ol.Feature({
						geometry: geom
					});
					featureHPvars['trackon' + rowdata.deviceid].setStyle([
						new ol.style.Style({
							stroke:new ol.style.Stroke({
								color:'#0069d9',
								width:2
							})
						})
					]);
					featureHPvars['trackon' + rowdata.deviceid].setId("trackonhproute");
					trackonHPSourcevars.addFeature(featureHPvars['trackon' + rowdata.deviceid]);
				}
			} else {
				addMarker(rowdata,layer);
			}
		}
		
		
		
		function plottrack()
		{
			var fromDate = $.trim($("#map_start_date").val());
			var toDate = $.trim($("#map_end_date").val());
			var deviceId = $.trim($("#map_device_id").val());
			var fromtime = $.trim($("#map_start_time").val());
			var totime = $.trim($("#map_end_time").val());
			
			
			$.ajax({
				method: "POST",
				dataType: "json",
				url: BASEURL + "traxreport/getdevicecoordinates",
				data: {fromdate: fromDate+' '+fromtime,todate: toDate+' '+totime, deviceid: deviceId},
				beforeSend: function () {
					 map.render();
					 map.updateSize();
				}
			}).done(function (datarecvd) {					
				var data = datarecvd.getcoordinates;    
				var historySummary =  datarecvd.history_summary; 
				var historyDetails = datarecvd.history_details;
				var respObjpole = datarecvd.getpoledata;
				var dataLengthpole = Object.keys(respObjpole).length;
				var respObjpoleline = datarecvd.getpolelinedata;
				var dataLengthpoleline = Object.keys(respObjpoleline).length;
			   
				var coords = [];
				//console.log(data.length)
				if (data != null && data.length > 0) {
					var dataLEN = data.length;
					globalData = data;
					globalDataLen = dataLEN;
					var startMarker = {};
					var endMarker = {};
					for (var i = 0; i < dataLEN; i++) {
						if (i == 0) {
							
							map.getView().setCenter(ol.proj.transform([parseFloat(data[i].longitude), parseFloat(data[i].latitude)], 'EPSG:4326', 'EPSG:3857'));
							map.getView().setZoom(11);
							startMarker = {deviceid:data[i].deviceid,longitude:data[i].longitude,latitude:data[i].latitude,faetureid:data[i].positionalid};
						}
						
						
							endMarker = {deviceid:data[i].deviceid,longitude:data[i].longitude,latitude:data[i].latitude,faetureid:data[i].positionalid};
						
						
						coords.push([parseFloat(data[i].longitude), parseFloat(data[i].latitude)]);
						var csvRow = [];
						csvRow.push(parseFloat(data[i].longitude).toFixed(6),parseFloat(data[i].latitude).toFixed(6),data[i].batterystats,data[i].currentdate,data[i].currenttime,parseFloat(data[i].trakerspeed).toFixed(3));
						csvDownload.push(csvRow);                            
					}
	
					var lineString = new ol.geom.LineString(coords);
					// transform to EPSG:3857
					lineString.transform('EPSG:4326', 'EPSG:3857');
					var feature = new ol.Feature({
						geometry: lineString,
						name: 'Line'
					});
	
					var lineStyle = new ol.style.Style({
						stroke: new ol.style.Stroke({
							color: '#ea303d',
							width: 2
						})
					});
					sourcehp = new ol.source.Vector({
						features: [feature]
					});
					//sourcehp = new ol.source.Vector({});
					vectorHP = new ol.layer.Vector({
						source: sourcehp,
						style: [lineStyle]
					});
					// vector.set('name', 'history');
					map.addLayer(vectorHP);
					// history track start						
					latlonHPvars = [];
					latlonHPvars.push([parseFloat(
									data[0].longitude),parseFloat(
									parseFloat(data[0].latitude))]);										
									
					var geom = new ol.geom.LineString(latlonHPvars);
					geom.transform('EPSG:4326', 'EPSG:3857');
					featureHPvars['trackon' + data[0].deviceid] = new ol.Feature({
					geometry: geom
					});
					featureHPvars['trackon' + data[0].deviceid].setStyle([
						new ol.style.Style({
							stroke: new ol.style.Stroke({
								color: '#0069d9',
								width: 2
							})
						})
					]);
					featureHPvars['trackon' + data[0].deviceid].setId("trackonhproute");
					trackonHPSourcevars.addFeature(featureHPvars['trackon' + data[0].deviceid]);
					// history track end
					var extent = vectorHP.getSource().getExtent();
					map.getView().fit(extent, map.getSize());
					// var coordLength = coords.length;
					 
				}
				else {
					alert('Please check searching inputs.');
				}
				/*if(historyDetails != null && Object.keys(historyDetails).length > 0){
					 var detailsTable = "";
					  var fields = [];
					 for(var i in historyDetails){
						 var refname = (historyDetails[i].refname == null)?'':historyDetails[i].refname;
						 var evtList = historyDetails[i].event_list;
								
						 if(evtList.indexOf("STOP Du.") !== -1){
							 evtList = evtList.split("STOP Du.(");
							 evtList = evtList[1].split(")");
							 
							 addMarker({deviceid:historyDetails[i].deviceid,longitude:historyDetails[i].longitude,latitude:historyDetails[i].latitude,faetureid:historyDetails[i].faetureid,stoptime:evtList[0]},'history_stop');
						 }
					 }                        
				}*/
				addMarker(startMarker,'history_start');
				addMarker(endMarker,'history_end');
			}).fail(function () {
				alert('Failed to fetch');
			});
		}
		
		
		function get_fields(fields) {
			for (var i in fields) {		
				var format = new ol.format.WKT();
				var field_polygon = format.readFeature(fields[i][0]);
				// transform... this line below is NOT required when mapping polygons retrieved from the API
				field_polygon.getGeometry().transform('EPSG:4326', 'EPSG:3857');
				// Add field id as id of feature for access later
				field_polygon.setId(i);        
				field_polygon.set('description', fields[i][1]);
			   field_polygon.setStyle(styleFunction);
			  //console.log(field_polygon)
				sourcehp.addFeature(field_polygon);
			}
			var extent = sourcehp.getExtent();
			map.getView().fit(extent, map.getSize());
		}
		
		
		
		
	
	function submit_form(){
		var device_id = $("#device_id").val();
		var dt = $("#stdt").val();
		
		/*if(device_id == '')
		{
			alert('Please select device');
			return false;
		}*/
		if(dt == '')
		{
			alert('Please enter date');
			return false;
		}
	}
	
	function submitFRM(url){
		var prevURL = document.frmsearch.action;
		document.frmsearch.action = url;
		document.frmsearch.submit();
		document.frmsearch.action = prevURL;
	}
	
	function pdfSubmit(){
        if($("#device_id").val()!="" && $("#dt").val()!=""){
            submitFRM(BASEURL + 'traxreport/activitysummeryreportpdf');
        }else{
           alert('All search fields are required');
		   return false;
        }
    }
	
	function excelSubmit(){
        if($("#dt").val()!=""){
            submitFRM(BASEURL + 'traxreport/activitysummeryreportexcel');
        }else{
           alert('All search fields are required');
		   return false;
        }
    }
	function plotnewtrack(devid,start_date,start_time,end_date,end_time)
	{
		$("#map_start_date").val(start_date);
		$("#map_end_date").val(end_date);
		$("#map_device_id").val(devid);
		$("#map_start_time").val(start_time);
		$("#map_end_time").val(end_time);
		removeAllMarkers();
		plottrack();
	}
	function populateSection(pwayid) {
		$.ajax({
			url: '<?php echo site_url('traxreport/getUser') ?>',
			data: {
				"user_id": pwayid,
				"pwi_id": '<?php echo $pwi_id ? $pwi_id : '' ?>'
			},
			type: 'POST',
			success: function(data) {
				if(data=='		') {
					var data1 = '<option value="">Select An Option</option><option value="All">All</option>';
					$("#user").html(data1);
				} else if(data!='') {
					$("#user").empty();
					$("#user").html(data);
					$('#user').val('<?php echo $pwi_id ?>');
				} 
				
			}
		});
	}

</script>