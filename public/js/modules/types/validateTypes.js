$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputDescripcion : "required", 
            inputPrecio      : "required",  
            inputFrecuencia  : "required", 
            inputPrioridad   : "required", 
            inputStatus      : "required"
        },
        
        // Se especifica el texto del mensaje a mostrar
        messages: {
            inputDescripcion : "Campo Requerido",         
            inputPrecio      : "Campo Requerido",         
            inputFrecuencia  : "Campo Requerido",         
            inputPrioridad   : "Campo Requerido",         
            inputStatus      : "Campo Requerido"
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
        url: "/admin/types/getinfo",
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
