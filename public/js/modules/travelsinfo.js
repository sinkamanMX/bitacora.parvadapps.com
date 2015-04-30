var geocoder;
var infoWindow;
var infoLocation;
var markers = [];
var bounds;
var arrayTravels=Array();
var map = null;

$( document ).ready(function() {
  /*$(".chosen-select").chosen({disable_search_threshold: 10});*/
    $('#tabs').tab();

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        var currentTab = $(e.target).text(); // get current tab
        var LastTab = $(e.relatedTarget).text(); // get last tab 
        console.log(currentTab);
        if(currentTab=='Mapa'){
          initMapa()
          google.maps.event.trigger(map, 'resize');
        }
    });   

/*
        var checkin = $('#inputFechaIn').datepicker({
          format: 'yyyy-mm-dd',
          onRender: function(date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
          }
        }).on('changeDate', function(ev) {
          if (ev.date.valueOf() > checkout.date.valueOf()) {
            var newDate = new Date(ev.date)
            newDate.setDate(newDate.getDate() + 1);
            checkout.setValue(newDate);
          }
          checkin.hide();
          $('#inputFechaFin').prop('disabled', false);
          $('#inputFechaFin')[0].focus();
        }).data('datepicker');
        var checkout = $('#inputFechaFin').datepicker({
          format: 'yyyy-mm-dd',
          onRender: function(date) {
            return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
          }
        }).on('changeDate', function(ev) {
          checkout.hide();
        }).data('datepicker');

        */
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var todayDay   = (nowTemp.getDate()<10) ? "0"+nowTemp.getDate(): nowTemp.getDate();        

    if($("#inputFechaIn").val()==""){
      $("#inputFechaIn").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay+ ' 00:00');      
    }

    if($("#inputFechaFin").val()==""){
      $("#inputFechaFin").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay+ ' 23:59');    
    }
    
    var checkin = $('#inputFechaIn').datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,
    }).on('changeDate', function(ev) {
      if(ev.date.valueOf() > $('#inputFechaFin').datetimepicker('getDate').valueOf()){
        $('#inputFechaFin').datetimepicker('setDate', ev.date);   
      }

      $('#inputFechaFin').datetimepicker('setStartDate', ev.date);      
      $('#inputFechaFin').prop('disabled', false);
      $('#inputFechaFin')[0].focus();      
    });

    var checkout = $('#inputFechaFin').datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: true
    }).on('changeDate', function(ev) {
      if(ev.date.valueOf() < $('#inputFechaIn').datetimepicker('getDate').valueOf()){
        $('#inputFechaIn').datetimepicker('setDate', ev.date);   
      }
      $('#inputFechaIn').datetimepicker('setEndDate', ev.date);
    });               

  $("#FormData").validate({
        rules: {
          inputFechaIn:     "required",
          inputFechaFin:    "required",
          inputNoTravel:    "required",
          inputDescripcion: "required",
          inputSucursal:    "required",
          inputCliente:     "required",
          inputTransportista: "required",
          inputUnidades:    "required",
          inputOperadores:  "required",
          inputMedioContacto: "required"
        },
        messages: {
            inputNoTravel:    "Campo Requerido",
            inputSucursal:    "Campo Requerido",
            inputDescripcion: "Campo Requerido",
            inputCliente:     "Debe de seleccionar una opción",
            inputTransportista: "Debe de seleccionar una opción",
            inputUnidades:    "Debe de seleccionar una opción",
            inputOperadores:  "Debe de seleccionar una opción",  
            inputFechaIn: "Campo Requerido",
            inputFechaFin: "Campo Requerido",
            inputMedioContacto: "Campo Requerido" 
        },
        
        submitHandler: function(form) {
            var error= 0;

            if($("#inputSucursal").val()=="" || $("#inputSucursal").val()=="-1"){
              alert("Debe de seleccionar una Sucursal");
              error++;
              return false;
            }

            if($("#inputCliente").val()==""  || $("#inputCliente").val()=="-1"){
             alert("Debe de seleccionar un Cliente"); 
             error++;
             return false;
            }

            if($("#inputTransportista").val()=="" || $("#inputTransportista").val()=="-1"){
              alert("Debe de seleccionar un Transportista");
              error++;
              return false;
            }

            if($("#inputUnidades").val()=="" || $("#inputUnidades").val()=="-1"){
              alert("Debe de seleccionar una Unidad");
              error++;
              return false;
            }        
            
            if($("#inputOperadores").val()=="" || $("#inputOperadores").val()=="-1"){
              alert("Debe de seleccionar un Operador");
              error++;
              return false;
            } 
            
            form.submit();  
            
        }
    });    

      $('#dataTable').dataTable( {
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "bDestroy": true,
        "bLengthChange": false,
        "bPaginate": true,
        "bFilter": true,
        "bSort": true,
        "bJQueryUI": true,
        "iDisplayLength": 5,      
        "bProcessing": true,
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

  $('#tableRecorrido').dataTable( {
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "bDestroy": true,
        "bLengthChange": false,
        "bPaginate": true,
        "bFilter": true,
        "bSort": true,
        "bJQueryUI": true,
        "iDisplayLength": 4,      
        "bProcessing": true,
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
});

function initMapa(){
  if(map==null){
    infoWindow = new google.maps.InfoWindow;
    var mapOptions = {
      zoom: 5,
      center: new google.maps.LatLng(24.52713, -104.41406),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById('MapRecorrido'),mapOptions);
    
    bounds = new google.maps.LatLngBounds();
    printTravelsMap();
  }
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