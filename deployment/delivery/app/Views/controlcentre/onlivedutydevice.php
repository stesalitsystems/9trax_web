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
            <i class="fa fa-mobile"></i> On Live Duty Device (<?php echo $online;?>)
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
												<li class="col-lg-12 col-md-12 col-sm-12 col-xs-12">Loading..</li>
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

<script>
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

//google satelite layer push
bingLayers.push(new ol.layer.Tile({
type: 'base',
title: 'Google Satelite',
visible: true,
source: new ol.source.TileImage({
url: 'https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
attributions: [
new ol.Attribution({ html: '© Google' }),
new ol.Attribution({ html: '<a href="https://developers.google.com/maps/terms">Terms of Use.</a>' })
]
})
}));

// google osm layer push
bingLayers.push(new ol.layer.Tile({
type: 'base',
title: 'Google Streetmaps',
visible: false,
source: new ol.source.OSM({
url: 'http://mt{0-3}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
attributions: [
new ol.Attribution({ html: '© Google' }),
new ol.Attribution({ html: '<a href="https://developers.google.com/maps/terms">Terms of Use.</a>' })
]
})
}));

var lat = 23.2599;
var lon = 77.4126;

$(document).ready(function () {
	$.ajax({
		url:BASEURL + 'controlcentre/onlivedutydeviceload',
		type:"GET",
		global:false,
		dataType:"json"
	}).done(function (resp) {
		if (typeof resp.status !== 'undefined') {
			if (resp.result != '') {
				$("#devicediv").html(resp.result);
			}
		}
	}).fail(function () {
		console.log("Fetch error");
	}).complete(function () {
	});
});

// device search in right panel
function rightpaneldevicesearch(key) {
	var input, filter, ul, li, a, i;
	input = document.getElementById("myInputDeviceSearch"+key);
	filter = input.value.toUpperCase();
	ul = document.getElementById("myUL"+key);
	li = ul.getElementsByTagName("li");
	for (i = 0; i < li.length; i++) {
		if(li[i].getElementsByTagName("a").length > 0){
			a = li[i].getElementsByTagName("a")[1];
			if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
				li[i].style.display = "";
			} else {
				li[i].style.display = "none";

			}
		}
	}
}

function dropdowndevicesearch(key, val) {
	var input, filter, ul, li, a, i;
	input = val;
	filter = input.toUpperCase();
	ul = document.getElementById("myUL"+key);
	li = ul.getElementsByTagName("li");
	for (i = 0; i < li.length; i++) {
		if(li[i].getElementsByTagName("a").length > 0){
			a = li[i].getElementsByTagName("a")[1];
			if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
				li[i].style.display = "";
			} else {
				li[i].style.display = "none";

			}
		}
	}
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

var GEOSERVER_URL = '<?php echo GEOSERVER_URL; ?>';
var pollLayer = new ol.layer.Tile({                                                        
	title: 'Route',
	visible: true,
	source: new ol.source.TileWMS({
	  url: GEOSERVER_URL+'/personal_tracker/wms?service=WMS&version=1.1.0&request=GetMap',
	  params: {
			   'VERSION': '1.1.0',
			   tiled: true,
			   STYLES: '',
			   LAYERS: 'personal_tracker:master_polldata'
	  }
	})
});

function followdevice(deviceid) {
	$("#mydiv"+followdevid).remove();		
	if(mapvars['followmap' + followdevid]){
		delete mapvars['followmap' + followdevid];
		trackLayervars['followmap' + followdevid].getSource().clear();
		locateLayervars['followmap' + followdevid].getSource().clear();
		followajaxvars['followmap' + followdevid].abort();
		clearTimeout(fetchDataFollowTimeoutCallvars['followmap' + followdevid]);
	}
	$(".product-listing").append('<div id="mydiv'+followdevid+'" class="followdiv"><div id="mydiv'+followdevid+'header" class="followdivheader">Follow Device <a href="javascript:void(0);" style="float: right;color: #fff;" onclick="deletefollow('+followdevid+')"><i class="fa fa-times" aria-hidden="true" style="padding: 0px; line-height: 11px; margin: 0px 6px 0px 0px; overflow: hidden;"></i></a></div><div id="followmap'+followdevid+'" class="followmap"></div></div>');
	
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
	dragElement(document.getElementById(("mydiv"+followdevid)));
	
	//trail object start
	var followstartMarker = {};
	if(followtrackonLayervars['trackon' + followdevid]){
		followtrackonLayervars['trackon' + followdevid].getSource().clear();
		//map.removeLayer(followtrackonLayervars['trackon' + followdevid]);
		delete followfeaturevars['trackon' + followdevid];
		delete followlatlonvars['trackon' + followdevid];
		delete followtrackonLayervars['trackon' + followdevid];
		delete followtrackonSourcevars['trackon' + followdevid];
		followtrackonLayervarspole['trackon' + followdevid].getSource().clear();
		//map.removeLayer(followtrackonLayervarspole['trackon' + followdevid]);
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
	//poll layer add
	mapvars['followmap' + followdevid].addLayer(pollLayer);
	followlatlonvars['trackon' + followdevid] = [];
	followlatlonvarspole['trackon' + followdevid] = [];
	$.ajax({
		url:BASEURL + "controlcentre/getdevicetodaycoordinates",
		data:{deviceid:deviceid},
		dataType:'json',
		type:"POST",
		global:false
	}).done(function (resp) {
		var respObj = resp.getcoordinates;
		var dataLength = Object.keys(respObj).length;
		var respObjpole = resp.getpoledata;
		var dataLengthpole = Object.keys(respObjpole).length;
		var respObjpoleline = resp.getpolelinedata;
		var dataLengthpoleline = Object.keys(respObjpoleline).length;
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

				return styles;
			};
			// line style end
			
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
				trackSourcevars['followmap' + followdevid].addFeature(feature);
				mapvars['followmap' + followdevid].getView().setCenter(ol.proj.transform([parseFloat(
					respObj[i].longitude),parseFloat(respObj[i].latitude)],
				'EPSG:4326','EPSG:3857'));
				
				$("#mydiv"+followdevid+"header").html(deviceObj.serial_no+' ('+deviceObj.device_name+') <a href="javascript:void(0);" style="float: right;color: #fff;" onclick="deletefollow('+followdevid+')"><i class="fa fa-times" aria-hidden="true" style="padding: 0px; line-height: 11px; margin: 0px 6px 0px 0px; overflow: hidden;"></i></a>');
				
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
						
						return styles;
					};
					
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
</script>