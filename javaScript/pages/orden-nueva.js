$('.typeahead').typeahead(null, {
  name: 'equipos',
  limit: 10,
  hint: true,
  highlight: true,
  minLength: 1,
  classNames: {
      menu: 'dropdown-menu',
      suggestion: 'dropdown-item',
      cursor: 'bgc-yellow-m2'
  },
 
  async: true,
  name: 'id', 
  displayKey: '_descripcion',
  source: function (query, processSync,callback) {
	
      $.ajax({
          url: "/lab/orden/clientes",
          data: {query:query},            
          dataType: "json",
          type: "POST",
          success: function (data) {
        	  callback($.map(data, function (item) {
            	
                  return item;
                  
              }));
          }
      });
  },
  
  
});

$('.typeahead').bind('typeahead:select', function(ev, suggestion) {	
	
	$('#orden-paciente_id').val(suggestion.id);
	$('#user-id').val(suggestion.id);
	$('#user-identificacion').val(suggestion.identificacion);
	$('#user-sexo_id').val(suggestion.sexo_id).change();
	$('#user-edad').val(suggestion.edad);
	$('#user-unidad_tiempo').val(suggestion.unidad_tiempo).change();
	
	
	$('#user-username').val(suggestion.username);
	$('#user-nombres').val(suggestion.nombres).change();
	$('#user-email').val(suggestion.email);
	$('#user-email_notificacion').val(suggestion.email_notificacion);
	
	$('#user-direccion').val(suggestion.direccion);
	$('#user-telefono').val(suggestion.telefono);
	if(suggestion.activo == 1 ){$('#user-activo').prop('checked', true);}
	else{$('#user-activo').prop('checked', false);}
});

function guardarCliente(){
	 var form = $('#fmAddCliente');	   
	    var url = form.attr('action'); 
	    $.ajax({
	           type: "POST",
	           url: url,
	           data: form.serialize(),
	           success: function(data)
	           {
	        	   var html = "";
	        	   $.each(data.errors, function(key, val) {
	        		html += ` ${val} \n`;
	  	    	     });
	        	   if( html != ""){
	        		   swal("Transacción fallida!.",html , "danger");
	        		}
	        	  
	        	   if(data.success==true){
	        		   swal("Transaccion realizada", "El registro de cliente ha sido actualizado con éxito!.", "success").then((res)=>{    form.trigger("reset"); });

	        	   }
	        	   
	        	 
	           } 
	        	   
	        	
	         });
}