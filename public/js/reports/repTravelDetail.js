$( document ).ready(function() {
  /*$(".chosen-select").chosen({disable_search_threshold: 10});*/
  $('#tabs').tab();
  $('#TabsRecorrido').tab();

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
          inputOption:     "required"
        },
        messages: {
            inputOption:    "Campo Requerido"
        },
        
        submitHandler: function(form) {
            var error= 0;
            if($("#inputOption").val()>0){
                if($("#inputNoTravel").val()==""){
                  alert("Debe de ingresar el valor a buscar");    
                  error++;
                  return false;
                }
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
    "iDisplayLength": 10,      
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

