function calculatePrice(countElement,idElement,priceElement){
	var iTotalIncidencias 	= 0;
	var TotalElement = countElement*priceElement;

	$("#lbTotal"+idElement).html("$ "+TotalElement.toFixed(2));	
	$("#inputTot"+idElement).val(TotalElement);

	$( ".txtTotal" ).each(function( i ) {
		iTotalIncidencias = parseFloat(iTotalIncidencias) + parseFloat(this.value);
	});

	$("#lbTotal").html("$ "+parseFloat(iTotalIncidencias).toFixed(2));	
	$("#inputTotalInc").val(parseFloat(iTotalIncidencias));	
	/*
	var iTotalViaje 		= $("#itotalViaje").val();
	var iTotal 				= parseFloat(iTotalIncidencias)+parseFloat(iTotalViaje);

	$("#inputTotalInc").val(parseFloat(iTotalIncidencias));
	$("#inputTotalTravel").val(iTotal);

	$("#inputBigTotal").val(iTotal);
	$("#lbTotalBig").html("$ "+iTotal.toFixed(2));	*/
}