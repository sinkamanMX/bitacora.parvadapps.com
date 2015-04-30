$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputPassword	: "required",
            inputNewPass	: "required",
            inputRepPass	: {
		      equalTo: "#inputNewPass"
		    }         
        },
        messages: {
            inputPassword	: "Campo Requerido",
            inputNewPass	: "Campo Requerido",
            inputRepPass	: {
				required	: "Campo Requerido",
			    equalTo		: "La contraseña no coincide."
			}
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });	
});