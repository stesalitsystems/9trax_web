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
            $notification['msg'] = $this->session->flashdata('msg');
            if (!empty($notification['msg'])) {
                $this->load->view('listpagenotification', $notification);
            }
            ?>           
                <?php echo form_open("traxreport/sosstatusreport",array("autocomplete"=>"off","name"=>"frmsearch","onsubmit"=>"return submit_form();")) ?>
                    <div class="form-row">
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
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label>From Date<span class="rqurd">*</span></label>
							<input type="input" class="form-control dt" id="frdt" name="frdt" value="<?php echo $frdt; ?>" placeholder="From Date" readonly>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label>To Date<span class="rqurd">*</span></label>
							<input type="input" class="form-control dt" id="todt" name="todt" value="<?php echo $todt; ?>" placeholder="To Date" readonly>
						</div>
					</div>
					<div class="form-row mb-0">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<button type="reset" class="btn btn-danger" name="res" id="res">Reset</button>
							<button type="submit" class="btn btn-primary pull-right" name="search" id="search">Search</button>
							<?php if(($resolve_sos_count > 0) || ($unresolve_sos_count > 0)){ ?>
							<button type="button" class="btn btn-primary pull-right" name="pdfdownload" id="pdfdownload" style="margin-right: 0.5em;" onclick="return pdfSubmit();">Download PDF</button>
							<button type="button" class="btn btn-primary pull-right" name="print" id="print" style="margin-right: 0.5em;">Print</button>
							<!--<button type="button" class="btn btn-primary pull-right" name="exceldownload" id="exceldownload" style="margin-right: 0.5em;" onclick="return excelSubmit();">Download Excel</button>-->
							<?php } ?>
						</div>
					</div>                    
                <!-- </form> -->
            </div>
        </div>
    </div>

    <div style="height:30px;"></div>

    <div class="table-responsive">
		<table class="table table-bordered" id="reportlists" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th>Device</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Resolved SOS</th>
					<th>Unresolved SOS</th>
				</tr>
			</thead>
			<?php if (!empty($_POST) && isset($_POST['search'])) { ?>
			<tbody class="reportlists-body">
				<?php if(($resolve_sos_count > 0) || ($unresolve_sos_count > 0)){ ?>
				<tr>
					<td><?php echo $device_details->serial_no.'-'.$device_details->device_name; ?></td>
					<td><?php echo $frdt; ?></td>
					<td><?php echo $todt; ?></td>
					<td><?php echo $resolve_sos_count; ?></td>
					<td><?php echo $unresolve_sos_count; ?></td>
				</tr>
				
				<?php if(!empty($resolve_sos)){ ?>
				<tr>
					<th colspan="7">Resolved SOS Details</th>
				</tr>
				<tr>
					<th>Time</th>
					<th>Location</th>
					<th colspan="5">Link</th>
				</tr>
				<?php foreach($resolve_sos as $resolve_sos_each){
				?>
				<tr>
					<td><?php echo $resolve_sos_each->currentdate." ".$resolve_sos_each->currenttime; ?></td>
					<td><?php echo $resolve_sos_each->location; ?></td>
					<td colspan="5"><a href="<?php echo $resolve_sos_each->url; ?>" target="_blank"><?php echo $resolve_sos_each->url; ?></a></td>
				</tr>
				<?php } } ?>
				
				<?php if(!empty($unresolve_sos)){ ?>
				<tr>
					<th colspan="7">Unresolved SOS Details</th>
				</tr>
				<tr>
					<th>Time</th>
					<th>Location</th>
					<th colspan="5">Link</th>
				</tr>
				<?php foreach($unresolve_sos as $unresolve_sos_each){
				?>
				<tr>
					<td><?php echo $unresolve_sos_each->currentdate." ".$unresolve_sos_each->currenttime; ?></td>
					<td><?php echo $unresolve_sos_each->location; ?></td>
					<td colspan="5"><a href="<?php echo $unresolve_sos_each->url; ?>" target="_blank"><?php echo $unresolve_sos_each->url; ?></a></td>
				</tr>
				<?php } } ?>
				
				<?php } else { ?>
				<tr><td colspan="7">No Records Found</td></tr>
				<?php } ?>
			</tbody>
			<?php } else { ?>
			<tbody class="reportlists-body">
				<tr><td colspan="7">Search To Generate Report</td></tr>
			</tbody>
			<?php } ?>
		</table>
	</div>
</div>
<script>
    $(document).ready(function() {
		//$('#device_id').chosen();
		$("#res").on('click', function() {
			window.location.href = BASEURL + 'traxreport/sosstatusreport';
		});
		
		$( ".dt" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            //minDate: new Date('2016/09/01'),
            maxDate: '0'
        });
		
		$( "#print" ).on( "click", function() {
			$('#pdfdownload').hide();
			$('#search').hide();
			$('#res').hide();
			$('#print').hide();
			window.print();
			$('#pdfdownload').show();
			$('#search').show();
			$('#res').show();
			$('#print').show();
		});
	
	});
	
	function submit_form(){
		var device_id = $("#device_id").val();
		var frdt = $("#frdt").val();
		var todt = $("#todt").val();
		
		if(device_id == '')
		{
			alert('Please select device');
			return false;
		}
		if(frdt == '')
		{
			alert('Please enter from date');
			return false;
		}
		if(todt == '')
		{
			alert('Please enter to date');
			return false;
		}
		var frmdtarr = frdt.split('-');
		var startDay = new Date(frmdtarr[1]+'/'+frmdtarr[0]+'/'+frmdtarr[2]);
		var todtarr = todt.split('-');
		var endDay = new Date(todtarr[1]+'/'+todtarr[0]+'/'+todtarr[2]);
		var millisecondsPerDay = 1000 * 60 * 60 * 24;

		var millisBetween = endDay.getTime() - startDay.getTime();
		var days = millisBetween / millisecondsPerDay;

		if(Math.floor(days)<0){
			alert('Invalid date range');
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
        if($("#device_id").val()!="" && $("#frdt").val()!="" && $("#todt").val()!=""){
            submitFRM(BASEURL + 'traxreport/sosstatusreportpdf');
        }else{
           alert('All search fields are required');
		   return false;
        }
    }

</script>