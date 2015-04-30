$( document ).ready(function() {
  var input = (document.getElementById('inputDir'));
  var autocomplete = new google.maps.places.Autocomplete(input); 
  google.maps.event.addListener(autocomplete, 'place_changed', function() {     
      var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }
    });

  $("#FormData").validate({
        rules: { 
          inputLatitud: {
            required: true,
            number: true
          },
          inputLongitud: {
            required: true,
            number: true
          },
          inputAngulo: {
            required: true,
            number: true
          },
          inputVelocidad: {
            required: true,
            number: true
          },
          inputDir: "required",
          inputObservaciones  : "required",
          inputFecha: "required",            
        },
        messages: {
            inputDir       :  "Campo Requerido",
            inputObservaciones:  "Campo Requerido",
            inputLatitud    : {
                     required: "Campo Requerido",
                     number: "Este campo acepta solo números"
            },     
            inputLongitud    : {
                     required: "Campo Requerido",
                     number: "Este campo acepta solo números"
            },
            inputAngulo    : {
                     required: "Campo Requerido",
                     number: "Este campo acepta solo números"
            },
            inputVelocidad    : {
                     required: "Campo Requerido",
                     number: "Este campo acepta solo números"
            },
            inputFecha: "Campo Requerido",                                  
        },
        
        submitHandler: function(form) {
            form.submit();  
        }
    });    

    $('.number').keypress(function(event) {
        /*if (event.which != 46 && (event.which < 47 || event.which > 59))
        {
            event.preventDefault();
            if ((event.which == 46) && ($(this).indexOf('.') != -1)) {
                event.preventDefault();
            }
        }*/
    }); 
    $('#inputFecha').datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: true
    });    

    $('.noEnterSubmit').keypress(function(e){
        if ( e.which == 13 ) return false;
        //or...
        if ( e.which == 13 ) e.preventDefault();
    });    
});

  function getPosition(){
    $("#btnGetPosition").hide('slow');    
    var catId = $("#catId").val();

    $.ajax({
        url: "/main/map/getpositionlog",
        type: "GET",
        dataType : 'json',
        data: { catId: catId },
        success: function(data) {
            var result = data.answer; 
            if(result=='ok'){
              var oPosition = data.dataPos; 
              var sLatitud  = oPosition.fLatitude;
              var sLongitud = oPosition.fLongitude;
              
              $("#inputFecha").val(oPosition.sFechaServer);
              $("#inputLatitud").val(sLatitud);
              $("#inputLongitud").val(sLongitud);
              $("#inputAngulo").val(oPosition.iAngle);
              $("#inputVelocidad").val(oPosition.iVelocidad);
              $("#inputDir").val(oPosition.sLocation);
              $("#inputObservaciones").html('Posicion obtenida de grupo UDA');
              enabledAll();
            }else if(result=='login'){
              alert("Existe un problema con el usuario y/o contraseña para conectarse al sistema de Ovision Favor de verificarlo con el supervisor.");
              enabledAll();
            }else{
              alert("La unidad no tiene pocisión válida");
            }
        }
    });
  }

  function enabledAll(){
      $("#inputFecha").prop( "disabled", false );      
      $("#inputLatitud").prop( "disabled", false );      
      $("#inputLongitud").prop( "disabled", false );      
      $("#inputAngulo").prop( "disabled", false );      
      $("#inputVelocidad").prop( "disabled", false );      
      $("#inputDir").prop( "disabled", false );      
  }