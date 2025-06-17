<?php

	$session = session();
	$sessdata = $session->get('login_sess_data');

	$client = \Config\Services::curlrequest();
	$databaseConfig = config('Database');
	$dbName = $databaseConfig->default['database'];
	
?>
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.12.1/ol.css" type="text/css"> -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">
<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/animate.css">
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<link href="<?php echo base_url() ?>assets/css/chosen.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ?>assets/css/ol3gm.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo base_url() ?>assets/js/BootSideMenu.js"></script>
<script src="<?php echo base_url() ?>assets/js/chosen.jquery.min.js" type="text/javascript"></script>

<style>
    .map, #geofencingmap {
        height: 100%;
        width: 100%;
    }

    #filter_container{
        position: absolute;
        z-index: 1;
        /*        right: 36%;
                width: 20%;*/
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
		right: 25em;
		top: 83px;
		display:none;
		min-height: 1.7em;
    }
	#showplacesearch{
        position: absolute;
        padding: 2px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        opacity: 0.7;
        white-space: nowrap;
        font: 10pt sans-serif;
        width: 165px;
        z-index: 1;
        text-align: center;
		right: 27em;
		top: 82px;
		display:none;
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
		width: 50em;
	}
	#calltab {
		font-size: 11px;
	}
	#alerttab {
		font-size: 11px;
	}
	#sosstab {
		font-size: 11px;
	}
	
	.myInput {
		width: 100%;
		font-size: 11px;
		padding: 3px 3px 3px 3px;
		border: 1px solid #ddd;
		margin-bottom: 10px;
		margin-top: 10px;
	}
	
	#exTab3 {
    box-shadow: 5px 3px 14px rgba(0, 0, 0, 0.176);
    border: 1px solid rgba(0, 0, 0, 0.15);
	
	}
	#test {z-index:9;}	
	#showactivecount{
        position: absolute;
        padding-top: 10px;
        background: rgba(2, 141, 48);
        color: white;
        opacity: 0.7;
        white-space: nowrap;
        font: 10pt sans-serif;
        width: 2.7em;
        z-index: 1;
        text-align: center;
		right: 21em;
		top: 77px;
		display:block;
		height: 2.7em;
		cursor: pointer;
		border-radius: 50%;
    }
	#showinactivecount{
        position: absolute;
        padding-top: 10px;
        background: rgba(212, 7, 7);
        color: white;
        opacity: 0.7;
        white-space: nowrap;
        font: 10pt sans-serif;
        width: 2.7em;
        z-index: 1;
        text-align: center;
		right: 18em;
		top: 77px;
		display:block;
		height: 2.7em;
		cursor: pointer;
		border-radius: 50%;
    }
	.zoomtoextentdiv{
		right: 3.5em;
		top: 5.64em;
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
	.th_background{
		background: rgb(71, 63, 63) !important;
	}

	.myInput {
		width: 100%;
		font-size: 11px;
		padding: 5px;
		border: 1px solid #ddd;
		margin-bottom: 10px;
		margin-top: 10px;
	}
	
	#exTab3 {
    	box-shadow: none;
    	border: 0;
		padding-left: 0;
		padding-right: 0;
		overflow-x: hidden;
	}
	#exTab3 ul.nav-pills {
		background-color: #adf;
	}
	#exTab3 .nav-pills > li > a {
		color: #376a9c;
    	background-color: #adf;
	}
	#exTab3 .nav-pills > li > a.active {
		background-color: #3879BB;
    	border: 1px solid #3879BB;
    	color: #fff;
    	line-height: 46px;
	}
	#exTab3 .tab-content {
    	padding: 5px;
	}
	#exTab3 .tab-content .collapse {
		background: #f8f8f8;
		padding: 0 4px;
	}
	#exTab3 .product-listing .panel-group .panel-heading .panel-title a {
		background-color: #ffd7eb;
	}
	#test {z-index:9;}	
	#showactivecount{
        position: absolute;
        padding-top: 10px;
        background: rgba(2, 141, 48);
        color: white;
        opacity: 0.7;
        white-space: nowrap;
        font: 10pt sans-serif;
        width: 2.7em;
        z-index: 1;
        text-align: center;
		right: 21em;
		top: 77px;
		display:block;
		height: 2.7em;
		cursor: pointer;
		border-radius: 50%;
    }
	#showinactivecount{
        position: absolute;
        padding-top: 10px;
        background: rgba(212, 7, 7);
        color: white;
        opacity: 0.7;
        white-space: nowrap;
        font: 10pt sans-serif;
        width: 2.7em;
        z-index: 1;
        text-align: center;
		right: 18em;
		top: 77px;
		display:block;
		height: 2.7em;
		cursor: pointer;
		border-radius: 50%;
    }
	.zoomtoextentdiv{
		right: 3.5em;
		top: 5.64em;
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
	.th_background{
		background: rgb(71, 63, 63) !important;
	}

	#popoverContent {
		box-shadow: 0 0 15px 3px #728292;
	}
	.sapopup-header .sapopup-title {
		padding: 11px 10px;
		background: #ad2;
	}
	.sapopup-header .nav .tab a {
		color: #376a9c;
    	background-color: #adf;
	}
	.sapopup-header .nav .tab a.active {
		background-color: #3879BB;
    	border: 1px solid #3879BB;
    	color: #fff;
	}
	.sapopup-body .table th {
		background: #ffd7eb;
    	color: #333;
    	border-color: #dcb4c8;
	}
	.sapopup-body .table td {
		border-color: #ccc;
	}
	.sapopup-body .table tr:first-child th, .sapopup-body .table tr:first-child td {
		border-top: 0;
	}
	.eventUL li {
		background: #ffd7eb;
    	color: #333;
		font-weight: bold;
	}
	.fleft .fs-wrap {
		width: 97%;
	}
	#map {
		width: 100%;
		height: 500px;
	}
</style>
<!-- The line below is only needed for old environments like Internet Explorer and Android 4.x -->

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.12.1/ol.js" type="text/javascript"></script> -->
<script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap-notify.min.js"></script>

<div class="container-fluid" style="height:calc(100vh - 75px - 75px);">
    <div id="map" class="map" style="height:100%;"> 
		<div id="doublerightpanel" class="collapse width">
			<div class="rightboxcontentarea">
			<div class="clearfix"></div>
			<div id="testleft">
				<div class="list-group">

					<div id="exTab3" class="container padnone">	
						<ul class="nav nav-pills">
							<li style="width: 50%;">
								<a href="#Objectltab" data-toggle="tab" class="active" style="display:block;padding: 0px 12px;"><i class="fa fa-podcast" aria-hidden="true"></i> Object</a>
							</li>
							<li style="width: 50%;">
								<a href="#Playbacktab" data-toggle="tab" style="display:block;padding: 0px 12px;"><i class="fa fa-bell" aria-hidden="true"></i> Playback</a>
							</li>
							<!--<li style="width: 35%;">
								<a href="#ReportSummerytab" data-toggle="tab" style="display:block;padding: 0px 12px;"><i class="fa fa-area-chart" aria-hidden="true"></i> Summary</a>
							</li>-->
                            <li class="backbtn">
                            	<a href="javascript:void(0);" id="cancelallinteractionleft"><i class="fa fa-arrow-left glyphicon-chevron-left" aria-hidden="true"></i></a>
                            </li>
						</ul>

						<div class="tab-content clearfix">
							<div id="Objectltab" class="tab-pane active">
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										
										
										<!--<div id="doublerightpanelcontent"></div>-->
										<div class="clearfix"></div>
									 <div id="accordion" role="tablist" aria-multiselectable="true">
			  <div class="rbccard">
				<div class="" role="tab" id="headingOne">
				  <h5 class="mb-0">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					  POI
					</a>
					<i class="fa fa-plus" aria-hidden="true" onclick="poiadd()"></i>
					<span class="bubble" style="display:none;"><span>Click on the map to create</span></span>
				  </h5>
				</div>

				<div id="collapseOne" class="collapse" role="tabpanel" aria-labelledby="headingOne">
				  <div class="card-block">
					<ul id="poilist"></ul>
				  </div>
				</div>
			  </div>
			  <div class="rbccard">
				<div class="" role="tab" id="headingTwo">
				  <h5 class="mb-0">
					<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
					  Route
					</a>
					<i class="fa fa-plus" aria-hidden="true" onclick="routeadd()"></i>
					<span class="bubble" style="display:none;"><span>Click on the map to create</span></span>
				  </h5>
				</div>
				<div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
				  <div class="card-block">
					<ul id="routelist"></ul>
				  </div>
				</div>
			  </div>
			  <div class="rbccard">
				<div class="" role="tab" id="headingThree">
				  <h5 class="mb-0">
					<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
					  Geofence
					</a>
					 <i class="fa fa-plus" aria-hidden="true" onclick="geofenceadd()"></i>
					 <span class="bubble" style="display:none;"><span>Click on the map to create</span></span>
				  </h5>
				</div>
				<div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">
				  <div class="card-block">
					<ul id="geofencelist"></ul>
				  </div>
				</div>
			  </div>
			</div>  
										
									</div>
								</div>
							</div>
							<div id="Playbacktab" class="tab-pane">
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									   
										<div class="form-horizontal">
										
											<div class="form-group" style="overflow: visible; min-height: 40px;">
												<div  class="col-lg-3 col-md-3 col-sm-3 col-xs-3 fleft padnone">
													<label>Device:</label>
												</div>
												<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 fleft padnone">
													<select name="deviceid" id="deviceid" class="select_mfc">
														<option value="">Select</option>
														<?php
														if (!empty($devicedropdown)) {
															foreach ($devicedropdown as $key => $value) {
																?>
																<option value="<?php echo $value->did; ?>"><?php echo $value->serial_no.' - '.$value->device_name; ?></option>
																<?php
															}
														}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<div  class="col-lg-3 col-md-3 col-sm-3 col-xs-3 fleft padnone">
													<label>From Date:</label>
												</div>
												<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 fleft padnone">
													<input type="text" value="" name="fromdate" readonly onchange="gettodate(this.value)"/>
												</div>
											</div>
											<div class="form-group">
												<div  class="col-lg-3 col-md-3 col-sm-3 col-xs-3 fleft padnone">
													<label>From Time:</label>
												</div>
												<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 fleft padnone">
													<input type="text" value="" name="fromtime" placeholder="HH:MM"/>
												</div>
											</div>
											<div class="form-group">
												<div  class="col-lg-3 col-md-3 col-sm-3 col-xs-3 fleft padnone">
													<label>To Date:</label>
												</div>
												<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 fleft padnone">
													<input type="text" value="" name="todate" readonly/>
												</div>
											</div>
											<div class="form-group">
												<div  class="col-lg-3 col-md-3 col-sm-3 col-xs-3 fleft padnone">
													<label>To Time:</label>
												</div>
												<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 fleft padnone">
													<input type="text" value="" name="totime" placeholder="HH:MM"/>
												</div>
											</div>
											<div class="form-group">
												<div  class="col-lg-3 col-md-3 col-sm-3 col-xs-3 fleft padnone">
													<label>Track On:</label>
												</div>
												<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 fleft padnone">
													<input id="track_to_road" name="track_to_road" type="checkbox" value="true" style="width: auto;"> 
												</div>
											</div>
											<div class="form-group" id="snap_to_road_div" style="display:none;">
												<div  class="col-lg-8 col-md- col-sm-8 col-xs-8 fleft padnone">
													<label>Snap To Road:</label>
												</div>
												<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fleft padnone">
													<input id="snap_to_road" name="snap_to_road" type="checkbox" value="true" style="width: auto;"> 
												</div>
											</div>
											
										 <div class="input-group">
											<button class="btn btn-primary btn-block" type="button" id="showhistory">Show history</button>
											
											<!--<div class="bs-example">
											   <div class="dropdown">
													<a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="fa fa-download" aria-hidden="true"></i>
													</a>
													<ul class="dropdown-menu dm">
														<li><a href="javascript:" onclick="generateKML()">Export to KML</a></li>
														<li><a href="javascript:" onclick="generateCSV()">Export to CSV</a></li>
													</ul>
												</div>
											</div>-->
											<button class="btn btn-default closeplaybk" type="button" onclick="resetHistory()" style="float: right;">
												<i class="fa fa-times" aria-hidden="true"></i>
											</button>                    
										</div>    
												<div class="histroymsg text-danger" style="padding: 5px;"></div>
										<div class="form-group" id="history_controls">
											<div  class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padnone" style="overflow: hidden;">
												<select style="float: left; width: 20%; margin: 7px 8px;display:none;" id="playspeed" onchange="increasePlaySpeed(this.value)">
													<option value="1">1X</option>
													<option value="2">2X</option>
													<option value="3">3X</option>
													<option value="4">4X</option>
												</select>
												<div class="player text-center" style="float: left; margin: 7px 0px; width: 72%;">
													
													<button type="button" id="forwardbtn" class="btn" onclick='fnForward()'>
													  <i class="fa fa-forward"></i>
													</button>
													<button type="button" id="stopbtn" class="btn" onclick='fnStop()'>                                                                       <i class="fa fa-stop"></i>
													</button>
													<button type="button" class="btn" onclick="fnPlay()" id="playbtn">                                                                       <i class="fa fa-play"></i>
													</button>
													<button type="button" class="btn" onclick="fnPause()" style="display:none;" id="pausebtn">                                                                       
														<i class="fa fa-pause"></i>
													</button>                                                        
													 <button type="button"  class="btn" onclick='fnBack()' id="backbtn">
													  <i class="fa fa-backward"></i>
													</button>
													<button type="button" class="btn" onclick='resetPointer()' id="resetbtn">
													  <i class="fa fa-refresh"></i>
													</button>
													
												  </div>                                                    
												
											</div>
										</div>
										<div class="form-group">
											<div  class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padnone" style="overflow: hidden;">
												<label>Speed: <strong><span id="displayspeed"></span> Km/h</strong></label>
											</div>
											<div  class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padnone" style="overflow: hidden;">
												<label>Battery Status: <strong><span id="displaybatterystatus"></span></strong></label>
											</div>
											<div  class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padnone" style="overflow: hidden;">
												<label>Date-Time: <strong><span id="displaydttime"></span></strong></label>
											</div>
										</div>
										<div class="clearfix"></div>
										
										<div id="accordion" role="tablist" aria-multiselectable="true">
											  <div class="rbccard">
												<div class="" role="tab" id="headingTwo">
												  <h5 class="mb-0">
													<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo1" aria-expanded="false" aria-controls="collapseTwo1">
													  Summary
													</a>
												  </h5>
												</div>
												<div id="collapseTwo1" class="collapse" role="tabpanel" aria-labelledby="headingTwo1">
													<div class="card-block">
														<table class="table sumtab">
															<tbody id="hsummary">
																
															</tbody>
														</table></div>
													</div>
												</div>
											  </div>
											  <div class="rbccard" style="display:none;">
												<div class="" role="tab" id="headingThree">
												  <h5 class="mb-0">
													<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree2" aria-expanded="false" aria-controls="collapseThree2">
													  Details
													</a>
												  </h5>
												</div>
												<div id="collapseThree2" class="collapse" role="tabpanel" aria-labelledby="headingThree2" >
												  <div class="card-block">
													<table class="table sumtab">
													  <thead>
															<tr>
														  <td><i class="fa fa-flag" title="Action"></i></td>
														  <td><i class="fa fa-calendar" title="Date"></i></td>
														  <td><i class="fa fa-clock-o" title="Duration"></i></td>
														</tr>
														  </thead>
														  <tbody id="hdetails">
												
														  </tbody>
													</table>
												  </div>
												</div>
											  </div>
										</div>	
									</div>									   
								</div>
							</div> 
							<!--<div id="ReportSummerytab" class="tab-pane">
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="form-horizontal">
											<div class="form-group" style="min-height: 15em;">
											<table class="table table-condensed">
												<thead>
													<th style="font-size: 0.8em;">Time</th>
													<th style="font-size: 0.8em;">Distance Patrolled</th>
													<th style="font-size: 0.8em;">Active Devices</th>
													<th style="font-size: 0.8em;">Inactive Devices</th>
												</thead>
												<tbody id="summerytb">
													<td colspan="4">Loading..</td>
												</tbody>
											</table>
											</div>
										</div>
									</div>
								</div>
							</div>-->
						</div>                    
							
					</div>
				</div>

			</div>
			</div>
		</div>
        
		<div id="showdistance"></div>
		<div id="showruler"></div>
		<a href="<?php echo site_url('controlcentre/ondutydevice');?>"><div id="showactivecount" title="On Duty Device">0</div></a>
		<a href="<?php echo site_url('controlcentre/offdutydevice');?>"><div id="showinactivecount" title="Off Duty Device">0</div></a>
		<div id="showplacesearch"><input type="text" id="pac-input" placeholder="Place Search.." style="width: 159px;"></div>
        <select id="layer-select" style="position: absolute;right: 80px;top: 82px;z-index: 1;">
            <option value="Road">Road (static)</option>
            <option value="RoadOnDemand">Road (dynamic)</option>
			<option value="Aerial">Aerial</option>
            <option value="AerialWithLabels">Aerial with labels</option>
			<option value="GoogleSatelite">Google Satelite</option>
			<option value="GoogleStreetmaps" selected>Google Streetmaps</option>		
        </select>
		<div class="zoomtoextentdiv" title="Fit To Extent"><button type="button" onclick="zoomextent()" id="zoomtoextentbtn" disabled>E</button></div>

        <div id="popup" style="display: none;">  
            <div class="tabbable hide" id="popoverContent">
                <div class="sapopup-header">
                    <div class="sapopup-title"><span id="popupheader"></span></div>
                    <ul class="nav nav-tabs nav-default">
                        <li class="tab"><a href="#tab1" data-toggle="tab" data-tooltip="tooltip" title="Details"><i class="fa fa-info" aria-hidden="true"></i></a></li>
                        <li class="tab"><a href="#tab2" data-toggle="tab" data-tooltip="tooltip" title="Alerts"><i class="fa fa-bell" aria-hidden="true"></i></a></li>
                        <li class="tab"><a href="#tab3" data-toggle="tab" data-tooltip="tooltip" title="SOS"><i class="fa fa-bullhorn" aria-hidden="true"></i></a></li>
                        <li class="tab"><a href="#tab4" data-toggle="tab" data-tooltip="tooltip" title="Calls"><i class="fa fa-phone" aria-hidden="true"></i></a></li>
                        <li class="tab"><a href="#tab5" data-toggle="tab" data-tooltip="tooltip" title="Actions"><i class="fa fa-bars" aria-hidden="true"></i></a></li>
                        <li class="tab"><a href="javascript:void(0)" id="closemappopup" style="background-color: #bd370f; color: #fff;"><i class="fa fa-times" aria-hidden="true" ></i></a></li>
                    </ul>
                </div>
                <div class="sapopup-body">
                    <div class="tab-content" id="changediv">
                        <div class="tab-pane" id="tab1">

<!--                            <table class="table table-condensed"><tbody><tr><th>Address:</th><td><span data-device="address" data-lat="20.751595" data-lng="95.91143">Pyawbwe, Yamethin, Mandalay, Myanmar</span></td></tr><tr><th>Time:</th><td><span data-device="time">2017-11-23 2017-11-23</span></td></tr><tr><th>Stop duration:</th><td><span data-device="stop_duration">0h</span></td></tr><tr><th>Ignition:</th><td>On</td></tr><tr><th>Ignition:</th><td>On</td></tr><tr><th>Ignition:</th><td>On</td></tr><tr><th>Ignition:</th><td>On</td></tr><tr><th>Ignition:</th><td>On</td></tr><tr><th>Ignition:</th><td>On</td></tr></tbody></table>-->

                        </div>
                        <div class="tab-pane" id="tab2"></div>
                        <div class="tab-pane" id="tab3"></div>
                        <div class="tab-pane" id="tab4"></div>
                        <div class="tab-pane" id="tab5"></div>
                    </div>
                    <div onclick="changec()" style="text-align:center;display:block;padding: 2px 5px;cursor: pointer;"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></div>
                </div>
            </div>
        </div> 

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
        
        <div id="myposition"></div>
    </div>

    <div class="modal fade" id="geofencingmodal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ceate Geo-fencing<span id="deviceserialGFModal"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="closethismodal('#geofencingmodal')">&times;</button>

                </div>
                <div class="modal-body">
                    <div id="geofencingmap">

                    </div>            
                </div>
                <div class="modal-footer">           
        <!--            <select id="geofencingtype" class="form-control pull-left" style="width:7em">
                        <option value="Point">Point</option>
                        <option value="LineString">LineString</option>
                        <option value="Polygon">Polygon</option>
                        <option value="Circle">Circle</option>
                        <option value="None" selected>None</option>
                        <option value="Reset">Reset</option>
                    </select>            -->
                    <span style="position: absolute;left: 10px;">
                        <span>Existing Geofences</span>
                        <select name="existingfences" id="existingfences" class="form-control" style="font-size:14px;">
                            <option value="">Select</option>                           
                        </select>
                    </span>

                    <button type="button" class="btn btn-default" style="display: none;" id="clearGeofencingMarks">Clear</button>
                    <button type="button" class="btn btn-danger" style="display: none;" id="deleteGeofencingMarks">Delete</button>
                    <!--          <button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>-->
                </div>
            </div>
        </div>
    </div>    

    <div class="modal fade" id="alertsddgeoexistingSave" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attach Fencing</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="email">Alert On <span class="text-danger">*</span></label>
                        <select name="alertsddgeoexisting" id="alertsddgeoexisting" class="form-control">
                            <option value="">Select</option>
                            <?php
                            if (!empty($alertdropdown)) {
                                foreach ($alertdropdown as $row) {
                                    ?>
                                    <option value="<?php echo $row->id ?>"><?php echo $row->description ?></option>
                                    <?php
                                }
                            }
                            ?>                                
                        </select>
                    </div> 
                </div> 
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-primary" onclick="cloneGeofence()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="saveGeofencing" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attach Fencing</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                    <form autocomplete="off" onsubmit="return false;">
                        <div class="form-group">
                            <label for="email">Name (200 Characters)<span class="text-danger">*</span></label>
                            <input type="text" onpaste="return false;" onkeypress="checkCharsLeft('200',this.value,'#showCharLeft')" class="form-control" id="description" name="description" maxlength="200"/>
                        </div> 
                        <div class="form-group" id="geofencemodedevice">
                            <label for="email">Alert On <span class="text-danger">*</span></label>
                            <select name="alertsdd" id="alertsdd" class="form-control">
                                <option value="">Select</option>
                                <?php
                                if (!empty($alertdropdown)) {
                                    foreach ($alertdropdown as $row) {
                                        ?>
                                        <option value="<?php echo $row->id ?>"><?php echo $row->description ?></option>
                                        <?php
                                    }
                                }
                                ?>                                
                            </select>
                        </div>   
                        <div class="form-group">
                            <label for="email">Buffer</label>
                            <input type="text" onpaste="return false;" onkeypress="return checkNumberTypeField(event)" class="form-control" id="bufferarea" name="description" maxlength="3"/>
                        </div>
                    </form>
                    <span id="showCharLeft" style="color:#3879bb;font-style: italic;"></span>
                </div>
                <div class="modal-footer">           
                    <span style="left: 0;position: absolute;margin-left: 10px;color:#a93434;" id="showmsgSavemodal"></span>
                    <button type="button" class="btn btn-primary" id="saveGeofencingData">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="changeicon" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Personalize Device Icon</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                    <form autocomplete="off" onsubmit="return false;">
                        <div class="form-group">
                            <label for="email">Icon Set</label>
                            <select name="iconset" id="iconset" class="form-control">
                                <option value="">Default</option>
                                <option value="1">Set1</option>
                            </select>
                        </div>
                        <div class="form-group" id="show-icons">

                        </div>      
                    </form>
                    <span id="showCharLeft" style="color:#3879bb;font-style: italic;"></span>
                </div>
                <div class="modal-footer">           
                    <button type="button" class="btn btn-primary" id="updateicon">Update Icon</button>
                </div>
            </div>
        </div>
    </div>    
    <div class="modal fade" id="configurealert" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header newsahead">
                    <h5 class="modal-title" id="alertconfiguretitle">Set Alert</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body newcw">
                    <!--<form autocomplete="off" onsubmit="return false;">
                    	<div class="form-group">
                        	<label for="email">Alert Type</label>
                        <select name="alertid" id="alertid" class="form-control">
                            <option value="">Set Alert</option>
						<?php
                        if (!empty($alertdd)) {
                            foreach ($alertdd as $key => $value) {
                                ?> 
                                   <option value="<?php echo $value->id ?>"><?php echo $value->description ?></option>
                                <?php
                            }
                        }
                        ?>
                                </select>
                                <input type="hidden" id="alertdeviceid">
                            </div>
    
                            <div id="nomovement_alert" class="alertforms">
                                <div class="form-group">                                  
                                    <label>Select Time (in sec)<span class="text-danger">*</span></label>
                                    <input type="text" name="timerangnomovement" id="timerangnomovement"  class="form-control" onkeypress="return checkNumberTypeField(event)" maxlength="4"/>
                                </div>
                                <div class="form-group">                                  
                                    <label style="margin-right:1em">Alert On/Off</label>
                                    <label class="radio-inline" style="margin-right: .5em;">
                                        <input type="radio" name="timerangnomovementonoff" value="1" checked="checked">On
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="timerangnomovementonoff" value="2">Off
                                    </label>                               
                                </div>
                            </div>      
                        </form>-->
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-xs-12 col-lg-12">
                            <div id="alerttabs">
                                <ul>                                   
                                    <li><a href="#tabs-lb" style="font-size: 11px !important;">Low Battery</a></li>
									<li><a href="#tabs-ff" style="font-size: 11px !important;">Free Fall</a></li>
                                    <li><a href="#tabs-nm" style="font-size: 11px !important;">No Movement</a></li>
                                    <li><a href="#tabs-sp" style="font-size: 11px !important;">Over speed</a></li>
                                    <li><a href="#tabs-nodata" style="font-size: 11px !important;">No Data</a></li>
                                    <li><a href="#tabs-prox" style="font-size: 11px !important;">Zone-In</a></li>
                                    <li><a href="#tabs-offroute" style="font-size: 11px !important;">Zone-Out</a></li>
									<li><a href="#tabs-routedeviation" style="font-size: 11px !important;">Route Deviation</a></li>									
                                    <li><a href="#tabs-notifi" style="font-size: 11px !important;">Notify To</a></li>
                                </ul>                                
                                <div id="tabs-notifi" class="alerttabs">
                                    
                                    <div id="notifyconfig" class="alertforms col-sm-12 col-xs-12 col-md-12 col-lg-12 padnone">
                                        <div class="form-group">                                            
                                            <div class="row">                                               
                                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" <?php if(isset($sessdata['neotificationemail']) && $sessdata['neotificationemail'] == 'N'){ ?>style="display:none;"<?php } ?>> 
                                                    <label>Email Notification Receivers</label>
                                                    <textarea name="notifyemail" class="form-control"></textarea>
                                                </div>
                                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" <?php if(isset($sessdata['notificationsms']) && $sessdata['notificationsms'] == 'N'){ ?>style="display:none;"<?php } ?>> 
                                                    <label>Mobile Notification Receivers</label>
                                                    <textarea name="notifyphone" class="form-control"></textarea>
                                                </div>
                                            </div>
											<label style="font-size: 12px;font-style: italic;color: blue;">** Enter comma separated Email</label>                                            
                                        </div>
                                    </div>      
                                    
                                </div>
                                <div id="tabs-lb" class="alerttabs newcw">
                                    
                                    <div id="lowbattery_alert" class="alertforms col-sm-12 col-xs-12 col-md-12 col-lg-12 padnone">
                                        <div class="form-group">                                  
                                            <label>Battery % For Alert<span class="text-danger">*</span></label>
<!--                                            <input type="text" name="lowbatteryval" id="lowbatteryval"  class="form-control" onkeypress="return checkNumberTypeField(event)" maxlength="4"/>-->
                                            <span class="form-control col-sm-12 col-xs-12 col-md-4">30</span>
                                        </div>
                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Alert On/Off</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="lowbatteryonoff" value="1" checked="checked">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="lowbatteryonoff" value="2" >Off
                                            </label>                               
                                        </div>  
                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Email Notification</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="lowbatterynotificationonoffemail" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="lowbatterynotificationonoffemail" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                        <div class="form-group" >                                  
                                            <label style="margin-right:1em">Mobile Notification</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="lowbatterynotificationonoffphone" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="lowbatterynotificationonoffphone" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                    </div>      
                                    
                                </div>
								<div id="tabs-ff" class="alerttabs newcw">
                                    
                                    <div id="freefall_alert" class="alertforms col-sm-12 col-xs-12 col-md-12 col-lg-12 padnone">
                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Alert On/Off</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="freefallonoff" value="1" checked="checked">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="freefallonoff" value="2" >Off
                                            </label>                               
                                        </div>  
                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Email Notification</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="freefallnotificationonoffemail" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="freefallnotificationonoffemail" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Mobile Notification</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="freefallnotificationonoffphone" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="freefallnotificationonoffphone" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                    </div>      
                                    
                                </div>
                                <div id="tabs-nm" class="alerttabs newcw">
                                    
                                    <div id="nomovement_alert" class="alertforms col-sm-12 col-xs-12 col-md-12 col-lg-12 padnone">
                                        <div class="form-group">                                  
                                            <label>Max idle time (HH:MM)<span class="text-danger">*</span></label>
                                            <input type="text" name="nomovementtimeval" id="nomovementtimeval"  class="form-control col-sm-12 col-xs-12 col-md-4" value="00:00:00"/>
                                        </div>
                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Alert On/Off</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="nomovementonoff" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="nomovementonoff" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Time</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="nomovementtime" value="1" checked="checked">All Day
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="nomovementtime"  value="2">Time Range
                                            </label>   
                                            <div id="nomovementfromtotime" style="display:none;">
                                                <div class="" style="display:inline-block;margin-right: 30px;">  
                                                    <label>From<span class="text-danger">*</span></label>
                                                    <input type="text" name="nomovementdaytimefrom" id="nomovementdaytimefrom"  class="tmpckr" readonly="readonly" style="height: 25px;"/>                                                    
                                                </div>
                                                <div class="" style="display:inline-block">                                  
                                                    <label>To<span class="text-danger">*</span></label>
                                                    <input type="text" name="nomovementdaytimeto" id="nomovementdaytimeto"  class="tmpckr" readonly="readonly" style="height: 25px;"/>                      
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Email Notification</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="nomovementnotificationonoffemail" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="nomovementnotificationonoffemail" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                        <div class="form-group" >                                  
                                            <label style="margin-right:1em">Mobile Notification</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="nomovementnotificationonoffphone" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="nomovementnotificationonoffphone" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                    </div>    
                                    
                                </div>
                                <div id="tabs-sp" class="alerttabs newcw">
                                    
                                    <div id="speed_alert" class="alertforms col-sm-12 col-xs-12 col-md-12 col-lg-12 padnone">
                                        <div class="form-group">                                  
                                            <label>Max Allowed Speed (Km/hr)<span class="text-danger">*</span></label>
                                            <input type="text" name="speedval" id="speedval"  class="form-control" onkeypress="return checkNumberTypeField(event)" maxlength="4" style="width:190px;"/>
                                        </div>

                                        <div class="form-group">                        
                                            <label style="margin-right:1em">Time</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="speedtime" value="1" checked="checked">All Day
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="speedtime"  value="2">Time Range
                                            </label>   
                                            <div id="speedfromtotime" style="display:none;">
                                                <div class="" style="display:inline-block;margin-right: 30px;">  
                                                    <label>From<span class="text-danger">*</span></label>
                                                    <input type="text" name="speeddayfromtime" id="speeddayfromtime"  class="tmpckr"  readonly="readonly" style="height: 25px;"/>                                                   
                                                </div>
                                                <div class="" style="display:inline-block">                                  
                                                    <label>To<span class="text-danger">*</span></label>
                                                    <input type="text" name="speeddaytotime" id="speeddaytotime"  class="tmpckr"  readonly="readonly" style="height: 25px;"/>
                                                </div>
                                            </div>  
                                        </div>  

                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Alert On/Off</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="speedonoff" value="1" >On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="speedonoff" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Email Notification</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="speednotificationonoffemail" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="speednotificationonoffemail" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                        <div class="form-group" >                                  
                                            <label style="margin-right:1em">Mobile Notification</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="speednotificationonoffphone" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="speednotificationonoffphone" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                    </div>  
                                    
                                </div>
                                <div id="tabs-nodata" class="alerttabs newcw">
                                    
                                    <div id="nodata_alert" class="alertforms col-sm-12 col-xs-12 col-md-12 col-lg-12 padnone">
                                        <div class="form-group">                                  
                                            <label>Duration (Sec)<span class="text-danger">*</span></label>
                                            <input type="text" name="nodataduration" id="nodataduration"  class="form-control" style="width:190px;" />
                                        </div>
                                        <div class="form-group">                        
                                            <label style="margin-right:1em">Time</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="nodatatime" value="1" checked="checked">All Day
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="nodatatime"  value="2">Time Range
                                            </label>   
                                            <div id="nodatafromtotime" style="display:none;">
                                                <div class="" style="display:inline-block;margin-right: 30px;">  
                                                    <label>From<span class="text-danger">*</span></label>
                                                    <input type="text" name="nodatafromtime" id="nodatafromtime"  class="tmpckr"  readonly="readonly" style="height: 25px;"/>                                                   
                                                </div>
                                                <div class="" style="display:inline-block">                                  
                                                    <label>To<span class="text-danger">*</span></label>
                                                    <input type="text" name="nodatatotime" id="nodatatotime"  class="tmpckr"  readonly="readonly" style="height: 25px;"/>
                                                </div>
                                            </div>  
                                        </div>
                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Alert On/Off</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="nodataonoff" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="nodataonoff" value="2" checked="checked">Off
                                            </label>                               
                                        </div> 
                                        <div class="form-group">                                  
                                            <label style="margin-right:1em">Email Notification</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="nodatanotificationonoffemail" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="nodatanotificationonoffemail" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                        <div class="form-group" >                                  
                                            <label style="margin-right:1em">Mobile Notification</label>
                                            <label class="radio-inline" style="margin-right: .5em;">
                                                <input type="radio" name="nodatanotificationonoffphone" value="1">On
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="nodatanotificationonoffphone" value="2" checked="checked">Off
                                            </label>                               
                                        </div>
                                    </div>      
                                    
                                </div>
                                <div id="tabs-prox" class="alerttabs newcw">
                                    
                                    <div id="poi_alert" class="alertforms col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                        <ul class="nav nav-tabs" id="myTabsproxy">
                                            <li class="activealerts col-md-4">
                                                <a data-target="#proxpoi" data-toggle="tab" class="col-md-12">POI</a>
                                            </li>
                                            <li class="col-md-4" style="display:none;">
                                                <a data-target="#proxroute" data-toggle="tab">Route</a>
                                            </li>
                                            <li  class="col-md-4">
                                                <a data-target="#proxgeo" data-toggle="tab">Geofence</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <div class="tab-pane active" id="proxpoi">
                                                <div class="table-responsive">          
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="th_background">Name</th>
                                                                <th class="th_background">Check</th>
                                                                <th class="th_background">From</th>
                                                                <th class="th_background">To</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="proxpoitable">
                                                            <tr>
                                                                <td colspan="4">No Record Found</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="proxroute" style="display:none;">
                                                <div class="table-responsive">          
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="th_background">Name</th>
                                                                <th class="th_background">Check</th>
                                                                <th class="th_background">From</th>
                                                                <th class="th_background">To</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="proxroutetable">
                                                            <tr>
                                                                <td colspan="4">No Record Found</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="proxgeo">
                                                <div class="table-responsive">          
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="th_background">Name</th>
                                                                <th class="th_background">Check</th>
                                                                <th class="th_background">From</th>
                                                                <th class="th_background">To</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="proxgeotable">
                                                            <tr>
                                                                <td colspan="4">No Record Found</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
<!--                                        <table class="table table-condensed">
                                        <thead></thead>
                                    </table>-->

                                    </div>
                                    
                                </div>
								<div id="tabs-routedeviation" class="alerttabs newcw">
                                    
                                    <div id="poi_alert" class="alertforms col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                        <ul class="nav nav-tabs" id="myTabsRoutedeviation">
                                            <li class="col-md-4" style="display:none;">
                                                <a data-target="#routedeviationpoi" data-toggle="tab">POI</a>
                                            </li>
                                            <li class="activealerts col-md-4">
                                                <a data-target="#routedeviationroute" data-toggle="tab" class="col-md-12">Route</a>
                                            </li>
                                            <li  class="col-md-4" style="display:none;">
                                                <a data-target="#routedeviationgeo" data-toggle="tab">Geofence</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <div class="tab-pane" id="routedeviationpoi" style="display:none;">
                                                <div class="table-responsive">          
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="th_background">Name</th>
                                                                <th class="th_background">Check</th>
                                                                <th class="th_background">From</th>
                                                                <th class="th_background">To</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="routedeviationpoitable">
                                                            <tr>
                                                                <td colspan="4">No Record Found</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane active" id="routedeviationroute">
                                                <div class="table-responsive">          
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="th_background">Name</th>
                                                                <th class="th_background">Check</th>
                                                                <th class="th_background">From</th>
                                                                <th class="th_background">To</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="routedeviationroutetable">
                                                            <tr>
                                                                <td colspan="4">No Record Found</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="routedeviationgeo" style="display:none;">
                                                <div class="table-responsive">          
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="th_background">Name</th>
                                                                <th class="th_background">Check</th>
                                                                <th class="th_background">From</th>
                                                                <th class="th_background">To</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="routedeviationgeotable">
                                                            <tr>
                                                                <td colspan="4">No Record Found</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
<!--                                        <table class="table table-condensed">
                                        <thead></thead>
                                    </table>-->

                                    </div>
                                    
                                </div>
                                <div id="tabs-offroute" class="alerttabs newcw">
                                    
                                    <div id="poi_alert" class="alertforms col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                        <ul class="nav nav-tabs" id="myTabsoffroute">
                                            <li class="activealerts col-md-4">
                                                <a data-target="#offroutepoi" data-toggle="tab" class="col-md-12">POI</a>
                                            </li>
                                            <li class="col-md-4" style="display:none;">
                                                <a data-target="#offrouteroute" data-toggle="tab">Route</a>
                                            </li>
                                            <li  class="col-md-4">
                                                <a data-target="#offroutegeo" data-toggle="tab">Geofence</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <div class="tab-pane active" id="offroutepoi">
                                                <div class="table-responsive">          
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="th_background">Name</th>
                                                                <th class="th_background">Check</th>
                                                                <th class="th_background">From</th>
                                                                <th class="th_background">To</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="offroutepoitable">
                                                           <tr>
                                                                <td colspan="4">No Record Found</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="offrouteroute" style="display:none;">
                                                <div class="table-responsive">          
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="th_background">Name</th>
                                                                <th class="th_background">Check</th>
                                                                <th class="th_background">From</th>
                                                                <th class="th_background">To</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="offrouteroutetable">
                                                            <tr>
                                                                <td colspan="4">No Record Found</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="offroutegeo">
                                                <div class="table-responsive">          
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="th_background">Name</th>
                                                                <th class="th_background">Check</th>
                                                                <th class="th_background">From</th>
                                                                <th class="th_background">To</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="offroutegeotable">
                                                            <tr>
                                                                <td colspan="4">No Record Found</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
