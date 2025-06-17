<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.12.1/ol.css" type="text/css">
<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/animate.css">
<link href="<?php echo base_url() ?>assets/css/chosen.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ?>assets/css/ol3gm.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo base_url() ?>assets/js/BootSideMenu.js"></script>
<script src="<?php echo base_url() ?>assets/js/chosen.jquery.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.12.1/ol.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap-notify.min.js"></script>
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
		right: 22em;
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
		font-size: 12px;
	}
	#alerttab {
		font-size: 12px;
	}
	#sosstab {
		font-size: 12px;
	}
	
	.myInput {
		width: 20%;
		font-size: 11px;
		padding: 3px 3px 3px 3px;
		border: 1px solid #ddd;
		margin-bottom: 10px;
		margin-top: 10px;
	}
	
	.myInputSelect {
		width: 20%;
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
</style>
<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-mobile"></i> Device Listing (<?php echo $deviceListings[0]->organisation;?>) -- <?php echo $count ;?>
        </div>
        <div class="card-body">
            <div class="row product-listing">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="panel panel-default">
						<div class="panel-body nested-accordion">
							<div class="row mt-10">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="panel-group" id="devicediv">
										<div class="panel panel-default">
											<ul class="eventUL">
											<?php 
												foreach($deviceListings as $key=>$deviceListing){
													if($deviceListing->status_color!='') {
											?>
												<li class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><?php 
													echo $deviceListing->serial_no . ' - ' .$deviceListing->device_name; 
												?></li>
											<?php }} ?>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>