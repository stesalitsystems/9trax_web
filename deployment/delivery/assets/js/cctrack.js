
var map, measureControls;
var size = [];
var ZoomLevel = 8;
var VehicleMarkers = new Array();
var UPTSMarkers = new Array();
var wms;
var VehicleIds = new Array();
var markers;
var timecount = null;
var IconMovingUP = basePublicUrl + "images/bus.png";
var IconNotmovingUP = basePublicUrl + "images/green_red.png";
var IconMovingDOWN = basePublicUrl + "images/mar_green.png";
var IconNotmovingDOWN = basePublicUrl + "images/mar_red.png";
var IconDiversion = basePublicUrl + "images/blinking.gif";
var IconDiversionGreen = basePublicUrl + "images/blinking_green.gif";
var IconUP = basePublicUrl + "images/up.png";
var IconDOWN = basePublicUrl + "images/down.png";
var IconActive = basePublicUrl + "images/active.png";
var Icondepot = basePublicUrl + "images/depots.png";
var Iconupts = basePublicUrl + "images/uptss.png";
var Iconcluster = basePublicUrl + "images/sub.png";
var proj4326 = 'EPSG:4326';
var proj900913 = 'EPSG:900913';
var LayersData = new Array();
var curUPTS = null;
var globalVehicle = '';
var globalParam = '';
var styles = ['OSM','Aerial','AerialWithLabels','Road','Bhuvan'];  
 var layers = [];
/*
 * UPTS Lattitude and Longitude
 */
var uptsLongLat = new Array();
// For All UPTS (Chattishgarh)
uptsLongLat['all'] = '81.8661,21.2787';
// For Bilaspur
uptsLongLat[10] = '82.1391,22.0796';
// For Bastar
uptsLongLat[15] = '81.9535,19.1071';
// For Durg-Bhilai
uptsLongLat[8] = '81.31788,21.19770';
// For Korba
uptsLongLat[11] = '82.66886,22.37500';
// For Koriya
uptsLongLat[13] = '82.33498,23.19528';
// For Raipur
uptsLongLat[7] = '81.63074,21.23842';
// For Raigarh
uptsLongLat[12] = '83.3950,21.8974';
// For Rajnandgaon
uptsLongLat[9] = '81.0302,21.0971';
// For Sarguja
uptsLongLat[14] = '83.19285,23.12276';

/*
 * End UPTS Lattitude and Longitude
 */

/*
 * Function to initialize map
 * Author - Shivaji
 * Date - 21/07/2016
 */
function initialize(uptsid)
{  
    var osmLayer = new ol.layer.Tile({
         source: new ol.source.OSM(),
         visible: true
    });
    layers.push(osmLayer);
    var i, ii;
    for (i = 1, ii = styles.length-1; i < ii; ++i) {
      layers.push(new ol.layer.Tile({
        visible: false,
        preload: Infinity,
        source: new ol.source.BingMaps({
          key: 'AiRWdWuzR_KVYwYjKRF96X09xA3DEhO_bPDdol4UC7nmE9D6PTGFrXWMpYG2RFnd',
          imagerySet: styles[i]
          // use maxZoom 19 to see stretched tiles instead of the BingMaps
          // "no photos at this zoom level" tiles
          // maxZoom: 19
        })
      }));
    }
	
	 bhuvan =   new ol.layer.Image({
					visible: false,
					preload: Infinity,
					source: new ol.source.ImageWMS({
					url: 'http://bhuvan3.nrsc.gov.in/cgi-bin/LULC250K.exe',
					params: {'LAYERS': 'LULC250K_1314'},
					ratio: 1,
					serverType: 'geoserver',
					projection: 'EPSG:4326'
    		})
 		 });
	 
	 layers.push(bhuvan);

    map = new ol.Map({
        layers: layers,
        target: 'map',
        loadTilesWhileAnimating: true,
        view: new ol.View({
            projection: proj900913,
            zoom: ZoomLevel
        })
    });
    mapCenter(uptsLongLat['all']);
    //map.addControl(new ol.control.FullScreen());
    map.addControl(new ol.control.ScaleLine());
    map.addControl(new ol.control.Zoom());
    map.addControl(new ol.control.ZoomSlider());
    map.addControl(new ol.control.Attribution());
    map.addControl(new ol.control.OverviewMap());
   // map.addControl(new ol.control.ZoomToExtent());
    var mousePositionControl = new ol.control.MousePosition({	
                                        coordinateFormat: ol.coordinate.createStringXY(5),
        				projection: proj4326
                                });
    map.addControl(mousePositionControl);
   
    addLayerToMap(10,'route');
    addLayerToMap(10,'stoppage');
    addLayerToMap(11,'route');
    addLayerToMap(11,'stoppage');
    addLayerToMap(7,'route');
    addLayerToMap(7,'stoppage');
    addLayerToMap(8,'route');
    addLayerToMap(8,'stoppage');
    addLayerToMap(9,'route');
    addLayerToMap(9,'stoppage');
    addLayerToMap(13,'route');
    addLayerToMap(13,'stoppage');
    addLayerToMap(14,'route');
    addLayerToMap(14,'stoppage');
    addLayerToMap(12,'route');
    addLayerToMap(12,'stoppage');
    addLayerToMap(15,'route');
    addLayerToMap(15,'stoppage');
    loadmap(uptsid);
      
}
var select = document.getElementById('layer-select');
function onChange() {
  var style = select.value;
  for (var i = 0, ii = layers.length; i < ii; ++i) {
      if(styles[i] === style)
		{
		console.log(styles[i]);
		}
    layers[i].setVisible(styles[i] === style);
  }
}
select.addEventListener('change', onChange);

// function to add layer to map
function addLayerToMap(workspace,layer)
{   
    var wms = new ol.layer.Tile({
	source: new ol.source.TileWMS({
		url: mapLayerDefinition[workspace].url,
		params: {
			'LAYERS': mapLayerDefinition[workspace][layer].layerName,
			'style': mapLayerDefinition[workspace][layer].layerLabelStyle
		}
	})
    });
    map.addLayer(wms);
    LayersData.push(wms);
}

// This function removes all layers from map
function removeAllLayers()
{
    for(var l=0 ; l< LayersData.length ; l++)
    {
        map.removeLayer(LayersData[l]);
        //console.log('removing layers for'+l);
    }
}

function makeMarker()
{
    var x = document.createElement("IMG");
    x.setAttribute('src', IconMovingUP);
    x.setAttribute('class', 'location-popover');

    $(x).popover({'placement': 'left', 'html': true, 'content': '<div >Tooltip1</div>', show: true})
            .tooltip({'placement': 'bottom', 'html': true, 'title': '<div >testing</div>', show: true})
            .on('click', function (e) {
                $(".location-popover").not(this).popover('hide');
            })
            ;
    return x;
}
function addmarker(Lonlat)
{

    var x = document.createElement("IMG");
    x.setAttribute('src', IconMovingUP);
    var maku = new ol.Overlay({
        position: Lonlat,
        element: makeMarker()
    });

    map.addOverlay(maku);
    $('.location-popover').tooltip('show');

}

/*
 * Function to load map  
 * Author - Shivaji
 * Date - 21/07/2016
 */

