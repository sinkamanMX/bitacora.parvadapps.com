var map = null;
var geocoder;
var infowindow;
var infoLocation;
var markers = [];
var bounds;

$( document ).ready(function() {
	initMapToDraw();	
});

function initMapToDraw(){
    var mapOptions = {
      zoom: 5,
      center: new google.maps.LatLng(24.52713, -104.41406),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
	map = new google.maps.Map(document.getElementById('Map'),mapOptions);

	bounds = new google.maps.LatLngBounds();
}