<!--                                        <table class="table table-condensed">
                                        <thead></thead>
                                    </table>-->

                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <span id="showCharLeft" style="color:#3879bb;font-style: italic;"></span>
                </div>
                <div class="modal-footer newcwfot">   
                    <span id="setalertmsg" style="color:#8c0909fa;"></span>
                    <button type="button" class="btn btn-primary" id="setalert" onclick="setAlert()">Set Alert</button>
                </div>
            </div>
        </div>
    </div>   
    <div class="modal fade" id="poimodal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create <span class="poimodalLevel"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="poimap">
                        <div id="showdistance"></div>
                        <div id="poidrawingmode">
                            <select id="poidrawingmodeselect">
                                <option value="Point">Point</option>
                                <!--                                <option value="Polygon">Polygon</option>-->
                                <!--                                <option value="Circle">Circle</option>                               -->
                            </select>
                        </div>
                    </div>            
                </div>
                <div class="modal-footer">           
        <!--            <select id="geofencingtype" class="form-control pull-left" style="width:7em">
                        <option value="Point">Point</option>
                        <option value="LineString">LineString</option>
                        <option value="Polygon">Polygon</option>
                        <option value="Circle">Circle</option>
                        <option value="None" selected>None</option>
                        <option value="Reset">Reset</option>
                    </select>            -->
<!--                    <span style="position: absolute;left: 10px;">
                        <span>Existing Geofences</span>
                        <select name="existingfences" id="existingfences" class="form-control" style="font-size:14px;">
                            <option value="">Select</option>                           
                        </select>
                    </span>-->

                    <button type="button" class="btn btn-default" style="display: none;" id="clearGeofencingMarks">Clear</button>
                    <button type="button" class="btn btn-danger" style="display: none;" id="deleteGeofencingMarks">Delete</button>
                    <!--          <button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>-->
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="savedatapoiandroute" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Save <span class="poimodalLevel"></span></h5>                   
                    <button type="button" class="close" data-dismiss="modal" onclick="discardPOI()">&times;</button>

                </div>
                <div class="modal-body">
                    <form autocomplete="off" onsubmit="return false;">
                        <div class="form-group">                                  
                            <label>Name<span class="text-danger">*</span></label>
                            <input type="text" name="poiroutename" id="poiroutename"  class="form-control" maxlength="100"/>
                        </div>
                        <div class="form-group bufferarea">                                  
                            <label>Buffer (in Mtrs)<span class="text-danger">*</span></label>
                            <input type="text" name="bufferarea" id="bufferarea"  class="form-control" onkeypress="return checkNumberTypeField(event)" maxlength="3"/>                            
                        </div>     
                    </form>
                    <span id="showCharLeft" style="color:#3879bb;font-style: italic;"></span>
                </div>
                <div class="modal-footer">           
                    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="discardPOI()">Cancel</button>
                    <button type="button" class="btn btn-primary" id="savepoiandroute" onclick="savePOIData()">Save</button>
                </div>
            </div>
        </div>
    </div>
	<div class="modal fade" id="updateroutebuffer" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update <span class="poimodalLevel"></span></h5>                   
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                    <form autocomplete="off" onsubmit="return false;">  
                        <input type="hidden" name="routeupdate_id" id="routeupdate_id"  class="form-control"/>
                        <div class="form-group bufferarea">                                  
                            <label>Buffer (in Mtrs)<span class="text-danger">*</span></label>
                            <input type="text" name="routeupdate_buffer" id="routeupdate_buffer"  class="form-control" onkeypress="return checkNumberTypeField(event)" maxlength="3"/>                            
                        </div>     
                    </form>
                    <span id="showCharLeft" style="color:#3879bb;font-style: italic;"></span>
                </div>
                <div class="modal-footer">           
                    <button type="button" class="btn btn-primary" id="savepoiandroute" onclick="updateRouteBuffer()">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div id="test">
        <div class="list-group">

            <div id="exTab3" class="container">	
                <ul class="nav nav-pills">
                    <li>
                        <a href="#devicestab" data-toggle="tab" class="active"><i class="fa fa-podcast" aria-hidden="true"></i> Objects</a>
                    </li>
                    <li>
                        <a href="#alertstab" data-toggle="tab"><i class="fa fa-bell" aria-hidden="true"></i> Alerts</a>
                    </li>
                    <li>
                        <a href="#sostab" data-toggle="tab"><i class="fa fa-bullhorn" aria-hidden="true"></i> SOS</a>
                    </li>
                    <li>
                        <a href="#callstab" data-toggle="tab"><i class="fa fa-phone" aria-hidden="true"></i> Calls</a>
                    </li>
                </ul>

                <div class="tab-content clearfix">
                    <div class="tab-pane active" id="devicestab">
                        <!--collapsable panel start-->
                        <div class="row product-listing">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="panel panel-default">


                                    <div class="panel-body nested-accordion">
                                        <div class="row mt-10">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <!--Right Panel Object with sub lavel and checkbox-->

                                                <div class="panel-group" id="level1-accordion">
                                                    <div class="panel panel-default">
														<ul class="eventUL">
															<li class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Loading..</li>
														</ul>
                                                    </div>
                                                </div>

                                                <!--//Right Panel Object with sub lavel and checkbox-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <!--end of collapsable panel-->
                    </div>
                    <div id="alertstab" class="tab-pane">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <ul class="eventUL">
                                    <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Date-Time</li>
                                    <li class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Description</li>
									<li class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Serial No.</li>
                                    <li class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Locate</li>
                                    <li class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Resolve</li>
                                </ul>
                                <ul class="event-subUL" id="alerttab">

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="sostab" class="tab-pane">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <ul class="eventUL">
                                    <li class="col-lg-4 col-md-4 col-sm-4 col-xs-4">Date-Time</li>
									<li class="col-lg-4 col-md-4 col-sm-4 col-xs-4">Serial No.</li>
                                    <li class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Locate</li>    
                                    <li class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Resolve</li>   
                                </ul>
                                <ul class="event-subUL" id="sosstab">

                                </ul>
                            </div>
                        </div>                       
                    </div>                    
                    <div id="callstab" class="tab-pane">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <ul class="eventUL">
                                    <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Date-Time</li>
									<li class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Serial No.</li>
                                    <li class="col-lg-1 col-md-1 col-sm-1 col-xs-1">I/O</li>
                                    <li class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Number</li>
                                    <li class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Locate</li>    
                                </ul>
                                <ul class="event-subUL" id="calltab">

                                </ul>
                            </div>
                        </div>                       
                    </div>
                </div>
            </div>

        </div>
    </div>

        
    <div id="myModalconfigure" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xlg modal-megamenu">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header newsahead">
                    <h5 class="modal-title">Device Configuration</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body padnone" style="background: #3879BB none repeat scroll 0% 0%;">
                    <iframe src="" frameborder="0" width="100%" height="520px;" class="sarightpanelpop"></iframe>
                </div>
                <div class="modal-footer" style="display:none;">
                    <button type="button" class="btn btn-default atst" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    <div id="myModaldeviceinfo" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-xlg modal-megamenu">
	
		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header newsahead">
			<h5 class="modal-title">Device Information</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
		  </div>
		  <div class="modal-body padnone" style="background: #3879BB none repeat scroll 0% 0%;">
			<iframe src="" frameborder="0" width="100%" height="230px" class="sarightpanelpop"></iframe>
		  </div>
		  <div class="modal-footer" style="display:none;">
			<button type="button" class="btn btn-default atst" data-dismiss="modal">Close</button>
		  </div>
		</div>
	
	  </div>
	</div>
</div>
</div>
<?php if(($sessdata['group_id'] == 3) || ($sessdata['group_id'] == 6)){ ?>
<div class="footerDrawer">

    <div class="showftbox">
        <div id="tabb1show" class="tab tabs" style="display: none;left: 3.9em;">
            <a href="javascript:void(0)" id="taclose1" class="taclose"><i class="fa fa-sort-desc taclosefa" aria-hidden="true"></i></a>
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                    <div id="tabb1container" style="min-width: 700px; height: 278px; max-width: 700px; margin: 0 auto"></div>  
                </div>
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
					<button type="button" class="btn btn-default navbar-btn pull-right disabledbutton" id="sosback">
					<i class="fa fa-arrow-left" aria-hidden="true"></i>
					</button>
				</div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="overflow-y: auto;max-height: 19em;">
                    <table class="table table-bordered" id="sostb">
					</table>
                </div> 
            </div>       

        </div>
        <div id="tabb2show" class="tab tabs" style="display: none;left: 3.9em;">
            <a href="javascript:void(0)" id="taclose2" class="taclose"><i class="fa fa-sort-desc taclosefa" aria-hidden="true"></i></a>
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                    <div id="tabb2container" style="min-width: 700px; height: 278px; max-width: 700px; margin: 0 auto"></div>  
                </div> 
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
					<button type="button" class="btn btn-default navbar-btn pull-right disabledbutton" id="alertback">
					<i class="fa fa-arrow-left" aria-hidden="true"></i>
					</button>
				</div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="overflow-y: auto;max-height: 19em;">
                    <table class="table table-bordered" id="alerttb">
					</table>
                </div> 
            </div>       

		</div>
		<div id="tabb3show" class="tab tabs" style="display: none;left: 3.9em;">
			<a href="javascript:void(0)" id="taclose3" class="taclose"><i class="fa fa-sort-desc taclosefa" aria-hidden="true"></i></a>
			<div class="row">
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
					<div id="tabb3container" style="min-width: 820px; height: 275px; max-width: 820px; margin: 0 auto"></div>  
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="overflow-y: auto;max-height: 19em;">
					<table class="table table-bordered" id="inventorytb">
					</table>
				</div> 
			</div>
		</div>
		<div id="tabb4show" class="tab tabs" style="display: none;left: 3.9em;">
			<a href="javascript:void(0)" id="taclose4" class="taclose"><i class="fa fa-sort-desc taclosefa" aria-hidden="true"></i></a>
			<div class="row">
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
					<div id="tabb4container" style="min-width: 700px; height: 275px; max-width: 700px; margin: 0 auto"></div>  
				</div>
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
					<button type="button" class="btn btn-default navbar-btn pull-right disabledbutton" id="statusback">
						<i class="fa fa-arrow-left" aria-hidden="true"></i>
					</button>
				</div>				
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="overflow-y: auto;max-height: 19em;">
					<table class="table table-bordered" id="statustb">
					</table>
				</div> 
			</div>
		</div>
		<div id="tabb5show" class="tab tabs" style="display: none;left: 3.9em;">
			<a href="javascript:void(0)" id="taclose5" class="taclose"><i class="fa fa-sort-desc taclosefa" aria-hidden="true"></i></a>
			<div class="row">
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
					<div id="tabb5container" style="min-width: 700px; height: 275px; max-width: 700px; margin: 0 auto"></div>					
				</div>
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
					<button type="button" class="btn btn-default navbar-btn pull-right disabledbutton" id="warrentyback">
						<i class="fa fa-arrow-left" aria-hidden="true"></i>
					</button>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="overflow-y: auto;max-height: 19em;">
					<table class="table table-bordered" id="warrantytb"></table>
				</div> 
			</div>
		</div>
		<div id="tabb6show" class="tab tabs" style="display: none;left: 3.9em;">
			<a href="javascript:void(0)" id="taclose6" class="taclose"><i class="fa fa-sort-desc taclosefa" aria-hidden="true"></i></a>
			<div class="row">
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
					<div id="tabb6container" style="min-width: 700px; height: 278px; max-width: 700px; margin: 0 auto"></div>  
				</div>
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
					<button type="button" class="btn btn-default navbar-btn pull-right disabledbutton" id="callback">
					<i class="fa fa-arrow-left" aria-hidden="true"></i>
					</button>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="overflow-y: auto;max-height: 19em;">
					<table class="table table-bordered" id="calltb">
					</table>
				</div> 
			</div>       
		</div>
		<div id="tabb7show" class="tab tabs" style="display: none;left: 3.9em;">
			<a href="javascript:void(0)" id="taclose7" class="taclose"><i class="fa fa-sort-desc taclosefa" aria-hidden="true"></i></a>
			<div class="row">
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
					<div id="tabb7container" style="min-width: 700px; height: 278px; max-width: 700px; margin: 0 auto"></div>  
				</div>
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
					<!--<button type="button" class="btn btn-default navbar-btn pull-right disabledbutton" id="deviceback">
					<i class="fa fa-arrow-left" aria-hidden="true"></i>
					</button>-->
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="overflow-y: auto;max-height: 19em;">
					<table class="table table-bordered" id="devicetb">
					</table>
				</div> 
			</div>
		</div>
    </div>

    <div class="open"><i id="ft1" class="fa fa-arrow-up" aria-hidden="true"></i></div>
	<div class="content">

		<div class="container positionrel">
			<div class="stuff" style="left: 12.5em;">
				<div id="tabb1" class="ta button" tabindex="1">
					<i class="fa fa-bolt"></i>
					<span>SOS</span>
				</div>
				<div id="tabb2" class="ta button" tabindex="2">
					<i class="fa fa-exclamation-triangle"></i>
					<span>Alert</span>
				</div>
				<div id="tabb3" class="ta button" tabindex="3" style="display:none;">
					<i class="fa fa-archive"></i>
					<span>Inventory</span>
				</div>
				<div id="tabb4" class="ta button" tabindex="4" style="display:none;">
					<i class="fa fa-check-circle-o"></i>
					<span>Status</span>
				</div>
				<div id="tabb5" class="ta button" tabindex="5" style="display:none;">
					<i class="fa fa-certificate"></i>
					<span>Warranty</span>
				</div>
				<div id="tabb6" class="ta button" tabindex="6">
					<i class="fa fa-phone"></i>
					<span>Call</span>
				</div>
				<div id="tabb7" class="ta button" tabindex="7">
					<i class="fa fa-tablet"></i>
					<span>Device</span>
				</div>
			</div>
		</div>        
	</div>


</div>
<?php } ?>

<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false&key=AIzaSyBRfwq_sSeSJLLNmIU8KML-MlWIy8C-GMk"></script>
<script type="text/javascript">
    var holdExistingMarkerIdsHistory = [];
    var globalData, globalCounter = 0, globalDataLen;
    var sourcehp;
    var vectorHP;
    var timeoutCounter;
    var csvDownload = [];
	var featureHPvars = {};
	var latlonHPvars = [];
	var latlonHPvars1 = [];
	var latlonHPvars2 = [];
	var HPfeaturevarspole = {};
	var HPlatlonvarspole = {};
	
	var snap_to_road_data;
	
    function reformatDate(dateStr)
	{
	  dArr = dateStr.split("-");  // ex input "2010-01-18"
	  return dArr[2]+ "-" +dArr[1]+ "-" +dArr[0]; //ex out: "18-01-10"
	}
	
	function resetHistory() {
        $("#pausebtn").hide(function () {
            $("#playbtn").show();
        });
        if (typeof timeoutCounter !== 'undefined') {
            clearTimeout(timeoutCounter);
        }
        globalCounter = 0;
        csvDownload = [];
        $("#history_controls").hide();
        $("#showspeed").hide();
        $("#hsummary").html('');
        $("#hdetails").html('');
        $("#displayspeed").html('');
		$("#displaybatterystatus").html('');
		$("#displaydttime").html('');
		trackonHPLayervars.getSource().clear();
		latlonHPvars = [];
		trackonHPLayervars1.getSource().clear();
		latlonHPvars1 = [];
        map.removeLayer(vectorHP);
        removeAllMarkers('history');
    }
   
    function generateCSV(){
        var csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Longitude,Latitude,Battery,Date,Time,Speed\r\n";
        if(csvDownload.length > 0){
            csvDownload.forEach(function(row){
                var rdata = row.join(",");
                csvContent += rdata + "\r\n"; 
            });
         }
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute('id', 'historycsvdwnld');
        link.setAttribute("href", encodedUri);
        var csvfilename = "history_"+Date.now()+".csv";
        link.setAttribute("download", csvfilename);
        
        document.body.appendChild(link); // Required for FF
        link.click(); 
        var element = document.getElementById('historycsvdwnld');
        link.parentNode.removeChild(link);
    }
	
	function generateKML(){
		var fromDate = $.trim($("[name='fromdate']").val());
		var toDate = $.trim($("[name='todate']").val());
		var deviceId = $.trim($("#deviceid").val());
		var deviceNo = $("#deviceid option:selected").text();
		$.ajax({
            url:BASEURL + "controlcentre/getKML",
            type:"POST",
            data:{deviceid:deviceId,fromdate:fromDate,todate:toDate,deviceNo:deviceNo},
            dataType:"json"
        }).done(function (resp) {
            console.log(resp.kmlfilename);
			var link=document.createElement('a');
			document.body.appendChild(link);
			link.href=BASEURL + resp.kmlfilename;
			link.click();
        }).fail(function () {
            console.log("Fetch error");
        }).complete(function () {
            
        });
    }
    
    function drawMarker(data, isCallTimeout) {  
  
        if (globalCounter <= (globalDataLen-1)) {
            
        if (holdExistingMarkerIdsHistory.indexOf(data.deviceid) == -1) {
            addMarker(data, 'history');
            $("#displayspeed").html(parseFloat(data.trakerspeed).toFixed(3));
			$("#displaybatterystatus").html(parseFloat(data.batterystats).toFixed(3)+"%");
			$("#displaydttime").html(reformatDate(data.currentdate) +' '+ data.currenttime);
        } else {
            moveMarker(data, 'history');
             $("#displayspeed").html(parseFloat(data.trakerspeed).toFixed(3));
			 $("#displaybatterystatus").html(parseFloat(data.batterystats).toFixed(3)+"%");
			 $("#displaydttime").html(reformatDate(data.currentdate) +' '+ data.currenttime);
        }
         if(globalCounter == (globalDataLen-1)){
            $("#pausebtn").hide(function () {
                $("#playbtn").show();
            });
            return false;
        }
       
            if ((typeof isCallTimeout === 'undefined')) {
                globalCounter += pointerSpeed; 
                if(globalCounter < (globalDataLen-1)){
                    timeoutCounter = setTimeout(function () {
                        drawMarker(globalData[globalCounter]);
                    }, 200);
                }else{
                    drawMarker(globalData[(globalDataLen-1)],0);
                }
            }
        } else{
            globalCounter = (globalDataLen-1);
            drawMarker(globalData[globalCounter],0);           
             $("#pausebtn").hide(function () {
                $("#playbtn").show();
            });
        }
    }
    var pointerSpeed = 1;
    function increasePlaySpeed(val){
        if (val < (globalDataLen - 1)) {  
            var steps = Math.ceil(((globalDataLen - 1) - globalCounter)/val);
            pointerSpeed = Math.ceil(((globalDataLen - 1) - globalCounter)/steps); 
        }else{
            pointerSpeed = 1;
        }
    }
    function resetPointer(){
        globalCounter = 0;
    }
    function fnPlay() {
        $("#track_to_road").attr("disabled", true);
        $("#playbtn").hide(function () {
            $("#pausebtn").show();
        });       
		if(globalData.length <= 0){
             $(".histroymsg").html('No data for play control');
                                setTimeout(function(){
                                     $(".histroymsg").html('');
                                },3000);
            return false;
        }
        if(globalCounter == (globalDataLen - 1)){
             globalCounter = 0;
             drawMarker(globalData[globalCounter]);
         }else{
             drawMarker(globalData[globalCounter]);
         }
    }
    function fnPause() {
        $("#pausebtn").hide(function () {
            $("#playbtn").show();
        });
        clearTimeout(timeoutCounter);
		$("#track_to_road").removeAttr("disabled");
    }
    function fnBack() {
        $("#pausebtn").hide(function () {
            $("#playbtn").show();
        });
        clearTimeout(timeoutCounter);
       // console.log(globalCounter)
        if (globalCounter > 0) {            
            if((globalCounter - pointerSpeed) < 0){
                globalCounter = 0;
            }else{
                globalCounter = globalCounter - pointerSpeed;
            }
            drawMarker(globalData[globalCounter], 0);
           // globalCounter = globalCounter - pointerSpeed;
        } else {
            globalCounter = 0;
            drawMarker(globalData[globalCounter], 0);
             $(".histroymsg").html('No more back steps');
                                setTimeout(function(){
                                     $(".histroymsg").html('');
                                },3000);
        }
    }
    
    function fnForward() {
        $("#pausebtn").hide(function () {
            $("#playbtn").show();
        });
        clearTimeout(timeoutCounter);
        if (globalCounter < (globalDataLen - 1)) {
            drawMarker(globalData[globalCounter + pointerSpeed], 0);
            globalCounter = globalCounter + pointerSpeed;
        }else if (globalCounter > (globalDataLen - 1)) {
            globalCounter = (globalDataLen - 1);
            drawMarker(globalData[globalCounter], 0);
        }else {
            globalCounter = (globalDataLen - 1);
            drawMarker(globalData[globalCounter], 0);
             $(".histroymsg").html('No more forward steps');
                                setTimeout(function(){
                                     $(".histroymsg").html('');
                                },3000);
        }
    }
    
            
    function fnStop() {
        $("#pausebtn").hide(function () {
            $("#playbtn").show();
        });
		trackonHPLayervars.getSource().clear();
		latlonHPvars = [];
        clearTimeout(timeoutCounter);
        globalCounter = 0;
        drawMarker(globalData[globalCounter], 0);
		$("#track_to_road").removeAttr("disabled");
    }
	
	function gettodate(d) {		
		d = d.split(" ");
		d = d[0].split("-");
		var sunday = new Date(d[2],(d[1]-1),d[0]);
		sunday.setDate(sunday.getDate() + (1 - 1 - sunday.getDay() + 6) % 7 + 1);//next sunday
		sunday.setHours(23);
		var monday = new Date(d[2],(d[1]-1),d[0]);
		monday.setDate(monday.getDate() - (monday.getDay() + 6) % 7);//prev monday
		$("[name='todate']").val('');
		//$("[name='todate']").datetimepicker('destroy');
		//$("[name='todate']").datetimepicker( { dateFormat: 'dd-mm-yy', minDate: new Date(d[2],(d[1]-1),d[0]), maxDate: sunday });
		$("[name='todate']").datepicker('destroy');
		$("[name='todate']").datepicker( { dateFormat: 'dd-mm-yy', minDate: new Date(d[2],(d[1]-1),d[0]), maxDate: sunday });
	}
    
    (function(){
         /*$("[name='fromdate']").datetimepicker({
             dateFormat: 'dd-mm-yy',
             maxDate: new Date()
         });*/
		 $("[name='fromdate']").datepicker({
             dateFormat: 'dd-mm-yy',
             maxDate: new Date()
         });
         /*$("[name='todate']").datetimepicker({
             dateFormat: 'dd-mm-yy',
             maxDate: '0'
         });*/
		 
		$('#track_to_road').click(function() {
			resetHistory();
			var fromDate = $.trim($("[name='fromdate']").val());
            var toDate = $.trim($("[name='todate']").val());
            var deviceId = $.trim($("#deviceid").val());
			var fromtime = $.trim($("[name='fromtime']").val());
			var totime = $.trim($("[name='totime']").val());
			if ((fromDate != '') && (deviceId != '') && (toDate != '') && (fromtime != '') && (totime != '')) {
			  $("#showhistory").trigger('click');
			}
		});
		
		$('#snap_to_road').click(function() {
			var deviceId = $.trim($("#deviceid").val());
			if($(this).prop('checked') == true){
				if(snap_to_road_data != ''){
				var data1 = snap_to_road_data;
				var dataLEN1 = data1.length;
				latlonHPvars1 = [];
				for (var i = 0; i < dataLEN1; i++) {
					latlonHPvars1.push([parseFloat(
								data1[i].longitude),parseFloat(
								parseFloat(data1[i].latitude))]);
				}											
								
				var geom1 = new ol.geom.LineString(latlonHPvars1);
				geom1.transform('EPSG:4326', 'EPSG:3857');
				featureHPvars['trackon1' + data1[0].deviceid] = new ol.Feature({
				geometry: geom1
				});
				featureHPvars['trackon1' + data1[0].deviceid].setStyle([
					new ol.style.Style({
						stroke: new ol.style.Stroke({
							color: '#228B22',
							width: 2
						})
					})
				]);
				featureHPvars['trackon1' + data1[0].deviceid].setId("trackonhproute1");
				trackonHPSourcevars1.addFeature(featureHPvars['trackon1' + data1[0].deviceid]);
				}
			}
			else {
				trackonHPLayervars1.getSource().clear();
				delete featureHPvars['trackon1' + deviceId];
			}
		});
		 
         $("#showhistory").on('click', function () {
            var fromDate = $.trim($("[name='fromdate']").val());
            var toDate = $.trim($("[name='todate']").val());
            var deviceId = $.trim($("#deviceid").val());
			var fromtime = $.trim($("[name='fromtime']").val());
			var totime = $.trim($("[name='totime']").val());
			$("#popupheaderalert").attr('title','');
            $("#popupheaderalert, #alerttab1").html('');
            $("#alertpopup").hide();
			vectorAgps.getSource().clear();
           // console.log(deviceId)
            if ((fromDate != '') && (deviceId != '') && (toDate != '') && (fromtime != '') && (totime != '')) {
				trackonHPLayervars.getSource().clear();
				trackonHPLayervars1.getSource().clear();
				delete featureHPvars['trackon' + deviceId];
				delete featureHPvars['trackon1' + deviceId];
				$("#snap_to_road"). prop("checked", false);
				latlonHPvars = [];
				latlonHPvars1 = [];
				HPlatlonvarspole['HP1'] = [];
                $.ajax({
                    method: "POST",
                    dataType: "json",
                    url: BASEURL + "historyplayback/getdevicecoordinates",
                    data: {fromdate: fromDate+' '+fromtime,todate: toDate+' '+totime, deviceid: deviceId},
                    beforeSend: function () {
                        resetHistory();
                    }
                }).done(function (datarecvd) {							
                    var data = datarecvd.getcoordinates;
					snap_to_road_data = datarecvd.getcoordinates1;      
                    var historySummary =  datarecvd.history_summary; 
                    var historyDetails = datarecvd.history_details;
                    var respObjpole = datarecvd.getpoledata;
					var dataLengthpole = Object.keys(respObjpole).length;
					var respObjpoleline = datarecvd.getpolelinedata;
					var dataLengthpoleline = Object.keys(respObjpoleline).length;
                   
                    var coords = [];
                    //console.log(data.length)
                    if (data != null && data.length > 0) {
						$('#snap_to_road_div').show();
						
                        var dataLEN = data.length;
                        globalData = data;
                        globalDataLen = dataLEN;
                        var startMarker = {};
                        var endMarker = {};
                        $("#history_controls").fadeIn();                        
                        $("#showspeed").show();
                        $("#displayspeed").html('');
						$("#displaydttime").html('');
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
                        /*sourcehp = new ol.source.Vector({
                            features: [feature]
                        });*/
						sourcehp = new ol.source.Vector({});
                        vectorHP = new ol.layer.Vector({
                            source: sourcehp,
                            style: [lineStyle]
                        });
                        // vector.set('name', 'history');
                        map.addLayer(vectorHP);
						
						// **************************** history track start						
						if ($('#track_to_road').is(':checked')) {
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
						}
						// ************************************ history track end
                        
						// *********** position marker add ***********************//
						var pointer_arr = ['pointer-1','pointer-2','pointer-3','pointer-4','pointer-5','pointer-6','pointer-7'];
						var pointer_flag = 0;
						for (var i = 0; i < dataLEN; i++) {
                            if (i != 0 && i != (dataLEN - 1)) {
								var geom = new ol.geom.Point(ol.proj.transform([parseFloat(
										data[i].longitude),parseFloat(data[i].latitude)],
									'EPSG:4326','EPSG:3857'));

								var sizeArr = [32,32];
								var scale = 0.7;
								
								if((i > 0) && (data[i].currentdate !=data[i-1].currentdate)){
									pointer_flag++;
								}
								
								var feature = new ol.Feature({
										geometry:geom,
										id:"historystop" + data[i].positionalid
									});
								var iconURL = BASEURLIMG + 'assets/images/'+pointer_arr[pointer_flag]+'.png';
								
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
								
								sourcehp.addFeature(feature);
							}
						}
						// *********** position marker end ***********************//
						addMarker(startMarker,'history_start');
                        addMarker(endMarker,'history_end');
                        // var coordLength = coords.length;
						// pole data
						 if (dataLengthpoleline > 0) {
							 var HPdevid = 1;
							 var coordinates = respObjpoleline.lonlat.split(",");
							 var j = 0;
							 var coordinateArr = [];
							 while (j != (coordinates.length - 0)) {
								coordinateArr.push([coordinates[j],coordinates[j + 1]]);
								j += 2;
							 }
							 for (var k = 0;k < coordinateArr.length;k++) {
								HPlatlonvarspole['HP' + HPdevid].push([parseFloat(
											coordinateArr[k][0]),parseFloat(
											coordinateArr[k][1])]);
								if(k == 0){
									startMarkerpole = {longitude:parseFloat(coordinateArr[k][0]),latitude:parseFloat(coordinateArr[k][1]),faetureid:'start'};
								}
								if(k == (coordinateArr.length-1)){
									endMarkerpole = {longitude:parseFloat(coordinateArr[k][0]),latitude:parseFloat(coordinateArr[k][1]),faetureid:'end'};
								}
							 }
							 var geompole = new ol.geom.LineString(HPlatlonvarspole['HP' + HPdevid]);
							geompole.transform('EPSG:4326', 'EPSG:3857');
							HPfeaturevarspole['HP' + HPdevid] = new ol.Feature({
								geometry: geompole
							});
							// line style start
							var styleFunctionpole = function(feature) {
								var geometry = geompole;
								var styles = [
								  // linestring
								  new ol.style.Style({
									stroke: new ol.style.Stroke({
									  color: 'yellow',
									  width: 5
									})
								  })
								];

								return styles;
							};
							HPfeaturevarspole['HP' + HPdevid].setStyle(styleFunctionpole);
							HPfeaturevarspole['HP' + HPdevid].setId("HPtrackonpole_" + HPdevid);
							sourcehp.addFeature(HPfeaturevarspole['HP' + HPdevid]);
							
							var startgeompole = new ol.geom.Point(ol.proj.transform([parseFloat(
							startMarkerpole.longitude),parseFloat(startMarkerpole.latitude)],
							'EPSG:4326','EPSG:3857'));
						
							var startfeaturepole = new ol.Feature({
								geometry:startgeompole,
								id:"HPstartpole" + startMarkerpole.faetureid
							});
								
							var starticonURL = BASEURLIMG + 'assets/images/greenpole.png'
							
							startfeaturepole.setStyle([
								new ol.style.Style({
									image:new ol.style.Icon(({
										anchor:[0.5,1],
										size:[32,32],
										scale:0.7,
										anchorXUnits:'fraction',
										anchorYUnits:'fraction',
										opacity:1,
										src:starticonURL
									}))
								})
							]);
							sourcehp.addFeature(startfeaturepole);
							
							var endgeompole = new ol.geom.Point(ol.proj.transform([parseFloat(
									endMarkerpole.longitude),parseFloat(endMarkerpole.latitude)],
								'EPSG:4326','EPSG:3857'));
							
							var endfeaturepole = new ol.Feature({
								geometry:endgeompole,
								id:"HPendpole" + endMarkerpole.faetureid
							});
								
							var endiconURL = BASEURLIMG + 'assets/images/redpole.png'
							
							endfeaturepole.setStyle([
								new ol.style.Style({
									image:new ol.style.Icon(({
										anchor:[0.5,1],
										size:[32,32],
										scale:0.7,
										anchorXUnits:'fraction',
										anchorYUnits:'fraction',
										opacity:1,
										src:endiconURL
									}))
								})
							]);
							sourcehp.addFeature(endfeaturepole);
						 }
                    }
					else {
						$(".histroymsg").html('Please check searching inputs.');
									   setTimeout(function(){
											$(".histroymsg").html('');
									   },3000);
					}
                    if(historySummary != null && Object.keys(historySummary).length > 0){
						var summaryTable = '';
						for(var j in historySummary){
							var dst  = (historySummary[j].distance_cover != null)?parseFloat(historySummary[j].distance_cover/1000).toFixed(2):0;
							var alrt = (historySummary[j].alert_no != null)?historySummary[j].alert_no:0;
							var sos =  (historySummary[j].sos_no != null)?historySummary[j].sos_no:0;
							var call = (historySummary[j].call_no != null)?historySummary[j].call_no:0;

							summaryTable += '<tr><th>Date</th><td>'+historySummary[j].result_date+'</td></tr><th>Start Time</th><td>'+historySummary[j].start_time+'</td></tr><tr><th>End Time</th><td>'+historySummary[j].end_time+'</td></tr><tr><th>Duration</th><td>'+historySummary[j].duration+'</td></tr><tr><th>Distance Traveled</th><td>'+dst+' Km</td></tr><tr><th>Alert Count</th><td>'+alrt+'</td></tr><tr><th>SOS Count</th><td>'+sos+'</td></tr><tr><th>Call Count</th><td>'+call+'</td></tr>';							
						}
						$("#hsummary").html(summaryTable);
                    }
                    if(historyDetails != null && Object.keys(historyDetails).length > 0){
                         var detailsTable = "";
                         var fields = [];
                         for(var i in historyDetails){
                             var refname = (historyDetails[i].refname == null)?'':historyDetails[i].refname;
                             var evtList = historyDetails[i].event_list;
                             
                            if(historyDetails[i].longitude != null){
                                 if(evtList.toLowerCase().indexOf("alert") !== -1){
                                    console.log(historyDetails[i].sosid);
									addMarker(historyDetails[i],'history_alerts');
                                 }
                                 if(evtList.toLowerCase().indexOf("sos") !== -1){
                                    console.log(historyDetails[i].sosid);
									addMarker(historyDetails[i],'history_sos');
                                 }
                                  if(evtList.toLowerCase().indexOf("call") !== -1){
                                    console.log(historyDetails[i].sosid);
									addMarker(historyDetails[i],'history_calls');
                                 }
                            }
                             
                             if(evtList == 'start_track'){
                                 evtList='<span class="action-icon white"><i class="fa fa-arrow-down"></i></span>';                                
                             }
                             if(evtList == 'End_track'){
                                 evtList='<span class="action-icon white"><i class="fa fa-flag-o"></i></span>';
                                 
                             }        
                             if(evtList.indexOf("STOP Du.") !== -1){
                                 evtList = evtList.split("STOP Du.");
                                 evtList = '<span class="action-icon grey"><img src="<?php echo base_url()?>assets/images/stop.png" style="width: 15px;"></span>\r\n'+evtList[1];
                                 addMarker({deviceid:historyDetails[i].deviceid,longitude:historyDetails[i].longitude,latitude:historyDetails[i].latitude,faetureid:historyDetails[i].faetureid},'history_stop');
                             }
                             detailsTable += '<tr style="cursor:pointer;" onclick="moveFocusOnClick('+historyDetails[i].longitude+','+historyDetails[i].latitude+')"><td>'+evtList+'</td><td>'+historyDetails[i].currentdate+"\r\n"+historyDetails[i].currenttime+'</td><td>'+refname+'</td></tr>';
                            if(historyDetails[i].lonlat != null){
                              var arr = [];
                              arr.push(historyDetails[i].lonlat,refname);
                              fields.push(arr);
                            }
                           
                         }                        
                        $("#hdetails").html(detailsTable);
                        if(fields.length > 0){
                            get_fields(fields);
                        }
                    }
                    //console.log(data);
					
                }).fail(function () {
                    $(".histroymsg").html('Failed to fetch');
                               setTimeout(function(){
                                    $(".histroymsg").html('');
                               },3000);
                });
            } else {
                $(".histroymsg").html('All fields are mandatory');
                               setTimeout(function(){
                                    $(".histroymsg").html('');
                               },3000);
            }
        });
    })();
    
    
    
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

function styleFunction() {
  return [
    new ol.style.Style({
    	fill: new ol.style.Fill({
        color: 'rgba(255,255,255,0.4)'
      }),
      stroke: new ol.style.Stroke({
        color: '#3393FF',
        width: 2
      }),
      text: new ol.style.Text({
        font: '12px Calibri,sans-serif',
        fill: new ol.style.Fill({ color: '#f00' }),
        stroke: new ol.style.Stroke({
          color: '#fff', width: 2
        }),
        // get the text from the feature - `this` is ol.Feature
        text: this.get('description')
      })
    })
  ];
}


    
var geoFenceMode = "general";
$(document).ready(function () {
	$('#test').BootSideMenu({
		side:"right",
		pushBody:false,
		remember:false
	});
	// place search
	$('#pac-input').keypress(function (e) {
		var key = e.which;
		if (key == 13)  // the enter key code
		{
			searchlocation();
			return false;
		}
	});
});
</script>
<script type="text/javascript">
    var styles = [
        'Road',
        'RoadOnDemand',
        'Aerial',
        'AerialWithLabels'
    ];
    var bingLayers = [];
    var i,ii;
    for (i = 0,ii = styles.length;i < ii;++i) {
        bingLayers.push(new ol.layer.Tile({
            visible:false,
            preload:Infinity,
            source:new ol.source.BingMaps({
                key:'AiRWdWuzR_KVYwYjKRF96X09xA3DEhO_bPDdol4UC7nmE9D6PTGFrXWMpYG2RFnd',
                imagerySet:styles[i]
                        // use maxZoom 19 to see stretched tiles instead of the BingMaps
                        // "no photos at this zoom level" tiles
                        // maxZoom: 19
            })
        }));
    }

	// Define Google Satellite Layer
    bingLayers.push(new ol.layer.Tile({
        type: 'base',
        title: 'Google Satellite',
        visible: true,
        source: new ol.source.XYZ({
            url: 'https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
            attributions: '© Google <a href="https://developers.google.com/maps/terms">Terms of Use</a>'
        })
    }));

    // Define Google Street Maps Layer
    bingLayers.push(new ol.layer.Tile({
        type: 'base',
        title: 'Google Streetmaps',
        visible: false,
        source: new ol.source.XYZ({
            url: 'https://mt{0-3}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
            attributions: '© Google <a href="https://developers.google.com/maps/terms">Terms of Use</a>'
        })
    }));

    var map;
    //var exceptids = [];
   // var lat = 22.6765;
  //  var lon =  85.6255;
	
	var lon = 79.439392;
	var lat = 25.548637;



    var holdExistingAlertDeviceId = [];
    var holdExistingSosDeviceId = [];
    var holdExistingCallsDeviceId = [];
    var selectedDeviceOPenedPopUp;
    var locateObjStoreArr = [];
    var globalCheckedDevieIdForTracking = [];
	var globalCheckedPoiId = [];
	var globalCheckedRouteId = [];
	var globalCheckedGeofenceId = [];
	var alertsonmap = 0;
	var GEOSERVER_URL = '<?php echo GEOSERVER_URL; ?>';
	var dbName = '<?php echo $dbName; ?>';
    //globalCheckedDevieIdForTracking.push(17);

    var holdExistingTrackingId = [];
    var getDetailsOfDeviceCall = null;
    var fetchDataForTrackingTimeoutCall = null;
    //a layer for markers - initially it has no markers

    var vectorSource = new ol.source.Vector({});
    var locateSource = new ol.source.Vector({});
    var trackSource = new ol.source.Vector({});
