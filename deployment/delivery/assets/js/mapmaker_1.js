var holdExistingMarkerIds = [];

var MapMaker = function ()
{
    var map;

    return {
        createOSMap: function (lon, lat, zoom)
        {
            //a layer for markers - initially it has no markers
            var markerLayer = new ol.layer.Vector({
                source: new ol.source.Vector({features: [], projection: 'EPSG:4326'})
            });

            var baseLayer = new ol.layer.Tile({
                source: new ol.source.OSM()
            });

            map = new ol.Map({
                target: 'map', // The DOM element that will contains the map
                renderer: 'canvas', // Force the renderer to be used
                layers: [baseLayer, markerLayer],
                // Create a view centered on the specified location and zoom level
                view: new ol.View({
                    center: ol.proj.transform([lon, lat], 'EPSG:4326', 'EPSG:3857'),
                    zoom: zoom
                }),
                crossOrigin: 'anonymous'
            });
            map.once('postrender',function(event){
               getPositionData(intervalMsVariable);
            });
        },
        addMarker: function (id, lon, lat)
        {
            //create a point
            var geom = new ol.geom.Point(ol.proj.transform([lon, lat], 'EPSG:4326', 'EPSG:3857'));
            var feature = new ol.Feature(geom);
            feature.setStyle([
                new ol.style.Style({
                    image: new ol.style.Icon(({
                        anchor: [0.5, 1],
                        anchorXUnits: 'fraction',
                        anchorYUnits: 'fraction',
                        opacity: 1,
                        src: BASEURL+'assets/images/walkingicon.png'
                    }))
                }),
                new ol.style.Style({
                    text: new ol.style.Text({
                        font: '12px arial,helvetica,sans-serif',
                        text: "Device : "+id,
                        offsetY: -38,
                        fill: new ol.style.Fill({
                            color: '#fff'                       
                        }),
                         stroke: new ol.style.Stroke({
                            color: '#1634cc',       
                            width: 3
                        })
                    })
                })
            ]);

            if (id != null)
            {
                feature.setId(id);
            }

            map.getLayers().item(1).getSource().addFeature(feature);
            
            holdExistingMarkerIds.push(id);
        },
        deleteMarkerById: function (id)
        {
            var id = map.getLayers().item(1).getSource().getFeatureById(id);
            map.getLayers().item(1).getSource().removeFeature(id);
        },
        moveMarker: function (id, lon, lat)
        {
            var feature = map.getLayers().item(1).getSource().getFeatureById(id);
            if (feature != null)
            {
                feature.setGeometry(new ol.geom.Point(ol.proj.transform([lon, lat], 'EPSG:4326', 'EPSG:3857')));
            } else
            {
                this.addMarker(id, lon, lat);
            }
        },
        removeAllMarkers: function ()
        {
            map.getLayers().item(1).getSource().clear();
        },
        markerCount: function ()
        {
            return map.getLayers().item(1).getSource().getFeatures().length;
        },
        mapclick: function(){}
    }
};

