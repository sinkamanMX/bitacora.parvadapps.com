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

$( document ).ready(function() {    
    getStatusExt(); 
}); 

function getStatusExt(){ 
    $("#dTravelMonitor").html('<img src="/images/assets/loading.gif" alt="loading gif"/>');
    $.ajax({
        url: "/main/main/validatemonitor",
        type: "GET",
        dataType : 'json',
        data: { 
            typaction: 'validate'
        },
        success: function(data) {
            $("#dTravelMonitor").html('');
            var iTotalTravels =  0;
            var aTravels     = data.travels;
            if(data.answer=='pendings'){
                var table = $('<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered"></table>');
                table.append('<thead><tr><td>Clave Viaje</td><td>Empresa</td><td>Ult. Registro</td><td>Retardo</td><td></td></tr></thead>' );
                table.append('<tbody></tbody>');

                $.each(aTravels, function(idx, obj) {
                    var row = $('<tr></tr>');
                    row.append('<td>'+obj.CLAVE+'</td>');
                    row.append('<td>'+obj.NOMBRE+'</td>');
                    row.append('<td>'+obj.LAST_FECHA+'</td>');
                    row.append('<td>'+obj.MINUTOS_SIN_REPORTAR+'</td>');
                    row.append('<td class="text-center"><a href="/monitor/map/index?catId='+obj.ID_VIAJE+'">'+
                                '<button class="btn-info"> <i class="icon-info-sign icon-white"></i></button>'+
                                '</a></td>');

                    row.appendTo(table);
                    iTotalTravels = iTotalTravels+1;           
                });


                table.appendTo($("#dTravelMonitor"));
            }

            if(iTotalTravels>0){
                $("#lblTravelsPen").html(iTotalTravels);
                $("#spanTravelsPen").show("slow");
            }else{
                $("#lblTravelsPen").html("");
                $("#spanTravelsPen").hide("slow");
            }

            callTimer();
        }
    });
}

function callTimer(){                   
    var timeoutId = setTimeout(function(){   
      getStatusExt();   
    },30000);
}

function showTravelPen(){
    $("#mTravelMonitor").modal('show');
}