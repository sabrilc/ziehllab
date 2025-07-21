
function aplicarDescuento(orden){

	
	swal("Ingrese el valor del descuento:", {
		  content: "input",
		}).then((value) => {
			if(value){ 
				
				$.ajax({
					  method: "POST",
					  url: "/lab/orden/descuento?orden_id="+orden.id,
					  data:{descuento: value }
					})
					  .done(function( data ) {
						  
						  if(data){
							  swal("Transacción realizada con Exito!.", 'El descuento ha sido aplicado con éxito.!' , "success").then((res)=>{  location.reload(); });
						  }
						  else{
							  swal("Transacción fallida!.", 'Intentelo otra vez' , "danger");
						  } 
					   
					  });	
			}
			
		});

}


function pagar(orden){
	swal("Esta seguro de pagar la orden # "+orden.codigo+"?", {
		  dangerMode: true,
		  buttons: true,
		}).then((value) => {
			if(value){ 
				
				$.ajax({
					  method: "POST",
					  url: "/lab/orden/pagar?orden_id="+orden.id,
					})
					  .done(function( data ) {
						  
						  if(data){
							  swal("Transacción realizada con Exito!.", 'La orden fue pagada con éxito.!' , "success").then((res)=>{  location.reload(); });
						  }
						  else{
							  swal("Transacción fallida!.", 'Intentelo otra vez' , "danger");
						  } 
					   
					  });	
			}
			
		});

}

function imprimirOrden(orden_id){
	

	window.open('/lab/orden/imprimir?id='+orden_id,'impresion','width=800px,height=800px');

	}


function finalizarOrden(orden_id){
	
	swal("Esta seguro de finanalizar la orden?", {
		  dangerMode: true,
		  buttons: true,
		}).then((value) => {
			if(value){

				$.ajax({
					  method: "POST",
					  url: "/lab/orden/finalizar?orden_id="+orden_id,
					  dataType :"json",
					  beforeSend: function( xhr ) {
						  JsLoadingOverlay.show({
							  "overlayBackgroundColor": "#2B2C2D",
							  "overlayOpacity": "0.8",
							  "spinnerIcon": "ball-climbing-dot",
							  "spinnerColor": "#000000",
							  "spinnerSize": "3x",
							  "overlayIDName": "overlay",
							  "spinnerIDName": "spinner",
							  "offsetX": 0,
							  "offsetY": 0,
							  "containerID": null,
							  "lockScroll": false,
							  "overlayZIndex": 9998,
							  "spinnerZIndex": 9999
							});
						  }
					})
					  .done(function( respuesta ) {
						  JsLoadingOverlay.hide();
						 // respuesta = JSON.parse(data);
						  if(respuesta.success != undefined){
							  swal("Transacción realizada con Exito!.", respuesta.success , "success").then((res)=>{  location.reload(); });
						  }
						  if(respuesta.warning != undefined){
							  swal("Advertencia!.", respuesta.warning , "warning").then((res)=>{  location.reload(); });
						  }
						  if(respuesta.error != undefined){
							  swal("Error!.", respuesta.error , "error");
						  }
						 
					   
					  })
					  .fail(function() {
						  JsLoadingOverlay.hide();
					  });	
			}
			
		});
	 

	
	}

function firmar(orden){
	swal("Esta seguro de firmar digitalmente  la orden # "+orden.codigo+"?", {
		dangerMode: true,
		buttons: true,
	}).then((value) => {
		if(value){

			$.ajax({
				method: "POST",
				url: "/lab/orden/firmar?orden_id="+orden.id,
				dataType: "json",
				beforeSend: function( xhr ) {
					document.getElementById("overlay").style.display = "block";
				}

			})
				.done(function( data ) {
					if(!data.errors ){
						swal("Transacción realizada con Exito!.", data.message , "success").then((res)=>{   });
					}
					else{
						swal("Error al firmar!.", data.message , "error");
					}

					document.getElementById("overlay").style.display = "none";

				});
		}

	});

}











