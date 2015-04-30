$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputName         : "required",
            inputApps         : "required",
            inputUser         : {
                required    : true,
                email       : true
            },              
            inputPerfil       : "required",
            inputPassword     : "required",
            inputPasswordC    : {
                required: true,
                equalTo: "#inputPassword",
            },
            inputStatus     : "required"
        },
        
        // Se especifica el texto del mensaje a mostrar
        messages: {
            inputName         : "Campo Requerido",
            inputApps         : "Campo Requerido",
            inputUser         : {
                required    : "Campo Requerido",
                email       : "Debe de ingresar un mail válido"
            },              
            inputPerfil       : "Campo Requerido",
            inputPassword     : "required",
            inputPasswordC    : {
                required    : "Campo Requerido",
                equalTo     : "La contraseña no coincide."
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

    var optionInit = $("#optReg").val();
    if(optionInit=='update'){
        $("#inputPassword").rules("remove", "required");
        $("#inputPasswordC").rules("remove", "required");
    }
});

function backToMain(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}

function deleteRow(){	
	var idItem = $("#inputDelete").val();
    $.ajax({
        url: "/admin/users/getinfo",
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
