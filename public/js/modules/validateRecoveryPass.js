$().ready(function() {
    $("#FormData").validate({
        rules: {
            inputPassword   : "required",
            inputCpassword  : {
                required: true,
                equalTo: "#inputPassword",
            }  
        },
        messages: {
            inputPassword   : "Campo Requerido",
            inputCpassword  : {
                required    : "Campo Requerido",
                equalTo     : "La contraseña no coincide."
            },                                         
        },
        submitHandler: function(form) {
            form.submit();
        }
    });     
});