//    var baseLayer = new ol.layer.Tile({
//        source: new ol.source.OSM()
//    });
    var trackLayer = new ol.layer.Vector({
        source:trackSource
    });
    var locateLayer = new ol.layer.Vector({
        source:locateSource
    });
    var vectoLayer = new ol.layer.Vector({
        source:vectorSource
    });
	var sourcePOI = new ol.source.Vector({wrapX:false});
    var vectorPOI = new ol.layer.Vector({
        source:sourcePOI
    });
	var sourceRuler = new ol.source.Vector({wrapX:false});
    var vectorRuler = new ol.layer.Vector({
        source:sourceRuler
    });
	var sourceRoute = new ol.source.Vector({wrapX:false});
    var vectorRoute = new ol.layer.Vector({
        source:sourceRoute
    });
	
	var sourceAlert = new ol.source.Vector({wrapX:false});
    var vectorAlert = new ol.layer.Vector({
        source:sourceAlert
    });
	
	var trackonHPSourcevars = new ol.source.Vector({});
	var trackonHPLayervars = new ol.layer.Vector({
		source:trackonHPSourcevars
	});
	
	var trackonHPSourcevars1 = new ol.source.Vector({});
	var trackonHPLayervars1 = new ol.layer.Vector({
		source:trackonHPSourcevars1
	});
	
	var raster = new ol.layer.Tile({
        source:new ol.source.BingMaps({
            key:'AiRWdWuzR_KVYwYjKRF96X09xA3DEhO_bPDdol4UC7nmE9D6PTGFrXWMpYG2RFnd',
            imagerySet:'Road'
                    // use maxZoom 19 to see stretched tiles instead of the BingMaps
                    // "no photos at this zoom level" tiles
                    // maxZoom: 19
        })
    });

    var geosource = new ol.source.Vector({wrapX:false});

    var vector = new ol.layer.Vector({
        source:geosource
    });

    var stylesGeoExisting = [
        /* We are using two different styles for the polygons:
         *  - The first style is for the polygons themselves.
         *  - The second style is to draw the vertices of the polygons.
         *    In a custom `geometry` function the vertices of a polygon are
         *    returned as `MultiPoint` geometry, which will be used to render
         *    the style.
         */
        new ol.style.Style({
            stroke:new ol.style.Stroke({
                color:'green',
                width:3
            }),
            fill:new ol.style.Fill({
                color:'rgba(0, 0, 255, 0.1)'
            })
        })
	];
	
    var sourceGeo = new ol.source.Vector({wrapX:false});
    var vectorGeo = new ol.layer.Vector({
        source:sourceGeo,
        style:stylesGeoExisting
    });
	
	var stylesGeoAgps = [
        new ol.style.Style({
            stroke:new ol.style.Stroke({
                color:'#2980B9',
                width:1
            }),
            fill:new ol.style.Fill({
                color:'rgba(0, 0, 255, 0.1)'
            })
        })
	];
	
	var sourceAgps = new ol.source.Vector({wrapX:false});
    var vectorAgps = new ol.layer.Vector({
        source:sourceAgps,
        style:stylesGeoAgps
    });


    var geofencingmap;
    var alreadyCreatedGeofences = {};
	
	var mapPOI;
	var mapRuler;
    var poiAndRouteData = {};
    var mapMode = "";
	var transeferMyCoordinates;
    var poiTypeSelect = document.getElementById('poidrawingmodeselect');
    var overlayToolTipRoute;
    var tooltipRoute;
	var drawpoly; // global so we can remove it later
    var drawLine;
	var overlayToolTipRuler;
    var tooltipRuler;
	var drawRuler;
	var historyflag = 0;
	
    // Fix OpenLayers map initialization
    // Create the map
    map = new ol.Map({
        target: 'map', // Target container
        layers: bingLayers.concat(
			vectoLayer,
			trackLayer,
			locateLayer,
			vectorPOI,
			vector,
			vectorGeo,
			vectorRoute,
			vectorRuler,
			vectorAlert,
			trackonHPLayervars,
			vectorAgps,
			trackonHPLayervars1
		),
        view: new ol.View({
            center: ol.proj.fromLonLat([lon, lat]), // Convert to map projection
            zoom: 9,
            minZoom: 1,
            maxZoom: 50
        }),
        /*controls: ol.control.defaults().extend([ // ✅ THIS WORKS WITH OpenLayers v6+
            new ol.control.FullScreen()
        ])*/
    });

	var schemaname = '<?php echo $sessdata['schemaname']; ?>';
	var loginid = '<?php echo $sessdata['user_id']; ?>';
	
	// poll layer add
	var pollLayer = new ol.layer.Tile({                                                        
		title: 'Pole',
		visible: true,
		source: new ol.source.TileWMS({
		  url: GEOSERVER_URL+'/'+dbName+'/wms?service=WMS&version=1.1.1&request=GetMap',
		  params: {
				   'VERSION': '1.1.1',
				   tiled: true,
				   STYLES: '',
				   LAYERS: dbName+':master_polldata'
		  }
		})
	});
	map.addLayer(pollLayer);
	
	// bridge layer add
	var bridgeLayer = new ol.layer.Tile({                                                        
		title: 'Bridge',
		visible: true,
		source: new ol.source.TileWMS({
		  url: GEOSERVER_URL+'/'+dbName+'/wms?service=WMS&version=1.1.1&request=GetMap',
		  params: {
				   'VERSION': '1.1.1',
				   tiled: true,
				   STYLES: '',
				   LAYERS: dbName+':master_bridgedata'
		  }
		})
	});
	map.addLayer(bridgeLayer);
	
	// station layer add
	var stationLayer = new ol.layer.Tile({                                                        
		title: 'Station',
		visible: true,
		source: new ol.source.TileWMS({
		  url: GEOSERVER_URL+'/'+dbName+'/wms?service=WMS&version=1.1.1&request=GetMap',
		  params: {
				   'VERSION': '1.1.1',
				   tiled: true,
				   STYLES: '',
				   LAYERS: dbName+':master_stationdata'
		  }
		})
	});
	map.addLayer(stationLayer);
	
	// cabin layer add
	var cabinLayer = new ol.layer.Tile({                                                        
		title: 'Cabin',
		visible: true,
		source: new ol.source.TileWMS({
		  url: GEOSERVER_URL+'/'+dbName+'/wms?service=WMS&version=1.1.1&request=GetMap',
		  params: {
				   'VERSION': '1.1.1',
				   tiled: true,
				   STYLES: '',
				   LAYERS: dbName+':master_cabindata'
		  }
		})
	});
	map.addLayer(cabinLayer);
	
	// level crossing layer add
	var lcLayer = new ol.layer.Tile({                                                        
		title: 'Level Crossing',
		visible: true,
		source: new ol.source.TileWMS({
		  url: GEOSERVER_URL+'/'+dbName+'/wms?service=WMS&version=1.1.1&request=GetMap',
		  params: {
				   'VERSION': '1.1.1',
				   tiled: true,
				   STYLES: '',
				   LAYERS: dbName+':master_lcdata'
		  }
		})
	});
	map.addLayer(lcLayer);
	
	// kilometer post layer add
	var kmpLayer = new ol.layer.Tile({                                                        
		title: 'KM Post',
		visible: true,
		source: new ol.source.TileWMS({
		  url: GEOSERVER_URL+'/'+dbName+'/wms?service=WMS&version=1.1.1&request=GetMap',
		  params: {
				   'VERSION': '1.1.1',
				   tiled: true,
				   STYLES: '',
				   LAYERS: dbName+':master_kmpdata'
		  }
		})
	});
	map.addLayer(kmpLayer);
	
	// get location
	function searchlocation()
	{ 
		var geocoder = new google.maps.Geocoder();
		var address = $("#pac-input").val();
	   // alert(address);
		if(address != '')
		{
			$(".loader-div").show();
			geocoder.geocode( { 'address': address}, function(results, status) 
			{
				if (status == google.maps.GeocoderStatus.OK) 
				{
					var latitude = results[0].geometry.location.lat();
					var longitude = results[0].geometry.location.lng();
					mapPOI.getView().setCenter(ol.proj.transform([longitude, latitude], 'EPSG:4326','EPSG:3857'));
					mapPOI.getView().setZoom(18);
					var placedata = {};
					placedata.latitude = latitude;
					placedata.longitude = longitude;
					addMarker(placedata,'placeSearch');
				}
				else 
				{
					alert("Invalid Address!!");
				}
				$(".loader-div").hide();
			}); 
		}
	}

	// device search in right panel
	function rightpaneldevicesearch(key) {
		var input, filter, ul, li, a, i;
		input = document.getElementById("myInputDeviceSearch"+key);
		filter = input.value.toUpperCase();
		ul = document.getElementById("myUL"+key);
		li = ul.getElementsByTagName("li");
		console.log("li.length");
		console.log(li.length);
		for (i = 0; i < li.length; i++) {
			if(li[i].getElementsByTagName("a").length > 0){
				a = li[i].getElementsByTagName("a")[1];
				console.log("a");
				console.log(a);
				if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
					li[i].style.display = "";
				} else {
					li[i].style.display = "none";

				}
			}
		}
	}
	
	//zoom to extent
	function zoomextent(){
		map.getView().fit(trackSource.getExtent(), map.getSize());
	}

    var mousePosition = new ol.control.MousePosition({
        coordinateFormat:ol.coordinate.createStringXY(6),
        projection:'EPSG:4326',
        target:document.getElementById('myposition'),
        undefinedHTML:'&nbsp;'
    });

    map.addControl(mousePosition);


    var select = document.getElementById('layer-select');
    function onChange() {
        var style = select.value;
        if(style == "GoogleSatelite"){
			bingLayers[5].setVisible(false);
			bingLayers[4].setVisible(true);
		}
		else if(style == "GoogleStreetmaps"){
			bingLayers[5].setVisible(true);
		}
		else{
			for (var i = 0,ii = bingLayers.length;i < ii;++i) {
				bingLayers[i].setVisible(styles[i] === style);
			}
		}
    }
    select.addEventListener('change',onChange);
    onChange();

    var element = document.getElementById('popup');
    var popup = new ol.Overlay({
        element:element,
        positioning:'bottom-left',
        stopEvent:false
    });
    map.addOverlay(popup);
	
	var alertelement = document.getElementById('alertpopup');
    var alertpopup = new ol.Overlay({
        element:alertelement,
        positioning:'bottom-left',
        stopEvent:false
    });
    map.addOverlay(alertpopup);

    map.on('click',function (evt) {
        if(mapMode == ""){
			var feature = map.forEachFeatureAtPixel(evt.pixel,
					function (feature,layer) {
						return feature;
					});
			// console.log(feature)
			if (feature) {
				 console.log(feature);				
				var deviceid = feature.get('devid');
				var featureid = feature.get('id');
				if (featureid.toString().indexOf('sos_') == -1 && featureid.toString().indexOf('alerts_') == -1 && featureid.toString().indexOf('calls_') == -1 && featureid.toString().indexOf('historycalls') == -1 && featureid.toString().indexOf('historyalerts') == -1 && featureid.toString().indexOf('historysos') == -1 && featureid.toString().indexOf('historystop') == -1 && featureid.toString().indexOf('historystart') == -1 && featureid.toString().indexOf('historyend') == -1 && featureid.toString().indexOf('locatepoi_') == -1 && featureid.toString().indexOf('existingroute_') == -1) {
					if (featureid.indexOf("locateflag_") != -1) {
						deleteMarkerById(feature,'locate');
						return true;
					}
					if (featureid.indexOf("historyplay_") != -1) {                   
						return true;
					}

					if(getDetailsOfDeviceajax){ getDetailsOfDeviceajax.abort(); clearTimeout(getDetailsOfDeviceCall);}
					selectedDeviceOPenedPopUp = deviceid;
					getDetailsOfDevice(deviceid);
					var geometry = feature.getGeometry();
					var coord = geometry.getCoordinates();
					var pixel = map.getPixelFromCoordinate(coord);

					popup.setPosition(coord);

					$("#map #popoverContent").css(
							{"bottom":"37px","position":"relative","right":"178px"});

					$("#popup a[href^='#tab'],[id^='tab']").removeClass(
							'active');
					$("#popup a[href='#tab1'],[id='tab1']").addClass('active');

					$("#tab1").show();
					
					changec(1);
					$("#popup").show();
				}
				else if (featureid.toString().indexOf('existingroute_') != -1){
					var routeupdate_id = featureid.replace("existingroute_", "");
					getroutebufferdata(routeupdate_id);
				}
				else if (featureid.toString().indexOf('existingroute_') == -1){
					if (featureid.toString().indexOf('sos_') != -1) {
						var sosid = featureid.replace("sos_", "");
						console.log(sosid);
						getsinglesosbyid(sosid);
					}
					if (featureid.toString().indexOf('alerts_') != -1) {
						var alertid = featureid.replace("alerts_", "");
						console.log(alertid);
						getsinglealertbyid(alertid);
					}
					if (featureid.toString().indexOf('historycalls') != -1) {
						var callid = featureid.replace("historycalls", "");
						console.log(callid);
						getsinglecallbyid(callid);
					}
					if (featureid.toString().indexOf('historyalerts') != -1) {
						var alertid = featureid.replace("historyalerts", "");
						console.log(alertid);
						getsinglealertbyid(alertid);
					}
					if (featureid.toString().indexOf('historysos') != -1) {
						var sosid = featureid.replace("historysos", "");
						console.log(sosid);
						getsinglesosbyid(sosid);
					}
					if (featureid.toString().indexOf('historystart') != -1) {
						var fid = featureid.replace("historystart", "");
						console.log(fid);
						getstartdetailsbyid(fid);
					}
					if (featureid.toString().indexOf('historyend') != -1) {
						var fid = featureid.replace("historyend", "");
						console.log(fid);
						getenddetailsbyid(fid);
					}
					if (featureid.toString().indexOf('historystop') != -1) {
						var fid = featureid.replace("historystop", "");
						console.log(fid);
						getstopdetailsbyid(fid);
					}
					
					var geometry = feature.getGeometry();
					var coord = geometry.getCoordinates();
					var pixel = map.getPixelFromCoordinate(coord);
					alertpopup.setPosition(coord);
					
					$("#map #popoverContentalert").css(
							{"bottom":"37px","position":"relative","right":"178px"});
					$("#alertpopup a[href='#alerttab1'],[id='alerttab1']").addClass('active');

					$("#alerttab1").show();					
					
				}
			} else {
				
			}
		}
    });

    $(element).on("hidden.bs.popover",function (e) {
        selectedDeviceOPenedPopUp = null;
    });
	
	function getroutebufferdata(routeupdate_id){
		console.log(routeupdate_id);
		$("#routeupdate_id").val(routeupdate_id);
		
		$.ajax({
            url:BASEURL + "controlcentre/get_routebuffer_data",
            method:"POST",
            data:{routeupdate_id:routeupdate_id},
            global:false,
            dataType:"json"
        }).done(function (resp) {
            $("#updateroutebuffer").modal({
				backdrop:'static',
				keyboard:false
			});
			
			$("#routeupdate_buffer").val(resp.buffervalue);
        }).fail(function () {
            console.log("error in fetching")
        }).complete(function () {
        });
	}
	
	function updateRouteBuffer(){
		var routeupdate_id = $("#routeupdate_id").val();
		var routeupdate_buffer = $("#routeupdate_buffer").val();
		$.ajax({
            url:BASEURL + "controlcentre/update_routebuffer_data",
            method:"POST",
            data:{routeupdate_id:routeupdate_id,routeupdate_buffer:routeupdate_buffer},
            global:false,
            dataType:"json"
        }).done(function (resp) {
            if(resp.status == 1){
				alert("Buffer has been updated successfully");
			}
        }).fail(function () {
            console.log("error in fetching")
        }).complete(function () {
        });
	}

//    // change mouse cursor when over marker
    map.on('pointermove',function (e) {
        if (e.dragging) {
            //$(element).popover('hide');
            return;
        }
        var pixel = map.getEventPixel(e.originalEvent);
        var hit = map.hasFeatureAtPixel(pixel);
        if (hit) {
            $("#" + map.getTarget()).css('cursor','pointer');
        } else {
            $("#" + map.getTarget()).css('cursor','');
        }
    });
	
	function getstartdetailsbyid(id){
		var fromDate = $.trim($("[name='fromdate']").val());
		var toDate = $.trim($("[name='todate']").val());
		var fromtime = $.trim($("[name='fromtime']").val());
		var totime = $.trim($("[name='totime']").val());
		var deviceId = $.trim($("#deviceid").val());
		$.ajax({
            url:BASEURL + "controlcentre/getstartdetailsbyid",
            type:"POST",
            data:{id:id,deviceid:deviceId,fromdate:fromDate+' '+fromtime,todate: toDate+' '+totime},
            dataType:"json"
        }).done(function (resp) {
            if (typeof resp.status !== 'undefined' && resp.status == '1') {
				
                if (typeof resp.html !== 'undefined' && resp.html.length > 0) {
                    $("#alerttab1").html(resp.html);
                }
                if (typeof resp.result.divise_serial !== 'undefined' && resp.result.divise_serial.length > 0) {
                    var header = resp.result.divise_serial;
                    $("#popupheaderalert").html(header).attr('title',resp.result.divise_serial);
                }
            }
        }).fail(function () {
            console.log("Fetch error");
        }).complete(function () {
            $("#alertpopup").show();
        });
	}
	
	function getenddetailsbyid(id){
		var fromDate = $.trim($("[name='fromdate']").val());
		var toDate = $.trim($("[name='todate']").val());
		var fromtime = $.trim($("[name='fromtime']").val());
		var totime = $.trim($("[name='totime']").val());
		var deviceId = $.trim($("#deviceid").val());
		$.ajax({
            url:BASEURL + "controlcentre/getenddetailsbyid",
            type:"POST",
            data:{id:id,deviceid:deviceId,fromdate:fromDate+' '+fromtime,todate: toDate+' '+totime},
            dataType:"json"
        }).done(function (resp) {
            if (typeof resp.status !== 'undefined' && resp.status == '1') {
				
                if (typeof resp.html !== 'undefined' && resp.html.length > 0) {
                    $("#alerttab1").html(resp.html);
                }
                if (typeof resp.result.divise_serial !== 'undefined' && resp.result.divise_serial.length > 0) {
                    var header = resp.result.divise_serial;
                    $("#popupheaderalert").html(header).attr('title',resp.result.divise_serial);
                }
            }
        }).fail(function () {
            console.log("Fetch error");
        }).complete(function () {
            $("#alertpopup").show();
        });
	}
	
	function getstopdetailsbyid(id){
		var fromDate = $.trim($("[name='fromdate']").val());
		var toDate = $.trim($("[name='todate']").val());
		var fromtime = $.trim($("[name='fromtime']").val());
		var totime = $.trim($("[name='totime']").val());
		var deviceId = $.trim($("#deviceid").val());
		$.ajax({
            url:BASEURL + "controlcentre/getstopdetailsbyid",
            type:"POST",
            data:{id:id,deviceid:deviceId,fromdate:fromDate+' '+fromtime,todate: toDate+' '+totime},
            dataType:"json"
        }).done(function (resp) {
            if (typeof resp.status !== 'undefined' && resp.status == '1') {
				
                if (typeof resp.html !== 'undefined' && resp.html.length > 0) {
                    $("#alerttab1").html(resp.html);
                }
                if (typeof resp.result.divise_serial !== 'undefined' && resp.result.divise_serial.length > 0) {
                    var header = resp.result.divise_serial;
                    $("#popupheaderalert").html(header).attr('title',resp.result.divise_serial);
                }
            }
        }).fail(function () {
            console.log("Fetch error");
        }).complete(function () {
            $("#alertpopup").show();
        });
	}

    function addMarker(rowdata,fromWhere) {
		if(parseFloat(rowdata.longitude) != 0 && parseFloat(rowdata.latitude) != 0){
        var geom = new ol.geom.Point(ol.proj.transform([parseFloat(
                    rowdata.longitude),parseFloat(rowdata.latitude)],
                'EPSG:4326','EPSG:3857'));

        var sizeArr = [32,32];
        var scale = 0.7;

        if (fromWhere == 'track') {
            var feature = new ol.Feature({
                geometry:geom,
                id:"track_" + rowdata.deviceid,
                devid:rowdata.deviceid
            });
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
        } else if (fromWhere == 'placeSearch') {
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
			sizeArr = [45,45];
        }else if (fromWhere == 'history_end') { 
            iconURL = BASEURLIMG + 'assets/images/redflag.png'
			sizeArr = [45,45];
        }else if (fromWhere == 'history_stop') { 
            iconURL = BASEURLIMG + 'assets/images/stop.png'
        }else if (fromWhere == 'track') { 
            if(rowdata.icon_details){
				iconURL = BASEURLIMG + rowdata.icon_details;
				if (rowdata.icon_details.indexOf('user_icons') !== -1) {
					sizeArr = [100,100];
					scale = 0.4;
				}
			}
			else{
				iconURL = BASEURLIMG + 'assets/iconset/device.png'
			}			
        }
		
        /*feature.setStyle([
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
        ]);*/

		feature.setStyle(new ol.style.Style({
			image: new ol.style.Icon({
				anchor: [0.5, 1], // Anchor at bottom center of the icon
				anchorXUnits: 'fraction',
				anchorYUnits: 'fraction',
				src: iconURL, // Ensure iconURL is a valid image path
				scale: scale, // Scale the image properly
				crossOrigin: 'anonymous' // Prevent CORS issues if fetching from external sources
			})
		}));

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
			//map.getView().setZoom(14);
        }else if(fromWhere == 'history_alerts' || fromWhere == 'history_sos' || fromWhere == 'history_calls'){
            //feature.setId(fromWhere+"_" + rowdata.deviceid);
            sourcehp.addFeature(feature);
//            map.getView().setCenter(ol.proj.transform([parseFloat(
//                        rowdata.longitude),parseFloat(rowdata.latitude)],
//                    'EPSG:4326','EPSG:3857'));
//            holdExistingMarkerIdsHistory.push(rowdata.deviceid);
        }else if(fromWhere == 'history_start' || fromWhere == 'history_end' || fromWhere == 'history_stop'){          
            sourcehp.addFeature(feature);
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
                holdExistingAlertDeviceId.push(rowdata.deviceid);

            }

        }
		}
    }
	
    /*function deleteMarkerById(id,layer) {
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

    }*/
	
	/*function deleteMarkerById(id, layer) {
		if (typeof layer !== 'undefined') {
			let source = null;
			let feature = null;

			if (layer === 'track') {
				source = trackLayer.getSource();
			} else if (layer === 'locate') {
				source = locateLayer.getSource();
			} else if (layer === 'history') {
				source = vectorHP.getSource();
			} else {
				source = vectoLayer.getSource();
			}

			if (source) {
				feature = source.getFeatureById(id);
			}

			if (feature) {
				source.removeFeature(feature);
			} else {
				console.log(`Feature with ID '${id}' not found in layer '${layer}'.`);
			}

			// If layer is 'locate', remove from locateObjStoreArr
			if (layer === 'locate' && feature) {
				var featureid = feature.get('id');
				var deviceid = featureid.split("_");
				locateObjStoreArr = locateObjStoreArr.filter(row => {
					var jsonRow = JSON.parse(row);
					return deviceid[1] !== jsonRow.deviceid;
				});
			}
		}
	}*/

	function deleteMarkerById(id, layer) {
		if (!layer) {
			console.warn("Layer is not defined.");
			return;
		}

		let source = null;

		// Assign the correct source based on layer
		if (layer === 'track') {
			source = trackLayer?.getSource();
		} else if (layer === 'locate') {
			source = locateLayer?.getSource();
		} else if (layer === 'history') {
			source = vectorHP?.getSource();
		} else {
			source = vectoLayer?.getSource();
		}

		if (!source) {
			console.warn(`❌ Source for layer '${layer}' not found.`);
			return;
		}

		// ✅ Log all feature IDs before removing
		console.log("🔍 Existing Features in Layer:", source.getFeatures().map(f => f.getId()));

		// ✅ Get the feature by ID
		let featureToRemove = source.getFeatureById(id);

		if (featureToRemove) {
			console.log(`✅ Feature '${id}' found. Removing...`);
			source.removeFeature(featureToRemove);

			// ✅ Verify if removal was successful
			setTimeout(() => {
				console.log("🔍 Features After Removal:", source.getFeatures().map(f => f.getId()));
			}, 500);
		} else {
			console.warn(`⚠️ Feature with ID '${id}' not found in layer '${layer}'.`);
		}

		// ✅ Remove from locateObjStoreArr if needed
		if (layer === 'locate' && featureToRemove) {
			let featureId = featureToRemove.getId();
			let deviceId = featureId.split("_")[1];

			locateObjStoreArr = locateObjStoreArr.filter(row => {
				let jsonRow = JSON.parse(row);
				return deviceId !== jsonRow.deviceid;
			});

			console.log(`✅ Feature removed from locateObjStoreArr.`);
		}

		// ✅ Ensure the layer is refreshed
		map.render();
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
				//console.log(latlonvars['trackon' + rowdata.deviceid]);
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
				//console.log(latlonHPvars);
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
							width:4,
 
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

    map.once('postrender',function (event) {
        //getPositionData(intervalMsVariable);

        getNotificationAndSosDataForAll();
		//getSummeryReport();
    });

    $(document).ready(function () {
//        $("#trackdevice").on('click', function () {
//            var value = $.trim($("#searchdevices").val());
//            var searchType = 'serialno';
//            if (value == '') {
//                openAlert('alert-danger', "Please enter device serial no");
//                return false;
//            }
//
//            $.ajax({
//                url: BASEURL + "controlcentre/locateonmap",
//                method: "POST",
//                data: {type: searchType, value: value}
//            }).done(function (data) {
//                if (data == '' || data == '[]') {
//                    openAlert('alert-danger', 'No result found');
//                    return false;
//                }
//                var dataReturned = JSON.parse(data)
//
//                map.getView().setCenter(ol.proj.transform([parseFloat(dataReturned.long), parseFloat(dataReturned.lat)], 'EPSG:4326', 'EPSG:3857'));
//            }).fail(function () {
//                openAlert('alert-danger', 'No result found');
//            });
//        });

        $("#sidenavToggler").on('click',function () {
            if (map) {
                map.render();
                map.updateSize();
            }
        })

        //fetchNotifications(); 
        initOnLoad();
		
		// ******************* Histrory playback load from report start
		setTimeout(function(){
			if(historyflag == 0){
				<?php if(!empty($get_dev_id) && !empty($get_date) && !empty($get_frm_time) && !empty($get_to_time)){ ?>
				var get_dev_id = '<?php echo $get_dev_id ?>';
				var get_date = '<?php echo $get_date ?>';
				var get_frm_time = '<?php echo $get_frm_time ?>';
				var get_to_time = '<?php echo $get_to_time ?>';
				$("#hplbck").trigger("click");
				$("#deviceid").val(get_dev_id);
				$('#deviceid').fSelect('reload');
				$("input[name=fromdate]").val(get_date);
				$("input[name=todate]").val(get_date);
				$("input[name=fromtime]").val(get_frm_time);
				$("input[name=totime]").val(get_to_time);
				<?php } ?>
				historyflag++;
			}
		}, 4000);
		// ******************* Histrory playback load from report end
    });


    $(document).ready(function () {
        $("#closemappopup").on('click',function () {
            selectedDeviceOPenedPopUp = null;
             $("#popupheader").attr('title','');
            $("#popupheader, #tab1, #tab2, #tab3, #tab4, #tab5").html('');
            // removeAllMarkers('notificationList');
            clearTimeout(getDetailsOfDeviceCall);
            getDetailsOfDeviceCall = null;
            locateObjStoreArr.length = 0;
            $("#popup").hide();
        });
        $("#alertsDropdown").on('click',function () {
            // alert("dddd");
        });
		$("#closemappopupalert").on('click',function () {
            $("#popupheaderalert").attr('title','');
            $("#popupheaderalert, #alerttab1").html('');
            $("#alertpopup").hide();
        });
    });


    function initOnLoad() {		
        $.ajax({
            url:BASEURL + 'controlcentre/get_my_rightmenu',
            type:"GET",
            global:false,
            dataType:"json"
        }).done(function (resp) {
            if (typeof resp.status !== 'undefined') {
                if (resp.result != '') {
                    $("#level1-accordion").html(resp.result);
					/*resp.activedevice.forEach(
					function (deviceid) {
						if (globalCheckedDevieIdForTracking.indexOf(deviceid) == -1) {
							globalCheckedDevieIdForTracking.push(deviceid);
						}
					});*/
					$("#showactivecount").html(resp.activeDevices.length);
					$("#showinactivecount").html(resp.inactiveDevices.length);
                    if (globalCheckedDevieIdForTracking.length > 0) {
                        globalCheckedDevieIdForTracking.forEach(
                                function (row) {                                    
                                    $('body').find(".clickmetotrack").filter("[id=" + row + "]").prop('checked',true);
									$('#trackon'+row).hide();
									$('#trackoff'+row).show();
                                });
                    }
                }
            }
        }).fail(function () {
            console.log("Fetch error");
        }).complete(function () {
            setTimeout(function () {
                initOnLoad()
            },600000);//10 minutes600000
        });

    }


	var respObjcalls;
	var respObjalerts;
	var respObjsos;
    function getNotificationAndSosDataForAll() {
        $.ajax({
            url:BASEURL + 'controlcentre/get_notification_and_sos_data',
            type:"POST",
            global:false,
            dataType:"json"
        }).done(function (result) {
            if (typeof result.status !== 'undefined' && result.status == 1) {
                respObjcalls = result.result.calls;
                respObjalerts = result.result.alerts;
                respObjsos = result.result.sos;
                var dataLengthCalls = Object.keys(respObjcalls).length;
                var dataLengthAlerts = Object.keys(respObjalerts).length;
                var dataLengthSos = Object.keys(respObjsos).length;
                var notificationListPanelHtml = "";
                var alertTab = '',sosTab = '',callTab = '';
                var flag = 0;
                //*******calls******/
                var callids = [];
                if (dataLengthCalls > 0) {
                    flag = 1;
                    for (var i = 0;i < dataLengthCalls;i++) {
                        //  console.log(holdExistingAlertSosId.indexOf(respObj[i].deviceid))
                        if (holdExistingCallsDeviceId.indexOf(respObjcalls[i].deviceid) == -1) {
                            if(alertsonmap == 1){
								//addMarker(respObjcalls[i],'calls');
							}
                        } else {
                            //  console.log(respObj[i].deviceid)
                            if (respObjcalls[i].deviceid != selectedDeviceOPenedPopUp) {
                                if(alertsonmap == 1){
									//moveMarker(respObjcalls[i],'calls');
								}
                            }
                        }
						if(isNaN(respObjcalls[i].duration)){
							respObjcalls[i].duration = 0;
						}
						else {
							respObjcalls[i].duration = respObjcalls[i].duration;
						}
						
                        var calltype = (respObjcalls[i].sendorrecive == 'I')?"In":"Out";
                        callTab += '<li class="col-lg-3 col-md-3 col-sm-3 col-xs-3">' + respObjcalls[i].currentdate.split("-").reverse().join("/") +'\n'+respObjcalls[i].currenttime+ '</li><li class="col-lg-3 col-md-3 col-sm-3 col-xs-3">' + respObjcalls[i].divise_serial + '</li><li class="col-lg-1 col-md-1 col-sm-1 col-xs-1">' + calltype + '</li><li class="col-lg-3 col-md-3 col-sm-3 col-xs-3">'+respObjcalls[i].connectto +' ('+SecondsTohhmmss(parseInt(respObjcalls[i].duration))+')'+'</li><li class="col-lg-2 col-md-2 col-sm-2 col-xs-2"><a href="javascript:void(0)" id="calltab_' + respObjcalls[i].sosid + '"><i class="fa fa-map-marker" aria-hidden="true" onclick="setFocusFromNotificationListCall('+respObjcalls[i].sosid+')" style="padding-left: 1.2em;"></i></a></li><div class="clearfix"></div>';
                        //   "<li onclick='setFocusFromNotificationList(" + respObjalerts[i] + ")'>" + respObjalerts[i].deviceid + "<li>";
                        
                        callids[respObjcalls[i].sosid] = respObjcalls[i];
                    }
                }

                //****************/
                //*****alerts****//
                var alertids = [];
                if (dataLengthAlerts > 0) {
                    flag = 1;
                    for (var i = 0;i < dataLengthAlerts;i++) {
                        //  console.log(holdExistingAlertSosId.indexOf(respObj[i].deviceid))
                        if (holdExistingAlertDeviceId.indexOf(respObjalerts[i].deviceid) == -1) {
                            if(alertsonmap == 1){
								addMarker(respObjalerts[i],'alerts');
							}
                        } else {
                            //  console.log(respObj[i].deviceid)
                            if (respObjalerts[i].deviceid != selectedDeviceOPenedPopUp) {
								if(alertsonmap == 1){
									//moveMarker(respObjalerts[i],'alerts');
								}
                            }
                        }
                        
                        alertTab += '<li class="col-lg-3 col-md-3 col-sm-3 col-xs-3">' + respObjalerts[i].currentdate.split("-").
                                reverse().
                                join("/")+"\n"+respObjalerts[i].currenttime + '</li><li class="col-lg-2 col-md-2 col-sm-2 col-xs-2">' + respObjalerts[i].description + '</li><li class="col-lg-3 col-md-3 col-sm-3 col-xs-3">' + respObjalerts[i].divise_serial + '</li><li class="col-lg-2 col-md-2 col-sm-2 col-xs-2"><a href="javascript:void(0)" id="alerttab_' + respObjalerts[i].sosid + '"><i class="fa fa-map-marker" aria-hidden="true" onclick="setFocusFromNotificationListAlert('+respObjalerts[i].sosid+')" style="padding-left: 1.2em;"></i></a></li><li class="col-lg-2 col-md-2 col-sm-2 col-xs-2" id="alerttdid_'+respObjalerts[i].sosid+'"><i class="fa fa-life-ring" aria-hidden="true" style="cursor:pointer;color:#f00; padding-left: 1.2em;" onclick="resolveAlert('+respObjalerts[i].sosid+',\''+respObjalerts[i].schema_name_+'\',1)"></i></li><div class="clearfix"></div>';
                        alertids[respObjalerts[i].sosid] = respObjalerts[i];
                    }
                }
                //************/
                //*****sos****/
                var sosids = [];
                if (dataLengthSos > 0) {
                    flag = 1;
                    for (var i = 0;i < dataLengthSos;i++) {
                        //  console.log(holdExistingAlertSosId.indexOf(respObj[i].deviceid))
                        if (holdExistingSosDeviceId.indexOf(respObjsos[i].deviceid) == -1) {
                            // console.log(respObjsos[i])
                            if(alertsonmap == 1){
								addMarker(respObjsos[i],'sos');
							}
                        } else {
                            //  console.log(respObj[i].deviceid)
                            if (respObjsos[i].deviceid != selectedDeviceOPenedPopUp) {
								if(alertsonmap == 1){
									//moveMarker(respObjsos[i],'sos');
								}
                            }
                        }
                        //notificationListPanelHtml += "<li onclick='setFocusFromNotificationList(" + respObjsos[i] + ")'>" + respObjsos[i].deviceid + "<li>";
                       
                        sosTab += '<li class="col-lg-4 col-md-4 col-sm-4 col-xs-4">' + respObjsos[i].currentdate.split("-").
                                reverse(). join("/")+'\n'+respObjsos[i].currenttime  + '</li><li class="col-lg-4 col-md-4 col-sm-4 col-xs-4">' + respObjsos[i].divise_serial + '</li><li class="col-lg-2 col-md-2 col-sm-2 col-xs-2"><a href="javascript:void(0);" id="sostab_' + respObjsos[i].sosid + '" ><i class="fa fa-map-marker" aria-hidden="true" onclick="setFocusFromNotificationListSos('+respObjsos[i].sosid+')" style="padding-left: 1.2em;"></i></a></li><li class="col-lg-2 col-md-2 col-sm-2 col-xs-2" id="sostdid_'+respObjsos[i].sosid+'"><i class="fa fa-life-ring" aria-hidden="true" style="cursor:pointer;color:#f00; padding-left: 1.2em;" onclick="resolveSos('+respObjsos[i].sosid+',\''+respObjsos[i].schema_name_+'\',1)"></i></li><div class="clearfix"></div>';

                        sosids[respObjsos[i].sosid] = respObjsos[i];
                        //      sosids.push(pushdata);                       
                    }
                }

                //************/

                if (flag == 1) {
                    //notificationListPanelHtml
                    $("#bell").fadeOut();
                    $("#notificationbell").fadeIn();
                    if (selectedDeviceOPenedPopUp == null || typeof selectedDeviceOPenedPopUp === 'undefined') {
                        //      var extent = vectoLayer.getSource().getExtent();
                        //   map.getView().fit(extent,map.getSize());
                        // map.getView().setZoom(14);
                    }
                }

                if (callTab != '') {
                    $("#calltab").html(callTab);                    
                }
                if (alertTab != '') {
                    $("#alerttab").html(alertTab);                   
                }
                if (sosTab != '') {
                    $("#sosstab").html(sosTab);                    
                }
            }
        }).fail(function () {
            console.log("fetch error");
        }).complete(function () {
            setTimeout(function () {
                getNotificationAndSosDataForAll();
            },10000)
        });
    }

	function getSummeryReport() {
        $.ajax({
            url:BASEURL + 'controlcentre/get_SummeryReport',
            type:"POST",
            global:false,
            dataType:"json"
        }).done(function (result) {
            if (typeof result.status !== 'undefined' && result.status == 1) {
                $("#summerytb").html(result.html);
            }
        }).fail(function () {
            console.log("fetch error");
        }).complete(function () {
            setTimeout(function () {
                getSummeryReport();
            },3600000)
        });
    }
	
    function setFocusFromNotificationListCall(id) {
		var dataLengthCalls = Object.keys(respObjcalls).length;
		for (var i = 0;i < dataLengthCalls;i++) {
			if(respObjcalls[i].sosid == id){
				addMarker(respObjcalls[i],"notificationList");
			}
		}
    }
	
	function setFocusFromNotificationListAlert(id) {
		var dataLengthAlerts = Object.keys(respObjalerts).length;
		for (var i = 0;i < dataLengthAlerts;i++) {
			if(respObjalerts[i].sosid == id){
				addMarker(respObjalerts[i],"notificationList");
			}
		}
    }
	
	function setFocusFromNotificationListSos(id) {
		var dataLengthSos = Object.keys(respObjsos).length;
		for (var i = 0;i < dataLengthSos;i++) {
			if(respObjsos[i].sosid == id){
				addMarker(respObjsos[i],"notificationList");
			}
		}
    }

    function moveFocusOnClick(longitude,latitude){
        map.getView().setCenter(ol.proj.transform([parseFloat(longitude),parseFloat(latitude)],'EPSG:4326','EPSG:3857'));
        map.getView().setZoom(18);
    }

    function attendAlertSosNotification(id,typeid) {
        $.ajax({
            url:BASEURL + "controlcentre/attendalert_sos",
            method:"POST",
            data:{},
            dataType:"json"
        }).done(function (resp) {
            if (typeof resp.status !== 'undefined') {
                var respData = resp.result;

                // REMOVE MARKER FROM HERE
            }
        }).fail(function () {
            console.log("error in fetch")
        }).complete(function () {

        });
    }
    var timerForDevicePositionDataAjax;
	var xhrdataPool = [];
	var abortdataall = function() {
		$.each(xhrdataPool, function(idx, jqXHR) {
		  jqXHR.abort();
		});
	  };
    /*$(document).on('click',".clickmetotrack",function () {
		$("#cancelallinteractionleft").trigger('click');
        // get all checked devices 
        $(document).find(".clickmetotrack").each(function () {

            if ($(this).prop('checked') == true) {
				timerForDevicePositionDataAjax.abort();
                var deviceid = $(this).attr('id');
                //deviceid = deviceid.split("_");
                // deviceid = deviceid[1];
				//console.log(globalCheckedDevieIdForTracking.indexOf(deviceid));
                if (globalCheckedDevieIdForTracking.indexOf(deviceid) == -1) {
                    globalCheckedDevieIdForTracking.push(deviceid);
                }
				delete featurevars['trackon' + deviceid];
				delete latlonvars['trackon' + deviceid];
				$('#trackon'+deviceid).hide();
				$('#trackoff'+deviceid).hide();
				$('#trackon'+deviceid).show();
				$('#zoomto'+deviceid).show();
				delete featureHPvars['trackon' + deviceid];
				abortdataall();
            } else {
				timerForDevicePositionDataAjax.abort();
                var deviceid = $(this).attr('id');
				//console.log(globalCheckedDevieIdForTracking);
                //   deviceid = deviceid.split("_");
                //  deviceid = deviceid[1];
                var index = globalCheckedDevieIdForTracking.indexOf(deviceid);
                if (index != -1) {
                    globalCheckedDevieIdForTracking.splice(index,1);
					deleteMarkerById('track_'+deviceid,'track');
                }
				if(trackonLayervars['trackon' + deviceid]){
					trackonLayervars['trackon' + deviceid].getSource().clear();
					map.removeLayer(trackonLayervars['trackon' + deviceid]);
				}
				delete featurevars['trackon' + deviceid];
				delete latlonvars['trackon' + deviceid];
				$('#trackon'+deviceid).hide();
				$('#trackoff'+deviceid).hide();
				$('#zoomto'+deviceid).hide();
				abortdataall();
            }
        });
        if (globalCheckedDevieIdForTracking.length > 0) {
			$("#zoomtoextentbtn").prop('disabled', false);
            fetchDataForTracking();
        } else {
			$("#zoomtoextentbtn").prop('disabled', true);
            clearTimeout(fetchDataForTrackingTimeoutCall);
            removeAllMarkers('track');
			vectorAgps.getSource().clear();
        }
        fetchDataForTracking();
    });*/
	$(document).on('click', ".clickmetotrack", function () {
		$("#cancelallinteractionleft").trigger('click');

		$(document).find(".clickmetotrack").each(function () {
			let deviceid = $(this).attr('id');

			if ($(this).prop('checked')) {
				timerForDevicePositionDataAjax.abort();
				
				if (!globalCheckedDevieIdForTracking.includes(deviceid)) {
					globalCheckedDevieIdForTracking.push(deviceid);
				}

				delete featurevars['trackon' + deviceid];
				delete latlonvars['trackon' + deviceid];

				$('#trackon' + deviceid).hide();
				$('#trackoff' + deviceid).hide();
				$('#trackon' + deviceid).show();
				$('#zoomto' + deviceid).show();

				delete featureHPvars['trackon' + deviceid];
				abortdataall();

			} else {
				timerForDevicePositionDataAjax.abort();
				let index = globalCheckedDevieIdForTracking.indexOf(deviceid);

				if (index !== -1) {
					globalCheckedDevieIdForTracking.splice(index, 1);

					// ✅ Check if feature exists before deleting
					if (trackLayer && trackLayer.getSource().getFeatureById('track_' + deviceid)) {
						deleteMarkerById('track_' + deviceid, 'track');
					} else {
						console.warn(`Feature 'track_${deviceid}' not found.`);
					}
				}

				if (trackonLayervars['trackon' + deviceid]) {
					trackonLayervars['trackon' + deviceid].getSource().clear();
					map.removeLayer(trackonLayervars['trackon' + deviceid]);
				}

				delete featurevars['trackon' + deviceid];
				delete latlonvars['trackon' + deviceid];

				$('#trackon' + deviceid).hide();
				$('#trackoff' + deviceid).hide();
				$('#zoomto' + deviceid).hide();

				abortdataall();
			}
		});

		if (globalCheckedDevieIdForTracking.length > 0) {
			$("#zoomtoextentbtn").prop('disabled', false);
			fetchDataForTracking();
		} else {
			$("#zoomtoextentbtn").prop('disabled', true);
			clearTimeout(fetchDataForTrackingTimeoutCall);
			removeAllMarkers('track');
			vectorAgps.getSource().clear();
		}
		fetchDataForTracking();
	});
    fetchDataForTracking();
    function fetchDataForTracking() {
        timerForDevicePositionDataAjax = $.ajax({
            url:BASEURL + "controlcentre/get_device_position_data",
            method:"POST",
            data:{allselecteddevices:globalCheckedDevieIdForTracking},
            global:false,
            dataType:"json"
        }).done(function (resp) {
            // console.log(resp)
            var respObj = resp.result;
            var dataLength = Object.keys(respObj).length;
            var isMarkerAdded = 0;
            if (dataLength > 0) {
				fetchGeofenceAgps(respObj);
                for (var i = 0;i < dataLength;i++) {
                    if (holdExistingTrackingId.indexOf(respObj[i].deviceid) == -1) {
                        addMarker(respObj[i],'track');
                        isMarkerAdded = 1;
                    } else {
                        // console.lo g(respObj[i].deviceid)
                        //if (respObj[i].deviceid != selectedDeviceOPenedPopUp) {
                            moveMarker(respObj[i],'track');
                        //}
                    }
                }
            }
        }).fail(function () {
            console.log("error in fetching")
        }).complete(function () {
            fetchDataForTrackingTimeoutCall = setTimeout(function () {
                fetchDataForTracking();
            },30000);
        });
		xhrdataPool.push(timerForDevicePositionDataAjax);
    }
	
	function fetchGeofenceAgps(respObj) {
        /*$.ajax({
            url:BASEURL + "controlcentre/get_buffer_by_point",
            method:"POST",
            data:{data:respObj},
            global:false,
            dataType:"json"
        }).done(function (resp) {
            // console.log(resp)
			vectorAgps.getSource().clear();
            var respObj = resp.result;
            var dataLength = Object.keys(respObj).length;
            if (dataLength > 0) {
                for (var i = 0;i < dataLength;i++) {
                    var polyCoordinates = [];
					if (typeof respObj[i].lonlat !== 'undefined' && respObj[i].lonlat.length > 1) {
						//console.log(respObj[i])
						var id = respObj[i].id;
						var coordinates = respObj[i].lonlat.split(",");
						var j = 0;
						var coordinateArr = [];
						
						while (j != (coordinates.length - 2)) {
							coordinateArr.push([coordinates[j],coordinates[j + 1]]);
							j += 2;
							// coordinateArr.push(myArray.splice(0, chunk_size));
						}
						for (var k = 0;k < coordinateArr.length;k++) {
							polyCoordinates.push(ol.proj.transform([parseFloat(
										coordinateArr[k][0]),parseFloat(
										coordinateArr[k][1])],'EPSG:4326',
									'EPSG:900913'));
						}
						var feature = new ol.Feature({
							geometry:new ol.geom.Polygon([polyCoordinates])
						});
						feature.setId("agpsgeofences_" + id);
						if(respObj[i].sourcetype == 'AGPS'){
							feature.setStyle([
								new ol.style.Style({
									stroke:new ol.style.Stroke({
										color:'#FF6347',
										width:2,
			 
									}),
									fill: new ol.style.Fill({
									  color:'rgb(255,99,71,0.1)'
									})
								})
							]);
						}
						else {
							feature.setStyle([
								new ol.style.Style({
									stroke:new ol.style.Stroke({
										color:'#1E90FF',
										width:2,
			 
									}),
									fill: new ol.style.Fill({
									  color:'rgb(30,144,255,0.1)',
									})
								})
							]);
						}
						
						sourceAgps.addFeature(feature);
					}
                }
				//console.log(polyCoordinates);
            }
        }).fail(function () {
            console.log("error in fetching")
        }).complete(function () {
        });*/
		vectorAgps.getSource().clear();
		var dataLength = Object.keys(respObj).length;
		if (dataLength > 0) {
			for (var i = 0;i < dataLength;i++) {
				drawCircleInMeter(map, respObj[i].radius, respObj[i]);

				//console.log(map.getView().getCenter());
				//console.log(ol.proj.transform([parseFloat(respObj[i].longitude),parseFloat(respObj[i].latitude)],'EPSG:4326','EPSG:3857'));
			}
		}
    }

	// Draw circle in meter radius
	/*var drawCircleInMeter = function(map, radius, respObj) {
        var view = map.getView();
        var projection = view.getProjection();
        var resolutionAtEquator = view.getResolution();
        var center = ol.proj.transform([parseFloat(respObj.longitude),parseFloat(respObj.latitude)],'EPSG:4326','EPSG:3857');//map.getView().getCenter(); 
        var pointResolution = projection.getPointResolution(resolutionAtEquator, center);
        var resolutionFactor = resolutionAtEquator/pointResolution;
        var radius = (radius / ol.proj.METERS_PER_UNIT.m) * resolutionFactor;


        var circle = new ol.geom.Circle(center, radius);
        var circleFeature = new ol.Feature(circle);
		
		circleFeature.setId("agpsgeofences_" + respObj.deviceid);
		if(respObj.sourcetype == 'AGPS'){
			circleFeature.setStyle([
				new ol.style.Style({
					stroke:new ol.style.Stroke({
						color:'#FF6347',
						width:2,

					}),
					fill: new ol.style.Fill({
					  color:'rgb(255,99,71,0.1)'
					})
				})
			]);
		}
		else {
			circleFeature.setStyle([
				new ol.style.Style({
					stroke:new ol.style.Stroke({
						color:'#1E90FF',
						width:2,

					}),
					fill: new ol.style.Fill({
					  color:'rgb(30,144,255,0.1)',
					})
				})
			]);
		}
		sourceAgps.addFeature(circleFeature);
    }*/

	// Draw circle in meter radius
	var drawCircleInMeter = function (map, radius, respObj) {
        var center = ol.proj.fromLonLat([parseFloat(respObj.longitude), parseFloat(respObj.latitude)]); // Convert to map projection

        // ✅ Correct way to calculate the radius in meters
        var circle = new ol.geom.Circle(center, radius);

        var circleFeature = new ol.Feature(circle);
        circleFeature.setId("agpsgeofences_" + respObj.deviceid);

        // ✅ Apply styles based on `sourcetype`
        var strokeColor = respObj.sourcetype == 'AGPS' ? '#FF6347' : '#1E90FF';
        var fillColor = respObj.sourcetype == 'AGPS' ? 'rgba(236, 43, 17, 0.1)' : 'rgba(236, 17, 171, 0.12)';

        circleFeature.setStyle([
            new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: strokeColor,
                    width: 2
                }),
                fill: new ol.style.Fill({
                    color: fillColor
                })
            })
        ]);

        // ✅ Add feature to the source
        sourceAgps.addFeature(circleFeature);
    };
	
    function resolveSos(resolveid,schema,callfromleftpanel) {

        $.ajax({
            url:BASEURL + "controlcentre/resolvesos",
            method:"POST",
            dataType:"json",
            data:{id:resolveid,schema:schema}
        }).done(function (resp) {
            if (typeof resp.status !== 'undefined' && resp.status == '1') {
                $("#sosid_" + resolveid).css('background-color','');
                $("#sostdid_" + resolveid).html('Resolved');
            }
        }).fail(function () {
            console.log("resolve error");
        }).complete(function(){
            if(typeof callfromleftpanel !== 'undefined' && callfromleftpanel == 1){
                //initOnLoad();
            }
        });
    }
    function resolveAlert(resolveid,schema,callfromleftpanel) {

        $.ajax({
            url:BASEURL + "controlcentre/resolvealert",
            method:"POST",
            dataType:"json",
            data:{id:resolveid,schema:schema}
        }).done(function (resp) {
            if (typeof resp.status !== 'undefined' && resp.status == '1') {
                $("#alertid_" + resolveid).css('background-color','');
                $("#alerttdid_" + resolveid).html('Resolved');
            }
        }).fail(function () {
            console.log("resolve error");
        }).complete(function(){
            if(typeof callfromleftpanel !== 'undefined' && callfromleftpanel == 1){
                //initOnLoad();
            }
        });
    }

    var getDetailsOfDeviceajax;
	function getDetailsOfDevice(deviceid,globalValuePassed) {        
		var globalValue = true;
        if (typeof globalValuePassed !== 'undefined') {
            globalValue = globalValuePassed;
        }
        getDetailsOfDeviceajax = $.ajax({
            url:BASEURL + "controlcentre/getdetailsofdevice",
            type:"POST",
            data:{deviceid:deviceid},
            dataType:"json",
            global:globalValue
        }).done(function (resp) {

            if (typeof resp.status !== 'undefined' && resp.status == '1') {

                if (typeof resp.result.alertdata !== 'undefined' && resp.result.alertdata.length > 0) {
                    $("#tab2").html(resp.result.alertdata);
                }
                if (typeof resp.result.calldata !== 'undefined' && resp.result.calldata.length > 0) {
                    $("#tab4").html(resp.result.calldata);
                }
                if (typeof resp.result.sosdata !== 'undefined' && resp.result.sosdata.length > 0) {
                    $("#tab3").html(resp.result.sosdata);
                }

                if (typeof resp.result.devicedata !== 'undefined' && resp.result.devicedata.length > 0) {
                    $("#tab1").html(resp.result.devicedata);
                }
                if (typeof resp.result.deviceheader !== 'undefined' && resp.result.deviceheader.length > 0) {
                    var header = resp.result.deviceheader;
                    if(header.length > 10){
                        header = header.substring(0,7)+"...";
                    }
                    $("#popupheader").html(header).attr('title',resp.result.deviceheader);
                }
                if (typeof resp.result.actionhtmls !== 'undefined' && resp.result.actionhtmls.length > 0) {

                    $("#tab5").html(resp.result.actionhtmls);
                }

//                    var resultdata = resp.result;
//                    if(type == 'calls'){
//                        $("#popupheader").html('Calls');
//                        $("#tab1").html(resultdata);
//                    }
//                     if(type == 'alerts'){
//                        $("#popupheader").html('Alerts');
//                        $("#tab1").html(resultdata);
//                    }
//                     if(type == 'sos'){
//                        $("#popupheader").html('SOS');
//                        $("#tab1").html(resultdata);
//                    }
            }
        }).fail(function () {
            console.log("Fetch error");
        }).complete(function () {
            getDetailsOfDeviceCall = setTimeout(function () {
                getDetailsOfDevice(deviceid,false)
            },30000);
        });
    }
	
	function getsinglesosbyid(id) {        
        $.ajax({
            url:BASEURL + "controlcentre/getsinglesosbyid",
            type:"POST",
            data:{id:id},
            dataType:"json"
        }).done(function (resp) {
            if (typeof resp.status !== 'undefined' && resp.status == '1') {
				
                if (typeof resp.html !== 'undefined' && resp.html.length > 0) {
                    $("#alerttab1").html(resp.html);
                }
                if (typeof resp.result.divise_serial !== 'undefined' && resp.result.divise_serial.length > 0) {
                    var header = resp.result.divise_serial;
                    $("#popupheaderalert").html(header).attr('title',resp.result.divise_serial);
                }
            }
        }).fail(function () {
            console.log("Fetch error");
        }).complete(function () {
            $("#alertpopup").show();
        });
    }
	
	function getsinglealertbyid(id) {        
        $.ajax({
            url:BASEURL + "controlcentre/getsinglealertbyid",
            type:"POST",
            data:{id:id},
            dataType:"json"
        }).done(function (resp) {
            if (typeof resp.status !== 'undefined' && resp.status == '1') {
				
                if (typeof resp.html !== 'undefined' && resp.html.length > 0) {
                    $("#alerttab1").html(resp.html);
                }
                if (typeof resp.result.divise_serial !== 'undefined' && resp.result.divise_serial.length > 0) {
                    var header = resp.result.divise_serial;
                    $("#popupheaderalert").html(header).attr('title',resp.result.divise_serial);
                }
            }
        }).fail(function () {
            console.log("Fetch error");
        }).complete(function () {
            $("#alertpopup").show();
        });
    }
	
	function getsinglecallbyid(id) {        
        $.ajax({
            url:BASEURL + "controlcentre/getsinglecallbyid",
            type:"POST",
            data:{id:id},
            dataType:"json"
        }).done(function (resp) {
            if (typeof resp.status !== 'undefined' && resp.status == '1') {
				
                if (typeof resp.html !== 'undefined' && resp.html.length > 0) {
                    $("#alerttab1").html(resp.html);
                }
                if (typeof resp.result.divise_serial !== 'undefined' && resp.result.divise_serial.length > 0) {
                    var header = resp.result.divise_serial;
                    $("#popupheaderalert").html(header).attr('title',resp.result.divise_serial);
                }
            }
        }).fail(function () {
            console.log("Fetch error");
        }).complete(function () {
            $("#alertpopup").show();
        });
    }

    var londetails,latdetails;
    var geofencingStoreData = {};
    var geoFenceCoordinates = [];
    function geofencingMark(deviceid,lon,lat,serialno,name,schema_name,userid) {
        londetails = lon;
        latdetails = lat;
        geofencingStoreData.deviceid = deviceid;
        geoFenceMode = "device";
        $("#geofencemodedevice").show();
        getExistingGeofencingData(schema_name,deviceid,userid,name,serialno);
    }

    function getExistingGeofencingData(schema_name,deviceid,userid,name,serialno) {
        var dataLoad = {};
        if (arguments.length > 1) {
            dataLoad = {
                schemaname:schema_name,
                deviceid:deviceid,
                userid:userid
            };
        }

        $.ajax({
            url:BASEURL + 'controlcentre/getgeofencing',
            type:'POST',
            dataType:'json',
            data:dataLoad,
            beforeSend:function () {
                $("#existingfences").html('').html(
                        '<option value="">Select</option>');
            }
        }).done(function (resp) {

            if (typeof resp.status !== 'undefined' && resp.status == 1) {
                if (geoFenceMode != "general") {
                    $("#deviceserialGFModal").html(
                            " for device : " + name + " (" + serialno + ")");
                }
                //$("#geofencingmodal").modal();
                if (geoFenceMode != "general") {
                    if (Object.keys(
                            resp.result.resultAssignedLonLat).length > 0) {
                        geoFenceCoordinates = resp.result.resultAssignedLonLat;
                        $("#deleteGeofencingMarks").show().attr("onclick",
                                "deleteDeviceGeoFencing(" + deviceid + ")");
                    }
                }
                if (Object.keys(
                        resp.result.resultAvailableLonLat).length > 0) {
                    var innerhtml = '';
                    alreadyCreatedGeofences = resp.result.resultAvailableLonLat;
                    for (var i in resp.result.resultAvailableLonLat) {
                        if (geoFenceMode != "general") {
                            if (resp.result.resultAvailableLonLat[i].clonedfrom == resp.result.assignedid || resp.result.resultAvailableLonLat[i].id == resp.result.assignedid) {
                                innerhtml += "<option value='" + resp.result.resultAvailableLonLat[i].id + "' selected='selected'>" + resp.result.resultAvailableLonLat[i].geoname + "</option>";
                            } else {
                                innerhtml += "<option value='" + resp.result.resultAvailableLonLat[i].id + "'>" + resp.result.resultAvailableLonLat[i].geoname + "</option>";
                            }
                        } else {
                            innerhtml += "<option value='" + resp.result.resultAvailableLonLat[i].id + "'>" + resp.result.resultAvailableLonLat[i].geoname + "</option>";
                            $("#existingfences").val('');
                        }
                    }
                    $("#existingfences").append(innerhtml);
                    if (geoFenceMode == "general") {
                        $("#existingfences").val('');
                        $(document).on('change',"#existingfences",function () {
                            var selectedValue = $(this).val();
                            if (selectedValue != '') {
                                $("#deleteGeofencingMarks").show().attr(
                                        "onclick",
                                        "deleteGeoFencing(" + selectedValue + ")");
                            } else {
                                $("#deleteGeofencingMarks").hide().removeAttr(
                                        'onclick');
                            }
                        });
                    }
                }
            }
        }).fail(function () {
            console.log("fetch error in geofence")
        });
    }

    function changeIconSet(deviceid) {
        $("#changeicon").modal();
    }

