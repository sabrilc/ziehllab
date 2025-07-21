function cargarExamenes(){
	 var orden_id =$('select[id=cbox_orden]').val();
	 
	 $.ajax({
		  method: "POST",
		  url: "/lab/orden/examenes?orden_id="+orden_id,
		  beforeSend: function( xhr ) { 
			  $('#examenes').html("<div class='col-12 text-center m-5 p-5 animated fadeIn'><img src='/imagen/loading.svg'><div>");
		  }
		})
		  .done(function( data ) {
		   $('#examenes').html(data);
		  });
	}

function cargarPlantillaExamen(examen_id){
 
	 $.ajax({
		  method: "POST",
		  url: "/lab/orden/examen-plantilla?examen_id="+examen_id,
		  beforeSend: function( xhr ) { setLoadind(); }
		})
		  .done(function( data ) {
		   $('#area_de_trabajo').html(data);
		  });
	 
	 
	}



function setLoadind(){
	$('#area_de_trabajo').html("<div class='col-12 text-center m-5 p-5 animated fadeIn'><img src='/imagen/loading.svg'><div>");
}

function guardarInfoOrden(){
	$.ajax({
		  method: "POST",
		  url: "/lab/orden/guardar-info",
		  data: $('#formularioOrden').serialize(),
		  beforeSend: function( xhr ) { setLoadind(); }
		}).done(function( data ) {
			  cargarExamenes();
		  });
	
	
	return false;
}

function guardarResultado(){
	 
	 $.ajax({
		  method: "POST",
		  url: "/lab/orden/guardar-resultados",
		  data: $('#formularioResultados').serialize(),
		  beforeSend: function( xhr ) { setLoadind(); }
		})
		  .done(function( data ) {
		  $('#area_de_trabajo').html(data);
		  swal("Transacción realizada con Exito!.", " Resultados guardados con éxito!", "success");
		  });
	
	
	return false;
}
	


function plantillaPruebaSensibilida(germen_id){
	 $.ajax({
		  method: "POST",
		  url: "/lab/orden/prueba-sensibilidad-examen-germen?examen_germen_id="+germen_id,
		  data: $('#form-add-germen').serialize()
		})
		  .done(function( data ) {		  
			  $('#content_germen'+germen_id).html(data);
		  });	

	
}

function guardarPruebaSensiblidad(germen_id){
 
	 $.ajax({
		  method: "POST",
		  url: "/lab/orden/guardar-prueba-sensiblidad",
		  data: $('#formularioSensiblidad'+germen_id).serialize(),
		  beforeSend: function( xhr ) {
			  $('#content_germen').html("<div class='col-12 text-center m-5 p-5 animated fadeIn'><img src='/imagen/loading.svg'><div>");
			  }
		})
		  .done(function( data ) {		 	 
		   plantillaPruebaSensibilida(germen_id);	
		   swal("Transacción realizada con Exito!.", " Se guardo la prueba de sensiblidad con éxito!", "success");
		  });	
	 
	 
	return false;
}
function guardarGermen(){
	 $('#modalGermen').modal('hide');	 
	 $.ajax({
		  method: "POST",
		  url: "/lab/orden/guardar-germen",
		  data: $('#form-add-germen').serialize(),
		  beforeSend: function( xhr ) { setLoadind(); }
		})
		  .done(function( data ) {		
		    cargarPlantillaExamen( $('#md_germen_id').val() );		  
		  });	

	return false;
}

function borrarGermen(){
 $('#borrarGermenModal').modal('hide');	
 $.ajax({
		  method: "POST",
		  url: "/lab/orden/borrar-germen",
		  data:  $("#formularioBorrarGermen").serialize(),
		  beforeSend: function( xhr ) { setLoadind(); }
		})
		  .done(function( data ) {			
			  cargarPlantillaExamen( $("#mdBorrar_germen_examen_id").val());
			  swal("Transacción realizada con Exito!.", " Germen Borrado!", "success");
		   
		  });	

	return false;
}
