$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputDescripcion: "required",
            inputPrioridad	: "required",
            inputCorreo	    : "required",
            inputCosto      : "required",
            inputCextra     : {
                required: true,
                number: true
            },

            /*Si se requiere validar un campo de solo nùmeros
            precio: {
		      required: true,
		      number: true
		    },
			
			Si se requiere validar una fecha
			inputFechaFin: {
		      required: true,
		      date: true
		    },	
			Si se requiere validar un email
        	inputEmail	: {
		      required: true,
		      email: true
		    },		    	
		    */
        },
        
        // Se especifica el texto del mensaje a mostrar
        messages: {
            inputDescripcion: "Campo Requerido",
            inputPrioridad  : "Debe indicar la prioridad",
            inputCorreo     : "Debe indicar si genera correo",
            inputCosto      : "Debe seleccionar una opción",
            inputCextra     : {
                required: "Campo Requerido",
                number: "Este campo acepta solo números"
            }
            /* 
			precio		: {
			         required: "Campo Requerido",
			         number: "Este campo acepta solo números"
			}
			Opcion para la fecha            
			inputFechaFin: {
			         required: "Campo Requerido",
			         date: "Ingresar una fecha válida"
			}
			Opcion para el email
        	inputEmail	: {
		      required: "Campo Requerido",
		      email: "Debe de ingresar un mail válido"
		    }			
			*/
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });	

    $('.upperClass').keyup(function()
    {
        $(this).val($(this).val().toUpperCase());
    }); 

    var optionInit = $("#inputCostoExtra").val();
    if(optionInit==0){
        $("#divCost").hide('slow');
        $("#inputCextra").rules("remove", "required");
    }
});

function addCost(optionSelect){
    if(optionSelect==1){
        $("#divCost").show('slow');
        $("#inputCextra").rules("add",  {required:true});
    }else{
        $("#divCost").hide('slow');
        $("#inputCextra").rules("remove", "required");
    }
}

function backToMain(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}

function deleteRow(){	
	var idItem = $("#inputDelete").val();
    $.ajax({
        url: "/admin/carriers/getinfounit",
        type: "GET",
        dataType : 'json',
        data: { catId : idItem, 
        		optReg: 'delete'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'deleted'){
            	$("#modalConfirmDelete").modal('hide'); 
            }else if(result == 'problem'){
                alert("hubo problema");          
            }else{
                alert("no hay data");          
            }
        }
    });    
}
