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
            if(holdExistingMarkerIds.indexOf(alldata.id) == -1){
                 mymap.addMarker(alldata.id,alldata.lon,alldata.lat);
            }else{
                mymap.moveMarker(alldata.id,alldata.lon,alldata.lat);
            }           
        }
    }).fail(function(){
        console.log('error in positiondata')
    }).complete(function(){
     setTimeOutReference =  setTimeout(function(){getPositionData(fetchingIntervalCall)},fetchingIntervalCall);   
      //console.log("completed")
    });
    
}
$(document).ready(function(){
    $("#customers").on('change',function(){
       var valueSelected = $(this).val();
       ajaxCalled.abort();
       clearTimeout(setTimeOutReference);
       mymap.removeAllMarkers();
       getPositionData(intervalMsVariable);
    });
    mymap.mapclick();
});
