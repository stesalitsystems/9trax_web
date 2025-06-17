var intervalMsVariable = 10000;
var ajaxCalled;
var setTimeOutReference;

/**************************************************************/
/*
 * Fetches the active devices positional
 * data after a given interval
 */
function getPositionData(fetchingIntervalCall){
   ajaxCalled = $.ajax({
       method: "POST",
       url: BASEURL+"controlcentre/get_device_position_data",
       data:{customerid: $.trim($("#customers option:selected").val())},
       global: false
    }).done(function(data){
        if(data != ''){
            var alldata = JSON.parse(data);  
            //console.log(alldata.length);
            /*******will be in foreach*******/
            for(var i = 0; i<alldata.length;i++){
                
                if(holdExistingMarkerIds.indexOf(alldata[i].id) == -1){
                 mymap.addMarker(alldata[i].id,parseFloat(alldata[i].lon), parseFloat(alldata[i].lat),alldata[i].divise_serial);
            }else{
                mymap.moveMarker(alldata[i].id,parseFloat(alldata[i].lon), parseFloat(alldata[i].lat),alldata[i].divise_serial);
            }
            }
                
            /*********************/
            $("#active_count").html(mymap.markerCount());
        }
    }).fail(function(){
        console.log('error in positiondata')
    }).complete(function(){
        // mymap.mapclick();
     setTimeOutReference =  setTimeout(function(){getPositionData(fetchingIntervalCall)},fetchingIntervalCall);   
      //console.log("completed")
    });
    
}
$(document).ready(function(){
    $("#customers").on('change',function(){
       var valueSelected = $(this).val();
       ajaxCalled.abort();
       clearTimeout(setTimeOutReference);
       holdExistingMarkerIds.length = 0;
       mymap.removeAllMarkers();
       getPositionData(intervalMsVariable);
    });
   

});
