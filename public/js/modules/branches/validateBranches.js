$().ready(function() {
    $("#FormData").validate({
        rules: {
            inputDescripcion    : "required", 
            inputCalle          : "required", 
            inputColonia        : "required", 
            inputMunicipio      : "required", 
            inputEstado         : "required", 
            inputCP             : "required", 
            inputEstatus        : "required"
        },
        messages: {
            inputDescripcion    : "Campo Requerido",     
            inputCalle          : "Campo Requerido",     
            inputColonia        : "Campo Requerido",      
            inputMunicipio      : "Campo Requerido",     
            inputEstado         : "Campo Requerido",     
            inputCP             : "Campo Requerido",     
            inputEstatus        : "Campo Requerido"
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
        url: "/admin/branches/getinfo",
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
