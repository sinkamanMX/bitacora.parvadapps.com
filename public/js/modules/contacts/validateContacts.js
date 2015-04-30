$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputName       : "required", 
            inputEmail      : {
                  required: true,
                  email: true
            },
            inputStatus     : "required"
        },
        messages: {
            inputName       : "Campo Requerido",      
            inputEmail      : {
              required: "Campo Requerido",
              email: "Debe de ingresar un mail válido"
            },
            inputStatus     : "Campo Requerido"  
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
        url: "/admin/contacts/getinfo",
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
