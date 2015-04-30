var map = null;
var geocoder;
var infoWindow;
var infoLocation;
var markers = [];
var bounds;
var arrayTravels=Array();
var mon_timer=60;

var dataRowsTable=null;

$( document ).ready(function() {
	initMapToDraw();
	$('#slideTimeUp').slider({
		formater: function(value) {
			mon_timer = value;
			timerUpdate()
			return 'Actualizar cada ' + value + ' segs.';			
		},
		value: 60
	}).on('slide', function(ev){
		var valorSegs = ev.value;
		mon_timer = valorSegs;
		timerUpdate()
    	$("#labelMinutes").html(valorSegs);
  	});

	$("#countdown").countdown360({
	    radius: 30,
	    seconds: 20,
	    seconds: mon_timer,
	    label: ['seg', 'segs'],
	    fontColor: '#FFFFFF',
	    autostart: false,
	    onComplete: function () {
	      mapLoadData()
	    }		
	}).start()  

	$('#myModalOptions').on('hidden.bs.modal', function () {
    	mapLoadData()
	})

    $('#iFrameModaltravel').on('load', function () {        
        $('#loader1').hide();
        $('#iFrameModaltravel').show();
    });

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
	infoWindow = new google.maps.InfoWindow;
    var mapOptions = {
      zoom: 5,
      center: new google.maps.LatLng(24.52713, -104.41406),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
	map = new google.maps.Map(document.getElementById('Map'),mapOptions);
	
	bounds = new google.maps.LatLngBounds();
}

$( document ).ready(function() {
	mapLoadData();
});	

function mapLoadData(){
	mapClearMap();
	$.ajax({
		type: "POST",
        url: "/main/map/getravels",
        success: function(datos){
			var result = datos;
			if(result!= 0){
				arrayTravels=new Array();
				arrayTravels=result.split('!');
				printTravelsMap();
			}
        }
	});
}

function setDataTable(){
	var table =  $('#dataTable').dataTable( {
		"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
		"sPaginationType": "bootstrap",
		"bDestroy": true,
		"bLengthChange": false,
		"bPaginate": true,
		"bFilter": true,
		"bSort": true,
		"bJQueryUI": true,
		"iDisplayLength": 10,      
		"bProcessing": true,
		"bAutoWidth": false,
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
}

function mapClearMap(){
	if(markers || markers.length>-1){
		for (var i = 0; i < markers.length; i++) {
	          markers[i].setMap(null);
		}	
		markers = [];
	}
	arrayTravels=null;
}

function timerUpdate(){
	if(mon_timer>0){
		$("#countdown").countdown360({
		    radius: 30,
		    seconds: 20,
		    label: ['seg', 'segs'],
		    fontColor: '#FFFFFF',
		    autostart: false,
		    onComplete: function () {
		      mapLoadData()
		    }		
		}).addSeconds(mon_timer);
	}else{
		$("#countdown").countdown360({
		    radius: 30,
		    seconds: 20,
		    label: ['seg', 'segs'],
		    fontColor: '#FFFFFF',
		    autostart: false,
		    onComplete: function () {
		      mapLoadData()
		    }		
		}).stop();
	}

}

function printTravelsMap(){
	$('#divTbody').html(""); 

	$('#divTbody').append('<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered"  id="dataTable">'+
                            '<thead>'+
                                '<tr>'+
                                    '<th style="width:160px;">&nbsp; </th>'+
                                    '<th># Viaje</th>'+
                                    '<th>Cliente</th>'+
                                    '<th>Unidad</th>'+
                                    '<th>Inicio</th>'+
                                    '<th>Hora GPS</th>'+
                                    '<th>Velocidad</th>'+
                                    '<th>Incidencia</th>'+
                                    '<th style="width:210px;">Ubicación</th>'+
                                '</tr>'+
                            '</thead>'+
                            '<tbody>'); 

	for(var i=0;i<arrayTravels.length;i++){
		var travelInfo = arrayTravels[i].split('|');

		var btnOptions   = "";
		var noDataTravel = "<td>--</td><td>--</td><td>--</td><td>--</td><td>--</td>";

		if(travelInfo[9]==1){
			btnOptions = '<button class="btn-success" onClick="startStopTravel('+travelInfo[0]+',\'start\')"><i class="icon-play icon-white"></i></button>';
		}else if(travelInfo[9]==2){
			btnOptions  = '<button class="btn-danger" onClick="cancelTravel('+travelInfo[0]+')"><i class="icon-stop icon-white"></i></button>';
			btnOptions += '<button class="btn-primary" onClick="setPositionManual('+travelInfo[0]+')"><i class="icon-globe icon-white"></i></button>';
			btnOptions += '<button class="btn-warning" onClick="setIncidencia('+travelInfo[0]+')"><i class="icon-warning-sign icon-white"></i></button>';
			noDataTravel = '<td>'+travelInfo[2]+'</td><td>'+travelInfo[12]+'</td><td>'+travelInfo[14]+' kms/h.</td><td>'+travelInfo[17]+'</td><td>'+travelInfo[13]+'</td>'; 
		}
	    var content     = '';
	    var markerTable = null;
	    if(travelInfo[10]!="null" && travelInfo[11]!="null" && travelInfo[9]==2){
	    	content='<table width="350" class="table-striped" ><tr><td align="right"><b># Viaje</b></td><td width="200" align="left">'+travelInfo[0]+'</td><tr>'+
	    			'<tr><td align="right"><b>Cliente</b></td><td align="left">'+travelInfo[5]+'</td><tr>'+
	    			'<tr><td align="right"><b>Unidad</b></td><td align="left">'+travelInfo[6]+'</td><tr>'+
	    			'<tr><td align="right"><b>Hora del Evento</b></td><td align="left">'+travelInfo[12]+'</td><tr>'+
	    			'<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[14]+' kms/h.</td><tr>'+
	    			'<tr><td align="right"><b>Incidencia</b></td><td align="left">'+travelInfo[17]+'</td><tr>'+
	    			'<tr><td align="right"><b>Ubicación</b></td><td align="left">'+travelInfo[13]+'</td><tr>'+
	    			'</table>';
			markerTable = new google.maps.Marker({
				map: map,
				position: new google.maps.LatLng(travelInfo[10],travelInfo[11]),
				title: 	travelInfo[0],
				icon: 	'/images/carMarker.png'
			});
			markers.push(markerTable);
			infoMarkerTable(markerTable,content);		    
	    }			
		$('#divTbody').find('tbody')
		    .append($('<tr>')
		        .append($('<td>')
		        	.append($('<button onClick="centerMap(\''+arrayTravels[i]+'\')" class="btn-info"><i class="icon-map-marker icon-white"></i></button>'))
		            .append($('<button onClick="editTravel(\''+travelInfo[0]+'\')" class="btn-primary"><i class="icon-eye-open icon-white"></i></button>'))
					.append($(btnOptions))	
		        )
		        .append($('<td>')
		        	.append(travelInfo[0])
		        )	
		        .append($('<td>')
		        	.append(travelInfo[5])
		        )				        
		        .append($('<td>')
		        	.append(travelInfo[6])
		        )
		        .append(noDataTravel)		        		        
		    );		    
		}	
	fitBoundsToVisibleMarkers();
	timerUpdate();
	setDataTable();
}

function centerMap(dataTravel){	
	var travelInfo  = dataTravel.split("|");
    var content     = '';
    if(travelInfo[10]!="null" && travelInfo[11]!="null" && travelInfo[9]==2){
		var listLatLon = new google.maps.LatLng(travelInfo[10],travelInfo[11]);

		var marker1 = new google.maps.Marker({
		    map: map,
		    position: listLatLon,
				icon: 	'/images/carMarker.png'
	    });

		var nivelBateria = '';
	    	content='<table width="450" class="table-striped" ><tr><td align="right"><b># Viaje</b></td><td width="200" align="left">'+travelInfo[0]+'</td><tr>'+
	    			'<tr><td align="right"><b>Cliente</b></td><td align="left">'+travelInfo[5]+'</td><tr>'+
	    			'<tr><td align="right"><b>Unidad</b></td><td align="left">'+travelInfo[6]+'</td><tr>'+
	    			'<tr><td align="right"><b>Hora del Evento</b></td><td align="left">'+travelInfo[12]+'</td><tr>'+
	    			'<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[14]+' kms/h.</td><tr>'+
	    			'<tr><td align="right"><b>Incidencia</b></td><td align="left">'+travelInfo[17]+' </td><tr>'+
	    			'<tr><td align="right"><b>Ubicación</b></td><td align="left">'+travelInfo[13]+'</td><tr>'+
	    			'</table>';
		markers.push(marker1);
		infoMarkerTable(marker1,content);	
		if(infoWindow){infoWindow.close();infoWindow.setMap(null);}
	    infoWindow.setContent(content);
      	infoWindow.open(map, marker1);	
		map.setZoom(20);
	  	map.panTo(listLatLon);   		  	   
    }else{
    	alert("El viaje no tiene pocisión válida");
    }	
}

function fitBoundsToVisibleMarkers() {
	if(markers.length>0){
	    for (var i=0; i<markers.length; i++) {
			bounds.extend( markers[i].getPosition() );
	    }
	    if(markers.length==1){
			map.setZoom(13);
		  	map.panTo(markers[0].getPosition());
	    }else{
			map.fitBounds(bounds);
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

function editTravel(dataTavel){
	$('#loader1').show();
	$('#iFrameModaltravel').hide(); 
    $('#iFrameModaltravel').attr('src','/main/map/infotravel?catId='+dataTavel);
    $('#myModalTravel').modal('show');   
}

function closeWindow(){
    $('#myModalTravel').modal('hide'); 
    mapLoadData();
}

function closeWindowInc(){
	$('#myModalInc').modal('hide'); 
}

function closeWindowM(){
    $('#myModalManual').modal('hide'); 
    mapLoadData();	
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
        data: { catId : idObject, 
        		option : optionValue },
        success: function(data) { 
            var result = data.answer; 

            if(result=='started'){
            	$("#tittleMessage").html('Viaje iniciado');
				$("#divMessage").html('El viaje #'+idObject+" ha sido iniciado.");
            }else if(result=='stoped'){
            	$("#tittleMessage").html('Viaje Terminado');
				$("#divMessage").html('El viaje #'+idObject+" ha sido terminado.");            	
            }else{
            	$("#tittleMessage").html('Error');
				$("#divMessage").html('Ha ocurrido un error, favor de intentar mas tarde.');            	            	
            }
            $("#myModalOptions").modal('show');
        }
    }); 
}

function setIncidencia(dataTravel){
	$('#loader2').show();
	$('#iFrameModalinc').hide(); 
    $('#iFrameModalinc').attr('src','/main/map/setincidencia?catId='+dataTravel);
    $('#myModalInc').modal('show');   	
}

function setPositionManual(dataTravel){
	$('#loader3').show();
	$('#iFrameModaManual').hide(); 		
    $('#iFrameModaManual').attr('src','/main/map/manualpos?catId='+dataTravel);
    $('#myModalManual').modal('show');   		
}

function goToTrackSystem(idTravel){
	window.open("/main/map/external?catId="+idTravel ,'_blank');
}