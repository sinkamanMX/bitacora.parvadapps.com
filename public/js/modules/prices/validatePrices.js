$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputDescripcion: "required", 
            inputCosto      :             {
                required: true,
                number: true
            },
            inputCostoExtra :             {
                required: false,
                number: true
            },
            inputStatus     : "required"




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
            inputCosto      : {
                required: "Campo Requerido",
                number: "Este campo acepta solo números"
            },
            inputCostoExtra : {
                number: "Este campo acepta solo números"
            },            
            inputStatus     : "Campo Requerido"
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

function backToMain(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}

function deleteRow(){	
	var idItem = $("#inputDelete").val();
    $.ajax({
        url: "/admin/prices/getinfo",
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
