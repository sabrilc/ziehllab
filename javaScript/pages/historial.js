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
          url: "/lab/historial/clientes",
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
	
	$('#user-id').val(suggestion.id);
	$('#user-identificacion').val(suggestion.identificacion);
	$('#user-sexo_id').val(suggestion.sexo_id).change();
	$('#user-edad').val(suggestion.edad);
	$('#user-unidad_tiempo').val(suggestion.unidad_tiempo).change();
	$('#user-nombres').val(suggestion.nombres).change();

	getAnalisis();
});

function getAnalisis(){
	 var form = $('#fmCliente');	   
	 var url = form.attr('action'); 
	    $.ajax({
	           type: "POST",
	           url: url,
	           data: form.serialize(),
	           success: function(data)
	           {
	        	   $('#content').empty();	
	        	   $('#content').html(data);	
	        	   
	        	 
	           } 
	        	   
	        	
	         });
}
function verHistorial(cliente,analisis){
	window.open('/historial/imprimir?cliente='+cliente+'&analisis='+analisis,'impresion','width=800px,height=800px');
}


