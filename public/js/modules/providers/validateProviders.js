$().ready(function() {
    $("#FormData").validate({
        rules: {
            inputDescripcion   : "required",            
            inputUrl           : "required",            
            inputUsuario       : "required",            
            inputPassword      : "required",            
            inputComentario    : "required"
        },
        // Se especifica el texto del mensaje a mostrar
        messages: {
            inputDescripcion   : "Campo Requerido",   
            inputUrl           : "Campo Requerido",            
            inputUsuario       : "Campo Requerido",        
            inputPassword      : "Campo Requerido",         
            inputComentario    : "Campo Requerido"
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
        url: "/admin/providers/getinfo",
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
