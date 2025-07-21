function verOrden(orden_id){
                 $.ajax({
                		  method: 'POST',
                          
                		  url: '/lab/medico/imprimir?orden_id='+orden_id,
                		  beforeSend: function( xhr ) { 
                			  $('#modal_content').html('<div class=\'col-12 text-center m-5 p-5 animated fadeIn\'><img src=\'/imagen/loading.svg\'><div>');
                		  }
                		}).done(function( data ) {
                			
                			$('#modal_content').html('<iframe  width="100%" height="800px" src= \'data:application/pdf;base64,' + data +'\' \></iframe>');


                		  });
                
                }









