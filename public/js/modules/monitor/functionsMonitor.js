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

    $('#iFrameSearch').on('load', function () {        
        $('#loader1').hide();
        $('#iFrameSearch').show();
    }); 

    $('#iFrameReasign').on('load', function () {        
        $('#loadeReasign').hide();
        $('#iFrameReasign').show();
    });         	
});	

function editTravel(dataTavel){
    $('#loader1').show();
    $('#iFrameModaltravel').hide(); 
    $('#iFrameModaltravel').attr('src','/monitor/map/infotravel?catId='+dataTavel);
    $('#myModalTravel').modal('show');   
}

function openSearch(idTravelAssign){
	$("#idTravel").val(idTravelAssign);
    $('#loader1').show();
    $('#iFrameSearch').hide();    
    $('#iFrameSearch').attr('src','/monitor/main/searchusers?mode=assign');
    $("#MyModalSearch").modal("show");
}

function openSearchReasign(idTravelAssign){
    $("#idTravelReassign").val(idTravelAssign);
    $('#loadeReasign').show();
    $('#iFrameReasign').hide().attr('src','/monitor/main/searchusers?mode=reasign');
    $("#MyModalReassign").modal("show");
}

function re_assignValue(idValue){
    $("#MyModalReassign").modal("hide");
    var catId = $("#idTravelReassign").val();
    $.ajax({
        url: "/monitor/main/assigntravel",
        type: "GET",
        dataType : 'json',
        data: { catId : catId ,
                userId: idValue,
                optReg: 'reassignMonitor'},
        success: function(data) {
            var result = data.answer; 
            if(result=='ok'){
                $("#tittleMessage").html('Viaje Asignado');
                $("#divMessage").html('El viaje #'+catId+" ha sido Re-asignado para monitorearlo.");                                
                location.reload();
            }else{
              alert("Ocurrio un error al asignar el viaje.");
            }
        }
    });
}

function assignValue(idValue){
	$("#MyModalSearch").modal("hide");
	var catId = $("#idTravel").val();
    $.ajax({
        url: "/monitor/main/assigntravel",
        type: "GET",
        dataType : 'json',
        data: { catId : catId ,
        		userId: idValue,
        		optReg: 'assignMonitor'},
        success: function(data) {
            var result = data.answer; 
            if(result=='ok'){
                $("#tittleMessage").html('Viaje Asignado');
                $("#divMessage").html('El viaje #'+catId+" ha sido asignado para monitorearlo.");                
            	location.reload();
            }else{
              alert("Ocurrio un error al asignar el viaje.");
            }
        }
    });
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
            }
            $("#myModalOptions").modal('show');
            location.href="/monitor/map/index?catId="+idObject;
        }
    }); 
}

function goToTrackSystem(idTravel){    
    window.open("/main/map/external?catId="+idTravel ,'_blank');
}