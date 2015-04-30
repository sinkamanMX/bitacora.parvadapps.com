$( document ).ready(function() {
  $("#FormData").validate({
        rules: { 
          inputIncidencia: "required",
          inputComentario: "required",
        },
        messages: {
            inputIncidencia:    "Campo Requerido",
            inputComentario:    "Campo Requerido",
        },
        
        submitHandler: function(form) {
            form.submit();  
        }
    });    

});