</script>
<script>
    $(document).ready(function () {
        $('body').on('focus',".frmto",function () {
            $(this).timepicker({
                timeFormat:'HH:mm:ss',
            });
        });

        $('body').on('focus',".tmpckr",function () {
            $(this).timepicker({
                timeFormat:'HH:mm',
            });
        });
		$('body').on('focus',"#nomovementtimeval",function () {
            $(this).timepicker({
				timeFormat:'HH:mm',
				hourMax: '2',
				minuteMax: '30'
            });
        });
		$('body').on('focus',"#nodataduration",function () {
            $(this).timepicker({
				timeFormat:'ss',
				secondMax: '59'
            });
        });
        $('body').on('click',"input[type='checkbox']",function () {
                 var that = this;
            
                var getid = $(that).attr('id');
                if (getid.indexOf('proxipoi_') != -1) {
                    var objectid = getid.split('_');
                    if ($(that).prop('checked') == true) {                       
                        if($("#offroutepoi_"+objectid[1]).prop('checked') == true){
                            $("#setalertmsg").html('Already set in Offroute');
                            $(that).prop('checked',false);
                            return false;
                        }
                       
                    $("#proxipoiFrom_" + objectid[1]).val('00:00:00');
                    $("#proxipoiTo_" + objectid[1]).val('23:59:59');
                    }else{
                         $("#proxipoiFrom_" + objectid[1]).val('');
                    $("#proxipoiTo_" + objectid[1]).val('');
                    }
                    
                }
                if (getid.indexOf('proxiroute_') != -1) {
                    var objectid = getid.split('_');
                     if ($(that).prop('checked') == true) {
                         if($("#offrouteroute_"+objectid[1]).prop('checked') == true){
                            $("#setalertmsg").html('Already set in Offroute');
                            $(that).prop('checked',false);
                            return false;
                        }                       
                        $("#proxirouteFrom_" + objectid[1]).val('00:00:00');
                        $("#proxirouteTo_" + objectid[1]).val('23:59:59');
                     }else{
                        $("#proxirouteFrom_" + objectid[1]).val('');
                        $("#proxirouteTo_" + objectid[1]).val('');
                     }
                  
                }
                if (getid.indexOf('proxigeo_') != -1) {
                    var objectid = getid.split('_');
                     if ($(that).prop('checked') == true) {
                          if($("#offroutegeo_"+objectid[1]).prop('checked') == true){
                            $("#setalertmsg").html('Already set in Offroute');
                            $(that).prop('checked',false);
                            return false;
                        }
                        
                    $("#proxigeoFrom_" + objectid[1]).val('00:00:00');
                    $("#proxigeoTo_" + objectid[1]).val('23:59:59');
                    }else{
                         $("#proxigeoFrom_" + objectid[1]).val('');
                    $("#proxigeoTo_" + objectid[1]).val('');
                    }
                }
                if (getid.indexOf('offroutepoi_') != -1) {
                    var objectid = getid.split('_');
                     if ($(that).prop('checked') == true) {
                         if($("#proxipoi_"+objectid[1]).prop('checked') == true){
                            $("#setalertmsg").html('Already set in Proximity');
                            $(that).prop('checked',false);
                            return false;
                        }
                    $("#offroutepoiFrom_" + objectid[1]).val('00:00:00');
                    $("#offroutepoiTo_" + objectid[1]).val('23:59:59');
                }else{
                     $("#offroutepoiFrom_" + objectid[1]).val('');
                    $("#offroutepoiTo_" + objectid[1]).val('');
                }
                }
                if (getid.indexOf('offrouteroute_') != -1) {
                    var objectid = getid.split('_');
                     if ($(that).prop('checked') == true) {
                         if($("#proxiroute_"+objectid[1]).prop('checked') == true){
                            $("#setalertmsg").html('Already set in Proximity');
                            $(that).prop('checked',false);
                            return false;
                        }
                        $("#offrouterouteFrom_" + objectid[1]).val('00:00:00');
                    $("#offrouterouteTo_" + objectid[1]).val('23:59:59'); 
                     }else{
                         $("#offrouterouteFrom_" + objectid[1]).val('');
                    $("#offrouterouteTo_" + objectid[1]).val('');
                     }
                    
                }
                if (getid.indexOf('offroutegeo_') != -1) {
                    var objectid = getid.split('_');
                    if ($(that).prop('checked') == true) {
                        if($("#proxigeo_"+objectid[1]).prop('checked') == true){
                            $("#setalertmsg").html('Already set in Proximity');
                            $(that).prop('checked',false);
                            return false;
                        }
                       $("#offroutegeoFrom_" + objectid[1]).val('00:00:00');
                    $("#offroutegeoTo_" + objectid[1]).val('23:59:59'); 
                    }else{
                        $("#offroutegeoFrom_" + objectid[1]).val('');
                    $("#offroutegeoTo_" + objectid[1]).val('');
                    }
                    
                }
        });


        $('body').tooltip({
            selector:"[data-tooltip=tooltip]",
            container:"body"
        });

        $("#saveGeofencingData").on('click',function () {
            $(this).attr('disabled',true);
            var description = $.trim($("#description").val());
            if (description == '') {
                $("#description").addClass("focus-required").focus();
                setTimeout(function () {
                    $("#description").removeClass("focus-required");
                },3000);
                return false;
            }
            if (geoFenceMode != "general") {

                var alertType = $.trim($("#alertsdd").val());
                if (alertType == '') {
                    $("#alertsdd").addClass("focus-required").focus();
                    setTimeout(function () {
                        $("#alertsdd").removeClass("focus-required");
                    },3000);
                    return false;

                }
                geofencingStoreData.alertid = alertType;
            }

            geofencingStoreData.description = description;
            geofencingStoreData.bufferarea = ($.trim($("#bufferarea").
                    val()) == '') ? 0 : $.trim($("#bufferarea").val());
            $.ajax({
                url:BASEURL + 'controlcentre/storegeofencing',
                type:'POST',
                dataType:'json',
                data:geofencingStoreData,
                beforeSend:function () {
                    $("#showmsgSavemodal").html('Please wait...')
                }
            }).done(function (resp) {
                if (typeof resp.status !== 'undefined' && resp.status == 1) {
                    if (resp.result == 1) {
                        //console.log(alertify)
                        $("#showmsgSavemodal").html("Geofenced successfully");
                        setTimeout(function () {
                            $("#saveGeofencing, #geofencingmodal").modal(
                                    'hide');
                        },2000);
						getallinteractionlist();
                    } else if (resp.result == 2) {
                        $("#showmsgSavemodal").html(
                                "Name given already exists");
                        setTimeout(function () {
                            $("#showmsgSavemodal").html('');
                        },3000);
                    } else if (resp.result == 4) {
                        $("#showmsgSavemodal").html(
                                "Permission Restricted");
                        setTimeout(function () {
                            $("#showmsgSavemodal").html('');
                        },3000);
                    } else if (resp.result == 3) {
                        $("#showmsgSavemodal").html("Failed to Geofence");
                        setTimeout(function () {
                            $("#showmsgSavemodal").html('');
                        },3000);
                    }
                }
            }).fail(function () {
                $("#showmsgSavemodal").html("Failed to store geo fencing");
                setTimeout(function () {
                    $("#showmsgSavemodal").html('');
                },3000);
            }).complete(function () {
                $("#saveGeofencingData").removeAttr('disabled');
            });
        });

        $("#updateicon").on('click',function () {
            $.ajax({
                url:BASEURL + "controlcentre/updateicon",
                type:'POST',
                dataType:'json',
                data:{
                    deviceid:deviceid,
                    icontypeid:icontypeid
                }
            }).done(function (resp) {

            }).fail(function () {
                console.log("failed to update");
            });
        });
    });

    function changec(isclosed) {
        var xDiv = document.getElementById('changediv');

        if (typeof isclosed !== 'undefined') {
            xDiv.style.height = '';
            xDiv.style.overflow = '';

        } else {
            if (xDiv.style.height == '')
            {
                xDiv.style.height = '350px'

            } else
            {
                xDiv.style.height = ''
            }

            if (xDiv.style.overflow == '')
            {
                xDiv.style.overflow = 'auto'
                //alert("ok");
            } else
            {
                xDiv.style.overflow = ''
                //alert("not ok");
            }
        }
    }

    $("#geofencingmodal").on('shown.bs.modal',function () {
        $("#clearGeofencingMarks").show().on('click',function () {
            vector.getSource().clear();
            vectorGeo.getSource().clear();
        });
        geofencingmap = new ol.Map({
            layers:[raster,vector,vectorGeo],
            target:'geofencingmap',
            view:new ol.View({
                center:map.getView().getCenter(),
                zoom:14
            }),
            crossOrigin:'anonymous',
            controls:ol.control.defaults().extend([
                new ol.control.FullScreen()
            ])
        });
        geofencingmap.on('click',function (e) {
            var feature = geofencingmap.forEachFeatureAtPixel(e.pixel,
                    function (feature,layer) {
                        return feature;
                    });
            if (feature) {
                var featureid = feature.getId();
                if (geoFenceMode != "general") {
                    if (typeof featureid !== 'undefined' && featureid.indexOf(
                            'existinggeofences_') != -1) {
                        var cloneid = featureid.split("_");
                        geofencingStoreData.cloneid = cloneid[1];
                        var dialogButtons = {
                            "Assign":function () {
                                $("#dialog_confirm").dialog("close");
                                $("#alertsddgeoexistingSave").modal();
                            },
                            "Discard":function () {
                                $("#dialog_confirm").dialog("close");
                            }
                        };
                        var closeFn = function () {
                            $("#dialog_confirm").removeAttr('title');
                            $("#alert_msg").html(
                                    '');
                        }
                        showDialogs('Attach GeoFencing',
                                'Are you sure, you want to attach this Geofencing?',
                                dialogButtons,closeFn);
                    }
                }
            }
        });


        geofencingmap.on('pointermove',function (e) {
            var feature = geofencingmap.forEachFeatureAtPixel(e.pixel,
                    function (feature,layer) {
                        return feature;
                    });
            if (feature) {
                var featureid = feature.getId();
                if (typeof featureid !== 'undefined' && featureid.indexOf(
                        'existinggeofences_') != -1) {
                    geofencingmap.removeInteraction(drawpoly);
                    $("#" + geofencingmap.getTarget()).css('cursor',
                            'pointer');
                } else {
                    $("#" + geofencingmap.getTarget()).css('cursor','');
                    addInteraction(1);
                }
            } else {
                $("#" + geofencingmap.getTarget()).css('cursor','');
                addInteraction("fromgeofence",0);
            }
            if (e.dragging) {
                //$(element).popover('hide');
                return;
            }
        });

        //  var geoFenceCoordinates = [];
        if (geoFenceCoordinates.length > 0) {
            var polyCoordinates = [];
            for (var i in geoFenceCoordinates) {
                polyCoordinates.push(ol.proj.transform([parseFloat(
                            geoFenceCoordinates[i][0]),parseFloat(
                            geoFenceCoordinates[i][1])],'EPSG:4326',
                        'EPSG:900913'));
            }
            //console.log(polyCoordinates);
            var feature = new ol.Feature({
                geometry:new ol.geom.Polygon([polyCoordinates])
            });
            geosource.addFeature(feature);
        }
        addInteraction("fromgeofence",1);
    });
    $("#geofencingmodal").on('hidden.bs.modal',function () {
        geofencingStoreData = {};
        geoFenceCoordinates = [];
        geofencingmap.setTarget(null);
        geofencingmap = null;
        vector.getSource().clear();
        vectorGeo.getSource().clear();
        $("#deviceserialGFModal").html('');
        $("#deleteGeofencingMarks").hide().removeAttr("onclick");
    });
    $("#saveGeofencing").on('hidden.bs.modal',function () {
        $("#showmsgSavemodal").html('');
        $("#alertsdd").val('');
        $("#description").val('');
		if(geofencingmap){
			if (typeof drawpoly !== 'undefined') {
				geofencingmap.removeInteraction(drawpoly);					
			}
			geofencingStoreData = {};
			geoFenceCoordinates = [];
			if (geofencingmapclick) {
				ol.Observable.unByKey(geofencingmapclick);
			}
			if (geofencingmappointermove) {
				ol.Observable.unByKey(geofencingmappointermove);
			}
			//geofencingmap.setTarget(null);				
			vector.getSource().clear();
			vectorGeo.getSource().clear();
			drawpoly = null;
			geofencingmap = null;
			mapMode = "";
			$("#deviceserialGFModal").html('');
			$("#deleteGeofencingMarks").hide().removeAttr("onclick");
		}
		geofencingmap = map;
    });

    // var typeSelect = document.getElementById('geofencingtype');


    function addInteraction(calledFrom,createDrawObject,selectedvalue) {
        // var value = typeSelect.value; console.log(typeSelect.value) 
//        if (value !== 'None' && value !=='Reset') {

        if (typeof createDrawObject !== 'undefined' && createDrawObject == 1) {
            var drawtype = '';
            if (calledFrom == 'fromgeofence') {
                drawpoly = new ol.interaction.Draw({
                    type: ('Polygon') //** @type {ol.geom.GeometryType} */
                });
            }
            if (calledFrom == 'calledfromroute') {
                drawLine = new ol.interaction.Draw({
                    source:sourcePOI,
                    type: ('LineString') //** @type {ol.geom.GeometryType} */
                });
            }

            if (calledFrom == 'calledfrompoi') {
                drawLine = new ol.interaction.Draw({
                    source:sourcePOI,
                    type: (selectedvalue) //** @type {ol.geom.GeometryType} */
                });
            }

        }
        if (calledFrom == 'fromgeofence') {
            geofencingmap.addInteraction(drawpoly);
        }
        if (calledFrom == 'calledfromroute') {
            mapPOI.addInteraction(drawLine);
        }
         if (calledFrom == 'calledfrompoi') {
            mapPOI.addInteraction(drawLine);
        }
//        }
//        if(value == 'Reset'){            
//            vector.getSource().clear();
//        }
        if (calledFrom == 'fromgeofence') {

            drawpoly.on('drawstart',function (event) {
				geosource.clear();
            });

            drawpoly.on('drawend',function (evt) {
				if(mapMode == "geofence"){
					var feature = evt.feature;
					var coords = feature.getGeometry().getCoordinates();
					var mainCoordinates = coords[0];
					var coordLen = Object.keys(mainCoordinates).length;
					var lonLat = [];

					if (calledFrom == 'fromgeofence') {
						if (coordLen > 0) {
							for (var i in mainCoordinates) {
								lonLat.push(ol.proj.transform([
									mainCoordinates[i][0],
									mainCoordinates[i][1]],'EPSG:3857',
										'EPSG:4326'));
							}
							geofencingStoreData.longLats = lonLat;
							if (calledFrom == 'fromgeofence') {
								$("#geofencemodedevice").hide();
								$("#saveGeofencing").modal();
							}
						}
					}

					//   
					//   //		var coords_str = coords.toString();
	//		var coord_arr = coords_str.split(',');
				}
            });
        }
        if (calledFrom == 'calledfromroute' || calledFrom == 'calledfrompoi') {
            drawLine.on('drawstart',function (event) {
                sourcePOI.clear();
                if (calledFrom == 'calledfromroute') {
                    $("#showdistance").html('');
                    var sketch = event.feature;
                    sketch.getGeometry().on('change',function (evt) {
                        var geom = evt.target;
                        if (geom instanceof ol.geom.LineString) {                         
                            tooltipRoute.setPosition(transeferMyCoordinates);
                            overlayToolTipRoute.innerHTML = formatLength(
                                    geom);
                        }
                        // console.log(formatLength(geom)); 

                    });
                }

            });
            drawLine.on('drawend',function (evt) {
                var feature = evt.feature;
                poiAndRouteData.feature = feature;
                var coords;
                if (calledFrom == 'calledfrompoi' && selectedvalue == 'Circle') {
                    coords = feature.getGeometry().getRadius();
                } else {
                    coords = feature.getGeometry().getCoordinates();
                }
                var mainCoordinates;
                if (calledFrom == 'calledfromroute') {
                    mainCoordinates = coords;
                } else {
                    mainCoordinates = coords[0];
                }
                var coordLen = Object.keys(mainCoordinates).length;
                var lonLat = [];

                if (calledFrom == 'calledfrompoi') {
                    if (coordLen > 0) {
                        for (var i in mainCoordinates) {
                            lonLat.push(ol.proj.transform([
                                mainCoordinates[i][0],
                                mainCoordinates[i][1]],'EPSG:3857',
                                    'EPSG:4326'));
                        }
                        geofencingStoreData.longLats = lonLat;
                        //console.log(geofencingStoreData)
                        $(".bufferarea").hide();
                        $("#savedatapoiandroute").modal({
                            backdrop:'static',
                            keyboard:false
                        });
                    }
                }
                if (calledFrom == 'calledfromroute') {

                    //console.log(mainCoordinates); return false;


                    if (coordLen > 0) {
                        for (var i in mainCoordinates) {
                            lonLat.push(ol.proj.transform([
                                mainCoordinates[i][0],
                                mainCoordinates[i][1]],'EPSG:3857',
                                    'EPSG:4326'));
                        }
                        geofencingStoreData.longLats = lonLat;
                        //console.log(geofencingStoreData)
                        //  $(".bufferarea").hide();
                        $("#savedatapoiandroute").modal({
                            backdrop:'static',
                            keyboard:false
                        });
                    }
                }
            });

        }
    }

    function formatLength(line) {
        var length;
//  if (geodesicCheckbox.checked) {
//    var coordinates = line.getCoordinates();
//    length = 0;
//    var sourceProj = map.getView().getProjection();
//    for (var i = 0, ii = coordinates.length - 1; i < ii; ++i) {
//      var c1 = ol.proj.transform(coordinates[i], sourceProj, 'EPSG:4326');
//      var c2 = ol.proj.transform(coordinates[i + 1], sourceProj, 'EPSG:4326');
//      length += wgs84Sphere.haversineDistance(c1, c2);
//    }
//  } else {
        length = Math.round(line.getLength() * 100) / 100;
//  }
        var output;
        if (length > 100) {
            output = (Math.round(length / 1000 * 100) / 100) +
                    ' ' + 'km';
        } else {
            output = (Math.round(length * 100) / 100) +
                    ' ' + 'm';
        }
        return output;
    }


    function checkCharsLeft(numb,value,elem) {
        $(elem).html(parseInt(numb) - parseInt(
                value.length) + " characters left");
    }


    function deleteDeviceGeoFencing(deviceid,isGlobal) {
        //  console.log(isGlobal)
        $("#dialog_confirm").attr('title','Delete existing geofence');
        $("#alert_msg").html('Are you sure?');
        var dataLoad = {};
        if (typeof isGlobal === 'undefined') {
            dataLoad.deviceid = deviceid;
        } else {
            dataLoad.geofenceid = deviceid;
        }

        $("#dialog_confirm").dialog({
            resizable:false,
            height:"auto",
            width:400,
            modal:true,
            buttons:{
                "OK":function () {
                    $.ajax({
                        url:BASEURL + "controlcentre/deletegeofencing",
                        dataType:'json',
                        type:'POST',
                        data:dataLoad,
                    }).done(function (data) {
                        $("#dialog_confirm").dialog(
                                "close");
                        if (typeof data.status !== 'undefined') {
                           // console.log(data.result);
                            if (data.result == 1) {
                                $("#deleteGeofencingMarks").hide();
                                vector.getSource().clear();
                                vectorGeo.getSource().clear();
                                var dialogButtons = {
                                    "OK":function () {
                                        $("#dialog_confirm").dialog("close");
                                    }
                                };
                                var closeFn = function () {
                                    $("#dialog_confirm").removeAttr(
                                            'title');
                                    $("#alert_msg").html('');
                                }
                                showDialogs('Geofence',
                                        'Successfully removed Geofencing',
                                        dialogButtons,closeFn);
                            }
                        }

                    }).fail(function () {
                        console.log("failed to update")
                    });
                },
                Cancel:function () {
                    $("#dialog_confirm").dialog("close");
                }
            },
            close:function () {
                $("#dialog_confirm").removeAttr('title');
                $("#alert_msg").html('');
            }
        });

    }

    var deviceAlerts = {};
    function alertmanagement(deviceid) {
        //console.log(deviceid);
		selectedDeviceOPenedPopUp = deviceid;
        if (deviceid != '') {
            $.ajax({
                url:BASEURL + "controlcentre/get_device_alerts_assigned",
                dataType:'json',
                type:'POST',
                data:{
                    deviceid:deviceid
                }
            }).done(function (resp) {
                //console.log(resp)
                if (typeof resp.status !== 'undefined') {
                    if (Object.keys(resp.result).length > 0) {
                        deviceAlerts = resp.result;
						var alerttitle = 'Set Alert ('+resp.dev_data.serial_no;
						if(resp.dev_data.device_name){
							alerttitle = alerttitle+ ' - '+resp.dev_data.device_name;
						}
						alerttitle = alerttitle+')';
						$("#alertconfiguretitle").html(alerttitle);
                    }
                }
            }).fail(function () {
                console.log("fetch error");
            }).complete(function () {				
                $("#configurealert").modal();
            });
        }

        // $("#alertdeviceid").val('').val(deviceid);
    }

