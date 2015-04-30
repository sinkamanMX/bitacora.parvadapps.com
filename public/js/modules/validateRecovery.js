$( document ).ready(function() {
    $("#FormData").validate({
        rules: {
          inputUser     : "required"
        },
        messages: {
            inputUser   : "Campo Requerido"   
        },
        
        submitHandler: function(form) {            
            form.submit();  
        }
    });    
}); 