$( document ).ready(function() {
    $('#iFrameModaltravel').on('load', function () {        
        $('#loader1').hide();
        $('#iFrameModaltravel').show();
    }); 

    $('#iFrameNewtravel').on('load', function () {        
        $('#loader2').hide();
        $('#iFrameNewtravel').show();
    });     

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

function infoTravel(idTravelInfo){
	$('#loader1').show();
	$('#iFrameModaltravel').hide();    
    $('#iFrameModaltravel').attr('src','/travels/main/infotravel?catId='+idTravelInfo);
    $('#myModalTravel').modal('show');   
}

function closeWindows(){
  $('#loader1').show();
  $('#iFrameModaltravel').hide();    
  $('#myModalTravel').modal('hide');  
  location.href="/travels/main/index"; 
}