$( document ).ready(function() {
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
          inputFechaIn      : "required",
          inputFechaFin     : "required",
          inputNoTravel     : "required",
          inputDescripcion  : "required",
          inputSucursal     : "required",
          inputCliente      : "required",
          inputTransportista: "required",
          inputUnidades     : "required",
          inputOperadores   : "required",
          inputRuta         : "required",
          inputTviaje       : "required",
          inputMedioContacto: "required"
        },
        messages: {
            inputNoTravel   : "Campo Requerido",
            inputSucursal   : "Campo Requerido",
            inputDescripcion: "Campo Requerido",
            inputCliente    : "Debe de seleccionar una opción",
            inputTransportista: "Debe de seleccionar una opción",
            inputUnidades   : "Debe de seleccionar una opción",
            inputOperadores : "Debe de seleccionar una opción",  
            inputFechaIn    : "Campo Requerido",
            inputFechaFin   : "Campo Requerido",
            inputRuta       : "Campo Requerido",
            inputTviaje     : "Campo Requerido",
          inputMedioContacto: "Campo Requerido" 
        },
        
        submitHandler: function(form) {            
            form.submit();  
        }
    });    
}); 

function getoptionsCbo(idCboTo,classObject,idObject,chosen){    
    $("#div"+idCboTo).html('<img src="/images/assets/loading.gif" alt="loading gif"/>');
    var classChosen = (chosen) ? 'chosen-select': '';

    $.ajax({
        url: "/main/dashboard/getselect",
        type: "GET",
        data: { catId : idObject, 
                oprDb : classObject },
        success: function(data) { 
            $("#div"+idCboTo).html("");
            var dataCbo = '<select class="m-wrap '+classChosen+'" id="input'+idCboTo+'" name="input'+idCboTo+'">';
            if(data!="no-info"){
                dataCbo += '<option value="">Seleccionar una opción</option>'+data+'</select>';
            }else{
                dataCbo += '<option value="">Sin Información</option>';
            }
            dataCbo += '</select>';
                                    
            $("#div"+idCboTo).html(dataCbo);
            $(".chosen-select").chosen({disable_search_threshold: 10});
        }
    });     
}

function getPrecios(idTipoViaje){
    $("#divPrecios").hide('slow');
    $.ajax({
        url: "/travels/main/getprecios",
        type: "GET",
        dataType : 'json',
        data: { catId : idTipoViaje, 
                oprDb : 'optPrecios' },
        success: function(data) { 
            var result = data.answer; 
            var precio     = Math.round(result.COSTO,2)
            var costoExtra = Math.round(result.COSTO_EXTRA,2)
            $("#divPrecios").show('slow');
            $("#lblPrecio").val("$ "+ precio);
            $("#lblCostoExtra").val("$ "+ costoExtra);
        }
    });
}
