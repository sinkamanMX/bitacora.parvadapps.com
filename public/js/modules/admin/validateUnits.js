$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputEco	: "required",
            inputPlacas	: "required",
            inputIden	: "required",
            inputStatus	: "required",
            inputTransportista: "required",
            inputProveedor: "required",

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
            inputEco	: "Campo Requerido",
            inputPlacas	: "Campo Requerido",
            inputIden	: "Campo Requerido",
            inputStatus	: "Debe de seleccionar una opción",
            inputTransportista : "Debe de seleccionar una opción",
            inputProveedor: "Debe de seleccionar una opción"
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
});

function backToMain(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}

function deleteRow(){	
	var idItem = $("#inputDelete").val();
    $.ajax({
        url: "/admin/units/getinfo",
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