//    $("#alertid").on('change',function () {
//        var alertid = $.trim($(this).val());
//        $(".alertforms").hide();
//        if (alertid != '') {
//            if (alertid == 1) {
//                $("#nomovement_alert").show();
//                var limit_range = (typeof deviceAlerts[alertid] !== 'undefined') ? $.trim(
//                        deviceAlerts[alertid].limit_range) : '';
//
//                var activedata = (typeof deviceAlerts[alertid] !== 'undefined') ? $.trim(
//                        deviceAlerts[alertid].active) : '';
//                $("#timerangnomovement").val(limit_range);
//                if (activedata != '') {
//                    $(
//                            "input:radio[name='timerangnomovementonoff'][value='" + deviceAlerts[alertid].active + "']").
//                            prop('checked',true);
//                }
//            }
//        } else {
//
//        }
//    });

    var saveAlertData = new Object();

    function setAlert() {
        saveAlertData.lowbattery = {};
		saveAlertData.freefall = {};
        saveAlertData.nomovement = {};
        saveAlertData.overspeed = {};
        saveAlertData.emaillists = {};
        saveAlertData.phonelists = {};
        saveAlertData.deviceid = {};
        saveAlertData.nodata = {};
        saveAlertData.deviceid = selectedDeviceOPenedPopUp;
        var isAllowSave = true;
         //---------------------Free Fall-----------------------
        var freefallonoff = $("[name='freefallonoff']");
        var freefallnotificationonoffemail = $("[name='freefallnotificationonoffemail']");
        var freefallnotificationonoffphone = $("[name='freefallnotificationonoffphone']");
        
        if (freefallonoff.filter(":checked").val() == '1') {
            saveAlertData.freefall.active = 1;
        } else {
            saveAlertData.freefall.active = 2;
        }
        saveAlertData.freefall.freefallnotificationonoffemail = $.trim(freefallnotificationonoffemail.filter(":checked").val());
        saveAlertData.freefall.freefallnotificationonoffphone = $.trim(freefallnotificationonoffphone.filter(":checked").val());
		//---------------------Low Battery-----------------------
        var notifyemail = $("[name='notifyemail']");
        var notifyphone = $("[name='notifyphone']");
        var lowbatteryonoff = $("[name='lowbatteryonoff']");
        var lowbatterynotificationonoffemail = $("[name='lowbatterynotificationonoffemail']");
        var lowbatterynotificationonoffphone = $("[name='lowbatterynotificationonoffphone']");
        
        if (lowbatteryonoff.filter(":checked").val() == '1') {
            saveAlertData.lowbattery.active = 1;
        } else {
            saveAlertData.lowbattery.active = 2;
        }
        saveAlertData.lowbattery.batval = 30;
        saveAlertData.lowbattery.lowbatterynotificationonoffemail = $.trim(lowbatterynotificationonoffemail.filter(":checked").val());
        saveAlertData.lowbattery.lowbatterynotificationonoffphone = $.trim(lowbatterynotificationonoffphone.filter(":checked").val());
        
        saveAlertData.emaillists = $.trim(notifyemail.val());
        saveAlertData.phonelists = $.trim(notifyphone.val());
		if(saveAlertData.emaillists.split(',').length>5){
			$("#setalertmsg").html("Maximum 5 emails allowed");
			isAllowSave = false;
			return false;
		}
		if(saveAlertData.phonelists.split(',').length>5){
			$("#setalertmsg").html("Maximum 5 emails allowed");
			isAllowSave = false;
			return false;
		}
        //-------------------------No Movement------------------------
        var nomovementtimeval = $("#nomovementtimeval");
        var nomovementonoff = $("[name='nomovementonoff']");
        var nomovementtime = $("[name='nomovementtime']");
        var nomovementdaytimefrom = $("#nomovementdaytimefrom");
        var nomovementdaytimeto = $("#nomovementdaytimeto");
        var nomovementnotificationonoffemail = $("[name='nomovementnotificationonoffemail']");
        var nomovementnotificationonoffphone = $("[name='nomovementnotificationonoffphone']");
        // console.log(nomovementonoff.filter(":checked").val())
        if (nomovementonoff.filter(":checked").val() == '1') {
            if ($.trim(nomovementtimeval.val()) == '') {
                $("#setalertmsg").html(
                        "Please enter max idle time for No Movement");
                isAllowSave = false;
                return false;
            }
            if ($.trim(nomovementtime.filter(":checked").val()) == '2') {
                if ($.trim(nomovementdaytimefrom.val()) == '' || $.trim(
                        nomovementdaytimeto.val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for No Movement)");
                    isAllowSave = false;
                    return false;
                }
            }
            saveAlertData.nomovement.active = 1;
        } else {
            saveAlertData.nomovement.active = 2;
        }
		if($.trim(nomovementtimeval.val()) != '' && (nomovementtimeval.val().split(':').length ==2)){
			saveAlertData.nomovement.nomovementval = $.trim(nomovementtimeval.val())+':00';
		}
		else{
			saveAlertData.nomovement.nomovementval = $.trim(nomovementtimeval.val());
		}        
        saveAlertData.nomovement.timetype = $.trim(nomovementtime.filter(":checked").val());
		if($.trim(nomovementdaytimefrom.val()) != '' && (nomovementdaytimefrom.val().split(':').length ==2)){
			saveAlertData.nomovement.timefrom = $.trim(nomovementdaytimefrom.val())+':00';
		}
		else{
			saveAlertData.nomovement.timefrom = $.trim(nomovementdaytimefrom.val());
		}
        if($.trim(nomovementdaytimeto.val()) != '' && (nomovementdaytimeto.val().split(':').length ==2)){
			saveAlertData.nomovement.timeto = $.trim(nomovementdaytimeto.val())+':00';
		}
		else{
			saveAlertData.nomovement.timeto = $.trim(nomovementdaytimeto.val());
		}
		var starttime_arr = saveAlertData.nomovement.timefrom.split(':');
		var start_h = starttime_arr[0];
		var start_m = starttime_arr[1];
		var endtime_arr = saveAlertData.nomovement.timeto.split(':');
		var end_h = endtime_arr[0];
		var end_m = endtime_arr[1];
		var d1 = new Date(2000, 0, 1,  start_h, start_m);
		var d2 = new Date(2000, 0, 1,  end_h, end_m);
		if(d1>d2){
			$("#setalertmsg").html("No Movement End Time must be greater than Start Time");
            isAllowSave = false;
            return false;
		}
        saveAlertData.nomovement.nomovementnotificationonoffemail = $.trim(nomovementnotificationonoffemail.filter(":checked").val());
        saveAlertData.nomovement.nomovementnotificationonoffphone = $.trim(nomovementnotificationonoffphone.filter(":checked").val());

        //-------------------------Speed------------------------------
        var speedval = $("#speedval");
        var speeddayfromtime = $("#speeddayfromtime");
        var speeddaytotime = $("#speeddaytotime");
        var speedtime = $("[name='speedtime']");
        var speedonoff = $("[name='speedonoff']");
        var speednotificationonoffemail = $("[name='speednotificationonoffemail']");
        var speednotificationonoffphone = $("[name='speednotificationonoffphone']");


        if (speedonoff.filter(":checked").val() == '1') {
            if ($.trim(speedval.val()) == '') {
                $("#setalertmsg").html("Please enter max allowed speed");
                isAllowSave = false;
                return false;
            }
            if ($.trim(speedtime.val()) == '2') {
                if ($.trim(speeddayfromtime.val()) == '' || $.trim(
                        speeddaytotime.val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for Speed");
                    isAllowSave = false;
                    return false;
                }
            }

            saveAlertData.overspeed.active = 1;
        } else {
            saveAlertData.overspeed.active = 2;
        }
        saveAlertData.overspeed.overspeedval = $.trim(speedval.val());
        saveAlertData.overspeed.timetype = $.trim(speedtime.filter(":checked").val());
		if($.trim(speeddayfromtime.val()) != '' && (speeddayfromtime.val().split(':').length ==2)){
			saveAlertData.overspeed.timefrom = $.trim(speeddayfromtime.val())+':00';
		}
		else{
			saveAlertData.overspeed.timefrom = $.trim(speeddayfromtime.val());
		}
        if($.trim(speeddaytotime.val()) != '' && (speeddaytotime.val().split(':').length ==2)){
			saveAlertData.overspeed.timeto = $.trim(speeddaytotime.val())+':00';
		}
		else{
			saveAlertData.overspeed.timeto = $.trim(speeddaytotime.val());
		} 
		var starttime_arr = saveAlertData.overspeed.timefrom.split(':');
		var start_h = starttime_arr[0];
		var start_m = starttime_arr[1];
		var endtime_arr = saveAlertData.overspeed.timeto.split(':');
		var end_h = endtime_arr[0];
		var end_m = endtime_arr[1];
		var d1 = new Date(2000, 0, 1,  start_h, start_m);
		var d2 = new Date(2000, 0, 1,  end_h, end_m);
		if(d1>d2){
			$("#setalertmsg").html("Over Speed End Time must be greater than Start Time");
            isAllowSave = false;
            return false;
		}
        saveAlertData.overspeed.speednotificationonoffemail = $.trim(speednotificationonoffemail.filter(":checked").val());
        saveAlertData.overspeed.speednotificationonoffphone = $.trim(speednotificationonoffphone.filter(":checked").val());
        //-------------------No Data------------------------
      //  saveAlertData.nodata
        var nodataduration = $("#nodataduration");
        var nodataonoff = $("[name='nodataonoff']");
        var nodatatimetype = $("[name='nodatatime']");
        var nodatatimefrom = $("#nodatafromtime");
        var nodatatimeto = $("#nodatatotime");
        var nodatanotificationonoffemail = $("[name='nodatanotificationonoffemail']");
        var nodatanotificationonoffphone = $("[name='nodatanotificationonoffphone']");

        if (nodataonoff.filter(":checked").val() == '1') {
            if ($.trim(nodataduration.val()) == '') {
                $("#setalertmsg").html("Please enter No Data duration");
                isAllowSave = false;
                return false;
            }
             if ($.trim(nodatatimetype.val()) == '2') {
                if ($.trim(nodatafromtime.val()) == '' || $.trim(nodatatotime.val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for No Data");
                    isAllowSave = false;
                    return false;
                }
            }
            
            saveAlertData.nodata.active = 1;
        } else {
            saveAlertData.nodata.active = 2;
        }
		
		if($.trim(nodataduration.val()) != '' && (nodataduration.val().split(':').length ==1)){
			saveAlertData.nodata.nodataduration = '00:00:'+$.trim(nodataduration.val());
		}
		else{
			saveAlertData.nodata.nodataduration = $.trim(nodataduration.val());
		}                
        saveAlertData.nodata.nodatatimetype = $.trim(nodatatimetype.filter(":checked").val());
        if($.trim(nodatatimefrom.val()) != '' && (nodatatimefrom.val().split(':').length ==2)){
			saveAlertData.nodata.nodatatimefrom = $.trim(nodatatimefrom.val())+':00';
		}
		else{
			saveAlertData.nodata.nodatatimefrom = $.trim(nodatatimefrom.val());
		}
		if($.trim(nodatatimeto.val()) != '' && (nodatatimeto.val().split(':').length ==2)){
			saveAlertData.nodata.nodatatimeto = $.trim(nodatatimeto.val())+':00';
		}
		else{
			saveAlertData.nodata.nodatatimeto = $.trim(nodatatimeto.val());
		}
		var starttime_arr = saveAlertData.nodata.nodatatimefrom.split(':');
		var start_h = starttime_arr[0];
		var start_m = starttime_arr[1];
		var endtime_arr = saveAlertData.nodata.nodatatimeto.split(':');
		var end_h = endtime_arr[0];
		var end_m = endtime_arr[1];
		var d1 = new Date(2000, 0, 1,  start_h, start_m);
		var d2 = new Date(2000, 0, 1,  end_h, end_m);
		if(d1>d2){
			$("#setalertmsg").html("Over Speed End Time must be greater than Start Time");
            isAllowSave = false;
            return false;
		}
        saveAlertData.nodata.nodatanotificationonoffemail = $.trim(nodatanotificationonoffemail.filter(":checked").val());
        saveAlertData.nodata.nodatanotificationonoffphone = $.trim(nodatanotificationonoffphone.filter(":checked").val());

        //-----------------------Proximity---------------------------------
        saveAlertData.proxipoi = [];
        var proxpoiCheck = $("[id^='proxipoi_']");
        $.each(proxpoiCheck,function (i,v) {
            var that = this;
            if ($(that).prop('checked') == true) {
                var thisID = $(that).attr('id');
                var objectID = thisID.split('_');
                objectID = objectID[1];

                if ($.trim($("#proxipoiFrom_" + objectID).
                        val()) == '' || $.trim($("#proxipoiTo_" + objectID).
                        val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for Proximity POI");
                    isAllowSave = false;
                    return false;
                }
                saveAlertData.proxipoi.push({objectid:objectID,timefrom:$.trim(
                            $("#proxipoiFrom_" + objectID).
                            val()),timeto:$.trim($("#proxipoiTo_" + objectID).
                            val())});
				var starttime_arr = $.trim($("#proxipoiFrom_" + objectID).val()).split(':');
				var start_h = starttime_arr[0];
				var start_m = starttime_arr[1];
				var endtime_arr = $.trim($("#proxipoiTo_" + objectID).val()).split(':');
				var end_h = endtime_arr[0];
				var end_m = endtime_arr[1];
				var d1 = new Date(2000, 0, 1,  start_h, start_m);
				var d2 = new Date(2000, 0, 1,  end_h, end_m);
				if(d1>d2){
					$("#setalertmsg").html("Zone In End Time must be greater than Start Time");
					isAllowSave = false;
					return false;
				}
            }

        });
        //  var proxipoiFrom = $("[id^='proxipoiFrom_']");
        //   var proxipoiTo   = $("[id^='proxipoiTo_']");

        saveAlertData.proxiroute = [];
        var proxirouteCheck = $("[id^='proxiroute_']");
        $.each(proxirouteCheck,function (i,v) {
            var that = this;
            if ($(that).prop('checked') == true) {
                var thisID = $(that).attr('id');
                var objectID = thisID.split('_');
                objectID = objectID[1];
                if ($.trim($("#proxirouteFrom_" + objectID).
                        val()) == '' || $.trim($("#proxirouteTo_" + objectID).
                        val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for Proximity Route");
                    isAllowSave = false;
                    return false;
                }
                saveAlertData.proxiroute.push(
                        {objectid:objectID,timefrom:$.trim($(
                                    "#proxirouteFrom_" + objectID).
                                    val()),timeto:$.trim($(
                                    "#proxirouteTo_" + objectID).val())});
				var starttime_arr = $.trim($("#proxirouteFrom_" + objectID).val()).split(':');
				var start_h = starttime_arr[0];
				var start_m = starttime_arr[1];
				var endtime_arr = $.trim($("#proxirouteTo_" + objectID).val()).split(':');
				var end_h = endtime_arr[0];
				var end_m = endtime_arr[1];
				var d1 = new Date(2000, 0, 1,  start_h, start_m);
				var d2 = new Date(2000, 0, 1,  end_h, end_m);
				if(d1>d2){
					$("#setalertmsg").html("Zone In End Time must be greater than Start Time");
					isAllowSave = false;
					return false;
				}
            }
        });
//    var proxirouteFrom  = $("[id^='proxirouteFrom_']");
//    var proxirouteTo    = $("[id^='proxirouteTo_']");

        saveAlertData.proxigeo = [];
        var proxigeoCheck = $("[id^='proxigeo_']");
        $.each(proxigeoCheck,function (i,v) {
            var that = this;
            if ($(that).prop('checked') == true) {
                var thisID = $(that).attr('id');
                var objectID = thisID.split('_');
                objectID = objectID[1];
                if ($.trim($("#proxigeoFrom_" + objectID).
                        val()) == '' || $.trim($("#proxigeoTo_" + objectID).
                        val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for Proximity Geofence");
                    isAllowSave = false;
                    return false;
                }
                saveAlertData.proxigeo.push({objectid:objectID,timefrom:$.trim(
                            $("#proxigeoFrom_" + objectID).
                            val()),timeto:$.trim($("#proxigeoTo_" + objectID).
                            val())});
				var starttime_arr = $.trim($("#proxigeoFrom_" + objectID).val()).split(':');
				var start_h = starttime_arr[0];
				var start_m = starttime_arr[1];
				var endtime_arr = $.trim($("#proxigeoTo_" + objectID).val()).split(':');
				var end_h = endtime_arr[0];
				var end_m = endtime_arr[1];
				var d1 = new Date(2000, 0, 1,  start_h, start_m);
				var d2 = new Date(2000, 0, 1,  end_h, end_m);
				if(d1>d2){
					$("#setalertmsg").html("Zone In End Time must be greater than Start Time");
					isAllowSave = false;
					return false;
				}
            }
        });
        //var proxigeoFrom  = $("[id^='proxigeoFrom_']");
        //var proxigeoTo    = $("[id^='proxigeoTo_']");

        //-----------------------Off Route---------------------------------
        saveAlertData.offroutepoi = [];
        var offroutepoiCheck = $("[id^='offroutepoi_']");
        $.each(offroutepoiCheck,function (i,v) {
            var that = this;
            if ($(that).prop('checked') == true) {
                var thisID = $(that).attr('id');
                var objectID = thisID.split('_');
                objectID = objectID[1];
                if ($.trim($("#offroutepoiFrom_" + objectID).
                        val()) == '' || $.trim($("#offroutepoiTo_" + objectID).
                        val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for Offroute POI");
                    isAllowSave = false;
                    return false;
                }
                saveAlertData.offroutepoi.push(
                        {objectid:objectID,timefrom:$.trim($(
                                    "#offroutepoiFrom_" + objectID).
                                    val()),timeto:$.trim($(
                                    "#offroutepoiTo_" + objectID).val())});
				var starttime_arr = $.trim($("#offroutepoiFrom_" + objectID).val()).split(':');
				var start_h = starttime_arr[0];
				var start_m = starttime_arr[1];
				var endtime_arr = $.trim($("#offroutepoiTo_" + objectID).val()).split(':');
				var end_h = endtime_arr[0];
				var end_m = endtime_arr[1];
				var d1 = new Date(2000, 0, 1,  start_h, start_m);
				var d2 = new Date(2000, 0, 1,  end_h, end_m);
				if(d1>d2){
					$("#setalertmsg").html("Zone Out End Time must be greater than Start Time");
					isAllowSave = false;
					return false;
				}
            }
        });
        //  var offroutepoiFrom  = $("[id^='offroutepoiFrom_']");
        // var offroutepoiTo    = $("[id^='offroutepoiTo_']");

        saveAlertData.offrouteroute = [];
        var offrouterouteCheck = $("[id^='offrouteroute_']");
        $.each(offrouterouteCheck,function (i,v) {
            var that = this;
            if ($(that).prop('checked') == true) {
                var thisID = $(that).attr('id');
                var objectID = thisID.split('_');
                objectID = objectID[1];
                if ($.trim($("#offrouterouteFrom_" + objectID).
                        val()) == '' || $.trim($(
                        "#offrouterouteTo_" + objectID).val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for Offroute Routes");
                    isAllowSave = false;
                    return false;
                }
                saveAlertData.offrouteroute.push(
                        {objectid:objectID,timefrom:$.trim($(
                                    "#offrouterouteFrom_" + objectID).
                                    val()),timeto:$.trim($(
                                    "#offrouterouteTo_" + objectID).val())});
				var starttime_arr = $.trim($("#offrouterouteFrom_" + objectID).val()).split(':');
				var start_h = starttime_arr[0];
				var start_m = starttime_arr[1];
				var endtime_arr = $.trim($("#offrouterouteTo_" + objectID).val()).split(':');
				var end_h = endtime_arr[0];
				var end_m = endtime_arr[1];
				var d1 = new Date(2000, 0, 1,  start_h, start_m);
				var d2 = new Date(2000, 0, 1,  end_h, end_m);
				if(d1>d2){
					$("#setalertmsg").html("Zone Out End Time must be greater than Start Time");
					isAllowSave = false;
					return false;
				}
            }
        });
        //  var offrouterouteFrom  = $("[id^='offrouterouteFrom_']");
        //  var offrouterouteTo    = $("[id^='offrouterouteTo_']");

        saveAlertData.offroutegeo = [];
        var offroutegeoCheck = $("[id^='offroutegeo_']");
        $.each(offroutegeoCheck,function (i,v) {
            var that = this;
            if ($(that).prop('checked') == true) {
                var thisID = $(that).attr('id');
                var objectID = thisID.split('_');
                objectID = objectID[1];
                if ($.trim($("#offroutegeoFrom_" + objectID).
                        val()) == '' || $.trim($("#offroutegeoTo_" + objectID).
                        val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for Offroute Geofencing");
                    isAllowSave = false;
                    return false;
                }
                saveAlertData.offroutegeo.push(
                        {objectid:objectID,timefrom:$.trim($(
                                    "#offroutegeoFrom_" + objectID).
                                    val()),timeto:$.trim($(
                                    "#offroutegeoTo_" + objectID).val())});
				var starttime_arr = $.trim($("#offroutegeoFrom_" + objectID).val()).split(':');
				var start_h = starttime_arr[0];
				var start_m = starttime_arr[1];
				var endtime_arr = $.trim($("#offroutegeoTo_" + objectID).val()).split(':');
				var end_h = endtime_arr[0];
				var end_m = endtime_arr[1];
				var d1 = new Date(2000, 0, 1,  start_h, start_m);
				var d2 = new Date(2000, 0, 1,  end_h, end_m);
				if(d1>d2){
					$("#setalertmsg").html("Zone Out End Time must be greater than Start Time");
					isAllowSave = false;
					return false;
				}
            }
        });
        // var offroutegeoFrom  = $("[id^='offroutegeoFrom_']");
        // var offroutegeoTo    = $("[id^='offroutegeoTo_']");
		//-----------------------Route Deviation---------------------------------
        saveAlertData.routedeviationpoi = [];
        var routedeviationpoiCheck = $("[id^='routedeviationpoi_']");
        $.each(routedeviationpoiCheck,function (i,v) {
            var that = this;
            if ($(that).prop('checked') == true) {
                var thisID = $(that).attr('id');
                var objectID = thisID.split('_');
                objectID = objectID[1];
                if ($.trim($("#routedeviationpoiFrom_" + objectID).
                        val()) == '' || $.trim($("#routedeviationpoiTo_" + objectID).
                        val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for Offroute POI");
                    isAllowSave = false;
                    return false;
                }
                saveAlertData.routedeviationpoi.push(
                        {objectid:objectID,timefrom:$.trim($(
                                    "#routedeviationpoiFrom_" + objectID).
                                    val()),timeto:$.trim($(
                                    "#routedeviationpoiTo_" + objectID).val())});
				var starttime_arr = $.trim($("#routedeviationpoiFrom_" + objectID).val()).split(':');
				var start_h = starttime_arr[0];
				var start_m = starttime_arr[1];
				var endtime_arr = $.trim($("#routedeviationpoiTo_" + objectID).val()).split(':');
				var end_h = endtime_arr[0];
				var end_m = endtime_arr[1];
				var d1 = new Date(2000, 0, 1,  start_h, start_m);
				var d2 = new Date(2000, 0, 1,  end_h, end_m);
				if(d1>d2){
					$("#setalertmsg").html("Route Deviation End Time must be greater than Start Time");
					isAllowSave = false;
					return false;
				}
            }
        });
        //  var routedeviationpoiFrom  = $("[id^='routedeviationpoiFrom_']");
        // var routedeviationpoiTo    = $("[id^='routedeviationpoiTo_']");

        saveAlertData.routedeviationroute = [];
        var routedeviationrouteCheck = $("[id^='routedeviationroute_']");
        $.each(routedeviationrouteCheck,function (i,v) {
            var that = this;
            if ($(that).prop('checked') == true) {
                var thisID = $(that).attr('id');
                var objectID = thisID.split('_');
                objectID = objectID[1];
                if ($.trim($("#routedeviationrouteFrom_" + objectID).
                        val()) == '' || $.trim($(
                        "#routedeviationrouteTo_" + objectID).val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for Route Daviation");
                    isAllowSave = false;
                    return false;
                }
                saveAlertData.routedeviationroute.push(
                        {objectid:objectID,timefrom:$.trim($(
                                    "#routedeviationrouteFrom_" + objectID).
                                    val()),timeto:$.trim($(
                                    "#routedeviationrouteTo_" + objectID).val())});
				var starttime_arr = $.trim($("#routedeviationrouteFrom_" + objectID).val()).split(':');
				var start_h = starttime_arr[0];
				var start_m = starttime_arr[1];
				var endtime_arr = $.trim($("#routedeviationrouteTo_" + objectID).val()).split(':');
				var end_h = endtime_arr[0];
				var end_m = endtime_arr[1];
				var d1 = new Date(2000, 0, 1,  start_h, start_m);
				var d2 = new Date(2000, 0, 1,  end_h, end_m);
				if(d1>d2){
					$("#setalertmsg").html("Route Deviation End Time must be greater than Start Time");
					isAllowSave = false;
					return false;
				}
            }
        });
        //  var routedeviationrouteFrom  = $("[id^='routedeviationrouteFrom_']");
        //  var routedeviationrouteTo    = $("[id^='routedeviationrouteTo_']");

        saveAlertData.routedeviationgeo = [];
        var routedeviationgeoCheck = $("[id^='routedeviationgeo_']");
        $.each(routedeviationgeoCheck,function (i,v) {
            var that = this;
            if ($(that).prop('checked') == true) {
                var thisID = $(that).attr('id');
                var objectID = thisID.split('_');
                objectID = objectID[1];
                if ($.trim($("#routedeviationgeoFrom_" + objectID).
                        val()) == '' || $.trim($("#routedeviationgeoTo_" + objectID).
                        val()) == '') {
                    $("#setalertmsg").html(
                            "Please enter From and To times for Offroute Geofencing");
                    isAllowSave = false;
                    return false;
                }
                saveAlertData.routedeviationgeo.push(
                        {objectid:objectID,timefrom:$.trim($(
                                    "#routedeviationgeoFrom_" + objectID).
                                    val()),timeto:$.trim($(
                                    "#routedeviationgeoTo_" + objectID).val())});
				var starttime_arr = $.trim($("#routedeviationgeoFrom_" + objectID).val()).split(':');
				var start_h = starttime_arr[0];
				var start_m = starttime_arr[1];
				var endtime_arr = $.trim($("#routedeviationgeoTo_" + objectID).val()).split(':');
				var end_h = endtime_arr[0];
				var end_m = endtime_arr[1];
				var d1 = new Date(2000, 0, 1,  start_h, start_m);
				var d2 = new Date(2000, 0, 1,  end_h, end_m);
				if(d1>d2){
					$("#setalertmsg").html("Route Deviation End Time must be greater than Start Time");
					isAllowSave = false;
					return false;
				}
            }
        });
		
        if (isAllowSave != false) {
            $.ajax({
                url:BASEURL + "controlcentre/saveallalertconfig",
                type:"POST",
                data:saveAlertData,
                dataType:'json',
                beforeSend:function () {
                    $("#setalert").attr('disabled',true);
                    $("#setalertmsg").html('');
                }
            }).done(function (resp) {
                if(typeof resp.status !== 'undefined' && resp.status == 1){
                    if(resp.result == true){
                         $("#setalertmsg").html('Configured successfully');
                    }else if(resp.result == '3'){
                        $("#setalertmsg").html('Failed! No Data value is less than configured data');
                    }
                }
               
            }).fail(function () {
                $("#setalertmsg").html('Failed to configure');
            }).complete(function () {
                $("#setalert").removeAttr('disabled');
            });

           // console.log(saveAlertData);
        } else {
            console.log("er");
        }

    }
    var SecondsTohhmmss = function(totalSeconds) {
	  
	  var hours   = Math.floor(totalSeconds / 3600);
	  var minutes = Math.floor((totalSeconds - (hours * 3600)) / 60);
	  var seconds = totalSeconds - (hours * 3600) - (minutes * 60);
		
	  // round seconds
	  seconds = Math.round(seconds * 100) / 100

	  var result = (hours < 10 ? "0" + hours : hours);
		  result += ":" + (minutes < 10 ? "0" + minutes : minutes);
		  result += ":" + (seconds  < 10 ? "0" + seconds : seconds);
		  
	  return result;
	}

    $("#configurealert").on('shown.bs.modal',function () {
        // $("#configurealert form")[0].reset();
        //console.log(deviceAlerts)
         var deviceAssignedAlerts = {};
        deviceAssignedAlerts = deviceAlerts.configured_alert_data;
        
        if (Object.keys(deviceAlerts.get_poi).length > 0) {
            var htmltblprox = '',htmltbloffroute = '',htmltblroutedeviation = '';
            for (var i in deviceAlerts.get_poi) {                
                htmltblprox += "<tr><td style='color:#000;'>" + deviceAlerts.get_poi[i].geoname + "</td><td><input type='checkbox' id='proxipoi_" + deviceAlerts.get_poi[i].id + "'/></td><td><input type='text' class='frmto' readonly id='proxipoiFrom_" + deviceAlerts.get_poi[i].id + "' /></td><td><input type='text' class='frmto' readonly id='proxipoiTo_" + deviceAlerts.get_poi[i].id + "'/></td></tr>";
				htmltblroutedeviation += "<tr><td style='color:#000;'>" + deviceAlerts.get_poi[i].geoname + "</td><td><input type='checkbox' id='routedeviationpoi_" + deviceAlerts.get_poi[i].id + "'/></td><td><input type='text' class='frmto' readonly id='routedeviationpoiFrom_" + deviceAlerts.get_poi[i].id + "' /></td><td><input type='text' class='frmto' readonly id='routedeviationpoiTo_" + deviceAlerts.get_poi[i].id + "'/></td></tr>";
                htmltbloffroute += "<tr><td style='color:#000;'>" + deviceAlerts.get_poi[i].geoname + "</td><td><input type='checkbox' id='offroutepoi_" + deviceAlerts.get_poi[i].id + "'/></td><td><input type='text' class='frmto' readonly id='offroutepoiFrom_" + deviceAlerts.get_poi[i].id + "' /></td><td><input type='text' class='frmto' readonly id='offroutepoiTo_" + deviceAlerts.get_poi[i].id + "'/></td></tr>";
            }
            $("#proxpoitable").html(htmltblprox);
			$("#routedeviationpoitable").html(htmltblroutedeviation);
            $("#offroutepoitable").html(htmltbloffroute);
        }
        if (Object.keys(deviceAlerts.get_geo).length > 0) {
            var htmltblprox = '',htmltbloffroute = '',htmltblroutedeviation = '';
            for (var i in deviceAlerts.get_geo) {
                htmltblprox += "<tr><td style='color:#000;'>" + deviceAlerts.get_geo[i].geoname + "</td><td><input type='checkbox' id='proxigeo_" + deviceAlerts.get_geo[i].id + "'/></td><td><input type='text' class='frmto' readonly id='proxigeoFrom_" + deviceAlerts.get_geo[i].id + "'/></td><td><input type='text' class='frmto' readonly id='proxigeoTo_" + deviceAlerts.get_geo[i].id + "'/></td></tr>";
				htmltblroutedeviation += "<tr><td style='color:#000;'>" + deviceAlerts.get_geo[i].geoname + "</td><td><input type='checkbox' id='routedeviationgeo_" + deviceAlerts.get_geo[i].id + "'/></td><td><input type='text' class='frmto' readonly id='routedeviationgeoFrom_" + deviceAlerts.get_geo[i].id + "'/></td><td><input type='text' class='frmto' readonly id='routedeviationgeoTo_" + deviceAlerts.get_geo[i].id + "'/></td></tr>";
                htmltbloffroute += "<tr><td style='color:#000;'>" + deviceAlerts.get_geo[i].geoname + "</td><td><input type='checkbox' id='offroutegeo_" + deviceAlerts.get_geo[i].id + "'/></td><td><input type='text' class='frmto' readonly id='offroutegeoFrom_" + deviceAlerts.get_geo[i].id + "'/></td><td><input type='text' class='frmto' readonly id='offroutegeoTo_" + deviceAlerts.get_geo[i].id + "'/></td></tr>";
            }
            $("#proxgeotable").html(htmltblprox);
			$("#routedeviationgeotable").html(htmltblroutedeviation);
            $("#offroutegeotable").html(htmltbloffroute);
        }
        if (Object.keys(deviceAlerts.get_route).length > 0) {
            var htmltblprox = '',htmltbloffroute = '',htmltblroutedeviation = '';
            for (var i in deviceAlerts.get_route) {
                htmltblprox += "<tr><td style='color:#000;'>" + deviceAlerts.get_route[i].geoname + "</td><td><input type='checkbox' id='proxiroute_" + deviceAlerts.get_route[i].id + "'/></td><td><input type='text' class='frmto' readonly id='proxirouteFrom_" + deviceAlerts.get_route[i].id + "' /></td><td><input type='text' class='frmto' readonly id='proxirouteTo_" + deviceAlerts.get_route[i].id + "'/></td></tr>";
				htmltblroutedeviation += "<tr><td style='color:#000;'>" + deviceAlerts.get_route[i].geoname + "</td><td><input type='checkbox' id='routedeviationroute_" + deviceAlerts.get_route[i].id + "'/></td><td><input type='text' class='frmto' readonly id='routedeviationrouteFrom_" + deviceAlerts.get_route[i].id + "' /></td><td><input type='text' class='frmto' readonly id='routedeviationrouteTo_" + deviceAlerts.get_route[i].id + "'/></td></tr>";
                htmltbloffroute += "<tr><td style='color:#000;'>" + deviceAlerts.get_route[i].geoname + "</td><td><input type='checkbox' id='offrouteroute_" + deviceAlerts.get_route[i].id + "'/></td><td><input type='text' class='frmto' readonly id='offrouterouteFrom_" + deviceAlerts.get_route[i].id + "'/></td><td><input type='text' class='frmto' readonly id='offrouterouteTo_" + deviceAlerts.get_route[i].id + "'/></td></tr>";
            }
            $("#proxroutetable").html(htmltblprox);
			$("#routedeviationroutetable").html(htmltblroutedeviation);
            $("#offrouteroutetable").html(htmltbloffroute);

        }
        //deviceAssignedAlerts
        if(deviceAssignedAlerts.notify != null && Object.keys(deviceAssignedAlerts.notify).length > 0){
            $("[name='notifyemail']").val(deviceAssignedAlerts.notify.alertemails);
            $("[name='notifyphone']").val(deviceAssignedAlerts.notify.alertphnumbers);
        }
        if(deviceAssignedAlerts.lowbattery != null && Object.keys(deviceAssignedAlerts.lowbattery).length > 0){
            $("input:radio[name='lowbatteryonoff'][value='"+deviceAssignedAlerts.lowbattery.active+"']").prop('checked',true);
            $("input:radio[name='lowbatterynotificationonoffemail'][value='"+deviceAssignedAlerts.lowbattery.isemailnotify+"']").prop('checked',true);
            $("input:radio[name='lowbatterynotificationonoffphone'][value='"+deviceAssignedAlerts.lowbattery.isphonenotify+"']").prop('checked',true);
        }
		if( deviceAssignedAlerts.freefall != null && Object.keys(deviceAssignedAlerts.freefall).length > 0){
            $("input:radio[name='freefallonoff'][value='"+deviceAssignedAlerts.freefall.active+"']").prop('checked',true);
            $("input:radio[name='freefallnotificationonoffemail'][value='"+deviceAssignedAlerts.freefall.isemailnotify+"']").prop('checked',true);
            $("input:radio[name='freefallnotificationonoffphone'][value='"+deviceAssignedAlerts.freefall.isphonenotify+"']").prop('checked',true);
        }
        if(deviceAssignedAlerts.nomovement != null && Object.keys(deviceAssignedAlerts.nomovement).length > 0){
            $("input:radio[name='nomovementonoff'][value='"+deviceAssignedAlerts.nomovement.active+"']").prop('checked',true);
            $("input:radio[name='nomovementnotificationonoffemail'][value='"+deviceAssignedAlerts.nomovement.isemailnotify+"']").prop('checked',true);
            $("input:radio[name='nomovementnotificationonoffphone'][value='"+deviceAssignedAlerts.nomovement.isphonenotify+"']").prop('checked',true);
            if(deviceAssignedAlerts.nomovement.limit_range != ''){
               $("#nomovementtimeval").val(SecondsTohhmmss(parseInt(deviceAssignedAlerts.nomovement.limit_range)));
            }
            var fromtime = deviceAssignedAlerts.nomovement.time_from;
            var totime   = deviceAssignedAlerts.nomovement.time_to;
            if($.trim(fromtime) == '00:00:00' && $.trim(totime) == '23:59:59'){
               $("input:radio[name='nomovementtime'][value='1']").prop('checked',true); 
            }else{
                 $("input:radio[name='nomovementtime'][value='2']").prop('checked',true); 
                 $("#nomovementdaytimefrom").val(fromtime);
                 $("#nomovementdaytimeto").val(totime);
                 $("#nomovementfromtotime").show();
            }            
        }
        if(deviceAssignedAlerts.overspeed != null && Object.keys(deviceAssignedAlerts.overspeed).length > 0){
            $("input:radio[name='speedonoff'][value='"+deviceAssignedAlerts.overspeed.active+"']").prop('checked',true);
            $("input:radio[name='speednotificationonoffemail'][value='"+deviceAssignedAlerts.overspeed.isemailnotify+"']").prop('checked',true);
            $("input:radio[name='speednotificationonoffphone'][value='"+deviceAssignedAlerts.overspeed.isphonenotify+"']").prop('checked',true);
            var fromtime = deviceAssignedAlerts.overspeed.time_from;
            var totime   = deviceAssignedAlerts.overspeed.time_to;
              $("#speedval").val(parseInt(deviceAssignedAlerts.overspeed.limit_range));
            if($.trim(fromtime) == '00:00:00' && $.trim(totime) == '23:59:59'){
               $("input:radio[name='speedtime'][value='1']").prop('checked',true); 
            }else{
                 $("input:radio[name='speedtime'][value='2']").prop('checked',true); 
                 $("#speeddayfromtime").val(fromtime);               
                 $("#speeddaytotime").val(totime);
                 $("#speedfromtotime").show();
            }            
        }
        if(Object.keys(deviceAssignedAlerts.nodata != null && deviceAssignedAlerts.nodata).length > 0){
            $("input:radio[name='nodataonoff'][value='"+deviceAssignedAlerts.nodata.active+"']").prop('checked',true);
            $("#nodataduration").val(SecondsTohhmmss(parseInt(deviceAssignedAlerts.nodata.limit_range)));
             var fromtime = deviceAssignedAlerts.nodata.time_from;
            var totime   = deviceAssignedAlerts.nodata.time_to;
             if($.trim(fromtime) == '00:00:00' && $.trim(totime) == '23:59:59'){
               $("input:radio[name='nodatatimetype'][value='1']").prop('checked',true); 
            }else{
                 $("input:radio[name='nodatatimetype'][value='2']").prop('checked',true); 
                 $("#nodatatimefrom").val(fromtime);               
                 $("#nodatatimeto").val(totime);
                 $("#nodatafromtotime").show();
            }  
            
            $("input:radio[name='nodatanotificationonoffemail'][value='"+deviceAssignedAlerts.nodata.isemailnotify+"']").prop('checked',true);
            $("input:radio[name='nodatanotificationonoffphone'][value='"+deviceAssignedAlerts.nodata.isphonenotify+"']").prop('checked',true);
            
        }
       
         if(deviceAssignedAlerts.proxipoi.length > 0){
           deviceAssignedAlerts.proxipoi.forEach(function(row){
               $("#proxipoi_"+row.geomaster_id).prop('checked',true);
               $("#proxipoiFrom_"+row.geomaster_id).val(row.time_from);
               $("#proxipoiTo_"+row.geomaster_id).val(row.time_to);
           });  
        }
         if(deviceAssignedAlerts.proxiroute.length > 0){
           deviceAssignedAlerts.proxiroute.forEach(function(row){
               $("#proxiroute_"+row.geomaster_id).prop('checked',true);
               $("#proxirouteFrom_"+row.geomaster_id).val(row.time_from);
               $("#proxirouteTo_"+row.geomaster_id).val(row.time_to);
           });  
        }
        if(deviceAssignedAlerts.proxigeo.length > 0){
           deviceAssignedAlerts.proxigeo.forEach(function(row){
               $("#proxigeo_"+row.geomaster_id).prop('checked',true);
               $("#proxigeoFrom_"+row.geomaster_id).val(row.time_from);
               $("#proxigeoTo_"+row.geomaster_id).val(row.time_to);
           });  
        }
		
        if(deviceAssignedAlerts.routedeviationroute.length > 0){
           deviceAssignedAlerts.routedeviationroute.forEach(function(row){
               $("#routedeviationroute_"+row.geomaster_id).prop('checked',true);
               $("#routedeviationrouteFrom_"+row.geomaster_id).val(row.time_from);
               $("#routedeviationrouteTo_"+row.geomaster_id).val(row.time_to);
           });  
        }
        
         if(deviceAssignedAlerts.offroutepoi.length > 0){
           deviceAssignedAlerts.offroutepoi.forEach(function(row){
               $("#offroutepoi_"+row.geomaster_id).prop('checked',true);
               $("#offroutepoiFrom_"+row.geomaster_id).val(row.time_from);
               $("#offroutepoiTo_"+row.geomaster_id).val(row.time_to);
           });  
        }
        if(deviceAssignedAlerts.offrouteroute.length > 0){
           deviceAssignedAlerts.offrouteroute.forEach(function(row){
               $("#offrouteroute_"+row.geomaster_id).prop('checked',true);
               $("#offrouterouteFrom_"+row.geomaster_id).val(row.time_from);
               $("#offrouterouteTo_"+row.geomaster_id).val(row.time_to);
           });  
        }
        if(deviceAssignedAlerts.offroutegeo.length > 0){
           deviceAssignedAlerts.offroutegeo.forEach(function(row){
               $("#offroutegeo_"+row.geomaster_id).prop('checked',true);
               $("#offroutegeoFrom_"+row.geomaster_id).val(row.time_from);
               $("#offroutegeoTo_"+row.geomaster_id).val(row.time_to);
           });  
        }

        //   $(".alertforms").hide();
    });





    $("#configurealert").on('hidden.bs.modal',function () {
        // $("#configurealert form")[0].reset();
        deviceAlerts = {};
        saveAlertData = {};
        $("#setalertmsg").html('');
        $("#configurealert").find("input[type='text']").val('');
        $("#notifyemail").val('');
        $("#notifyphone").val('');
        $("#configurealert").find("input[type='radio'][value='2']").prop('checked',true);
        $("input:radio[name='nomovementtime'][value='1']").prop('checked',true);
        $("input:radio[name='speedtime'][value='1']").prop('checked',true);
        $("input:radio[name='nodatatime'][value='1']").prop('checked',true);
        $("#nomovementfromtotime").hide();
        $("#speedfromtotime").hide();
        $("#nodatafromtotime").hide();
		selectedDeviceOPenedPopUp = null;
    });


    $("#existingfences").on('change',function () {
        var selectedGeofenceId = $.trim($(this).val());
        vectorGeo.getSource().clear();
        if (selectedGeofenceId != '' && Object.keys(
                alreadyCreatedGeofences).length > 0) {
            var polyCoordinates = [];

            if (typeof alreadyCreatedGeofences[selectedGeofenceId].lonlat !== 'undefined' && alreadyCreatedGeofences[selectedGeofenceId].lonlat.length > 1) {
                var coordinates = alreadyCreatedGeofences[selectedGeofenceId].lonlat.split(
                        ",");
                var i = 0;
                var coordinateArr = [];
                while (i != (coordinates.length - 2)) {
                    coordinateArr.push([coordinates[i],coordinates[i + 1]]);
                    i += 2;
                    // coordinateArr.push(myArray.splice(0, chunk_size));
                }
                for (var i = 0;i < coordinateArr.length;i++) {
                    polyCoordinates.push(ol.proj.transform([parseFloat(
                                coordinateArr[i][0]),parseFloat(
                                coordinateArr[i][1])],'EPSG:4326',
                            'EPSG:900913'));
                }
                var feature = new ol.Feature({
                    geometry:new ol.geom.Polygon([polyCoordinates])
                });
                feature.setId("existinggeofences_" + selectedGeofenceId);
                sourceGeo.addFeature(feature);
                geofencingmap.getView().setCenter(ol.proj.transform([
                    parseFloat(
                            coordinateArr[0][0]),parseFloat(
                            coordinateArr[0][1])],
                        'EPSG:4326','EPSG:3857'));
            }


            //console.log(polyCoordinates);

        }
    });




    
//      typeSelect.onchange = function() {
//        geofencingmap.removeInteraction(drawpoly);
//        addInteraction();
//      };

    // addInteraction();

</script>
<script>
//Below script for footer slider up  by saheb
    $(document).ready(function () {

        $('.footerDrawer .open').on('click',function () {

            $('.footerDrawer .content').slideToggle();

            if (document.getElementById("ft1").classList.contains(
                    'fa-arrow-down'))
            {
                document.getElementById("ft1").classList.add('fa-arrow-up');
                document.getElementById("ft1").classList.remove(
                        'fa-arrow-down');
            } else
            {
                document.getElementById("ft1").classList.add('fa-arrow-down');
                document.getElementById("ft1").classList.remove('fa-arrow-up');
            }
        });

    });



</script>

<script>
   
   
    $(document).ready(function () {
        $("#createpoi").on('click',function () {   
			if(mapPOI){
				if (typeof drawLine !== 'undefined') {
					mapPOI.removeInteraction(drawLine);
				}
				mapPOI.removeOverlay(tooltipRoute);
				mapPOI.unByKey(typeofpoievent);
				tooltipRoute=null;
				vectorPOI.getSource().clear();
				vectorRoute.getSource().clear();
				//mapPOI.setTarget(null);
				mapPOI = null;
				poiAndRouteData = {};
				$("#showdistance").html('').hide();
				$("#showplacesearch").hide();
				mapMode = "";
				$('#pac-input').val('');
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(geofencingmap){
				if (typeof drawpoly !== 'undefined') {
					geofencingmap.removeInteraction(drawpoly);					
				}
				geofencingStoreData = {};
				geoFenceCoordinates = [];
				if (geofencingmapclick) {
					ol.Observable.unByKey(geofencingmapclick);
				}
				if (geofencingmappointermove) {
					ol.Observable.unByKey(geofencingmappointermove);
				}
				//geofencingmap.setTarget(null);				
				vector.getSource().clear();
				vectorGeo.getSource().clear();
				drawpoly = null;
				geofencingmap = null;
				mapMode = "";
				$("#deviceserialGFModal").html('');
				$("#deleteGeofencingMarks").hide().removeAttr("onclick");
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(mapRuler){
				if (typeof drawRuler !== 'undefined') {
					mapRuler.removeInteraction(drawRuler);
				}
				mapRuler.removeOverlay(tooltipRuler);
				tooltipRuler=null;
				sourceRuler.clear();
				//mapPOI.setTarget(null);
				mapRuler = null;
				$("#showruler").html('').hide();
				mapMode = "";
			}
			//mapMode = "poi";			
			//$(".poimodalLevel").html('POI');
			//$("#poimodal").modal();
			mapPOI = map;
			getallinteractionlist();
			creategeof();
			$("#collapseOne").addClass("show");
			$("#collapseTwo").removeClass("show");
			$("#collapseThree").removeClass("show");
        });

        $("#createroute").on('click',function () {            
			if(mapPOI){
				if (typeof drawLine !== 'undefined') {
					mapPOI.removeInteraction(drawLine);
				}
				mapPOI.removeOverlay(tooltipRoute);
				if (typeofpoievent) {
					ol.Observable.unByKey(typeofpoievent);
				}
				tooltipRoute=null;
				vectorPOI.getSource().clear();
				vectorRoute.getSource().clear();
				//mapPOI.setTarget(null);
				mapPOI = null;
				poiAndRouteData = {};
				$("#showdistance").html('').hide();
				$("#showplacesearch").hide();
				mapMode = "";
				$('#pac-input').val('');
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(geofencingmap){
				if (typeof drawpoly !== 'undefined') {
					geofencingmap.removeInteraction(drawpoly);					
				}
				geofencingStoreData = {};
				geoFenceCoordinates = [];
				if (geofencingmapclick) {
					ol.Observable.unByKey(geofencingmapclick);
				}
				if (geofencingmappointermove) {
					ol.Observable.unByKey(geofencingmappointermove);
				}
				//geofencingmap.setTarget(null);				
				vector.getSource().clear();
				vectorGeo.getSource().clear();
				drawpoly = null;
				geofencingmap = null;
				mapMode = "";
				$("#deviceserialGFModal").html('');
				$("#deleteGeofencingMarks").hide().removeAttr("onclick");
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(mapRuler){
				if (typeof drawRuler !== 'undefined') {
					mapRuler.removeInteraction(drawRuler);
				}
				mapRuler.removeOverlay(tooltipRuler);
				tooltipRuler=null;
				sourceRuler.clear();
				//mapPOI.setTarget(null);
				mapRuler = null;
				$("#showruler").html('').hide();
				mapMode = "";
			}
			//mapMode = "route";
			//$(".poimodalLevel").html('Route');
			//$("#poimodal").modal();
			mapPOI = map;
			getallinteractionlist();
			creategeof();
			$("#collapseOne").removeClass("show");
			$("#collapseTwo").addClass("show");
			$("#collapseThree").removeClass("show");
        });
        $("#creategeof").on("click",creategeof);
		$("#hplbck").on("click",function(){
			//clear live track start
			$(document).find(".clickmetotrack").each(function () {
				if ($(this).prop('checked') == true) {
					$(this).prop('checked',false);
					timerForDevicePositionDataAjax.abort();
					var deviceid = $(this).attr('id');
					//console.log(globalCheckedDevieIdForTracking);
					//   deviceid = deviceid.split("_");
					//  deviceid = deviceid[1];
					var index = globalCheckedDevieIdForTracking.indexOf(deviceid);
					if (index != -1) {
						globalCheckedDevieIdForTracking.splice(index,1);
						deleteMarkerById('track_'+deviceid,'track');
					}
					if(trackonLayervars['trackon' + deviceid]){
						trackonLayervars['trackon' + deviceid].getSource().clear();
						map.removeLayer(trackonLayervars['trackon' + deviceid]);
					}
					delete featurevars['trackon' + deviceid];
					delete latlonvars['trackon' + deviceid];
					$('#trackon'+deviceid).hide();
					$('#trackoff'+deviceid).hide();
					$('#zoomto'+deviceid).hide();
					abortdataall();
				}
			});
			//clear live track end
			resetHistory();
			if(mapPOI){
				if (typeof drawLine !== 'undefined') {
					mapPOI.removeInteraction(drawLine);
				}
				mapPOI.removeOverlay(tooltipRoute);
				if (typeofpoievent) {
					ol.Observable.unByKey(typeofpoievent);
				}
				tooltipRoute=null;
				vectorPOI.getSource().clear();
				vectorRoute.getSource().clear();
				//mapPOI.setTarget(null);
				mapPOI = null;
				poiAndRouteData = {};
				$("#showdistance").html('').hide();
				$("#showplacesearch").hide();
				mapMode = "";
				$('#pac-input').val('');
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(geofencingmap){
				if (typeof drawpoly !== 'undefined') {
					geofencingmap.removeInteraction(drawpoly);					
				}
				geofencingStoreData = {};
				geoFenceCoordinates = [];
				if (geofencingmapclick) {
					ol.Observable.unByKey(geofencingmapclick);
				}
				if (geofencingmappointermove) {
					ol.Observable.unByKey(geofencingmappointermove);
				}
				//geofencingmap.setTarget(null);				
				vector.getSource().clear();
				vectorGeo.getSource().clear();
				drawpoly = null;
				geofencingmap = null;
				mapMode = "";
				$("#deviceserialGFModal").html('');
				$("#deleteGeofencingMarks").hide().removeAttr("onclick");
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(mapRuler){
				if (typeof drawRuler !== 'undefined') {
					mapRuler.removeInteraction(drawRuler);
				}
				mapRuler.removeOverlay(tooltipRuler);
				tooltipRuler=null;
				sourceRuler.clear();
				//mapPOI.setTarget(null);
				mapRuler = null;
				$("#showruler").html('').hide();
				mapMode = "";
			}
			$("#popupheaderalert").attr('title','');
            $("#popupheaderalert, #alerttab1").html('');
            $("#alertpopup").hide();
			mapPOI = map;
			getallinteractionlist();
			creategeof();
			$("a[href='#Playbacktab']").trigger('click');
			vectorAgps.getSource().clear();
		});
		
		$("#sumrept").on("click",function(){
			//clear live track start
			$(document).find(".clickmetotrack").each(function () {
				if ($(this).prop('checked') == true) {
					$(this).prop('checked',false);
					timerForDevicePositionDataAjax.abort();
					var deviceid = $(this).attr('id');
					//console.log(globalCheckedDevieIdForTracking);
					//   deviceid = deviceid.split("_");
					//  deviceid = deviceid[1];
					var index = globalCheckedDevieIdForTracking.indexOf(deviceid);
					if (index != -1) {
						globalCheckedDevieIdForTracking.splice(index,1);
						deleteMarkerById('track_'+deviceid,'track');
					}
					if(trackonLayervars['trackon' + deviceid]){
						trackonLayervars['trackon' + deviceid].getSource().clear();
						map.removeLayer(trackonLayervars['trackon' + deviceid]);
					}
					delete featurevars['trackon' + deviceid];
					delete latlonvars['trackon' + deviceid];
					$('#trackon'+deviceid).hide();
					$('#trackoff'+deviceid).hide();
					$('#zoomto'+deviceid).hide();
				}
			});
			//clear live track end
			resetHistory();
			if(mapPOI){
				if (typeof drawLine !== 'undefined') {
					mapPOI.removeInteraction(drawLine);
				}
				mapPOI.removeOverlay(tooltipRoute);
				mapPOI.unByKey(typeofpoievent);
				tooltipRoute=null;
				vectorPOI.getSource().clear();
				vectorRoute.getSource().clear();
				//mapPOI.setTarget(null);
				mapPOI = null;
				poiAndRouteData = {};
				$("#showdistance").html('').hide();
				$("#showplacesearch").hide();
				mapMode = "";
				$('#pac-input').val('');
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(geofencingmap){
				if (typeof drawpoly !== 'undefined') {
					geofencingmap.removeInteraction(drawpoly);					
				}
				geofencingStoreData = {};
				geoFenceCoordinates = [];
				if (geofencingmapclick) {
					ol.Observable.unByKey(geofencingmapclick);
				}
				if (geofencingmappointermove) {
					ol.Observable.unByKey(geofencingmappointermove);
				}
				//geofencingmap.setTarget(null);				
				vector.getSource().clear();
				vectorGeo.getSource().clear();
				drawpoly = null;
				geofencingmap = null;
				mapMode = "";
				$("#deviceserialGFModal").html('');
				$("#deleteGeofencingMarks").hide().removeAttr("onclick");
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(mapRuler){
				if (typeof drawRuler !== 'undefined') {
					mapRuler.removeInteraction(drawRuler);
				}
				mapRuler.removeOverlay(tooltipRuler);
				tooltipRuler=null;
				sourceRuler.clear();
				//mapPOI.setTarget(null);
				mapRuler = null;
				$("#showruler").html('').hide();
				mapMode = "";
			}
			$("#popupheaderalert").attr('title','');
            $("#popupheaderalert, #alerttab1").html('');
            $("#alertpopup").hide();
			mapPOI = map;
			getallinteractionlist();
			creategeof();
			$("a[href='#ReportSummerytab']").trigger('click');    
		});
		
        $("[name='nomovementtime']").on('click',function () {
            if ($(this).val() == 1) {
                $("#nomovementfromtotime").hide();
            } else {
                $("#nomovementfromtotime").show();
            }
        });

        $("[name='speedtime']").on('click',function () {
            if ($(this).val() == 1) {
                $("#speedfromtotime").hide();
            } else {
                $("#speedfromtotime").show();
            }
        });

        $("[name='nodatatime']").on('click',function () {
            if ($(this).val() == 1) {
                $("#nodatafromtotime").hide();
            } else {
                $("#nodatafromtotime").show();
            }
        });
		
		$("#placesearch").on('click',function () {
			mapPOI = map;
			if (typeof drawLine !== 'undefined') {
				mapPOI.removeInteraction(drawLine);
			}
			mapPOI.removeOverlay(tooltipRoute);
			if (typeofpoievent) {
				ol.Observable.unByKey(typeofpoievent);
			}
			tooltipRoute=null;
			vectorPOI.getSource().clear();
			poiAndRouteData = {};
			$("#showdistance").html('').hide();
			$("#doublerightpanelcontent").html('');
			$("#doublerightpanel").hide();
			$("#showplacesearch").show();
		});
		
		$("#cancelallinteraction").on('click',function () {
			resetHistory();
			if(geofencingmap){
				if (typeof drawpoly !== 'undefined') {
					geofencingmap.removeInteraction(drawpoly);					
				}
				geofencingStoreData = {};
				geoFenceCoordinates = [];
				if (geofencingmapclick) {
					ol.Observable.unByKey(geofencingmapclick);
				}
				if (geofencingmappointermove) {
					ol.Observable.unByKey(geofencingmappointermove);
				}
				//geofencingmap.setTarget(null);				
				vector.getSource().clear();
				vectorGeo.getSource().clear();
				drawpoly = null;
				geofencingmap = null;
				mapMode = "";
				$("#deviceserialGFModal").html('');
				$("#deleteGeofencingMarks").hide().removeAttr("onclick");
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(mapPOI){
				if (typeof drawLine !== 'undefined') {
					mapPOI.removeInteraction(drawLine);
				}
				mapPOI.removeOverlay(tooltipRoute);
				if (typeofpoievent) {
					ol.Observable.unByKey(typeofpoievent);
				}
				tooltipRoute=null;
				vectorPOI.getSource().clear();
				vectorRoute.getSource().clear();
				//mapPOI.setTarget(null);
				mapPOI = null;
				poiAndRouteData = {};
				$("#showdistance").html('').hide();
				$("#showplacesearch").hide();
				mapMode = "";
				$('#pac-input').val('');
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(mapRuler){
				if (typeof drawRuler !== 'undefined') {
					mapRuler.removeInteraction(drawRuler);
				}
				mapRuler.removeOverlay(tooltipRuler);
				tooltipRuler=null;
				sourceRuler.clear();
				//mapPOI.setTarget(null);
				mapRuler = null;
				$("#showruler").html('').hide();
				mapMode = "";
			}
			$("#popupheaderalert").attr('title','');
            $("#popupheaderalert, #alerttab1").html('');
            $("#alertpopup").hide();
		});
		
		$("#cancelallinteractionleft").on('click',function () {
            resetHistory();
			if(mapPOI){
				if (typeof drawLine !== 'undefined') {
					mapPOI.removeInteraction(drawLine);
				}
				mapPOI.removeOverlay(tooltipRoute);
				if (typeofpoievent) {
					ol.Observable.unByKey(typeofpoievent);
				}
				tooltipRoute=null;
				vectorPOI.getSource().clear();
				vectorRoute.getSource().clear();
				//mapPOI.setTarget(null);
				mapPOI = null;
				poiAndRouteData = {};
				$("#showdistance").html('').hide();
				$("#showplacesearch").hide();
				mapMode = "";
				$('#pac-input').val('');
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(geofencingmap){
				if (typeof drawpoly !== 'undefined') {
					geofencingmap.removeInteraction(drawpoly);					
				}
				geofencingStoreData = {};
				geoFenceCoordinates = [];
				if (geofencingmapclick) {
					ol.Observable.unByKey(geofencingmapclick);
				}
				if (geofencingmappointermove) {
					ol.Observable.unByKey(geofencingmappointermove);
				}
				//geofencingmap.setTarget(null);				
				vector.getSource().clear();
				vectorGeo.getSource().clear();
				drawpoly = null;
				geofencingmap = null;
				mapMode = "";
				$("#deviceserialGFModal").html('');
				$("#deleteGeofencingMarks").hide().removeAttr("onclick");
				$("#doublerightpanelcontent").html('');
				$("#doublerightpanel").hide();
			}
			if(mapRuler){
				if (typeof drawRuler !== 'undefined') {
					mapRuler.removeInteraction(drawRuler);
				}
				mapRuler.removeOverlay(tooltipRuler);
				tooltipRuler=null;
				sourceRuler.clear();
				//mapPOI.setTarget(null);
				mapRuler = null;
				$("#showruler").html('').hide();
				mapMode = "";
			}
			$("#popupheaderalert").attr('title','');
            $("#popupheaderalert, #alerttab1").html('');
            $("#alertpopup").hide();
			
		});
		
		$("#addinteractionleft").on('click',function () {
			var type = $("#addinteractionleft").attr("data-type");
			if(type == "poi"){	
				mapMode = "poi";			
				$(".poimodalLevel").html('POI');
				poiDrawModeCall();				
			}
			else if(type == "route"){	
				mapMode = "route";
				$(".poimodalLevel").html('Route');
				$("#showdistance").show();
				overlayToolTipRoute = document.getElementById(
						'showdistance');
				tooltipRoute = new ol.Overlay({
					element:tooltipRoute,
					offset:[10,0]
				});
				mapPOI.addOverlay(tooltipRoute);
				addInteraction("calledfromroute",1);
				mapPOI.on('pointermove',function (evt) {
					transeferMyCoordinates = evt.coordinate;
				});				
			}
			else if(type == "geofence"){
				mapMode = "geofence";
				$("#clearGeofencingMarks").show().on('click',function () {
					vector.getSource().clear();
					vectorGeo.getSource().clear();
				});
				
				if(mapMode == "geofence"){
					geofencingmapclick = geofencingmap.on('click',function (e) {
						console.log('click');
						var feature = geofencingmap.forEachFeatureAtPixel(e.pixel,
								function (feature,layer) {
									return feature;
								});
						if (feature) {
							var featureid = feature.getId();
							if (geoFenceMode != "general") {
								if (typeof featureid !== 'undefined' && featureid.indexOf(
										'existinggeofences_') != -1) {
									var cloneid = featureid.split("_");
									geofencingStoreData.cloneid = cloneid[1];
									var dialogButtons = {
										"Assign":function () {
											$("#dialog_confirm").dialog("close");
											$("#alertsddgeoexistingSave").modal();
										},
										"Discard":function () {
											$("#dialog_confirm").dialog("close");
										}
									};
									var closeFn = function () {
										$("#dialog_confirm").removeAttr('title');
										$("#alert_msg").html(
												'');
									}
									showDialogs('Attach GeoFencing',
											'Are you sure, you want to attach this Geofencing?',
											dialogButtons,closeFn);
								}
							}
						}
					});


					geofencingmappointermove = geofencingmap.on('pointermove',function (e) {
						console.log('move');
						var feature = geofencingmap.forEachFeatureAtPixel(e.pixel,
								function (feature,layer) {
									return feature;
								});
						if (feature) {
							var featureid = feature.getId();
							if (typeof featureid !== 'undefined' && featureid.indexOf(
									'existinggeofences_') != -1) {
								geofencingmap.removeInteraction(drawpoly);
								$("#" + geofencingmap.getTarget()).css('cursor',
										'pointer');
							} else {
								$("#" + geofencingmap.getTarget()).css('cursor','');
								addInteraction(1);
							}
						} else {
							$("#" + geofencingmap.getTarget()).css('cursor','');
							addInteraction("fromgeofence",0);
						}
						if (e.dragging) {
							//$(element).popover('hide');
							return;
						}
					});

					//  var geoFenceCoordinates = [];
					if (geoFenceCoordinates.length > 0) {
						var polyCoordinates = [];
						for (var i in geoFenceCoordinates) {
							polyCoordinates.push(ol.proj.transform([parseFloat(
										geoFenceCoordinates[i][0]),parseFloat(
										geoFenceCoordinates[i][1])],'EPSG:4326',
									'EPSG:900913'));
						}
						//console.log(polyCoordinates);
						var feature = new ol.Feature({
							geometry:new ol.geom.Polygon([polyCoordinates])
						});
						geosource.addFeature(feature);
					}
					addInteraction("fromgeofence",1);
				}
			}
		});
		
		$("#ruler").on('click',function () {
			$("#showruler").show();
			mapMode = "Ruler";
			mapRuler = map;
			overlayToolTipRuler = document.getElementById('showruler');
			tooltipRuler = new ol.Overlay({
				element:tooltipRuler,
				offset:[10,0]
			});
			mapRuler.addOverlay(tooltipRuler);
			drawRuler = new ol.interaction.Draw({
				source:sourceRuler,
				type: ('LineString') 
			});
			mapRuler.addInteraction(drawRuler);
			drawRuler.on('drawstart',function (event) {
                sourceRuler.clear();
				$("#showruler").html('');
				var sketch = event.feature;
				sketch.getGeometry().on('change',function (evt) {
					var geom = evt.target;
					if (geom instanceof ol.geom.LineString) {
						overlayToolTipRuler.innerHTML = formatLength(
								geom);
					}
					// console.log(formatLength(geom)); 

				});
            });
            drawRuler.on('drawend',function (evt) {
                
            });
		});
		
		$("#notificationbell").on('click',function () {
			if(alertsonmap == 1){
				alertsonmap = 0;
				sourceAlert.clear();
				holdExistingAlertDeviceId = [];
				holdExistingSosDeviceId = [];
				holdExistingCallsDeviceId = [];
			}
			else{
				alertsonmap = 1;
				getNotificationAndSosDataForAll();
			}
		});

    });
	
	function poiadd(){
		mapMode = "poi";			
		$(".poimodalLevel").html('POI');
		poiDrawModeCall();
	}
	
	function routeadd(){
		mapMode = "route";
		$(".poimodalLevel").html('Route');
		$("#showdistance").show();
		overlayToolTipRoute = document.getElementById(
				'showdistance');
		tooltipRoute = new ol.Overlay({
			element:tooltipRoute,
			offset:[10,0]
		});
		mapPOI.addOverlay(tooltipRoute);
		addInteraction("calledfromroute",1);
		mapPOI.on('pointermove',function (evt) {
			transeferMyCoordinates = evt.coordinate;
		});
	}
	
	function geofenceadd(){
		mapMode = "geofence";
		$("#clearGeofencingMarks").show().on('click',function () {
			vector.getSource().clear();
			vectorGeo.getSource().clear();
		});
		
		if(mapMode == "geofence"){
			geofencingmapclick = geofencingmap.on('click',function (e) {
				console.log('click');
				var feature = geofencingmap.forEachFeatureAtPixel(e.pixel,
						function (feature,layer) {
							return feature;
						});
				if (feature) {
					var featureid = feature.getId();
					if (geoFenceMode != "general") {
						if (typeof featureid !== 'undefined' && featureid.indexOf(
								'existinggeofences_') != -1) {
							var cloneid = featureid.split("_");
							geofencingStoreData.cloneid = cloneid[1];
							var dialogButtons = {
								"Assign":function () {
									$("#dialog_confirm").dialog("close");
									$("#alertsddgeoexistingSave").modal();
								},
								"Discard":function () {
									$("#dialog_confirm").dialog("close");
								}
							};
							var closeFn = function () {
								$("#dialog_confirm").removeAttr('title');
								$("#alert_msg").html(
										'');
							}
							showDialogs('Attach GeoFencing',
									'Are you sure, you want to attach this Geofencing?',
									dialogButtons,closeFn);
						}
					}
				}
			});


			geofencingmappointermove = geofencingmap.on('pointermove',function (e) {
				console.log('move');
				var feature = geofencingmap.forEachFeatureAtPixel(e.pixel,
						function (feature,layer) {
							return feature;
						});
				if (feature) {
					var featureid = feature.getId();
					if (typeof featureid !== 'undefined' && featureid.indexOf(
							'existinggeofences_') != -1) {
						geofencingmap.removeInteraction(drawpoly);
						$("#" + geofencingmap.getTarget()).css('cursor',
								'pointer');
					} else {
						$("#" + geofencingmap.getTarget()).css('cursor','');
						addInteraction(1);
					}
				} else {
					$("#" + geofencingmap.getTarget()).css('cursor','');
					addInteraction("fromgeofence",0);
				}
				if (e.dragging) {
					//$(element).popover('hide');
					return;
				}
			});

			//  var geoFenceCoordinates = [];
			if (geoFenceCoordinates.length > 0) {
				var polyCoordinates = [];
				for (var i in geoFenceCoordinates) {
					polyCoordinates.push(ol.proj.transform([parseFloat(
								geoFenceCoordinates[i][0]),parseFloat(
								geoFenceCoordinates[i][1])],'EPSG:4326',
							'EPSG:900913'));
				}
				//console.log(polyCoordinates);
				var feature = new ol.Feature({
					geometry:new ol.geom.Polygon([polyCoordinates])
				});
				geosource.addFeature(feature);
			}
			addInteraction("fromgeofence",1);
		}
	}
	
	function getpoilist(){
		// get poi list
		$.ajax({
			url:BASEURL + "controlcentre/getpoilist",
			dataType:'json',
			type:"GET"
		}).done(function (resp) {
			if (typeof resp.status !== 'undefined' && resp.status == '1') {
				if (typeof resp.result.html !== 'undefined' && resp.result.html.length > 0) {
					$("#doublerightpanelcontent").html(resp.result.html);
					$("#doublerightpanel").show();
				}
			}
		}).fail(function () {
			console.log("Fetch error");
		});
	}
	
	function getallinteractionlist(){
		// get poi list
		$.ajax({
			url:BASEURL + "controlcentre/getallinteractionlist",
			dataType:'json',
			type:"GET"
		}).done(function (resp) {
			if (typeof resp.status !== 'undefined' && resp.status == '1') {
				if (typeof resp.result.poihtml !== 'undefined' && resp.result.poihtml.length > 0) {
					$("#poilist").html(resp.result.poihtml);					
				}
				if (typeof resp.result.routehtml !== 'undefined' && resp.result.routehtml.length > 0) {
					$("#routelist").html(resp.result.routehtml);					
				}
				if (typeof resp.result.geofencehtml !== 'undefined' && resp.result.geofencehtml.length > 0) {
					$("#geofencelist").html(resp.result.geofencehtml);					
				}
				$("#doublerightpanel").show();
			}
		}).fail(function () {
			console.log("Fetch error");
		});
	}
	
	$(document).on('click',".clickpoi",function () {

        // get all checked pois 
        $(document).find(".clickpoi").each(function () {

            if ($(this).prop('checked') == true) {
                var poiid = $(this).attr('id');
                if (globalCheckedPoiId.indexOf(poiid) == -1) {
                    globalCheckedPoiId.push(poiid);
                }
            } else {
                var poiid = $(this).attr('id');
                var index = globalCheckedPoiId.indexOf(poiid);
                if (index != -1) {
                    globalCheckedPoiId.splice(index,1);
                }
            }
        });
        if (globalCheckedPoiId.length > 0) {
            fetchPoiToPlot();
        } else {
            removeAllMarkers('poi');
        }
    });
	
    function fetchPoiToPlot() {
        $.ajax({
            url:BASEURL + "controlcentre/get_poi_position_data",
            method:"POST",
            data:{allselectedpoi:globalCheckedPoiId},
            global:false,
            dataType:"json"
        }).done(function (resp) {
            // console.log(resp)
			removeAllMarkers('poi');
            var respObj = resp.result;
            var dataLength = Object.keys(respObj).length;
            if (dataLength > 0) {
                for (var i = 0;i < dataLength;i++) {
                    addMarker(respObj[i],'poi');
                }
            }
        }).fail(function () {
            console.log("error in fetching")
        }).complete(function () {
        });
    }
	
	function getroutelist(){
		// get route list
		$.ajax({
			url:BASEURL + "controlcentre/getroutelist",
			dataType:'json',
			type:"GET"
		}).done(function (resp) {
			if (typeof resp.status !== 'undefined' && resp.status == '1') {
				if (typeof resp.result.html !== 'undefined' && resp.result.html.length > 0) {
					$("#doublerightpanelcontent").html(resp.result.html);
					$("#doublerightpanel").show();
				}
			}
		}).fail(function () {
			console.log("Fetch error");
		});
	}
	
	$(document).on('click',".clickroute",function () {

        // get all checked 
        $(document).find(".clickroute").each(function () {

            if ($(this).prop('checked') == true) {
                var id = $(this).attr('id');
                if (globalCheckedRouteId.indexOf(id) == -1) {
                    globalCheckedRouteId.push(id);
                }
            } else {
                var id = $(this).attr('id');
                var index = globalCheckedRouteId.indexOf(id);
                if (index != -1) {
                    globalCheckedRouteId.splice(index,1);
                }
            }
        });
        if (globalCheckedRouteId.length > 0) {
            fetchRouteToPlot();
        } else {
            vectorRoute.getSource().clear();
        }
    });
	
	function fetchRouteToPlot() {
        $.ajax({
            url:BASEURL + "controlcentre/get_route_position_data",
            method:"POST",
            data:{allselectedroute:globalCheckedRouteId},
            global:false,
            dataType:"json"
        }).done(function (resp) {
            // console.log(resp)
			vectorRoute.getSource().clear();
            var respObj = resp.result;
            var dataLength = Object.keys(respObj).length;
            if (dataLength > 0) {
                for (var i = 0;i < dataLength;i++) {
                    var polyCoordinates = [];
					if (typeof respObj[i].lonlat !== 'undefined' && respObj[i].lonlat.length > 1) {
						//console.log(respObj[i])
						var id = respObj[i].id;
						var coordinates = respObj[i].lonlat.split(",");
						var j = 0;
						var coordinateArr = [];
						while (j != (coordinates.length - 0)) {
							coordinateArr.push([coordinates[j],coordinates[j + 1]]);
							j += 2;
							// coordinateArr.push(myArray.splice(0, chunk_size));
						}
						for (var k = 0;k < coordinateArr.length;k++) {
							polyCoordinates.push([parseFloat(
										coordinateArr[k][0]),parseFloat(
										coordinateArr[k][1])]);
						}
						var geom = new ol.geom.LineString(polyCoordinates);
						geom.transform('EPSG:4326', 'EPSG:3857');
						var feature = new ol.Feature({
							geometry: geom,
							id: "existingroute_" + id
						});
						feature.setStyle([
							new ol.style.Style({
								stroke:new ol.style.Stroke({
									color:'green',
									width:3
								})
							})
						]);
						feature.setId("existingroute_" + id);
						sourceRoute.addFeature(feature);
						mapPOI.getView().setCenter(ol.proj.transform([
							parseFloat(
									coordinateArr[0][0]),parseFloat(
									coordinateArr[0][1])],
								'EPSG:4326','EPSG:3857'));
						mapPOI.getView().setZoom(17);
					}
                }
				//console.log(polyCoordinates);
            }
        }).fail(function () {
            console.log("error in fetching")
        }).complete(function () {
        });
    }
	
	function getgeofencelist(){
		// get geofence list
		$.ajax({
			url:BASEURL + "controlcentre/getgeofencelist",
			dataType:'json',
			type:"GET"
		}).done(function (resp) {
			if (typeof resp.status !== 'undefined' && resp.status == '1') {
				if (typeof resp.result.html !== 'undefined' && resp.result.html.length > 0) {
					$("#doublerightpanelcontent").html(resp.result.html);
					$("#doublerightpanel").show();
				}
			}
		}).fail(function () {
			console.log("Fetch error");
		});
	}
	
	$(document).on('click',".clickgeofence",function () {

        // get all checked 
        $(document).find(".clickgeofence").each(function () {

            if ($(this).prop('checked') == true) {
                var id = $(this).attr('id');
                if (globalCheckedGeofenceId.indexOf(id) == -1) {
                    globalCheckedGeofenceId.push(id);
                }
            } else {
                var id = $(this).attr('id');
                var index = globalCheckedGeofenceId.indexOf(id);
                if (index != -1) {
                    globalCheckedGeofenceId.splice(index,1);
                }
            }
        });
        if (globalCheckedGeofenceId.length > 0) {
            fetchGeofenceToPlot();
        } else {
            vectorGeo.getSource().clear();
        }
    });
	
	function fetchGeofenceToPlot() {
        $.ajax({
            url:BASEURL + "controlcentre/get_geofence_position_data",
            method:"POST",
            data:{allselectedgeofence:globalCheckedGeofenceId},
            global:false,
            dataType:"json"
        }).done(function (resp) {
            // console.log(resp)
			vectorGeo.getSource().clear();
            var respObj = resp.result;
            var dataLength = Object.keys(respObj).length;
            if (dataLength > 0) {
                for (var i = 0;i < dataLength;i++) {
                    var polyCoordinates = [];
					if (typeof respObj[i].lonlat !== 'undefined' && respObj[i].lonlat.length > 1) {
						//console.log(respObj[i])
						var id = respObj[i].id;
						var coordinates = respObj[i].lonlat.split(",");
						var j = 0;
						var coordinateArr = [];
						
						while (j != (coordinates.length - 2)) {
							coordinateArr.push([coordinates[j],coordinates[j + 1]]);
							j += 2;
							// coordinateArr.push(myArray.splice(0, chunk_size));
						}
						for (var k = 0;k < coordinateArr.length;k++) {
							polyCoordinates.push(ol.proj.transform([parseFloat(
										coordinateArr[k][0]),parseFloat(
										coordinateArr[k][1])],'EPSG:4326',
									'EPSG:900913'));
						}
						var feature = new ol.Feature({
							geometry:new ol.geom.Polygon([polyCoordinates])
						});
						feature.setId("existinggeofences_" + id);
						sourceGeo.addFeature(feature);
						geofencingmap.getView().setCenter(ol.proj.transform([
							parseFloat(
									coordinateArr[0][0]),parseFloat(
									coordinateArr[0][1])],
								'EPSG:4326','EPSG:3857'));
						geofencingmap.getView().setZoom(17);
					}
                }
				//console.log(polyCoordinates);
            }
        }).fail(function () {
            console.log("error in fetching")
        }).complete(function () {
        });
    }
	
    var transeferMyCoordinates;
    var poiTypeSelect = document.getElementById('poidrawingmodeselect');
    var overlayToolTipRoute;
    var tooltipRoute;
    $("#poimodal").on('shown.bs.modal',function () {
//        $("#clearGeofencingMarks").show().on('click',function () {
//            vector.getSource().clear();
//            vectorGeo.getSource().clear();
//        });   
        mapPOI = new ol.Map({
            layers:[raster,vectorPOI],
            target:'poimap',
            view:new ol.View({
                center:map.getView().getCenter(),
                zoom:14
            }),
            crossOrigin:'anonymous',
            controls:ol.control.defaults().extend([
                new ol.control.FullScreen()
            ])
        });


        if (mapMode == 'poi') {
            //  $("#showdistance").hide();
            //    $("#poidrawingmode").show();
            poiDrawModeCall();
        }
        if (mapMode == 'route') {
            //     $("#poidrawingmode").hide();
            $("#showdistance").show();
            overlayToolTipRoute = document.getElementById(
                    'showdistance');
            tooltipRoute = new ol.Overlay({
                element:tooltipRoute,
                offset:[10,0]
            });
            mapPOI.addOverlay(tooltipRoute);
            addInteraction("calledfromroute",1);
            mapPOI.on('pointermove',function (evt) {
                transeferMyCoordinates = evt.coordinate;
            });
        }
    });



    poiTypeSelect.onchange = function () {
        mapPOI.removeInteraction(drawLine);
        poiDrawModeCall();
        // addInteraction();
    };
    var typeofpoievent;
    function poiDrawModeCall() {
        var selectedvalue = poiTypeSelect.value;
        //   console.log(selectedvalue)
        if (selectedvalue == 'Point') {
            if (typeofpoievent) {
				ol.Observable.unByKey(typeofpoievent);
			}
            var iconStyle = new ol.style.Style({
                image:new ol.style.Icon({
                    anchor:[0.5,46],
                    anchorXUnits:'fraction',
                    anchorYUnits:'pixels',
                    opacity:0.75,
                    src:BASEURLIMG + 'assets/images/blackflag.png'
                }),
                text:new ol.style.Text({
                    font:'12px Calibri,sans-serif',
                    fill:new ol.style.Fill({color:'#000'}),
                    stroke:new ol.style.Stroke({
                        color:'#fff',width:2
                    })})
            });
            typeofpoievent = mapPOI.on('click',function (evt) {
                var feature = mapPOI.forEachFeatureAtPixel(evt.pixel,
                        function (feature,layer) {
                            return feature;
                        });

                if (typeof feature === 'undefined') {
                    var feature = new ol.Feature(
                            new ol.geom.Point(evt.coordinate)
                            );
                    feature.setStyle(iconStyle);
                    sourcePOI.addFeature(feature);
                    var coords = feature.getGeometry().getCoordinates();
                    var coordLen = Object.keys(coords).length;
                    var lonLat = [];
                    poiAndRouteData.feature = feature;
                    if (coordLen > 0) {
                        lonLat.push(ol.proj.transform([
                            coords[0],coords[1]],'EPSG:3857','EPSG:4326'));
                        geofencingStoreData.longLats = lonLat;
                        // console.log(geofencingStoreData)
                        $(".bufferarea").show();
                        $("#savedatapoiandroute").modal({
                            backdrop:'static',
                            keyboard:false
                        });
                    }
                }
            });
        }
        if (selectedvalue == 'Polygon') {
            mapPOI.unByKey(typeofpoievent);
            addInteraction("calledfrompoi",1,selectedvalue);
        }
        if (selectedvalue == 'Circle') {
            mapPOI.unByKey(typeofpoievent);
            addInteraction("calledfrompoi",1,selectedvalue);
        }
    }

    $("#poimodal").on('hidden.bs.modal',function () {
        if (typeof drawLine !== 'undefined') {
            mapPOI.removeInteraction(drawLine);
        }
        mapPOI.removeOverlay(tooltipRoute);
        tooltipRoute = null;
        vectorPOI.getSource().clear();
        mapPOI.setTarget(null);
        mapPOI = null;
        poiAndRouteData = {};
        $("#showdistance").html('').hide();
    });

    function discardPOI() {
        vectorPOI.getSource().removeFeature(poiAndRouteData.feature);
		$("#showdistance").html('');
    }

	var geofencingmapclick;
	var geofencingmappointermove;
    function creategeof() {
        geoFenceMode = "general";
        $("#geofencemodedevice").hide();
        getExistingGeofencingData();
        if(mapPOI){
			if (typeof drawLine !== 'undefined') {
				mapPOI.removeInteraction(drawLine);
			}
			mapPOI.removeOverlay(tooltipRoute);
			if (typeofpoievent) {
				ol.Observable.unByKey(typeofpoievent);
			}
			tooltipRoute=null;
			vectorPOI.getSource().clear();
			vectorRoute.getSource().clear();
			//mapPOI.setTarget(null);
			mapPOI = null;
			poiAndRouteData = {};
			$("#showdistance").html('').hide();
			$("#showplacesearch").hide();
			mapMode = "";
			$('#pac-input').val('');
			$("#doublerightpanelcontent").html('');
			$("#doublerightpanel").hide();
		}
		if(geofencingmap){
			if (typeof drawpoly !== 'undefined') {
				geofencingmap.removeInteraction(drawpoly);					
			}
			geofencingStoreData = {};
			geoFenceCoordinates = [];
			if (geofencingmapclick) {
				ol.Observable.unByKey(geofencingmapclick);
			}
			if (geofencingmappointermove) {
				ol.Observable.unByKey(geofencingmappointermove);
			}
			//geofencingmap.setTarget(null);				
			vector.getSource().clear();
			vectorGeo.getSource().clear();
			drawpoly = null;
			geofencingmap = null;
			mapMode = "";
			$("#deviceserialGFModal").html('');
			$("#deleteGeofencingMarks").hide().removeAttr("onclick");
			$("#doublerightpanelcontent").html('');
			$("#doublerightpanel").hide();
		}
		if(mapRuler){
			if (typeof drawRuler !== 'undefined') {
				mapRuler.removeInteraction(drawRuler);
			}
			mapRuler.removeOverlay(tooltipRuler);
			tooltipRuler=null;
			sourceRuler.clear();
			//mapPOI.setTarget(null);
			mapRuler = null;
			$("#showruler").html('').hide();
			mapMode = "";
		}
		getallinteractionlist();
		//$("#addinteractionleft").attr("data-type", "geofence");
		mapPOI = map;		
		geofencingmap = map;
		$("#collapseOne").removeClass("show");
		$("#collapseTwo").removeClass("show");
		$("#collapseThree").addClass("show");
    }

    function deleteGeoFencing(geofenceid) {
        deleteDeviceGeoFencing(geofenceid,true);
    }


    function savePOIData() {

        var dataLoad = {};
        var poiroutename = $("#poiroutename");
        var bufferarea = $("#savedatapoiandroute #bufferarea");

        if (poiroutename.val() == '') {
            poiroutename.addClass("focus-required").focus();
            setTimeout(function () {
                poiroutename.removeClass("focus-required");
            },3000);
            return false;
        }


        //  if(bufferarea.is(":visible") == true){
        if (bufferarea.val() == '') {
            bufferarea.addClass("focus-required").focus();
            setTimeout(function () {
                bufferarea.removeClass("focus-required");
            },3000);
            return false;
        }
        //   }

        dataLoad.bufferarea = $.trim(bufferarea.val());
        dataLoad.name = $.trim(poiroutename.val());
        dataLoad.lonlatdata = geofencingStoreData.longLats;

        if ($("#showdistance").is(":visible") == true) {
            dataLoad.typeid = 3;
        } else {
            dataLoad.typeid = 2;
        }

        $.ajax({
            url:BASEURL + "controlcentre/createpoi",
            dataType:'json',
            type:"POST",
            data:dataLoad
        }).done(function (resp) {
            if (typeof resp.status !== 'undefined') {
                var dialogButtons = {
                    "OK":function () {
                        $("#dialog_confirm").dialog(
                                "close");
                    }
                };

                if (resp.result == 1) {
                    var closeFn = function () {
                        $("#savedatapoiandroute").modal('hide');
                        $("#dialog_confirm").removeAttr('title');
                        $("#alert_msg").html('');
                        $("#alertdeviceid").val('');
                        $("#configurealert").modal('hide');
                    }
                    if (dataLoad.typeid == 2) {
                        showDialogs('POI','POI created successfully',
                                dialogButtons,closeFn);
						getallinteractionlist();
                    } else if (dataLoad.typeid == 3) {
                        showDialogs('Route','Route created successfully',
                                dialogButtons,closeFn);
						getallinteractionlist();
                    }


                } else if (resp.result == 2) {
                    var closeFn = function () {
                        $("#dialog_confirm").removeAttr('title');
                        $("#alert_msg").html('');
                        $("#alertdeviceid").val('');
                        $("#configurealert").modal('hide');
                    }
                    if (dataLoad.typeid == 2) {
                        showDialogs('POI','Name already exists',
                                dialogButtons,closeFn);
                    } else if (dataLoad.typeid == 3) {
                        showDialogs('Route','Name already exists',
                                dialogButtons,closeFn);
                    }

                } else if (resp.result == 4) {
					var closeFn = function () {
                        $("#dialog_confirm").removeAttr('title');
                        $("#alert_msg").html('');
                        $("#alertdeviceid").val('');
                        $("#configurealert").modal('hide');
                    }
                    if (dataLoad.typeid == 2) {
                        showDialogs('POI','Permission Restricted',
                                dialogButtons,closeFn);
                    } else if (dataLoad.typeid == 3) {
                        showDialogs('Route','Permission Restricted',
                                dialogButtons,closeFn);
                    }
				} else {
                    var closeFn = function () {
                        $("#dialog_confirm").removeAttr('title');
                        $("#alert_msg").html('');
                        $("#alertdeviceid").val('');
                        $("#configurealert").modal('hide');
                    }
                    if (dataLoad.typeid == 2) {
                        showDialogs('POI','Failed to create POI',
                                dialogButtons,closeFn);
                    } else if (dataLoad.typeid == 3) {
                        showDialogs('Route','Failed to create Route',
                                dialogButtons,closeFn);
                    }
                }
            }
        }).fail(function () {
            console.log("poi save error");
        });
    }

    $("#savedatapoiandroute").on('hidden.bs.modal',function () {
        $("#savedatapoiandroute form")[0].reset();
		if(mapPOI){
			if (typeof drawLine !== 'undefined') {
				mapPOI.removeInteraction(drawLine);
			}
			mapPOI.removeOverlay(tooltipRoute);
			mapPOI.unByKey(typeofpoievent);
			tooltipRoute=null;
			vectorPOI.getSource().clear();
			poiAndRouteData = {};
			$("#showdistance").html('').hide();
			$("#showplacesearch").hide();
			$('#pac-input').val('');
			mapPOI = null;			
		}
		mapPOI = map;
    });


    function cloneGeofence() {
        var alertsddgeoexisting = $("#alertsddgeoexisting");
        var dialogButtons = {
            "OK":function () {
                $("#dialog_confirm").dialog(
                        "close");
            }
        }
        var closeFn = function () {
            $("#dialog_confirm").removeAttr(
                    'title');
            $("#alert_msg").html(
                    '');
        }
        if ($.trim(alertsddgeoexisting.val()) == '') {
            showDialogs('Attach GeoFencing',
                    'Alert is required',
                    dialogButtons,closeFn);
        } else {

            $.ajax({
                url:BASEURL + "controlcentre/clonegeofencing",
                type:'POST',
                dataType:'json',
                data:{
                    deviceid:geofencingStoreData.deviceid,
                    cloneid:geofencingStoreData.cloneid,
                    alertid:$.trim(alertsddgeoexisting.val())
                }
            }).done(function (resp) {
                // $("#dialog_confirm").dialog("close");

                if (typeof resp.status !== 'undefined' && resp.result == 1) {
                    showDialogs('Attach GeoFencing',
                            'Geofence created',
                            dialogButtons,closeFn);
                } else {
                    showDialogs('Attach GeoFencing',
                            'Failed to create Geofence',
                            dialogButtons,closeFn);
                }
            }).fail(function () {
                showDialogs('Attach GeoFencing',
                        'Failed to create Geofence',
                        dialogButtons,closeFn);
                console.log("assign error");
            });
        }
    }

    function closethismodal(elem) {
		$(elem).modal('hide');
    }
    $(function () {
        //$( "#proximitytabs" ).tabs();
        $('#myTabsproxy a').click(function (e) {
            e.preventDefault()
            $('#myTabsproxy li').removeClass('activealerts');
            $(this).tab('show').parent('li').addClass('activealerts');
        });
        $('#myTabsoffroute a').click(function (e) {
            e.preventDefault()
            $('#myTabsoffroute li').removeClass('activealerts');
            $(this).tab('show').parent('li').addClass('activealerts');
        })
        $(function () {
            var tabs = $("#alerttabs").tabs();
            tabs.find(".ui-tabs-nav").sortable({
                axis:"x",
                stop:function () {
                    tabs.tabs("refresh");
                }
            });
        });
    });
	
	$(document).on('click',".mdlBtn",function () {
		var link = $(this).attr("dat_link");
		//alert(link);		
		$("#myModalconfigure").find('iframe').attr('src',link);
		$("#myModalconfigure").modal("show");
    });
	$(document).on('click',".mdlBtninfo",function () {
		var link = $(this).attr("dat_link");
		//alert(link);		
		$("#myModaldeviceinfo").find('iframe').attr('src',link);
		$("#myModaldeviceinfo").modal("show");
    });
	
	function currentlocation(deviceid) {
		$.ajax({
			url:BASEURL + "controlcentre/getcurrentlocation",
			data:{deviceid:deviceid},
			dataType:'json',
			type:"POST",
			global:false
		}).done(function (resp) {
			//$("#" + deviceid).attr('checked',true);
			var devid = deviceid.toString()
			if (globalCheckedDevieIdForTracking.indexOf(devid) == -1) {
				globalCheckedDevieIdForTracking.push(devid);
			}
			timerForDevicePositionDataAjax.abort();
			fetchDataForTracking();
			var respObj = resp.result;
            var dataLength = Object.keys(respObj).length;
            var isMarkerAdded = 0;
            if (dataLength > 0) {
                for (var i = 0;i < dataLength;i++) {
					map.getView().setCenter(ol.proj.transform([parseFloat(
                        respObj[i].longitude),parseFloat(respObj[i].latitude)],
                    'EPSG:4326','EPSG:3857'));
                }
            }
		}).fail(function () {
			console.log("Fetch error");
		});
	}
	
	function deviceswitchoff(deviceid) {
		$.ajax({
			url:BASEURL + "controlcentre/deviceswitchoff",
			data:{deviceid:deviceid},
			dataType:'json',
			type:"POST",
			global:false
		}).done(function (resp) {
			
		}).fail(function () {
			console.log("Fetch error");
		});
	}
	
	function silentcallon(serial_no) {
		$.ajax({
			url:BASEURL + "controlcentre/silentcallon",
			data:{serial_no:serial_no},
			dataType:'json',
			type:"POST",
			global:true
		}).done(function (resp) {
			if(resp.status == 1){
				alert(resp.msg);
				$("#silentoff"+resp.deviceid).show();
				$("#silenton"+resp.deviceid).hide();
			}
			else {
				alert(resp.msg);
			}
		}).fail(function () {
			console.log("Fetch error");
		});
	}
	
	function silentcalloff(serial_no) {
		$.ajax({
			url:BASEURL + "controlcentre/silentcalloff",
			data:{serial_no:serial_no},
			dataType:'json',
			type:"POST",
			global:true
		}).done(function (resp) {
			if(resp.status == 1){
				alert(resp.msg);
				$("#silentoff"+resp.deviceid).hide();
				$("#silenton"+resp.deviceid).show();
			}
			else {
				alert(resp.msg);
			}
		}).fail(function () {
			console.log("Fetch error");
		});
	}
	
	// follow device start
	
	var mapvars = {};
	var trackSourcevars = {};
	var trackLayervars = {};
	var vectorSourcevars = {};
	var vectoLayervars = {};
	var locateSourcevars = {};
	var locateLayervars = {};
	var followajaxvars = {};
	var fetchDataFollowTimeoutCallvars = {};
	var followdevid = 1;
	var followfeaturevars = {};
	var followlatlonvars = {};
	var followtrackonSourcevars = {};
	var followtrackonLayervars = {};
	var followfeaturevarspole = {};
	var followlatlonvarspole = {};
	var followtrackonSourcevarspole = {};
	var followtrackonLayervarspole = {};
	/*function followdevice(deviceid) {
		$("#mydiv"+deviceid).remove();
		delete mapvars['followmap' + deviceid];
		$(".map").append('<div id="mydiv'+deviceid+'" class="followdiv"><div id="mydiv'+deviceid+'header" class="followdivheader">Follow Device <a href="javascript:void(0);" style="float: right;color: #fff;" onclick="deletefollow('+deviceid+')"><i class="fa fa-times" aria-hidden="true" style="padding: 0px; line-height: 11px; margin: 0px 6px 0px 0px; overflow: hidden;"></i></a></div><div id="followmap'+deviceid+'" class="followmap"></div></div>');
		
		vectorSourcevars['followmap' + deviceid] = new ol.source.Vector({});
		vectoLayervars['followmap' + deviceid] = new ol.layer.Vector({
			source:vectorSourcevars['followmap' + deviceid]
		});
		locateSourcevars['followmap' + deviceid] = new ol.source.Vector({});		
		locateLayervars['followmap' + deviceid] = new ol.layer.Vector({
			source:locateSourcevars['followmap' + deviceid]
		});
		trackSourcevars['followmap' + deviceid] = new ol.source.Vector({});
		trackLayervars['followmap' + deviceid] = new ol.layer.Vector({
			source:trackSourcevars['followmap' + deviceid]
		});
		mapvars['followmap' + deviceid] = new ol.Map({
			target:'followmap'+deviceid,// The DOM element that will contains the map
			layers:bingLayers.concat(vectoLayervars['followmap' + deviceid],trackLayervars['followmap' + deviceid],locateLayervars['followmap' + deviceid]),
			// Create a view centered on the specified location and zoom level
			view:new ol.View({
				center:ol.proj.transform([lon,lat],'EPSG:4326','EPSG:3857'),
				zoom:16
			}),
			crossOrigin:'anonymous',
			controls: []
		});
		dragElement(document.getElementById(("mydiv"+deviceid)));
		fetchFollowTracking(deviceid);		
	}
	
	function fetchFollowTracking(deviceid){
		followajaxvars['followmap' + deviceid] = $.ajax({
			url:BASEURL + "controlcentre/getfollowlocation",
			data:{deviceid:deviceid},
			dataType:'json',
			type:"POST",
			global:false
		}).done(function (resp) {			
			var respObj = resp.result;
			var deviceObj = resp.dev_data;
			//console.log(deviceObj);
			var dataLength = Object.keys(respObj).length;
            if (dataLength > 0) {
                for (var i = 0;i < dataLength;i++) {
					trackLayervars['followmap' + deviceid].getSource().clear();
					var geom = new ol.geom.Point(ol.proj.transform([parseFloat(
						respObj[i].longitude),parseFloat(respObj[i].latitude)],
					'EPSG:4326','EPSG:3857'));
					var sizeArr = [32,30];
					var scale = 0.8;
					var feature = new ol.Feature({
						geometry:geom,
						id:"track_" + respObj[i].deviceid,
						devid:respObj[i].deviceid
					});
					var iconURL = BASEURLIMG + 'assets/iconset/worker1.png';
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
					trackSourcevars['followmap' + deviceid].addFeature(feature);
					mapvars['followmap' + deviceid].getView().setCenter(ol.proj.transform([parseFloat(
                        respObj[i].longitude),parseFloat(respObj[i].latitude)],
                    'EPSG:4326','EPSG:3857'));
					
					$("#mydiv"+deviceid+"header").html(deviceObj.serial_no+' ('+deviceObj.device_name+') <a href="javascript:void(0);" style="float: right;color: #fff;" onclick="deletefollow('+deviceid+')"><i class="fa fa-times" aria-hidden="true" style="padding: 0px; line-height: 11px; margin: 0px 6px 0px 0px; overflow: hidden;"></i></a>');
                }
            }
		}).fail(function () {
			console.log("Fetch error");
		}).complete(function () {
            fetchDataFollowTimeoutCallvars['followmap' + deviceid] = setTimeout(function () {
                fetchFollowTracking(deviceid);
            },10000);
        });
	}
	
	function deletefollow(deviceid){
		trackLayervars['followmap' + deviceid].getSource().clear();
		$("#mydiv"+deviceid).remove();
		delete mapvars['followmap' + deviceid];
		followajaxvars['followmap' + deviceid].abort();
		clearTimeout(fetchDataFollowTimeoutCallvars['followmap' + deviceid]);
		console.log(mapvars);
	}*/
	
	//New logic of follow only one device will be followed

	function playbackdevice(deviceid) {
		window.open('<?php echo site_url('controlcentre/view').'/'?>' + deviceid + '<?php echo '/'.date("d-m-Y").'/00:00/00:00' ?>', '_blank');
	}
	
	function followdevice(deviceid) {
		$("#mydiv"+followdevid).remove();		
		if(mapvars['followmap' + followdevid]){
			delete mapvars['followmap' + followdevid];
			trackLayervars['followmap' + followdevid].getSource().clear();
			locateLayervars['followmap' + followdevid].getSource().clear();
			followajaxvars['followmap' + followdevid].abort();
			clearTimeout(fetchDataFollowTimeoutCallvars['followmap' + followdevid]);
		}
		$(".map").append('<div id="mydiv'+followdevid+'" class="followdiv"><div id="mydiv'+followdevid+'header" class="followdivheader">Follow Device <a href="javascript:void(0);" style="float: right;color: #fff;" onclick="deletefollow('+followdevid+')"><i class="fa fa-times" aria-hidden="true" style="padding: 0px; line-height: 11px; margin: 0px 6px 0px 0px; overflow: hidden;"></i></a></div><div id="followmap'+followdevid+'" class="followmap"></div></div>');
		
		vectorSourcevars['followmap' + followdevid] = new ol.source.Vector({});
		vectoLayervars['followmap' + followdevid] = new ol.layer.Vector({
			source:vectorSourcevars['followmap' + followdevid]
		});
		locateSourcevars['followmap' + followdevid] = new ol.source.Vector({});		
		locateLayervars['followmap' + followdevid] = new ol.layer.Vector({
			source:locateSourcevars['followmap' + followdevid]
		});
		trackSourcevars['followmap' + followdevid] = new ol.source.Vector({});
		trackLayervars['followmap' + followdevid] = new ol.layer.Vector({
			source:trackSourcevars['followmap' + followdevid]
		});
		trackLayervars['followmap' + followdevid].setZIndex(10);
		mapvars['followmap' + followdevid] = new ol.Map({
			target:'followmap'+followdevid,// The DOM element that will contains the map
			layers:bingLayers.concat(vectoLayervars['followmap' + followdevid],trackLayervars['followmap' + followdevid],locateLayervars['followmap' + followdevid]),
			// Create a view centered on the specified location and zoom level
			view:new ol.View({
				center:ol.proj.transform([lon,lat],'EPSG:4326','EPSG:3857'),
				zoom:16,
				minZoom: 6,
				maxZoom: 20
			}),
			crossOrigin:'anonymous',
			controls: []
		});
		
		var pollLayer = new ol.layer.Tile({                                                        
			title: 'Pole',
			visible: true,
			source: new ol.source.TileWMS({
			  url: GEOSERVER_URL+'/nfrtsk/wms?service=WMS&version=1.1.1&request=GetMap',
			  params: {
					   'VERSION': '1.1.1',
					   tiled: true,
					   STYLES: '',
					   LAYERS: 'nfrtsk:master_polldata'
			  }
			})
		});
		mapvars['followmap' + followdevid].addLayer(pollLayer);
		
		dragElement(document.getElementById(("mydiv"+followdevid)));
		
		//trail object start
		var followstartMarker = {};
		if(followtrackonLayervars['trackon' + followdevid]){
			followtrackonLayervars['trackon' + followdevid].getSource().clear();
			map.removeLayer(followtrackonLayervars['trackon' + followdevid]);
			delete followfeaturevars['trackon' + followdevid];
			delete followlatlonvars['trackon' + followdevid];
			delete followtrackonLayervars['trackon' + followdevid];
			delete followtrackonSourcevars['trackon' + followdevid];
			followtrackonLayervarspole['trackon' + followdevid].getSource().clear();
			map.removeLayer(followtrackonLayervarspole['trackon' + followdevid]);
			delete followfeaturevarspole['trackon' + followdevid];
			delete followlatlonvarspole['trackon' + followdevid];
			delete followtrackonLayervarspole['trackon' + followdevid];
			delete followtrackonSourcevarspole['trackon' + followdevid];
		}
		followtrackonSourcevars['trackon' + followdevid] = new ol.source.Vector({});
		followtrackonLayervars['trackon' + followdevid] = new ol.layer.Vector({
			source:followtrackonSourcevars['trackon' + followdevid]
		});
		mapvars['followmap' + followdevid].addLayer(followtrackonLayervars['trackon' + followdevid]);
		followtrackonSourcevarspole['trackon' + followdevid] = new ol.source.Vector({});
		followtrackonLayervarspole['trackon' + followdevid] = new ol.layer.Vector({
			source:followtrackonSourcevarspole['trackon' + followdevid]
		});
		mapvars['followmap' + followdevid].addLayer(followtrackonLayervarspole['trackon' + followdevid]);
		followlatlonvars['trackon' + followdevid] = [];
		followlatlonvarspole['trackon' + followdevid] = [];
		$.ajax({
			url:BASEURL + "controlcentre/getdevicetodaycoordinates",
			data:{deviceid:deviceid},
			dataType:'json',
			type:"POST",
			global:false
		}).done(function (resp) { console.log(resp.getcoordinates);
			var respObj = resp.getcoordinates;
			var dataLength = Object.keys(respObj).length;
			var respObjpole = resp.getpoledata;
			var dataLengthpole = Object.keys(respObjpole).length;
			var respObjpoleline = resp.getpolelinedata;
			var dataLengthpoleline = Object.keys(respObjpoleline).length;console.log(dataLength);
            if (dataLength > 0) {
                for (var i = 0;i < dataLength;i++) {
					if(i == 0){
						startMarker = {deviceid:respObj[i].deviceid,longitude:respObj[i].longitude,latitude:respObj[i].latitude,faetureid:respObj[i].positionalid};
					}
					followlatlonvars['trackon' + followdevid].push([parseFloat(
										respObj[i].longitude),parseFloat(
										parseFloat(respObj[i].latitude))]);
				}
				var geom = new ol.geom.LineString(followlatlonvars['trackon' + followdevid]);
				geom.transform('EPSG:4326', 'EPSG:3857');
				followfeaturevars['trackon' + followdevid] = new ol.Feature({
					geometry: geom
				});
				// line style start
				var styleFunction = function(feature) {
					var geometry = geom;
					var styles = [
					  // linestring
					  new ol.style.Style({
						stroke: new ol.style.Stroke({
						  color: '#0069d9',
						  width: 1.5
						})
					  })
					];

					/*var coords = geometry.getCoordinates();
					var coordslen = coords.length;
					var start = coords[coordslen-3];
					var end = coords[coordslen-2];
					var dx = end[0] - start[0];
					var dy = end[1] - start[1];
					var rotation = Math.atan2(dy, dx);
					// arrows
					styles.push(new ol.style.Style({
					geometry: new ol.geom.Point(end),
					image: new ol.style.Icon({
					  src: BASEURLIMG+'assets/images/arrow.png',
					  anchor: [0.75, 0.5],
					  rotateWithView: true,
					  rotation: -rotation
					})
					}));
					var arrowcounter = 0;
					geometry.forEachSegment(function(start, end) {
					  if(arrowcounter%20 == 0){
					  var dx = end[0] - start[0];
					  var dy = end[1] - start[1];
					  var rotation = Math.atan2(dy, dx);
					  // arrows
					  styles.push(new ol.style.Style({
						geometry: new ol.geom.Point(end),
						image: new ol.style.Icon({
						  src: BASEURLIMG+'assets/images/arrow.png',
						  anchor: [0.75, 0.5],
						  rotateWithView: true,
						  rotation: -rotation
						})
					  }));
					  }
					  arrowcounter++;
					});*/

					return styles;
				};
				// line style end
				/*followfeaturevars['trackon' + followdevid].setStyle([
					new ol.style.Style({
						stroke:new ol.style.Stroke({
							color:'#0069d9',
							width:1.5
						})
					})
				]);*/
				followfeaturevars['trackon' + followdevid].setStyle(styleFunction);
				followfeaturevars['trackon' + followdevid].setId("followtrackonroute_" + followdevid);
				followtrackonSourcevars['trackon' + followdevid].addFeature(followfeaturevars['trackon' + followdevid]);                
            }
			// pole data
			 if (dataLengthpoleline > 0) {
				 var HPdevid = 1;
				 var coordinates = respObjpoleline.lonlat.split(",");
				 var j = 0;
				 var coordinateArr = [];
				 while (j != (coordinates.length - 0)) {
					coordinateArr.push([coordinates[j],coordinates[j + 1]]);
					j += 2;
				 }
				for (var k = 0;k < coordinateArr.length;k++) {	
					followlatlonvarspole['trackon' + followdevid].push([parseFloat(
											coordinateArr[k][0]),parseFloat(
											coordinateArr[k][1])]);
					if(k == 0){
						startMarkerpole = {longitude:parseFloat(coordinateArr[k][0]),latitude:parseFloat(coordinateArr[k][1]),faetureid:'start'};
					}
					if(k == (coordinateArr.length-1)){
						endMarkerpole = {longitude:parseFloat(coordinateArr[k][0]),latitude:parseFloat(coordinateArr[k][1]),faetureid:'end'};
					}				
				}
				var geompole = new ol.geom.LineString(followlatlonvarspole['trackon' + followdevid]);
				geompole.transform('EPSG:4326', 'EPSG:3857');
				followfeaturevarspole['trackon' + followdevid] = new ol.Feature({
					geometry: geompole
				});
				// line style start
				var styleFunctionpole = function(feature) {
					var geometry = geompole;
					var styles = [
					  // linestring
					  new ol.style.Style({
						stroke: new ol.style.Stroke({
						  color: 'yellow',
						  width: 5
						})
					  })
					];

					return styles;
				};
				followfeaturevarspole['trackon' + followdevid].setStyle(styleFunctionpole);
				followfeaturevarspole['trackon' + followdevid].setId("followtrackonpole_" + followdevid);
				followtrackonSourcevarspole['trackon' + followdevid].addFeature(followfeaturevarspole['trackon' + followdevid]);
				
				var startgeompole = new ol.geom.Point(ol.proj.transform([parseFloat(
                    startMarkerpole.longitude),parseFloat(startMarkerpole.latitude)],
                'EPSG:4326','EPSG:3857'));
			
				var startfeaturepole = new ol.Feature({
					geometry:startgeompole,
					id:"followstartpole" + startMarkerpole.faetureid
				});
					
				var starticonURL = BASEURLIMG + 'assets/images/greenpole.png'
				
				startfeaturepole.setStyle([
					new ol.style.Style({
						image:new ol.style.Icon(({
							anchor:[0.5,1],
							size:[32,32],
							scale:0.7,
							anchorXUnits:'fraction',
							anchorYUnits:'fraction',
							opacity:1,
							src:starticonURL
						}))
					})
				]);
				locateSourcevars['followmap' + followdevid].addFeature(startfeaturepole);
				
				var endgeompole = new ol.geom.Point(ol.proj.transform([parseFloat(
						endMarkerpole.longitude),parseFloat(endMarkerpole.latitude)],
					'EPSG:4326','EPSG:3857'));
				
				var endfeaturepole = new ol.Feature({
					geometry:endgeompole,
					id:"followendpole" + endMarkerpole.faetureid
				});
					
				var endiconURL = BASEURLIMG + 'assets/images/redpole.png'
				
				endfeaturepole.setStyle([
					new ol.style.Style({
						image:new ol.style.Icon(({
							anchor:[0.5,1],
							size:[32,32],
							scale:0.7,
							anchorXUnits:'fraction',
							anchorYUnits:'fraction',
							opacity:1,
							src:endiconURL
						}))
					})
				]);
				locateSourcevars['followmap' + followdevid].addFeature(endfeaturepole);
			 }
		}).fail(function () {
			console.log("Fetch error");
		});
		//trail object end
		
		fetchFollowTracking(deviceid);		
	}
	
	function fetchFollowTracking(deviceid){
		followajaxvars['followmap' + followdevid] = $.ajax({
			url:BASEURL + "controlcentre/getfollowlocation",
			data:{deviceid:deviceid},
			dataType:'json',
			type:"POST",
			global:false
		}).done(function (resp) {			
			var respObj = resp.result;
			var deviceObj = resp.dev_data;
			//console.log(deviceObj);
			var dataLength = Object.keys(respObj).length;
            if (dataLength > 0) {
                for (var i = 0;i < dataLength;i++) {
					trackLayervars['followmap' + followdevid].getSource().clear();					
					var geom = new ol.geom.Point(ol.proj.transform([parseFloat(
						respObj[i].longitude),parseFloat(respObj[i].latitude)],
					'EPSG:4326','EPSG:3857'));
					var sizeArr = [32,30];
					var scale = 0.8;
					var feature = new ol.Feature({
						geometry:geom,
						id:"track_" + respObj[i].deviceid,
						devid:respObj[i].deviceid
					});
					var iconURL = BASEURLIMG + 'assets/iconset/device.png';
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
					trackSourcevars['followmap' + followdevid].addFeature(feature);
					mapvars['followmap' + followdevid].getView().setCenter(ol.proj.transform([parseFloat(
                        respObj[i].longitude),parseFloat(respObj[i].latitude)],
                    'EPSG:4326','EPSG:3857'));
					
					var dname = '';
					if(deviceObj.device_name){
						dname = ' ('+deviceObj.device_name+')';
					}
					
					$("#mydiv"+followdevid+"header").html(deviceObj.serial_no + dname +'<a href="javascript:void(0);" style="float: right;color: #fff;" onclick="deletefollow('+followdevid+')"><i class="fa fa-times" aria-hidden="true" style="padding: 0px; line-height: 11px; margin: 0px 6px 0px 0px; overflow: hidden;"></i></a>');
					
					followtrackonLayervars['trackon' + followdevid].getSource().clear();					
					if(followlatlonvars['trackon' + followdevid].length>0){
						followlatlonvars['trackon' + followdevid].push([parseFloat(
										respObj[i].longitude),parseFloat(
										parseFloat(respObj[i].latitude))]);
						var linegeom = new ol.geom.LineString(followlatlonvars['trackon' + followdevid]);
						linegeom.transform('EPSG:4326', 'EPSG:3857');
						followfeaturevars['trackon' + followdevid] = new ol.Feature({
							geometry: linegeom
						});
						// line style start
						var styleFunction = function(feature) {
							var geometry = linegeom;
							var styles = [
							  // linestring
							  new ol.style.Style({
								stroke: new ol.style.Stroke({
								  color: '#0069d9',
								  width: 1.5
								})
							  })
							];
							
							/*var coords = geometry.getCoordinates();
							var coordslen = coords.length;
							var start = coords[coordslen-3];
							var end = coords[coordslen-2];
							var dx = end[0] - start[0];
							var dy = end[1] - start[1];
							var rotation = Math.atan2(dy, dx);
							// arrows
							styles.push(new ol.style.Style({
							geometry: new ol.geom.Point(end),
							image: new ol.style.Icon({
							  src: BASEURLIMG+'assets/images/arrow.png',
							  anchor: [0.75, 0.5],
							  rotateWithView: true,
							  rotation: -rotation
							})
							}));
							var arrowcounter = 0;
							geometry.forEachSegment(function(start, end) {
							  if(arrowcounter%20 == 0){
							  var dx = end[0] - start[0];
							  var dy = end[1] - start[1];
							  var rotation = Math.atan2(dy, dx);
							  // arrows
							  styles.push(new ol.style.Style({
								geometry: new ol.geom.Point(end),
								image: new ol.style.Icon({
								  src: BASEURLIMG+'assets/images/arrow.png',
								  anchor: [0.75, 0.5],
								  rotateWithView: true,
								  rotation: -rotation
								})
							  }));
							  }
							  arrowcounter++;
							});*/

							return styles;
						};
						/*followfeaturevars['trackon' + followdevid].setStyle([
							new ol.style.Style({
								stroke:new ol.style.Stroke({
									color:'#0069d9',
									width:1.5
								})
							})
						]);*/
						followfeaturevars['trackon' + followdevid].setStyle(styleFunction);
						followfeaturevars['trackon' + followdevid].setId("followtrackonroute_" + followdevid);
						followtrackonSourcevars['trackon' + followdevid].addFeature(followfeaturevars['trackon' + followdevid]); 
					}
                }
            }
		}).fail(function () {
			console.log("Fetch error");
		}).complete(function () {
            fetchDataFollowTimeoutCallvars['followmap' + followdevid] = setTimeout(function () {
                fetchFollowTracking(deviceid);
            },20000);
        });
	}
	
	function deletefollow(deviceid){
		trackLayervars['followmap' + followdevid].getSource().clear();
		$("#mydiv"+followdevid).remove();
		delete followlatlonvars['trackon' + followdevid];
		delete mapvars['followmap' + followdevid];
		followajaxvars['followmap' + followdevid].abort();
		clearTimeout(fetchDataFollowTimeoutCallvars['followmap' + followdevid]);
		console.log(mapvars);
	}

	function dragElement(elmnt) {
	  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
	  if (document.getElementById(elmnt.id + "header")) {
		/* if present, the header is where you move the DIV from:*/
		document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
	  } else {
		/* otherwise, move the DIV from anywhere inside the DIV:*/
		elmnt.onmousedown = dragMouseDown;
	  }

	  function dragMouseDown(e) {
		e = e || window.event;
		// get the mouse cursor position at startup:
		pos3 = e.clientX;
		pos4 = e.clientY;
		document.onmouseup = closeDragElement;
		// call a function whenever the cursor moves:
		document.onmousemove = elementDrag;
	  }

	  function elementDrag(e) {
		e = e || window.event;
		// calculate the new cursor position:
		pos1 = pos3 - e.clientX;
		pos2 = pos4 - e.clientY;
		pos3 = e.clientX;
		pos4 = e.clientY;
		// set the element's new position:
		elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
		elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
	  }

	  function closeDragElement() {
		/* stop moving when mouse button is released:*/
		document.onmouseup = null;
		document.onmousemove = null;
	  }
	}
	
	// follow device end
	
	// track on device start
	
	var featurevars = {};
	var latlonvars = {};
	var trackonSourcevars = {};
	var trackonLayervars = {};
	function trackondevice(deviceid) {
		trackonSourcevars['trackon' + deviceid] = new ol.source.Vector({});
		trackonLayervars['trackon' + deviceid] = new ol.layer.Vector({
			source:trackonSourcevars['trackon' + deviceid]
		});
		map.addLayer(trackonLayervars['trackon' + deviceid]);
		latlonvars['trackon' + deviceid] = [];
		$.ajax({
			url:BASEURL + "controlcentre/getfollowlocation",
			data:{deviceid:deviceid},
			dataType:'json',
			type:"POST",
			global:false
		}).done(function (resp) {			
			var respObj = resp.result;
			var dataLength = Object.keys(respObj).length;
            if (dataLength > 0) {
                for (var i = 0;i < dataLength;i++) {
					latlonvars['trackon' + deviceid].push([parseFloat(
										respObj[i].longitude),parseFloat(
										parseFloat(respObj[i].latitude))]);
					var geom = new ol.geom.LineString(latlonvars['trackon' + deviceid]);
					geom.transform('EPSG:4326', 'EPSG:3857');
					featurevars['trackon' + deviceid] = new ol.Feature({
						geometry: geom
					});
					featurevars['trackon' + deviceid].setStyle([
						new ol.style.Style({
							stroke:new ol.style.Stroke({
								color:'green',
								width:2
							})
						})
					]);
					featurevars['trackon' + deviceid].setId("trackonroute_" + deviceid);
					trackonSourcevars['trackon' + deviceid].addFeature(featurevars['trackon' + deviceid]);
                }
            }
		}).fail(function () {
			console.log("Fetch error");
		}).complete(function () {
            $('#trackon'+deviceid).hide();
			$('#trackoff'+deviceid).show();
        });		
	}
	
	function trackoffdevice(deviceid) {
		trackonLayervars['trackon' + deviceid].getSource().clear();
		map.removeLayer(trackonLayervars['trackon' + deviceid]);
		delete featurevars['trackon' + deviceid];
		delete latlonvars['trackon' + deviceid];
		delete trackonLayervars['trackon' + deviceid];
		delete trackonSourcevars['trackon' + deviceid];
		$('#trackon'+deviceid).show();
		$('#trackoff'+deviceid).hide();
	}
	
	// track on device end
	// zoom to device start
	function zoomtodevice(deviceid) {
		$.ajax({
			url:BASEURL + "controlcentre/getfollowlocation",
			data:{deviceid:deviceid},
			dataType:'json',
			type:"POST",
			global:false
		}).done(function (resp) {			
			var respObj = resp.result;
			var dataLength = Object.keys(respObj).length;
			console.log("Stesalit starts");
			console.log(dataLength);
			console.log(respObj);
			console.log("Stesalit ends");
            if (dataLength > 0) {
                for (var i = 0;i < dataLength;i++) {
					map.getView().setCenter(ol.proj.transform([parseFloat(
                        respObj[i].longitude),parseFloat(respObj[i].latitude)],
                    'EPSG:4326','EPSG:3857'));
                }
            }
		}).fail(function () {
			console.log("Fetch error");
		}).complete(function () {
        });		
	}
	// zoom to device end
</script>

<?php if(($sessdata['group_id'] == 3) || ($sessdata['group_id'] == 6)){ ?>
<!--//Below script for footer slider up content show/hide-->
<script type='text/javascript'>
    var chartSeriesData = [];
	var alertid;
	var soslevel = 1;
	var sosdata;
	var alertlevel = 1;
	var alertdata;
	var calllevel = 1;
	var calldata;
	
	$(window).load(function () {
        $('#tabb1show').hide();
        $('#tabb2show').hide();
        $('#tabb3show').hide();
        $('#tabb4show').hide();
        $('#tabb5show').hide();
		$('#tabb6show').hide();

        $('.ta').click(function () {
            $('.tabs').hide();
            console.log("#" + $(this).attr("id") + "show");
            $("#" + $(this).attr("id") + "show").show();

            $('.ta').removeClass('activeft');
            $(this).addClass('activeft');
			if($(this).attr("id") == 'tabb1'){
				monthgraph('sos');
			}
			else if($(this).attr("id") == 'tabb2'){
				alertpie();
			}
			else if($(this).attr("id") == 'tabb3'){
				inventory();
			}
			else if($(this).attr("id") == 'tabb4'){
				status();
			}
			else if($(this).attr("id") == 'tabb5'){
				warranty();
			}
			else if($(this).attr("id") == 'tabb6'){
				monthgraph('call');
			}
			else if($(this).attr("id") == 'tabb7'){
				devicecountgraph();
			}
        });

        $('#taclose1').click(function () {
            $('.tabs').hide();
            $('.ta').removeClass('activeft');
        });
        $('#taclose2').click(function () {
            $('.tabs').hide();
            $('.ta').removeClass('activeft');
        });
        $('#taclose3').click(function () {
            $('.tabs').hide();
            $('.ta').removeClass('activeft');
        });
		$('#taclose4').click(function () {
            $('.tabs').hide();
            $('.ta').removeClass('activeft');
        });
		$('#taclose5').click(function () {
            $('.tabs').hide();
            $('.ta').removeClass('activeft');
        });
		$('#taclose6').click(function () {
            $('.tabs').hide();
            $('.ta').removeClass('activeft');
        });
		$('#taclose7').click(function () {
            $('.tabs').hide();
            $('.ta').removeClass('activeft');
        });
		
		// back functionality
		$('#sosback').click(function () {
            if(soslevel == 2){
				monthgraph('sos');
			}
			else if(soslevel == 3){
				dategraph('sos',sosdata);
			}
        });
		$('#alertback').click(function () {
            if(alertlevel == 2){
				alertpie();
			}
			else if(alertlevel == 3){
				monthgraph('alert');
			}
			else if(alertlevel == 4){
				dategraph('alert',alertdata);
			}
        });
		$('#statusback').click(function () {
            status();
        });		
		$('#warrentyback').click(function () {
            warranty();
        });
		$('#callback').click(function () {
            if(calllevel == 2){
				monthgraph('call');
			}
			else if(calllevel == 3){
				dategraph('call',calldata);
			}
        });

    });
	
	var highchartcolor = ['#2b908f', '#90ee7e', '#f45b5b', '#FFFF84', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'];
	Highcharts.setOptions({
		colors: highchartcolor
	});

	function alertpie() {
		alertlevel = 1;
		var url = '<?php echo site_url('controlcentre/alertPieData/'); ?>';
		$.ajax({
			type: "post",/*method type*/
			url:  url, 
			success: function(data) {
				//console.log(data);
				var dataJson = JSON.parse(data);
				var thtml = '<thead><tr><th>Alert Name</th><th>Count</th></tr></thead><tbody>';
				var colorpointer = 0;	
				for (var i = 0;i <dataJson.length; i++)
				{
					chartSeriesData.push({
						name: dataJson[i].name,
						y: parseFloat(dataJson[i].percentage),
						alertid: dataJson[i].alertid
					});
					thtml += '<tr>';
					thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+dataJson[i].name+'</td>';
					thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+parseInt(dataJson[i].val)+'</td>';
					thtml += '</tr>';
					colorpointer++;
						if(colorpointer == highchartcolor.length){
							colorpointer = 0;
						}
				}
				thtml += '</tbody>';
				$('#alerttb').html(thtml);
				Highcharts.chart('tabb2container',{
					chart:{
						plotBackgroundColor:null,
						plotBorderWidth:null,
						plotShadow:false,
						type:'pie',
						backgroundColor:false
					},
					credits: {
						enabled: false
					},
					title:{
						text:''
					},
					tooltip:{
						pointFormat:'{series.name}: <b>{point.percentage:.1f}%</b>'
					},
					plotOptions:{
						pie:{
							allowPointSelect:true,
							cursor:'pointer',
							dataLabels:{
								enabled:false
							},
							showInLegend:true
						},
						series: {
						  cursor: 'pointer',
						  point: {
							events: {
							  click: function (e) {					
								console.log(this.options.y);
								console.log(this.name);
								alertid = this.alertid;
								monthgraph('alert');								
							  }
							}
						  }
						}
					},
					legend:{
						itemStyle:{
							color:'#fff'
						},
						itemHoverStyle:{
							color:'#FFF'
						},
						itemHiddenStyle:{
							color:'#fff'
						}
					},
					series:[{
						name:'Brands',
						colorByPoint:true,
						data: chartSeriesData
					}]
				});
			}
		});
		$('#alertback').addClass("disabledbutton");
		chartSeriesData = [];
	}
	
	function inventory() {
		var url = '<?php echo site_url('controlcentre/inventoryData/'); ?>';
		var categorydata = [];
		$.ajax({
			type: "post",
			url:  url, 
			success: function(data) {
				var dataJson = JSON.parse(data);
				var thtml = '<thead><tr><th>Department</th><th>Stock</th><th>Use</th><th>Useage %</th></tr></thead><tbody>';
					
				for (var i = 0;i <dataJson.length; i++)
				{
					chartSeriesData.push(parseFloat(dataJson[i].usagepercentage));
					categorydata.push(dataJson[i].name);
					thtml += '<tr>';
					thtml += '<td>'+dataJson[i].name+'</td>';
					thtml += '<td>'+dataJson[i].stock+'</td>';
					thtml += '<td>'+dataJson[i].use+'</td>';
					thtml += '<td>'+parseInt(dataJson[i].usagepercentage)+'</td>';
					thtml += '</tr>';
				}
				thtml += '</tbody>';
				$('#inventorytb').html(thtml);
				Highcharts.chart('tabb3container', {
					chart: {
						type: 'column',
						backgroundColor:false,
						 options3d: {
							enabled: true,
							alpha: 10,
							beta: 15,
							depth: 80
						}
					},
					credits: {
						enabled: false
					},
					title: {
						text: ''
					},
					exporting: {
						enabled: false
					},
					subtitle: {
						text: ''
					},
					xAxis: {
						categories: categorydata,
						labels: {
							skew3d: true,
							style: {
								fontSize: '16px'
							}
						}
					},
					yAxis: {
						title: {
							text: null
						}
					},
					legend:{
						itemStyle:{
							color:'#fff'
						},
						itemHoverStyle:{
							color:'#FFF'
						},
						itemHiddenStyle:{
							color:'#fff'
						}
					},
					plotOptions: {
						series: {
							borderWidth: 0,
							dataLabels: {
								enabled: false,
								format: ''
							},
							cursor: 'pointer',
							point: {
								events: {
									click: function (e) {					
										console.log(this.category);
										console.log(this.options.y);
									}
								}
							}
						},
						column: {
							depth: 25
						}
					},
					series: [{
						name: 'Usage %',
						data: chartSeriesData
					}]
				});
			}
		});
		chartSeriesData = [];
	}
	
	function status() {
		var url = '<?php echo site_url('controlcentre/statusData/'); ?>';
		var categorydata = [];
		var iddata = [];
		var activeData = [];
		var inactiveData = [];
		$.ajax({
			type: "post",
			url:  url, 
			success: function(data) {
				var dataJson = JSON.parse(data);
				var thtml = '<thead><tr><th>Department</th><th>Use</th><th>Active</th><th>Inactive</th></tr></thead><tbody>';
					
				for (var i = 0;i <dataJson.length; i++)
				{
					activeData.push(dataJson[i].active);
					inactiveData.push(dataJson[i].inactive);
					categorydata.push(dataJson[i].name);
					iddata.push(dataJson[i].id);
					thtml += '<tr>';
					thtml += '<td>'+dataJson[i].name+'</td>';
					thtml += '<td>'+dataJson[i].use+'</td>';
					thtml += '<td>'+dataJson[i].active+'</td>';
					thtml += '<td>'+dataJson[i].inactive+'</td>';
					thtml += '</tr>';
				}
				thtml += '</tbody>';
				$('#statustb').html(thtml);
				Highcharts.chart('tabb4container', {
					chart: {
						type: 'column',
						backgroundColor:false
					},
					credits: {
						enabled: false
					},
					title: {
						text: ''
					},
					exporting: {
						enabled: false
					},
					subtitle: {
						text: ''
					},
					xAxis: {
						categories: categorydata						
					},
					yAxis: {
						min: 0,
						title: {
							text: ''
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: (Highcharts.theme && Highcharts.theme.textColor) || 'white'
							}
						}
					},
					legend: {
						align: 'right',
						x: -30,
						verticalAlign: 'top',
						y: 25,
						floating: true,
						backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
						borderColor: null,
						borderWidth: 1,
						shadow: false
					},
					tooltip: {
						headerFormat: '<b>{point.x}</b><br/>',
						pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							dataLabels: {
								enabled: true,
								color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
							}
						},
						series: {
							cursor: 'pointer',
							point: {
								events: {
									click: function (e) {					
										console.log(this.options.y);
										console.log(this.series.name);
										console.log(this.category);
										console.log(this.series.userOptions.id[this.x]);
										statussecondlevel(this.series.name,this.series.userOptions.id[this.x]);
									}
								}
							}
						}
					},
					series: [{
						name: 'Active',
						data: activeData,
						id: iddata
					}, {
						name: 'Inactive',
						data: inactiveData,
						id: iddata
					}]
				});
			}
		});
		chartSeriesData = [];
		$('#statusback').addClass("disabledbutton");
	}
	
	function statussecondlevel(devicetype, id) {
		var url = '<?php echo site_url('controlcentre/statussecondlevelData'); ?>';
		$.ajax({
			type: "post",
			url:  url+'/'+devicetype+'/'+id, 
			success: function(data) {
				var dataJson = JSON.parse(data);				
				if(devicetype == 'Active'){
					var thtml = '<thead><tr><th>Active Device</th><th>Name</th><th>Last Data</th></tr></thead><tbody>';
					var color = highchartcolor[0];
				}
				else if(devicetype == 'Inactive'){
					var thtml = '<thead><tr><th>Inactive Device</th><th>Name</th><th>Last Data</th></tr></thead><tbody>';
					var color = highchartcolor[1];
				}
					
				for (var i = 0;i <dataJson.length; i++)
				{
					thtml += '<tr>';
					thtml += '<td style="color:'+color+'">'+dataJson[i].device+'</td>';
					thtml += '<td style="color:'+color+'">'+dataJson[i].name+'</td>';
					thtml += '<td style="color:'+color+'">'+dataJson[i].lastdata+'</td>';
					thtml += '</tr>';
				}
				thtml += '</tbody>';
				$('#statustb').html(thtml);
				$('#statusback').removeClass("disabledbutton");
			}
		});
	}
	
	function warranty() {
		var url = '<?php echo site_url('controlcentre/warrantyData/'); ?>';
		var categorydata = [];
		var iddata = [];
		var expData = [];
		var expsoonData = [];
		$.ajax({
			type: "post",
			url:  url, 
			success: function(data) {
				var dataJson = JSON.parse(data);
				var thtml = '<thead><tr><th>Department</th><th>Expired</th><th>Expired Soon</th></tr></thead><tbody>';
					
				for (var i = 0;i <dataJson.length; i++)
				{
					expData.push(dataJson[i].exp);
					expsoonData.push(dataJson[i].expsoon);
					categorydata.push(dataJson[i].name);
					iddata.push(dataJson[i].id);
					thtml += '<tr>';
					thtml += '<td>'+dataJson[i].name+'</td>';
					thtml += '<td>'+dataJson[i].exp+'</td>';
					thtml += '<td>'+dataJson[i].expsoon+'</td>';
					thtml += '</tr>';
				}
				thtml += '</tbody>';
				$('#warrantytb').html(thtml);
				Highcharts.chart('tabb5container', {
					chart: {
						type: 'column',
						backgroundColor:false
					},
					credits: {
						enabled: false
					},
					title: {
						text: ''
					},
					exporting: {
						enabled: false
					},
					subtitle: {
						text: ''
					},
					xAxis: {
						categories: categorydata
					},
					yAxis: {
						min: 0,
						title: {
							text: ''
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: (Highcharts.theme && Highcharts.theme.textColor) || 'white'
							}
						}
					},
					legend: {
						align: 'right',
						x: -30,
						verticalAlign: 'top',
						y: 25,
						floating: true,
						backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
						borderColor: null,
						borderWidth: 1,
						shadow: false
					},
					tooltip: {
						headerFormat: '<b>{point.x}</b><br/>',
						pointFormat: '{series.name}: {point.y}'
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							dataLabels: {
								enabled: true,
								color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
							}
						},
						series: {
							cursor: 'pointer',
							point: {
								events: {
									click: function (e) {					
										console.log(this.options.y);
										console.log(this.series.name);
										console.log(this.category);
										console.log(this.series.userOptions.id[this.x]);
										if(this.series.name == 'Expired'){
											var devicetype = 'exp';
										}
										else if(this.series.name == 'Expired Soon'){
											var devicetype = 'expsoon';
										}
										warrantysecondlevel(devicetype,this.series.userOptions.id[this.x]);
									}
								}
							}
						}
					},
					series: [{
						name: 'Expired',
						data: expData,
						id: iddata
					}, {
						name: 'Expired Soon',
						data: expsoonData,
						id: iddata
					}]
				});
			}
		});
		chartSeriesData = [];
		$('#warrentyback').addClass("disabledbutton");
	}
	
	function warrantysecondlevel(devicetype, id) {
		var url = '<?php echo site_url('controlcentre/warrantysecondlevelData'); ?>';
		$.ajax({
			type: "post",
			url:  url+'/'+devicetype+'/'+id, 
			success: function(data) {
				var dataJson = JSON.parse(data);
				if(devicetype == 'exp'){
					var thtml = '<thead><tr><th>Device</th><th>Name</th><th>Expired</th></tr></thead><tbody>';
					var color = highchartcolor[0];
				}
				else if(devicetype == 'expsoon'){
					var thtml = '<thead><tr><th>Device</th><th>Name</th><th>Expired Soon</th></tr></thead><tbody>';
					var color = highchartcolor[1];
				}
					
				for (var i = 0;i <dataJson.length; i++)
				{
					thtml += '<tr>';
					thtml += '<td style="color:'+color+'">'+dataJson[i].device+'</td>';
					thtml += '<td style="color:'+color+'">'+dataJson[i].name+'</td>';
					thtml += '<td style="color:'+color+'">'+dataJson[i].expdate+'</td>';
					thtml += '</tr>';
				}
				thtml += '</tbody>';
				$('#warrantytb').html(thtml);
				$('#warrentyback').removeClass("disabledbutton");
			}
		});
	}
	
	function monthgraph(type) {
		if(type == 'sos'){
			var url = '<?php echo site_url('controlcentre/sosMnData/'); ?>';
			$.ajax({
				type: "post",/*method type*/
				url:  url, 
				success: function(data) {
					//console.log(data);
					var dataJson = JSON.parse(data);
					var thtml = '<thead><tr><th>Month</th><th>SOS Count</th></tr></thead><tbody>';
					var colorpointer = 0;	
					for (var i = 0;i <dataJson.length; i++)
					{
						
						chartSeriesData.push({
							name: dataJson[i].monthshow,
							y: parseInt(dataJson[i].val),
							val: dataJson[i].month
						});
						thtml += '<tr>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+dataJson[i].monthshow+'</td>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+parseInt(dataJson[i].val)+'</td>';
						thtml += '</tr>';
						colorpointer++;
						if(colorpointer == highchartcolor.length){
							colorpointer = 0;
						}
					}
					thtml += '</tbody>';
					$('#sostb').html(thtml);
					Highcharts.chart('tabb1container', {
						chart: {
							type: 'column',
							backgroundColor:false
						},
						credits: {
							enabled: false
						},
						title: {
							text: ''
						},
						exporting: {
							enabled: false
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							type: 'category',
							labels: {
								rotation: 0,
							}
						},
						yAxis: {
							title: {
								text: ''
							},
						min: 0
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							series: {
								borderWidth: 0,
								dataLabels: {
									enabled: false,
									format: ''
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function (e) {					
											console.log(this.options.val);
											dategraph('sos',this.options.val);
										}
									}
								}
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
							pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
						},

						series: [{
							name: 'SOS Alert',
							colorByPoint: true,
							data: chartSeriesData
						}]
					});
				}
			});
			soslevel = 1;
			$('#sosback').addClass("disabledbutton");
		}
		else if(type == 'alert'){
			alertlevel = 2;
			$('#alertback').removeClass("disabledbutton");
			var url = '<?php echo site_url('controlcentre/alertMnData/'); ?>';
			$.ajax({
				type: "post",/*method type*/
				url:  url+'/'+alertid, 
				success: function(data) {
					//console.log(data);
					var dataJson = JSON.parse(data);
					var thtml = '<thead><tr><th>Month</th><th>Count</th></tr></thead><tbody>';
					var colorpointer = 0;	
					for (var i = 0;i <dataJson.length; i++)
					{
						chartSeriesData.push({
							name: dataJson[i].monthshow,
							y: parseInt(dataJson[i].val),
							val: dataJson[i].month
						});
						thtml += '<tr>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+dataJson[i].monthshow+'</td>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+parseInt(dataJson[i].val)+'</td>';
						thtml += '</tr>';
						colorpointer++;
						if(colorpointer == highchartcolor.length){
							colorpointer = 0;
						}
					}
					thtml += '</tbody>';
					$('#alerttb').html(thtml);
					Highcharts.chart('tabb2container', {
						chart: {
							type: 'column',
							backgroundColor:false
						},
						credits: {
							enabled: false
						},
						title: {
							text: ''
						},
						exporting: {
							enabled: false
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							type: 'category',
							labels: {
								rotation: 0,
							}
						},
						yAxis: {
							title: {
								text: ''
							},
						min: 0
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							series: {
								borderWidth: 0,
								dataLabels: {
									enabled: false,
									format: ''
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function (e) {					
											console.log(this.options.val);
											dategraph('alert',this.options.val);
										}
									}
								}
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
							pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
						},

						series: [{
							name: 'Alert Count',
							colorByPoint: true,
							data: chartSeriesData
						}]
					});
				}
			});
		}
		else if(type == 'call'){
			var url = '<?php echo site_url('controlcentre/callMnData/'); ?>';
			$.ajax({
				type: "post",/*method type*/
				url:  url, 
				success: function(data) {
					//console.log(data);
					var dataJson = JSON.parse(data);
					var thtml = '<thead><tr><th>Month</th><th>Call Count</th></tr></thead><tbody>';
					var colorpointer = 0;	
					for (var i = 0;i <dataJson.length; i++)
					{
						
						chartSeriesData.push({
							name: dataJson[i].monthshow,
							y: parseInt(dataJson[i].val),
							val: dataJson[i].month
						});
						thtml += '<tr>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+dataJson[i].monthshow+'</td>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+parseInt(dataJson[i].val)+'</td>';
						thtml += '</tr>';
						colorpointer++;
						if(colorpointer == highchartcolor.length){
							colorpointer = 0;
						}
					}
					thtml += '</tbody>';
					$('#calltb').html(thtml);
					Highcharts.chart('tabb6container', {
						chart: {
							type: 'column',
							backgroundColor:false
						},
						credits: {
							enabled: false
						},
						title: {
							text: ''
						},
						exporting: {
							enabled: false
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							type: 'category',
							labels: {
								rotation: 0,
							}
						},
						yAxis: {
							title: {
								text: ''
							},
						min: 0
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							series: {
								borderWidth: 0,
								dataLabels: {
									enabled: false,
									format: ''
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function (e) {					
											console.log(this.options.val);
											dategraph('call',this.options.val);
										}
									}
								}
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
							pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
						},

						series: [{
							name: 'Call',
							colorByPoint: true,
							data: chartSeriesData
						}]
					});
				}
			});
			calllevel = 1;
			$('#callback').addClass("disabledbutton");
		}		
		chartSeriesData = [];
	}
	
	function dategraph(type, month) {
		if(type == 'sos'){
			soslevel = 2;
			sosdata = month;
			var url = '<?php echo site_url('controlcentre/sosDtData'); ?>';
			$.ajax({
				type: "post",/*method type*/
				url:  url+'/'+month, 
				success: function(data) {
					//console.log(data);
					var dataJson = JSON.parse(data);
					var thtml = '<thead><tr><th>Date</th><th>SOS Count</th></tr></thead><tbody>';
					var colorpointer = 0;
					for (var i = 0;i <dataJson.length; i++)
					{
						chartSeriesData.push({
							name: dataJson[i].date,
							y: parseInt(dataJson[i].val)                           
						});
						thtml += '<tr>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+dataJson[i].date+'</td>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+parseInt(dataJson[i].val)+'</td>';
						thtml += '</tr>';
						colorpointer++;
						if(colorpointer == highchartcolor.length){
							colorpointer = 0;
						}
					}
					thtml += '</tbody>';
					$('#sostb').html(thtml);
					Highcharts.chart('tabb1container', {
						chart: {
							type: 'column',
							backgroundColor:false
						},
						credits: {
							enabled: false
						},
						title: {
							text: ''
						},
						exporting: {
							enabled: false
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							type: 'category',
							labels: {
								rotation: -60,
							}
						},
						yAxis: {
							title: {
								text: ''
							},
						min: 0
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							series: {
								borderWidth: 0,
								dataLabels: {
									enabled: false,
									format: ''
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function (e) {					
											console.log(this.options.name);
											devicegraph('sos',this.options.name);
										}
									}
								}
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
							pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
						},

						series: [{
							name: 'SOS Alert',
							colorByPoint: true,
							data: chartSeriesData
						}]
					});
				}
			});
			$('#sosback').removeClass("disabledbutton");
		}
		else if(type == 'alert'){
			alertlevel = 3;
			alertdata = month;
			$('#alertback').removeClass("disabledbutton");
			var url = '<?php echo site_url('controlcentre/alertDtData'); ?>';
			$.ajax({
				type: "post",/*method type*/
				url:  url+'/'+month+'/'+alertid, 
				success: function(data) {
					//console.log(data);
					var dataJson = JSON.parse(data);
					var thtml = '<thead><tr><th>Date</th><th>Count</th></tr></thead><tbody>';
					var colorpointer = 0;
					for (var i = 0;i <dataJson.length; i++)
					{
						chartSeriesData.push({
							name: dataJson[i].date,
							y: parseInt(dataJson[i].val)                           
						});
						thtml += '<tr>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+dataJson[i].date+'</td>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+parseInt(dataJson[i].val)+'</td>';
						thtml += '</tr>';
						colorpointer++;
						if(colorpointer == highchartcolor.length){
							colorpointer = 0;
						}
					}
					thtml += '</tbody>';
					$('#alerttb').html(thtml);
					Highcharts.chart('tabb2container', {
						chart: {
							type: 'column',
							backgroundColor:false
						},
						credits: {
							enabled: false
						},
						title: {
							text: ''
						},
						exporting: {
							enabled: false
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							type: 'category',
							labels: {
								rotation: -60,
							}
						},
						yAxis: {
							title: {
								text: ''
							},
						min: 0
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							series: {
								borderWidth: 0,
								dataLabels: {
									enabled: false,
									format: ''
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function (e) {					
											console.log(this.options.name);
											devicegraph('alert',this.options.name);
										}
									}
								}
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
							pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
						},

						series: [{
							name: 'Alert Count',
							colorByPoint: true,
							data: chartSeriesData
						}]
					});
				}
			});
		}
		else if(type == 'call'){
			calllevel = 2;
			calldata = month;
			var url = '<?php echo site_url('controlcentre/callDtData'); ?>';
			$.ajax({
				type: "post",/*method type*/
				url:  url+'/'+month, 
				success: function(data) {
					//console.log(data);
					var dataJson = JSON.parse(data);
					var thtml = '<thead><tr><th>Date</th><th>Call Count</th></tr></thead><tbody>';
					var colorpointer = 0;
					for (var i = 0;i <dataJson.length; i++)
					{
						chartSeriesData.push({
							name: dataJson[i].date,
							y: parseInt(dataJson[i].val)                           
						});
						thtml += '<tr>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+dataJson[i].date+'</td>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+parseInt(dataJson[i].val)+'</td>';
						thtml += '</tr>';
						colorpointer++;
						if(colorpointer == highchartcolor.length){
							colorpointer = 0;
						}
					}
					thtml += '</tbody>';
					$('#calltb').html(thtml);
					Highcharts.chart('tabb6container', {
						chart: {
							type: 'column',
							backgroundColor:false
						},
						credits: {
							enabled: false
						},
						title: {
							text: ''
						},
						exporting: {
							enabled: false
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							type: 'category',
							labels: {
								rotation: -60,
							}
						},
						yAxis: {
							title: {
								text: ''
							},
						min: 0
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							series: {
								borderWidth: 0,
								dataLabels: {
									enabled: false,
									format: ''
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function (e) {					
											console.log(this.options.name);
											devicegraph('call',this.options.name);
										}
									}
								}
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
							pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
						},

						series: [{
							name: 'Call',
							colorByPoint: true,
							data: chartSeriesData
						}]
					});
				}
			});
			$('#callback').removeClass("disabledbutton");
		}
		chartSeriesData = [];
	}
	
	function devicegraph(type, date) {
		if(type == 'sos'){			
			soslevel = 3;
			var url = '<?php echo site_url('controlcentre/sosDvData'); ?>';
			$.ajax({
				type: "post",/*method type*/
				url:  url+'/'+date, 
				success: function(data) {
					//console.log(data);
					var dataJson = JSON.parse(data);
					var thtml = '<thead><tr><th>Device</th><th>SOS Count</th></tr></thead><tbody>';
					var colorpointer = 0;
					for (var i = 0;i <dataJson.length; i++)
					{
						chartSeriesData.push({
							name: dataJson[i].device,
							y: parseInt(dataJson[i].val)                           
						});
						thtml += '<tr>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+dataJson[i].device+'</td>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+parseInt(dataJson[i].val)+'</td>';
						thtml += '</tr>';
						colorpointer++;
						if(colorpointer == highchartcolor.length){
							colorpointer = 0;
						}
					}
					thtml += '</tbody>';
					$('#sostb').html(thtml);
					Highcharts.chart('tabb1container', {
						chart: {
							type: 'column',
							backgroundColor:false
						},
						credits: {
							enabled: false
						},
						title: {
							text: ''
						},
						exporting: {
							enabled: false
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							type: 'category',
							labels: {
								rotation: -60,
							}
						},
						yAxis: {
							title: {
								text: ''
							},
						min: 0
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							series: {
								borderWidth: 0,
								dataLabels: {
									enabled: false,
									format: ''
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function (e) {					
											console.log(this.options.name);
										}
									}
								}
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
							pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
						},

						series: [{
							name: 'SOS Alert',
							colorByPoint: true,
							data: chartSeriesData
						}]
					});
				}
			});			
			$('#sosback').removeClass("disabledbutton");
		}
		else if(type == 'alert'){			
			alertlevel = 4;
			$('#alertback').removeClass("disabledbutton");
			var url = '<?php echo site_url('controlcentre/alertDvData'); ?>';
			$.ajax({
				type: "post",/*method type*/
				url:  url+'/'+date+'/'+alertid, 
				success: function(data) {
					//console.log(data);
					var dataJson = JSON.parse(data);
					var thtml = '<thead><tr><th>Device</th><th>Count</th></tr></thead><tbody>';
					var colorpointer = 0;
					for (var i = 0;i <dataJson.length; i++)
					{
						chartSeriesData.push({
							name: dataJson[i].device,
							y: parseInt(dataJson[i].val)                           
						});
						thtml += '<tr>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+dataJson[i].device+'</td>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+parseInt(dataJson[i].val)+'</td>';
						thtml += '</tr>';
						colorpointer++;
						if(colorpointer == highchartcolor.length){
							colorpointer = 0;
						}
					}
					thtml += '</tbody>';
					$('#alerttb').html(thtml);
					Highcharts.chart('tabb2container', {
						chart: {
							type: 'column',
							backgroundColor:false
						},
						credits: {
							enabled: false
						},
						title: {
							text: ''
						},
						exporting: {
							enabled: false
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							type: 'category',
							labels: {
								rotation: -60,
							}
						},
						yAxis: {
							title: {
								text: ''
							},
						min: 0
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							series: {
								borderWidth: 0,
								dataLabels: {
									enabled: false,
									format: ''
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function (e) {					
											console.log(this.options.name);
										}
									}
								}
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
							pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
						},

						series: [{
							name: 'Alert Count',
							colorByPoint: true,
							data: chartSeriesData
						}]
					});
				}
			});
		}
		else if(type == 'call'){			
			calllevel = 3;
			var url = '<?php echo site_url('controlcentre/callDvData'); ?>';
			$.ajax({
				type: "post",/*method type*/
				url:  url+'/'+date, 
				success: function(data) {
					//console.log(data);
					var dataJson = JSON.parse(data);
					var thtml = '<thead><tr><th>Device</th><th>Call Count</th></tr></thead><tbody>';
					var colorpointer = 0;
					for (var i = 0;i <dataJson.length; i++)
					{
						chartSeriesData.push({
							name: dataJson[i].device,
							y: parseInt(dataJson[i].val)                           
						});
						thtml += '<tr>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+dataJson[i].device+'</td>';
						thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+parseInt(dataJson[i].val)+'</td>';
						thtml += '</tr>';
						colorpointer++;
						if(colorpointer == highchartcolor.length){
							colorpointer = 0;
						}
					}
					thtml += '</tbody>';
					$('#calltb').html(thtml);
					Highcharts.chart('tabb6container', {
						chart: {
							type: 'column',
							backgroundColor:false
						},
						credits: {
							enabled: false
						},
						title: {
							text: ''
						},
						exporting: {
							enabled: false
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							type: 'category',
							labels: {
								rotation: -60,
							}
						},
						yAxis: {
							title: {
								text: ''
							},
						min: 0
						},
						legend: {
							enabled: false
						},
						plotOptions: {
							series: {
								borderWidth: 0,
								dataLabels: {
									enabled: false,
									format: ''
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function (e) {					
											console.log(this.options.name);
										}
									}
								}
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
							pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
						},

						series: [{
							name: 'Call',
							colorByPoint: true,
							data: chartSeriesData
						}]
					});
				}
			});			
			$('#callback').removeClass("disabledbutton");
		}
		chartSeriesData = [];
	}
	
	function devicecountgraph(){
		var url = '<?php echo site_url('controlcentre/deviceCountData/'); ?>';
		$.ajax({
			type: "post",/*method type*/
			url:  url, 
			success: function(data) {
				//console.log(data);
				var dataJson = JSON.parse(data);
				var thtml = '<thead><tr><th>Title</th><th>Device Count</th></tr></thead><tbody>';
				var colorpointer = 0;	
				for (var i = 0;i <dataJson.length; i++)
				{
					
					chartSeriesData.push({
						name: dataJson[i].title,
						y: parseInt(dataJson[i].val),
						val: dataJson[i].val
					});
					thtml += '<tr>';
					thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+dataJson[i].title+'</td>';
					thtml += '<td style="color:'+highchartcolor[colorpointer]+'">'+parseInt(dataJson[i].val)+'</td>';
					thtml += '</tr>';
					colorpointer++;
					if(colorpointer == highchartcolor.length){
						colorpointer = 0;
					}
				}
				thtml += '</tbody>';
				$('#devicetb').html(thtml);
				Highcharts.chart('tabb7container', {
					chart: {
						type: 'column',
						backgroundColor:false
					},
					credits: {
						enabled: false
					},
					title: {
						text: ''
					},
					exporting: {
						enabled: false
					},
					subtitle: {
						text: ''
					},
					xAxis: {
						type: 'category',
						labels: {
							rotation: 0,
						}
					},
					yAxis: {
						title: {
							text: ''
						},
					min: 0
					},
					legend: {
						enabled: false
					},
					plotOptions: {
						series: {
							borderWidth: 0,
							dataLabels: {
								enabled: false,
								format: ''
							},
							cursor: 'pointer',
							point: {
								events: {
									click: function (e) {					
										console.log(this.options.val);
									}
								}
							}
						}
					},
					tooltip: {
						headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
						pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
					},

					series: [{
						name: 'Device',
						colorByPoint: true,
						data: chartSeriesData
					}]
				});
			}
		});
		chartSeriesData = [];
	}

	function addsudeptdevice(deptkey)
	{
		const checkbox = document.getElementById('myInputDeviceAll'+deptkey);
		if (checkbox.checked) {
			console.log('Checkbox is checked!');
		} else {
			console.log('Checkbox is not checked.');
		}
		//alert(deptkey);
		$.ajax({
			method: "POST",
            dataType: "json",
            url:BASEURL + 'controlcentre/getalldevicesudept',
			data: {deptkey: deptkey}
        }).done(function (result) {
			//alert(result.data);
			var data = result.data;
			var dataarr = data.split(',');
			for(var i=0;i < dataarr.length;i++)
			{
				if(dataarr[i] != '')
				{
					//alert(dataarr[i]);
					var deviceid = dataarr[i];
					if (checkbox.checked) {
						if (globalCheckedDevieIdForTracking.indexOf(deviceid) == -1) 
						{
							globalCheckedDevieIdForTracking.push(deviceid);
						}
						delete featurevars['trackon' + deviceid];
						delete latlonvars['trackon' + deviceid];
						$('#trackon'+deviceid).hide();
						$('#trackoff'+deviceid).hide();
						$('#trackon'+deviceid).show();
						$('#zoomto'+deviceid).show();
						delete featureHPvars['trackon' + deviceid];
						abortdataall();//alert(deviceid);
						//document.getElementById(deviceid).checked = true;
					} else {
						var index = globalCheckedDevieIdForTracking.indexOf(deviceid);
						if (index != -1) {
							globalCheckedDevieIdForTracking.splice(index,1);
							deleteMarkerById('track_'+deviceid,'track');
						}
						if(trackonLayervars['trackon' + deviceid]){
							trackonLayervars['trackon' + deviceid].getSource().clear();
							map.removeLayer(trackonLayervars['trackon' + deviceid]);
						}
						delete featurevars['trackon' + deviceid];
						delete latlonvars['trackon' + deviceid];
						$('#trackon'+deviceid).hide();
						$('#trackoff'+deviceid).hide();
						$('#zoomto'+deviceid).hide();
						abortdataall();
					}
				}
			}
			if (globalCheckedDevieIdForTracking.length > 0) 
			{
				fetchDataForTracking();
			}
		});
	}

/* --------------- New Script R --------------- */

function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

function waitForElementToExist(selector, callback) {
    const checkExist = setInterval(function () {
        if ($(selector).length) {
            clearInterval(checkExist);
            callback($(selector));
        }
    }, 1000);
}

$(document).ready(function () {
    const deviceId = getQueryParam('d');
    if (deviceId) {
        console.log('Looking for device checkbox with ID:', deviceId);
        waitForElementToExist(`#${deviceId}`, function($el) {
            console.log('Checkbox found:', $el);
            trackDevice(deviceId);
        });
    }
});

function trackDevice(deviceId) {
    const $deviceCheckbox = $(`#${deviceId}`);
    if ($deviceCheckbox.length) {
        console.log('Device Checkbox:', $deviceCheckbox);

        // Simulate user interaction
        $deviceCheckbox.trigger('click');

        // Confirm checkbox is now checked
        if ($deviceCheckbox.is(':checked')) {
            console.log('Device checkbox is checked.');
        }
    } else {
        console.warn(`Device with ID ${deviceId} not found.`);
    }
}

</script>
<?php } ?>