function loadmap(uptsid)
{
    document.getElementById("loadergif").style.display = 'block';
    $("ul li").removeClass('active');
    var d = document.getElementById("uptsid" + uptsid);
    d.className += "active";
    removeAllLayers();
    removeAllMarker();
    removeAllUPTSMarker();
    VehicleIds = new Array();
    if(uptsid != 'all')
    {
        ZoomLevel = 12;
        getUPTSLatLong(uptsid);
        getClusterLatLong(uptsid);
        getDepotLatLong(uptsid);
        mapCenter(uptsLongLat[uptsid]);
        addLayerToMap(uptsid,'route');
        addLayerToMap(uptsid,'stoppage');
		// for text broadcaste
		jQuery("#uptsid").val(uptsid);
		jQuery("#depotid").html("<option value=''>loading..</option>");
		// get depot	
		jQuery.ajax(
		{
		   url: baseUrl + 'vts/index.php?c=cctrack&m=getdepot',
			type: "POST",
			data: {upts:uptsid},
		 //    datatype: "json",
			success: function (result)
			{
			   if(result!=''){
					$("#depotid").html("<option value=''>--All--</option>"+result);

				}else{
					$("#depotid").html("<option value=''>--All--</option>");
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				
			}
		});
    }
    else 
    {
        ZoomLevel = 8;
        addLayerToMap(10,'route');
        addLayerToMap(10,'stoppage');
        addLayerToMap(11,'route');
        addLayerToMap(11,'stoppage');
        addLayerToMap(7,'route');
        addLayerToMap(7,'stoppage');
        addLayerToMap(8,'route');
        addLayerToMap(8,'stoppage');
        addLayerToMap(9,'route');
        addLayerToMap(9,'stoppage');
        addLayerToMap(13,'route');
        addLayerToMap(13,'stoppage');
        addLayerToMap(14,'route');
        addLayerToMap(14,'stoppage');
        addLayerToMap(12,'route');
        addLayerToMap(12,'stoppage');
        addLayerToMap(15,'route');
        addLayerToMap(15,'stoppage');
        mapCenter(uptsLongLat['all']);
        getUPTSLatLong('all');
        getClusterLatLong('all');
        getDepotLatLong('all');
    }
    getActiveVehiclesupts(uptsid);
}
/*
function getActiveVehiclesupts(uptsid)
{
    curUPTS = uptsid;
    if (uptsid != "")
    {   
       
       $.ajax(
            {
                url: baseUrl + 'vts/index.php?c=cctrack&m=getvehiclebyupts',
                type: "POST",
                data: {uptsid: uptsid},
                datatype: "json",
                async: true,
                success: function (data)
                {
                    var activejsonvehicle = JSON.parse(data);
                    if (activejsonvehicle.ActiveVehicle == 1)
                    {
                        addAllVehicle(activejsonvehicle.VehicleList)
						$(".transbox #countspan").html(activejsonvehicle.VehicleList.length);
                    }
                    else
                    {
                        alert('No Active Vehicles!!!');
                        document.getElementById("loadergif").style.display = 'none';
						$(".transbox #countspan").html(0);
                    }
               },
                error: function (jqXHR, textStatus, errorThrown)
                {
                   console.log("Something Went Wrong!!!");
                }
            });
        }
        //DispAllAlertBDivTimeout(uptsid);
        //console.log('hhh'+curUPTS);
        setTimeout(function () {
              getActiveVehiclesupts(curUPTS)
          }, 900000);

    }*/
	
 function getActiveVehiclesupts(uptsid){
    curUPTS = uptsid;
    if (uptsid != "")
    {   
       
       $.ajax(
            {
                url: baseUrl + 'vts/index.php?c=cctrack&m=getvehiclebyupts',
                type: "POST",
                data: {uptsid: uptsid},
                datatype: "json",
                async: true,
                beforeSend: function(){
                   $("#inactiveCount").html('Loading...'); 
                   $("#inactiveVehicleListShow").html('').html('Loading...');  
                },
                success: function (data)
                {
                        var activejsonvehicle = JSON.parse(data);
                        if (activejsonvehicle.ActiveVehicle == 1)
                        {
                            addAllVehicle(activejsonvehicle.VehicleList)
                            $(".transbox #countspan").html(activejsonvehicle.VehicleList.length);
                        } else
                        {
                            alert('No Active Vehicles!!!');
                            document.getElementById("loadergif").style.display = 'none';
                            $(".transbox #countspan").html(0);
                        }
                        if (activejsonvehicle.InActiveVehicle == 1){
                            var totalInactive = activejsonvehicle.InActiveVehicleList.length;
                            var appendInactiveList = '';
                            for(i=0;i<totalInactive;i++){
                                 appendInactiveList +='<div class="breakDiv" id="vid_'+activejsonvehicle.InActiveVehicleList[i].vehicleid+'" onclick="return getVehicleInactiveDetails('+activejsonvehicle.InActiveVehicleList[i].vehicleid+');">'+activejsonvehicle.InActiveVehicleList[i].vehicleno+'</div>';
                            } 
                            $("#inactiveCount").html('('+totalInactive+')');
                            $("#inactiveVehicleListShow").html(appendInactiveList);
                        }else{
                            var totalInactive = 0;
                            $("#inactiveCount").html('('+totalInactive+')');
                            $("#inactiveVehicleListShow").html('No Inactive Vehicles');
                        }
               },
                error: function (jqXHR, textStatus, errorThrown)
                {
                   console.log("Something Went Wrong!!!");
                }
            });
        }
        //DispAllAlertBDivTimeout(uptsid);
        //console.log('hhh'+curUPTS);
        setTimeout(function () {
              getActiveVehiclesupts(curUPTS)
          }, 900000);

    }

/*
 * Function to add vehicles on map
 * Author - Shivaji
 * Date - 21/07/2016
 */

function addAllVehicle(vid) {
   // document.getElementById("myMarquee").style.display = 'block';
    var len = vid.length;
    var icnt = 0;
    for (icnt = 0; icnt < len; icnt++) {
        var vehicleid = vid[icnt].vehicleid;
        if (vehicleid != "" && inArray(VehicleIds, vehicleid) == false) {
            VehicleIds.push(vehicleid);
        }
    }
    PlotPositionData();
}

/*
 * End Load Map
 */

/*
 * Function to set map center
 * Author - Shivaji
 * Date - 21/07/2016
 */

function mapCenter(longlat) {
    longlat = longlat.split(",");
    var long = parseFloat(longlat[0]);
    var lat = parseFloat(longlat[1]);
   // console.log("Long: " + longlat);
    map.getView().setCenter(ol.proj.transform([long, lat], proj4326, proj900913));
    map.getView().setZoom(ZoomLevel);
}

/*
 * Function to get positional data
 * Author - Shivaji
 * Date - 21/07/2016
 */
function PlotPositionData()
{
    if(VehicleIds.length > 0 )
    {
        var allvehicle = VehicleIds.join();
        $('.location-popover').tooltip('show');
        $.ajax(
                {
                    url: baseUrl + 'vts/index.php?c=cctrack&m=getPositionalData',
                    type: "POST",
                    data: {vechileId: allvehicle},
                    datatype: "json",
                    async: true,
                    success: function (data)
                    {
                        var allposdata = JSON.parse(data);
                        if (allposdata.DataFound == 1)
                        {
                            var vehicleposdata = allposdata.PosData;
                            for (var r = 0; r < vehicleposdata.length; r++)
                            {
                                var vehicle_new_id = vehicleposdata[r].vehicleid;

                                if (vehicle_new_id != '')
                                {
                                    
                                    var vehicle_number = vehicleposdata[r].vehicleno;
									 var vehicleTypeID = vehicleposdata[r].bustypeid; // added on 10022017
                                    var long = parseFloat(vehicleposdata[r].lon);
                                    var lat = parseFloat(vehicleposdata[r].lat);
                                    var lonLat = ol.proj.transform([long, lat], proj4326, proj900913);
                                    if (vehicle_new_id != "" && getVehicleMarker(vehicle_new_id) == true) {
                                      var existingmarker =  getExistingMarker(vehicle_new_id);
                                      //var lastangle = getExistingAngle(vehicle_new_id);
                                      moveMarker(existingmarker,lonLat,vehicle_number,vehicle_new_id);
                                    }
                                    else 
                                    {
                                        createMarkerForVehicle(lonLat, vehicle_number, vehicle_new_id,vehicleTypeID); //vehicleTypeID added on 10022017
                                    }

                                }

                            }

                            //remove loader message
                            document.getElementById("loadergif").style.display = 'none';
                            // trigger tooltip
                             $('.location-popover').tooltip('show');
                            if (timecount == null) {
                                timecount = setInterval("PlotPositionData()", 8000);

                            }
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                       console.log("Something Went Wrong!!!");
                    }
                });
    }        

}

/*
 * Function to add marker on map
 * Author - Shivaji
 * Date - 21/07/2016
 */

function  createMarkerForVehicle(lonLat, vehicle_number, vehicle_new_id,vehicleTypeid)
{
	/****/
    var ImgSrc = basePublicUrl + "images/";
    if(vehicleTypeid == 1){//Midi
        ImgSrc = ImgSrc + "Bus_12.png"
    }
    if(vehicleTypeid == 2){//AC Midi
         ImgSrc = ImgSrc + "Bus_15.png"
    }
    if(vehicleTypeid == 3){//Std
         ImgSrc = ImgSrc + "Bus_16.png"
    }
    if(vehicleTypeid == 4){//AC Std
         ImgSrc = ImgSrc + "Bus_09.png"
    }
    /*****/
	
    var markerimage = document.createElement("IMG");
     //  markerimage.setAttribute('src', IconMovingUP); Commented for different types of Bus Logo based on bustypeid
      markerimage.setAttribute('src', ImgSrc); 
	  markerimage.setAttribute('width', 20);
      markerimage.setAttribute('height', 52);
    markerimage.setAttribute('class', 'location-popover');
    $(markerimage).tooltip({
        'placement': 'bottom',
        'html': true,
        'title': '<strong>' + vehicle_number + '</strong>'
    })
        .on('click', function (e) {
            getVehicleDetails( vehicle_new_id);
    });


    var marker = new ol.Overlay({
        position: lonLat,
        positioning: 'center-center',
        offset: [0, 0],
        element: markerimage
    });
    map.addOverlay(marker);
    var MarkersData = new Array();
    MarkersData['vehicleid'] = vehicle_new_id;
    MarkersData['marker'] = marker;
    MarkersData['angle'] = 360;
    VehicleMarkers.push(MarkersData);
    return marker;
}


/*function getVehicleDetails(vehicleid)
{
   document.getElementById("loadergif").style.display = 'block'; 
   $.ajax(
           {
               url: baseUrl + 'vts/index.php?c=cctrack&m=getVehicleDetails',
               type: "POST",
               data: {vehicleid: vehicleid},
               datatype: "json",
               async: true,
               success: function (data)
               {
                   document.getElementById("loadergif").style.display = 'none';
                   $("#vcontent").html(data);
                   $("#vmodal").modal('show');
                   getCompleteVehicleData(vehicleid);
                   getvhmdFuelGraph(vehicleid);
                   getgpsrateGraph(vehicleid);
                   getvhmdrateGraph(vehicleid);
                   
               },
               error: function (jqXHR, textStatus, errorThrown)
               {
                  console.log("Something Went Wrong!!!");
               }
           });
}*/

function get_vehicle_ETA(vehicleid) {
	var loadingImg = baseUrl+'public/images/ajax-loader-red.gif'
	var loadingHtml = "<img src='"+loadingImg+"' alt='Loading'/>"
    $.ajax({
                url: baseUrl + 'vts/index.php?c=cctrack&m=getCompleteVehicleDataETA',
                type: "POST",
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                dataType: 'json',
                data: {vehicleid: vehicleid},
                async: true,
                beforeSend: function () {
                     $("#routeno").html('').html('<span>Current Route No : </span>  <p>'+loadingHtml+'</p>');
					 $("#routename").html('').html('<span>Present Route Direction : </span>  <p>'+loadingHtml+'</p>');
					  $("#starttime").html('').html('<span>Departed from : </span>  <p>' +loadingHtml+'</p>');
					   $("#timeofarrival").html('').html('<span>ETA at Destination : </span>  <p>'+loadingHtml+'</p>');
					   $("#upcomingstoppage").html('').html('<span>Upcoming Stop : </span>  <p>' +loadingHtml+'</p>');
					 $("#driver").html('').html('<span>Driver Name : </span>  <p>'  +loadingHtml+'</p>');
					 $("#conductor").html('').html('<span>Conductor Name : </span>  <p>' + loadingHtml+'</p>');
                    //  $("#tripnos").after("<tr><td colspan='2' style='color:#fff'>Loading...</td></tr>");
                },
                success: function (data)
                {
                    $("#routeno").html('');
                    var alldata = data;
                    var etadetail = alldata.etadetail;
                    etadetail.routeno !== null ? $("#routeno").html('<span>Current Route No : </span>  <p>' + etadetail.routeno + '</p>') : $("#routeno").html('<span>Current Route No : </span>  <p>NA</p>');
                    etadetail.routename !== null ? $("#routename").html('<span>Present Route Direction : </span>  <p>' + etadetail.routename + '</p>') : $("#routename").html('<span>Present Route Direction : </span>  <p>NA</p>');
                    etadetail.starttime !== null ? $("#starttime").html('<span>Departed from : </span>  <p>' + etadetail.starttime + '</p>') : $("#starttime").html('<span>Departed from : </span>  <p>NA</p>');
                    etadetail.timeofarrival !== null ? $("#timeofarrival").html('<span>ETA at Destination : </span>  <p>' + etadetail.timeofarrival + '</p>') : $("#timeofarrival").html('<span>ETA at Destination : </span>  <p>NA</p>');
                    etadetail.upcomingstoppage !== null ? $("#upcomingstoppage").html('<span>Upcoming Stop : </span>  <p>' + etadetail.upcomingstoppage + '</p>') : $("#upcomingstoppage").html('<span>Upcoming Stop : </span>  <p>NA</p>');
                    etadetail.driver !== null ? $("#driver").html('<span>Driver Name : </span>  <p>' + etadetail.driver + '</p>') : $("#driver").html('<span>Driver Name : </span>  <p>NA</p>');
                    etadetail.conductor !== null ? $("#conductor").html('<span>Conductor Name : </span>  <p>' + etadetail.conductor + '</p>') : $("#conductor").html('<span>Conductor Name : </span>  <p>NA</p>');
                    var departedfrom = 'Departed From';
                    if (etadetail.routename !== null)
                    {
                        var rdetail = etadetail.routename;
                        var dfrom = rdetail.split('-');
                        departedfrom = departedfrom + ' ' + dfrom[0] + ' at';
                    }

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log("Something Went Wrong!!!!!!!");
                }
            });

}

function getVehicleDetails(vehicleid)
{
   document.getElementById("loadergif").style.display = 'block'; 
   $.ajax(
           {
               url: baseUrl + 'vts/index.php?c=cctrack&m=getVehicleDetails',
               type: "POST",
               data: {vehicleid: vehicleid},
               datatype: "json",
               async: true,
               success: function (data)
               {
                   var data = JSON.parse(data);
				 //  get_vehicle_ETA(vehicleid); uncomment when ETA SP is running fast
				   globalVehicle = data.vehicleData.vehicleid;
				   $(".veh_number").html(data.vehicleData.vehicleno);
				   $("#bus_type").html('<span>Bus Type : </span>	        <p>'+data.vehicleData.bustype+'</p>');
				   $("#scu_maker").html('<span>SCU Maker : </span> 	        <p>'+data.vehicleData.scucompanyname+'</p>');
				   $("#scu_no").html('<span>SCU No : </span>	                 <p>'+data.vehicleData.vtuno+'</p>');
				   $("#gsm_no").html('<span>GSM No : </span>	        <p onclick="return callivrs('+data.vehicleData.gsmno+');"><a href="javascript:void(0)" data-rel="external" style="margin:0;">'+data.vehicleData.gsmno+'</a>  <a style="text-align:right;position:absolute;top:7px;right:50px;margin:0;" href="javascript:void(0)" data-rel="external"><img src="public/icon/phone16x16.png" style="height: 12px; margin: -5px 0 0 0px;"></a></p>');
				   $("#eng_cap").html('<span>Engine Capacity : </span>      <p>'+data.vehicleData.enginecapacity+'</p>');
				   $("#st").html('<span>Seats : </span> 	                 <p>'+data.vehicleData.totalseats+'</p>');
				   $("#no_camera").html('<span>No. Of Camera : </span>        <p>2</p>');
				   $("#veh_maker").html('<span>Vehicle Maker : </span>         <p>'+data.vehicleData.vehiclecompany+'</p>');
				   $("#make_dt").html('<span>Make Date : </span>	        <p>'+data.vehicleData.vmakedate+'</p>');
				   getCompleteVehicleData(globalVehicle);
				   document.getElementById("loadergif").style.display = 'none';
				   $("#vhmddetailmodalheading").html('<h3>'+data.vehicleData.vehicleno+' : Vehicle Health Information</h3>');
                   //$("#vcontent").html(data);
                   $("#vmodal").modal('show');
                   
               },
               error: function (jqXHR, textStatus, errorThrown)
               {
                  console.log("Something Went Wrong!!!");
               }
           });
}

/*function getCompleteVehicleData(vehicleid)
{
   $.ajax(
           {
               url: baseUrl + 'vts/index.php?c=cctrack&m=getCompleteVehicleData',
               type: "POST",
               data: {vehicleid: vehicleid},
               datatype: "json",
               async: true,
               success: function (data)
               {
                    var alldata = JSON.parse(data);
                    var posdata = alldata.posdetail;
                    var etadetail = alldata.etadetail; 
                    var speedcount = alldata.speedcount;
                    var tripcount = alldata.tripcount; 
                    var alerthtml = alldata.alerthtml; 
                    $("#lastgps").html(posdata.curtime+'&nbsp;' + posdata.currentdate);
                    $("#totalposdata").html(posdata.totaldata);
                    $("#alerthtml").html(alerthtml);
                    
                    etadetail.routeno !== null ? $("#routeno").html( etadetail.routeno) : $("#routeno").html('NA');
                    etadetail.routename !== null ? $("#routename").html( etadetail.routename) : $("#routename").html('NA');
                    etadetail.starttime !== null ? $("#starttime").html( etadetail.starttime) : $("#starttime").html('NA');
                    etadetail.timeofarrival !== null ? $("#timeofarrival").html( etadetail.timeofarrival) : $("#timeofarrival").html('NA');
                    etadetail.upcomingstoppage !== null ? $("#upcomingstoppage").html( etadetail.upcomingstoppage) : $("#upcomingstoppage").html('NA');
                    etadetail.driver !== null ? $("#driver").html( etadetail.driver) : $("#driver").html('NA');
                    etadetail.conductor !== null ? $("#conductor").html( etadetail.conductor) : $("#conductor").html('NA');
                    var departedfrom = 'Departed From';
                    if( etadetail.routename !== null)
                    {
                        var rdetail = etadetail.routename;
                        var dfrom =  rdetail.split('-');
                        departedfrom = departedfrom + ' ' +dfrom[0]+' at';
                    }
                    $("#departedfrom").html(departedfrom);
                    $("#tripscompleted").html(tripcount.tripcount);
                    $("#vspeed").html(speedcount.speeddata);
                    
               },
               error: function (jqXHR, textStatus, errorThrown)
               {
                  console.log("Something Went Wrong!!!");
               }
           });
}*/

function getCompleteVehicleData(vehicleid){
	var loadingImg = baseUrl+'public/images/ajax-loader-red.gif';
	var loadingHtml = "<img src='"+loadingImg+"' alt='Loading'/>";
   $.ajax(
           {
               url: baseUrl + 'vts/index.php?c=cctrack&m=getCompleteVehicleData',
               type: "POST",
               dataType: 'json',              
               data: {vehicleid: vehicleid},               
               async: true,
               beforeSend: function(){
                    $("#tripnos").nextAll().remove();
                    $("#tripnos").after("<tr><td colspan='2' style='color:#fff'>"+loadingHtml+"</td></tr>");
               },
               success: function (data)
               {
                   var alldata = data;
                   var posdata = alldata.posdetail;
                 // console.log(alldata);
                   var UpcomingLength = 0;
                   var CurrentLength = 0;
                    var speedcount = 0;// alldata.speedcount;
                    var tripcount = alldata.tripcount; 
                    var alerthtml = alldata.alerthtml;
                    var trip_details_upcoming = alldata.trip_details_upcoming;
                    var trip_details_running = alldata.trip_details_running;
                    var trip_details_completed = alldata.trip_details_completed;  
                    $("#tripnos").nextAll().remove(); 
                   
                    //$("#departedfrom").html(departedfrom);
                    $("#tripscompleted").html('<span>Trips Completed : </span>  <p>'+tripcount+'</p>');
                   // $("#vspeed").html('<span>Speed > 40 KM : </span>  <p>'+speedcount+'</p>');  
                    var tripDetailsHTML = '';
                    if(trip_details_upcoming.length != '0'){
                       var tripDetailsLength = trip_details_upcoming.length;
                       if(tripDetailsLength > 5){
                           tripDetailsLength = 5;
                       }
                       UpcomingLength = tripDetailsLength;
                       for(var indx=0; indx<parseInt(tripDetailsLength); indx++){
                          var endTime = (trip_details_upcoming[indx].endtime == null)?"N.A":trip_details_upcoming[indx].endtime;
                          tripDetailsHTML += '<tr><td style="color:#fff;display:inline-block;font-family:Arial;font-size:13px;font-weight:bold;line-height:27px;width:150px;padding-left:15px;">'+trip_details_upcoming[indx].tripno+'</td><td style="color:#fff;display:inline-block;font-family:Arial;font-size:13px;font-weight:bold;line-height:27px;"> '+trip_details_upcoming[indx].starttime+' - '+endTime+' </td></tr>';
                       }                      
                       
                    }   
                     if(trip_details_running.length != '0' && (parseInt(UpcomingLength) < 5)){
                       var tripDetailsLength = 1; //trip_details_running.length; current trip can be one only at any moment  
                       CurrentLength = tripDetailsLength;
                       for(var indx=0; indx<parseInt(tripDetailsLength); indx++){
                          var endTime = (trip_details_running[indx].endtime == null)?"N.A":trip_details_running[indx].endtime;
                          tripDetailsHTML += '<tr><td style="color:#FFA500;display:inline-block;font-family:Arial;font-size:13px;font-weight:bold;line-height:27px;width:150px;padding-left:15px;">'+trip_details_running[indx].tripno+'</td><td style="color:#FFA500;display:inline-block;font-family:Arial;font-size:13px;font-weight:bold;line-height:27px;"> '+trip_details_running[indx].starttime+' - '+endTime+' </td></tr>';
                       } 
                    }   
                    if(trip_details_completed.length != '0' && ((parseInt(UpcomingLength)+parseInt(CurrentLength)) < 5)){
                       
                       var alreadyAdded = parseInt(UpcomingLength)+parseInt(CurrentLength);                       
                       var tripDetailsLength = trip_details_completed.length;   
                     
                       if(alreadyAdded == 0){
                            if (tripDetailsLength > 5) {
                                tripDetailsLength = 5;
                            }else{
                                tripDetailsLength = tripDetailsLength;
                            }
                       }else{
                            var tripDetailsLengthRemains = 5-parseInt(alreadyAdded);
                            if(tripDetailsLengthRemains > tripDetailsLength){
                                tripDetailsLength = tripDetailsLength;
                            }else{
                                tripDetailsLength = tripDetailsLengthRemains;
                            }
                       } 
                       
                       if(tripDetailsLength > 0){
                            for(var indx=0; indx<parseInt(tripDetailsLength); indx++){
                          var endTime = (trip_details_completed[indx].endtime == null)?"N.A":trip_details_completed[indx].endtime;
                          tripDetailsHTML += '<tr><td style="color:#228B22;display:inline-block;font-family:Arial;font-size:13px;font-weight:bold;line-height:27px;width:150px;padding-left:15px;">'+trip_details_completed[indx].tripno+'</td><td style="color:#228B22;display:inline-block;font-family:Arial;font-size:13px;font-weight:bold;line-height:27px;"> '+trip_details_completed[indx].starttime+' - '+endTime+' </td></tr>';
                         }         
                       }
                    }  
                    
                     $("#tripnos").after(tripDetailsHTML);
                     get_vehicle_ETA(vehicleid); 
                   
               },
               error: function (jqXHR, textStatus, errorThrown)
               {
                  console.log("Something Went Wrong.");
               }
           });
}

function getvhmdFuelGraph(vehicleid)
{
   //document.getElementById("loadergif").style.display = 'block'; 
   $.ajax(
           {
               url: baseUrl + 'vts/index.php?c=cctrack&m=getvhmdFuelGraph',
               type: "POST",
               data: {vehicleid: vehicleid},
               datatype: "json",
               async: true,
               success: function (data)
               {
                   //document.getElementById("loadergif").style.display = 'none';
                    $("#vhmdreport").html(data);
                    
               },
               error: function (jqXHR, textStatus, errorThrown)
               {
                  console.log("Something Went Wrong!!!");
               }
           });
}

function getgpsrateGraph(vehicleid)
{
   $.ajax(
           {
               url: baseUrl + 'vts/index.php?c=cctrack&m=getgpsrateGraph',
               type: "POST",
               data: {vehicleid: vehicleid},
               datatype: "json",
               async: true,
               success: function (data)
               {
                    $("#gpsgraph").html(data);
                    
               },
               error: function (jqXHR, textStatus, errorThrown)
               {
                  console.log("Something Went Wrong!!!");
               }
           });
}

function getvhmdrateGraph(vehicleid)
{
   $.ajax(
           {
               url: baseUrl + 'vts/index.php?c=cctrack&m=getvhmdrateGraph',
               type: "POST",
               data: {vehicleid: vehicleid},
               datatype: "json",
               async: true,
               success: function (data)
               {
                    $("#vhmdspeedgraph").html(data);
                    
               },
               error: function (jqXHR, textStatus, errorThrown)
               {
                  console.log("Something Went Wrong!!!");
               }
           });
}

/*
 * Function to check vehicle marker already exist or not
 * Author - Shivaji
 * Date - 21/07/2016
 */

function getVehicleMarker(vehicleid)
{
    var markerExist = false;
    for (var m = 0; m < VehicleMarkers.length; m++)
    {
        //console.log(VehicleMarkers[m]['vehicleid']);
        if (VehicleMarkers[m]['vehicleid'] == vehicleid)
        {
            markerExist = true;
        }
    }
    return markerExist;
}


function inArray(arr, val) {
    for (cnt = 0; cnt < arr.length; cnt++) {
        if (arr[cnt] == val) {
            return true;
        }
    }
  return false;
}
/*
 * Function to remove vehicle from map
 * Author - Shivaji
 * Date - 21/07/2016
 */
function removeVehicle(vechileId)
{
    if (vechileId != null)
    {
        for (var u = 0; u < VehicleIds.length; u++)
        {
            if (VehicleIds[u] == vechileId)
            {
                VehicleIds.splice(u, 1);
            }
        }
    }
}

/*
 * Function to remove marker from map
 * Author - Shivaji
 * Date - 21/07/2016
 */

function removeMarker(vehicleid)
{
    for (var r = 0; r < VehicleMarkers.length; r++)
    {
        if (VehicleMarkers[r]['vehicleid'] == vehicleid)
        {
            map.removeOverlay(VehicleMarkers[r]['marker']);
            VehicleMarkers.splice(r, 1);
        }
    }
}

function removeAllMarker()
{
    for (var r = 0; r < VehicleMarkers.length; r++)
    {
        map.removeOverlay(VehicleMarkers[r]['marker']);
       // VehicleMarkers.splice(r, 1);
        //console.log('marker removed for'+r);
    }
    VehicleMarkers = new Array();
}

/*
 * Function to get Alert Messages
 * Author - Shivaji
 * Date - 21/07/2016
 */

function DispAllAlertBDivTimeout(uptsid)
{
    $.ajax(
           {
               url: baseUrl + 'vts/index.php?c=cctrack&m=getAllAlertBDivTimeout',
               type: "POST",
               data: {uptsid: uptsid},
               datatype: "json",
               async: false,
               success: function (data)
               {
                    var allmsgdata = JSON.parse(data);
                    if(allmsgdata.msg == 1)
                    {   
                        
                        document.getElementById("scroller").innerHTML = '';
                        var allmsg = allmsgdata.msgdata ;
                        for(var i=0;i< allmsg.length;i++)
                        {
                            var id = allmsg[i].vehicleid;
                            var number = allmsg[i].vehicleno;
                            var desc = allmsg[i].description;
                            var type = allmsg[i].type;
                            addRow(id,number,desc,type); 
                        }
                    }
              },
               error: function (jqXHR, textStatus, errorThrown)
               {
                  console.log("Something Went Wrong!!!");
               }
           });
    
    //setTimeout("DispAllAlertBDivTimeout("+uptsid+")", 60000);
}

function addRow(id,number,msg,type)
{
    var divTag = document.createElement("div");
    var rowid = 'divmove_'+id+type;
    divTag.id = rowid;	
    divTag.innerHTML = number+" "+msg;
    document.getElementById("scroller").appendChild(divTag);
}
/*
 * End Alert Message
 */

/*
 * Function to search vehicle
 * Author - Shivaji
 * Date - 03/08/2016
 */
function searchVehicle()
{
   
   var vehicleno = $("#vsearch").val();
   if(vehicleno != '')
   {
       document.getElementById("loadergif").style.display = 'block'; 
        $.ajax(
                {
                    url: baseUrl + 'vts/index.php?c=cctrack&m=searchVehicle',
                    type: "POST",
                    data: {vehicleno: vehicleno},
                    datatype: "json",
                    async: true,
                    success: function (data)
                    {
                         document.getElementById("loadergif").style.display = 'none';
                         var allvehicle = JSON.parse(data);
                         if (allvehicle.DataFound == 1)
                         {
                            var alldata = allvehicle.PosData;
                            var outerloop = 1 ;
                            for(var v = 0 ; v < alldata.length ; v++)
                            {
                                if(outerloop == 0)
                                {
                                    break;
                                }
                                for(var vi = 0 ; vi < VehicleIds.length ; vi++ )
                                {
                                    if(VehicleIds[vi] == alldata[v].vehicleid)
                                    {
                                        var latlon = alldata[v].lon + ',' + alldata[v].lat ;
                                        ZoomLevel = 14;
                                        var pan = ol.animation.pan({
                                                        duration: 2000,
                                                        source: map.getView().getCenter()
                                                    });
                                        map.beforeRender(pan);
                                        mapCenter(latlon);
                                        outerloop = 0 ;
                                        break;
                                    }
                                }
                            }
                            if(outerloop == 1)
                            {
                              alert('Vehicle Not Found!!!'); 
							
                            }
                         }
                         else
                         {
                            // alert('No Vehicle Found!!!');
							 alert('No GPS data of the vehicle found');
                             document.getElementById("loadergif").style.display = 'none';
                         }

                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                       console.log("Something Went Wrong!!!");
                    }
                });
    }
}

/*
 * Function to search location
 * Author - Shivaji
 * Date - 03/08/2016
 */
function searchlocation()
{ 
    var geocoder = new google.maps.Geocoder();
    var address = $("#pac-input").val();
    var selectedtab = ($("ul li.active").text());
    if(selectedtab == 'All')
    {
       address = address +', '+'chhattisgarh' ;
    }
    else 
    {
        address = address +', '+selectedtab+',chhattisgarh' ;
    }
   // alert(address);
    if(address != '')
    {
        document.getElementById("loadergif").style.display = 'block'; 
        geocoder.geocode( { 'address': address}, function(results, status) 
        {
            if (status == google.maps.GeocoderStatus.OK) 
            {
                var latitude = results[0].geometry.location.lat();
                var longitude = results[0].geometry.location.lng();
                var lonlat = longitude+','+latitude ;
                //alert(lonlat);
                ZoomLevel = 15;
                var pan = ol.animation.pan({
                                duration: 2000,
                                source: map.getView().getCenter()
                            });
                map.beforeRender(pan);
                mapCenter(lonlat);
            }
            else 
            {
                alert("Invalid Address!!");
            }
            document.getElementById("loadergif").style.display = 'none'; 
        }); 
    }
}

/*
 * Full map extent
 * Author - Shivaji
 * Date - 08/08/2016
 */
function mapToFullExtent()
{ 
    if(curUPTS != 'all')
    {
        ZoomLevel = 12;
        mapCenter(uptsLongLat[curUPTS]);
    }
    else 
    {
        ZoomLevel = 8;
        mapCenter(uptsLongLat['all']);
    }
}

/*
 * Get All UPTS Lat Long
 * Author - Shivaji
 * Date - 29/08/2016
 */
function getUPTSLatLong(uptsid)
{
   
    $.ajax(
         {
             url: baseUrl + 'vts/index.php?c=cctrack&m=getUPTSLatLong',
             type: "POST",
             data: {uptsid: uptsid},
             datatype: "json",
             async: true,
             success: function (data)
             {
                 var activeuptsjson = JSON.parse(data);
                 if (activeuptsjson.DataFound == 1)
                 {
                    var allupts = activeuptsjson.PosData ;
                    var len = allupts.length;
                    var icnt = 0;
                    for (icnt = 0; icnt < len; icnt++) {
                        var uptsidr = allupts[icnt].uptsid;
                        var uptslat = allupts[icnt].ccclat;
                        var uptslon = allupts[icnt].ccclng;
                        if(uptslat != null && uptslon !=null && uptslat != '' && uptslon !='')
                        { 
                           if(uptsid == 'all')
                           {
                                // uptsLongLat[uptsid] = uptslon+','+uptslat ; 
                                // console.log(uptsLongLat[uptsid]);
                           }
                           var uptslonlat = uptslon +',' + uptslat;
                           createMarkerForUPTS(uptsidr,uptslonlat, 'U')
                        }
                    }
                 }
                 else
                 {
                     //alert('No Active UPTS!!!');
                     document.getElementById("loadergif").style.display = 'none';
                 }
            },
             error: function (jqXHR, textStatus, errorThrown)
             {
                console.log("Something Went Wrong!!!");
             }
         });
       
    }
  
/*
 * Get All Cluster Lat Long
 * Author - Shivaji
 * Date - 29/08/2016
 */
function getClusterLatLong(uptsid)
{
   
    $.ajax(
         {
             url: baseUrl + 'vts/index.php?c=cctrack&m=getClusterLatLong',
             type: "POST",
             data: {uptsid: uptsid},
             datatype: "json",
             async: true,
             success: function (data)
             {
                 var activeuptsjson = JSON.parse(data);
                 if (activeuptsjson.DataFound == 1)
                 {
                    var allupts = activeuptsjson.PosData ;
                    var len = allupts.length;
                    var icnt = 0;
                    for (icnt = 0; icnt < len; icnt++) {
                        var clusterid = allupts[icnt].clusterid;
                        var clusterlat = allupts[icnt].ccclat;
                        var clusterlon = allupts[icnt].ccclng;
                        if(clusterlat != null && clusterlon !=null && clusterlat != '' && clusterlon !='')
                        { 
                           var clusterlatlon =  clusterlon +','+clusterlat;
                           createMarkerForUPTS(clusterid,clusterlatlon,'C')
                        }
                    }
                 }
                 else
                 {
                     //alert('No Active UPTS!!!');
                     //document.getElementById("loadergif").style.display = 'none';
                 }
            },
             error: function (jqXHR, textStatus, errorThrown)
             {
                console.log("Something Went Wrong!!!");
             }
         });
       
    }
    
/*
 * Get All Depot Lat Long
 * Author - Shivaji
 * Date - 29/08/2016
 */
function getDepotLatLong(uptsid)
{
   
    $.ajax(
         {
             url: baseUrl + 'vts/index.php?c=cctrack&m=getDepotLatLong',
             type: "POST",
             data: {uptsid: uptsid},
             datatype: "json",
             async: true,
             success: function (data)
             {
                 var activeuptsjson = JSON.parse(data);
                 if (activeuptsjson.DataFound == 1)
                 {
                    var allupts = activeuptsjson.PosData ;
                    var len = allupts.length;
                    var icnt = 0;
                    for (icnt = 0; icnt < len; icnt++) {
                        var depotid = allupts[icnt].depotid;
                        var depotlat = allupts[icnt].ccclat;
                        var depotlon = allupts[icnt].ccclng;
                        if(depotlat != null && depotlon !=null && depotlat != '' && depotlon !='')
                        { 
                           var depotlatlon =  depotlon +','+depotlat;
                           createMarkerForUPTS(depotid,depotlatlon,'D')
                        }
                    }
                 }
                 else
                 {
                     //alert('No Active UPTS!!!');
                     //document.getElementById("loadergif").style.display = 'none';
                 }
            },
             error: function (jqXHR, textStatus, errorThrown)
             {
                console.log("Something Went Wrong!!!");
             }
         });
       
    }    
    
  
/*
 * Get Control Center Info
 * Author - Shivaji
 * Date - 30/08/2016
 */
function getCCDetails(id,mtype)
{
   document.getElementById("loadergif").style.display = 'block';
    $.ajax(
         {
             url: baseUrl + 'vts/index.php?c=cctrack&m=getCCDetails',
             type: "POST",
             data: {ccid: id , cctype :mtype},
             datatype: "json",
             async: true,
             success: function (data)
             {
                document.getElementById("loadergif").style.display = 'none';
                $("#inuu").html(data);
                $("#myModal").modal('show');
            },
             error: function (jqXHR, textStatus, errorThrown)
             {
                console.log("Something Went Wrong!!!");
             }
         });
       
    }    
    /*
 * Marker For UPTS,Cluster,Depot
 * Author - Shivaji
 * Date - 30/08/2016
 */

function createMarkerForUPTS(uid,lonLat, mtype)
{
    var markerimage = document.createElement("IMG");
    if(mtype == 'U' )
    {
      markerimage.setAttribute('src', Iconupts);  
    }
    else if(mtype == 'C' )
    {
      markerimage.setAttribute('src', Iconcluster); 
      // console.log(Iconcluster);
    }
    else if(mtype == 'D' )
    {
      markerimage.setAttribute('src', Icondepot);   
       //console.log(Icondepot);
    }
    markerimage.setAttribute('width', 32);
    markerimage.setAttribute('height', 32);
    $(markerimage) .on('click', function (e) {
            getCCDetails( uid,mtype);
    });

    var lonLatAry = lonLat.split(",");
    var mlong = parseFloat(lonLatAry[0]);
    var mlat = parseFloat(lonLatAry[1]);
    var plonLat = ol.proj.transform([mlong, mlat], proj4326, proj900913);
    var marker = new ol.Overlay({
        position: plonLat,
        offset: [-15, -30],
        element: markerimage
    });
    map.addOverlay(marker);
    var MarkersData = new Array();
    MarkersData['uid'] = uid;
    MarkersData['marker'] = marker;
    UPTSMarkers.push(MarkersData);   
    return marker;
}

function removeAllUPTSMarker()
{
    for (var r = 0; r < UPTSMarkers.length; r++)
    {
        map.removeOverlay(UPTSMarkers[r]['marker']);
    }
    UPTSMarkers = new Array();
}

function showmessagebox()
{
  $("#myModalcomment").modal('show');  
}

function sendmessage()
{
    var message = $("#message-text").val();
	if($("#uptsid").val() != ''){
	var uptsid = $("#uptsid").val();
	}
	else{
	var uptsid = 0;
	}
	if($("#depotid").val() != ''){
	var depotid = $("#depotid").val();
	}
	else{
	var depotid = 0;
	}
    if(message == '')
    {
        alert('Message cannot be blank');
		return false;
    }
    document.getElementById("loadergif").style.display = 'block';
    $.ajax(
         {
             url: baseUrl + 'vts/index.php?c=cctrack&m=sendmessage',
             type: "POST",
             data: {message: message, upts : uptsid, depot : depotid},
             datatype: "json",
             async: true,
             success: function (data)
             {
			  // alert(data.flag);
			   
			   
             //  if(data.flag) {
			    document.getElementById("loadergif").style.display = 'none';
                alert("The message has been broadcasted successfully.");
                $("#myModalcomment").modal('hide');
               // console.log(data);
			  /* }
			   else{
				    document.getElementById("loadergif").style.display = 'none';
				    alert("Something Went Wrong111!!!");
               		 $("#myModalcomment").modal('hide');
				   }*/
            },
             error: function (jqXHR, textStatus, errorThrown)
             {
                console.log("Something Went Wrong!!!");
             }
         });
}

function sendmessagevehicle()
{
    var message = $("#instmsg").val();
    if(message == '')
    {
        alert('Message cannot be blank');
		return false;
    }
    document.getElementById("load_instmsg").style.display = 'block';
	$('#instmsgbtn').prop('disabled', true);
    $.ajax(
         {
             url: baseUrl + 'vts/index.php?c=cctrack&m=sendmessagevehicle',
             type: "POST",
             data: {message: message, vehicleid: globalVehicle},
             datatype: "json",
             async: true,
             success: function (data)
             {
			    document.getElementById("load_instmsg").style.display = 'none';
                alert("The message has been broadcasted successfully.");
				$("#instmsg").val('');
				$('#instmsgbtn').prop('disabled', false);
             },
             error: function (jqXHR, textStatus, errorThrown)
             {
                console.log("Something Went Wrong!!!");
             }
         });
}

/***
 * 
 * @param {type} lat1
 * @param {type} lng1
 * @param {type} lat2
 * @param {type} lng2
 * @returns {Angle}
 */
function bearing (lat1,lng1,lat2,lng2) 
{
    var dLon = (lng2-lng1);
    var y = Math.sin(dLon) * Math.cos(lat2);
    var x = Math.cos(lat1)*Math.sin(lat2) - Math.sin(lat1)*Math.cos(lat2)*Math.cos(dLon);
    var brng = toDeg(Math.atan2(y, x));
    return 360 - ((brng + 360) % 360);
}

function toRad (deg) {
    return deg * Math.PI / 180;
}

function toDeg (rad) {
    return rad * 180 / Math.PI;
}

/*
 * Function to get Last positional data
 * Author - Shivaji
 * Date - 11/16/2016
 */

function getExistingMarker(vehicleid)
{
    for (var r = 0; r < VehicleMarkers.length; r++)
    {
        if (VehicleMarkers[r]['vehicleid'] == vehicleid)
        {
           return VehicleMarkers[r]['marker'];
        }
    }
}

/*
 * Function to get Last positional data
 * Author - Shivaji
 * Date - 11/16/2016
 */

function getExistingAngle(vehicleid)
{
    for (var r = 0; r < VehicleMarkers.length; r++)
    {
        if (VehicleMarkers[r]['vehicleid'] == vehicleid)
        {
           return VehicleMarkers[r]['angle'];
        }
    }
}

/*
 * Function to Move Existing Marker
 * Author - Shivaji
 * Date - 11/16/2016
 */

function moveMarker(existingmarker,lonLat,vehicle_number,vehicleid)
{
    var oldlonlat = existingmarker.getPosition();
    var oldlonlatTransformed = ol.proj.transform(oldlonlat, proj900913, proj4326);
    var LonlatTransformed = ol.proj.transform(lonLat, proj900913, proj4326);
    var degrees = bearing(oldlonlatTransformed[1],oldlonlatTransformed[0],LonlatTransformed[1],LonlatTransformed[0]);
    //console.log(oldlonlatTransformed);
    //console.log(LonlatTransformed);
  
    for (var r = 0; r < VehicleMarkers.length; r++)
     {
         if (VehicleMarkers[r]['vehicleid'] == vehicleid)
         {
            var lastangle = VehicleMarkers[r]['angle'];
            if(degrees != 360){
               VehicleMarkers[r]['angle'] = degrees;  
            }
           
         }
     }
//     if(vehicle_number == 'CG10G1577')
//     {
//         console.log(lastangle);
//    console.log(degrees);
//    console.log(vehicle_number); 
//     }
    
     if(degrees == 360)
     {
         degrees = lastangle;
     }
     
    existingmarker.getElement().style.cssText = 'transform: rotate(' + degrees + 'deg)';
    existingmarker.setPosition(lonLat);
}

function getVehicleInactiveDetails(vid){
    $.ajax(
            {
                url: baseUrl + 'vts/index.php?c=cctrack&m=get_inactive_vehicle_popup_details',
                type: "POST",
                data: {vehicleid: vid},               
                async: true,
                beforeSend: function () {
                    $(".loadergif").show();
                    $(".inactvMdl").html('');
                }
            }).done(function (data) {
                $(".loadergif").hide();				
        if (data != '') {
			data = JSON.parse(data);
            var routename = data.routenames;
            if (routename.length > 0) {
                routename = routename.split(",");
            }
            $("#vehicleInactiveDetails").modal();
            $(document).find("#vehicleInactiveDetails #inactive_vehicleNum").html((data.vehicleno == '' || data.vehicleno == null) ? "N.A" : data.vehicleno);
            $(document).find("#vehicleInactiveDetails .modal-body #inactive_vehicleDepo").html((data.depotname == '' || data.depotname == null) ? "N.A" : data.depotname);
            var upRoute = (routename[0] == '' || routename[0] == null) ? "N.A" : routename[0];
            var dwnRoute = (routename[1] == '' || routename[1] == null) ? "N.A" : routename[1];
            $(document).find("#vehicleInactiveDetails #inactive_vehicleRoute").html("<p>" + upRoute + "</p><p>" + dwnRoute + "</p>");
            $(document).find("#vehicleInactiveDetails #inactive_vehicleDriver").html((data.driver == '' || data.driver == null) ? "N.A" : data.driver);
            $(document).find("#vehicleInactiveDetails #inactive_vehicleConductor").html((data.conductor == '' || data.conductor == null) ? "N.A" : data.conductor);
            $(document).find("#vehicleInactiveDetails #inactive_vehicleVtunum").html((data.vtuno == '' || data.vtuno == null) ? "N.A" : data.vtuno);
            $(document).find("#vehicleInactiveDetails #inactive_vehicleGsmno").html((data.gsmno == '' || data.gsmno == null) ? "N.A" : data.gsmno);
            $(document).find("#vehicleInactiveDetails #inactive_vehicleBusType").html((data.bustype == '' || data.bustype == null) ? "N.A" : data.bustype);
            $(document).find("#vehicleInactiveDetails #inactive_vehicleBusMake").html((data.companyname == '' || data.companyname == null) ? "N.A" : data.companyname);
        } else {
            $(document).find("#vehicleInactiveDetails #inactive_vehicleNum").html("N.A");
            $(document).find("#vehicleInactiveDetails .modal-body #inactive_vehicleDepo").html("N.A");
            $(document).find("#vehicleInactiveDetails #inactive_vehicleRoute").html("N.A");
            $(document).find("#vehicleInactiveDetails #inactive_vehicleDriver").html("N.A");
            $(document).find("#vehicleInactiveDetails #inactive_vehicleConductor").html("N.A");
            $(document).find("#vehicleInactiveDetails #inactive_vehicleVtunum").html("N.A");
            $(document).find("#vehicleInactiveDetails #inactive_vehicleGsmno").html("N.A");
            $(document).find("#vehicleInactiveDetails #inactive_vehicleBusType").html("N.A");
            $(document).find("#vehicleInactiveDetails #inactive_vehicleBusMake").html("N.A");
        }
        var imgLoading = baseUrl+"public/images/ajax-loader-red.gif";
        //TRIP INFO
        $.ajax(
                {
                    url: baseUrl + 'vts/index.php?c=cctrack&m=get_inactive_vehicle_popup_trip_gps_info',
                    type: "POST",
                    data: {vehicleid: vid, get_trip: 1},                    
                    async: true,
                    beforeSend: function(){
                        $(document).find("#vehicleInactiveDetails #inactive_vehicleTrpInfoDt").html("<img src='"+imgLoading+"' alt='loading'/>");
                        $(document).find("#vehicleInactiveDetails #inactive_vehicleTrpInfoTime").html("<img src='"+imgLoading+"' alt='loading'/>");
                    }
                }).done(function (tripdata) {   
				
			if(tripdata != ''){
				var tripdataResult = JSON.parse(tripdata);
				var triplen = Object.keys(tripdataResult).length;				
				if (triplen > 0) {
                $(document).find("#vehicleInactiveDetails #inactive_vehicleTrpInfoDt").html((tripdataResult.currentdate == '' || tripdataResult.currentdate == null) ? "N.A" : tripdataResult.currentdate);				
                var starttime = (tripdataResult.starttime != '' || tripdataResult.starttime != null) ? tripdataResult.starttime : "N.A";
                var endtime = (tripdataResult.endtime != '' || tripdataResult.endtime != null) ? tripdataResult.endtime : "N.A";
                $(document).find("#vehicleInactiveDetails #inactive_vehicleTrpInfoTime").html("<p>Start Time - " + starttime + "</p><p>End Time - " + endtime + "</p>");
            } else {
                $(document).find("#vehicleInactiveDetails #inactive_vehicleTrpInfoDt").html("N.A");
                $(document).find("#vehicleInactiveDetails #inactive_vehicleTrpInfoTime").html("<span style='margin-right: 98px;'>N.A</span>");
            }
			}else{
				 $(document).find("#vehicleInactiveDetails #inactive_vehicleTrpInfoDt").html("N.A");
                $(document).find("#vehicleInactiveDetails #inactive_vehicleTrpInfoTime").html("<span style='margin-right: 98px;'>N.A</span>");
			}
			
           

            //GPS INFO
            $.ajax(
                    {
                        url: baseUrl + 'vts/index.php?c=cctrack&m=get_inactive_vehicle_popup_trip_gps_info',
                        type: "POST",
                        data: {vehicleid: vid, get_trip: 0},                       
                        async: true,
                        beforeSend: function(){
                            $(document).find("#vehicleInactiveDetails #inactive_vehicleGpsInfoDt").html("<img src='"+imgLoading+"' alt='loading'/>");
                            $(document).find("#vehicleInactiveDetails #inactive_vehicleGpsInfoTime").html("<img src='"+imgLoading+"' alt='loading'/>");
                        }
                    }).done(function (gpsdata) {  			   
									
                if (gpsdata != '') {
					var gpsdataResult = 	JSON.parse(gpsdata);
					var triplen = Object.keys(gpsdataResult).length;	
					if(triplen > 0){
						 $(document).find("#vehicleInactiveDetails #inactive_vehicleGpsInfoDt").html((gpsdataResult.currentdate == '' || gpsdataResult.currentdate == null) ? "N.A" : gpsdataResult.currentdate);                    
                    $(document).find("#vehicleInactiveDetails #inactive_vehicleGpsInfoTime").html((gpsdataResult.currenttime == '' || gpsdataResult.currenttime == null) ? "N.A" : gpsdataResult.currenttime);
					}else{
						 $(document).find("#vehicleInactiveDetails #inactive_vehicleGpsInfoDt").html("N.A");
                    $(document).find("#vehicleInactiveDetails #inactive_vehicleGpsInfoTime").html("N.A");
					}
                   
                } else {
                    $(document).find("#vehicleInactiveDetails #inactive_vehicleGpsInfoDt").html("N.A");
                    $(document).find("#vehicleInactiveDetails #inactive_vehicleGpsInfoTime").html("N.A");
                }
            }).fail();
        }).fail();
    }).fail(function () {
        $(".loadergif").hide();
    });
}

function openCloseInactiveVehicleList(){
   if($(".inactive-vehicles").toggle()){
       $("#map_legend").hide();
   }
}

function callivrs(mobileNumber){   
    if(mobileNumber != '' && parseInt(mobileNumber)){
        window.open('http://indore.tecd.in:8086/steslait-ig2bxt/dialler.php?mob='+mobileNumber, '_blank');    
    }
}