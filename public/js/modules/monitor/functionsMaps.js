var map = null;
var geocoder;
var infoWindow;
var infoLocation;
var markers = [];
var bounds;
var arrayTravels=Array();
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();

$( document ).ready(function() {
	$('.table').dataTable( {
		"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
		"sPaginationType": "bootstrap",
		"bDestroy": true,
		"bLengthChange": false,
		"bPaginate": true,
		"bFilter": true,
		"bSort": true,
		"bJQueryUI": true,
		"iDisplayLength": 10,      
		"bProcessing": false,
		"bAutoWidth": true,
		"bSortClasses": false,
	      "oLanguage": {
	          "sInfo": "Mostrando _TOTAL_ registros (_START_ a _END_)",
	          "sEmptyTable": "Sin registros.",
	          "sInfoEmpty" : "Sin registros.",
	          "sInfoFiltered": " - Filtrado de un total de  _MAX_ registros",
	          "sLoadingRecords": "Leyendo información",
	          "sProcessing": "Procesando",
	          "sSearch": "Buscar:",
	          "sZeroRecords": "Sin registros",
	          "oPaginate": {
	            "sPrevious": "Anterior",
	            "sNext": "Siguiente"
	          }          
	      }
	} );	
	initMapToDraw();

    $('#iFrameModalinc').on('load', function () {        
        $('#loader2').hide();
        $('#iFrameModalinc').show();
    });      

    $('#iFrameModaManual').on('load', function () {        
        $('#loader3').hide();
        $('#iFrameModaManual').show();
    });             
});


function initMapToDraw(){
  directionsDisplay = new google.maps.DirectionsRenderer();
	infoWindow = new google.maps.InfoWindow;
    var mapOptions = {
      zoom: 5,
      center: new google.maps.LatLng(24.52713, -104.41406),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
	map = new google.maps.Map(document.getElementById('Map'),mapOptions);
	
	bounds = new google.maps.LatLngBounds();	
  calcRoute();
}

function calcRoute() {
    if($("#inputLatOrigen").val()!="" && $("#inputLonOrigen").val()!="" && 
        $("#inputLatDestino").val()!="" && $("#inputLonDestino").val()!=""){
        var latsOrigen  = new google.maps.LatLng($("#inputLatOrigen").val(), $("#inputLonOrigen").val());
        var lastDestino = new google.maps.LatLng($("#inputLatDestino").val(), $("#inputLonDestino").val());
        var request = {
          origin: latsOrigen,
          destination: lastDestino,
          travelMode: google.maps.TravelMode.DRIVING
        };
        directionsService.route(request, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
          directionsDisplay.setDirections(response);
          directionsDisplay.setMap(map);
        }
        });
    }
    printTravelsMap();
}


function printTravelsMap(){
  var result = $("#positions").html();
  if(result!=""){

      arrayTravels=new Array();
        arrayTravels=result.split('!');
    var content     = '';
    var markerTable = null;

    for(var i=0;i<arrayTravels.length;i++){    
      var travelInfo = arrayTravels[i].split('|');
        var markerTable = null;
        if(travelInfo[0]!="null" && travelInfo[1]!="null" ){
            var tipo = (travelInfo[6]=='A') ? 'Automàtico': 'Manual';

            content='<table width="350" class="table-striped" >'+  
                '<tr><td align="right"><b>Hora del Evento</b></td><td width="200" align="left">'+travelInfo[2]+'</td><tr>'+
                '<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[4]+' kms/h.</td><tr>'+
                '<tr><td align="right"><b>Angulo</b></td><td align="left">'+travelInfo[5]+'</td><tr>'+
                '<tr><td align="right"><b>Tipo</b></td><td align="left">'+ tipo+'</td><tr>'+
                '<tr><td align="right"><b>Incidencia</b></td><td align="left">'+travelInfo[7]+'</td><tr>'+
                '<tr><td align="right"><b>Ubicación</b></td><td align="left">'+travelInfo[3]+'</td><tr>'+
                '</table>';
            var Latitud  = parseFloat(travelInfo[0])
            var Longitud = parseFloat(travelInfo[1])

            markerTable = new google.maps.Marker({
              map: map,
              position: new google.maps.LatLng(Latitud,Longitud),
              title:  travelInfo[0],
              icon:   '/images/carMarker.png'
            });
            markers.push(new google.maps.LatLng(Latitud,Longitud));
            infoMarkerTable(markerTable,content);   
            bounds.extend( markerTable.getPosition() );
        }   
      }

      var iconsetngs = {
          path: google.maps.SymbolPath.FORWARD_OPEN_ARROW,
          strokeColor: '#155B90',
          fillColor: '#155B90',
          fillOpacity: 1,
          strokeWeight: 4        
      };

      var line = new google.maps.Polyline({
        map: map,
        path: markers,
        strokeColor: "#098EF3",
        strokeOpacity: 1.0,
        strokeWeight: 2,
          icons: [{
              icon: iconsetngs,
              repeat:'35px',         
              offset: '100%'}]
      });   
      if(arrayTravels.length>1){
        map.fitBounds(bounds);  
      }else if(arrayTravels.length==1){
        map.setZoom(13);
        map.panTo(markerTable.getPosition());  
      }

  }  
}

function infoMarkerTable(marker,content){ 
    google.maps.event.addListener(marker, 'click',function() {
        if(infoWindow){infoWindow.close();infoWindow.setMap(null);}
        var marker = this;
        var latLng = marker.getPosition();
        infoWindow.setContent(content);
        infoWindow.open(map, marker);
        map.setZoom(18);
        map.setCenter(latLng); 
        map.panTo(latLng);     
  });
}

function cancelTravel(idObject){
	$('#lblConfirm').html(idObject);
    $('#MyModalConfirm').modal('show');   
}

function cancelConfirm(){
	var idObject = $('#lblConfirm').html();
    $('#MyModalConfirm').modal('hide'); 
	startStopTravel(idObject,'stop');
}

function startStopTravel(idObject,optionValue){	
    $.ajax({
        url: "/main/map/chagestatus",
        type: "GET",
		dataType : 'json',
        data: { catId  : idObject, 
        		option : optionValue },
        success: function(data) { 
            var result = data.answer; 
			if(result=='stoped'){
            	$("#tittleMessage").html('Viaje Terminado');
				$("#divMessage").html('El viaje #'+idObject+" ha sido terminado.");            	
            }else if(result=='started'){	
            	$("#tittleMessage").html('Viaje iniciado');
				$("#divMessage").html('El viaje #'+idObject+" ha sido iniciado.");
            }
            $("#myModalOptions").modal('show');
            location.reload();
        }
    }); 
}

function setIncidencia(dataTravel){
	$('#loader2').show();
	$('#iFrameModalinc').hide(); 
    $('#iFrameModalinc').attr('src','/monitor/map/setincidencia?catId='+dataTravel);
    $('#myModalInc').modal('show');   	
}

function icidenciaOk(idObject){
	$("#tittleMessage").html('¡Atención!');
	$("#divMessage").html('La incidencia se ha registrado correctamente.');	
    $("#myModalOptions").modal('show');
    window.location.href = "/monitor/map/index?catId="+idObject;
}

function setPositionManual(dataTravel){
	$('#loader3').show();
	$('#iFrameModaManual').hide(); 		
    $('#iFrameModaManual').attr('src','/monitor/map/manualpos?catId='+dataTravel);
    $('#myModalManual').modal('show');   		
}

function positionOk(idObject){
	$("#tittleMessage").html('¡Atención!');
	$("#divMessage").html('La pocisión se ha registrado correctamente.');	
    $("#myModalOptions").modal('show');
    window.location.href = "/monitor/map/index?catId="+idObject;
}

function goToTrackSystem(idTravel){
  	window.open("/main/map/external?catId="+idTravel ,'_blank');
}
