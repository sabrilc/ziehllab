<?php
namespace utils;

class Tools
{
    /**
     * Crea una carpeta si no existe, en la ruta especificada por la variable $carpeta
     * @param String $carpeta
     */
    public static function makeFolder($carpeta) {
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        };
    }
    
    
   public static function cedulaEsValida( $cedula ){
        
        $multiplicador = [2,1,2,1,2,1,2,1,2];
        $suma = 0;
        for ($i = 0; $i < strlen($cedula); $i++) {
            $num = $cedula[$i] * $multiplicador[$i];
            $suma = $num + $suma;          
        }
        
        if (($suma % 10 === 0) && ($suma > 0)) {
            return true;
        } else {   return false;  }
        
    }
    
    public static function imageToBase64($path) {
        if(@getimagesize( $path)<=0){ $path = __DIR__ . '/../media/imagen/defaults/no_image.jpg';}
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
        
      
    }
	
	public static function pdfToBase64($path) {
        if(filesize( $path)<=0){ $path = __DIR__ . '/../media/documents/pdf_no_encontrado.pdf';}
        $data = file_get_contents($path);
        return base64_encode($data);
        
      
    }

    public static function removeFile($path) {
		 try {
				if (file_exists($path)) {
					return unlink($path);
				}
		 }catch ( \Exception $e){}
    }

    public static function validP12File($path) {
        try {
            if ($cert_store = file_get_contents($path)) {  return true;}
        }catch ( \Exception $e){}


        return false;
    }
    
    
    
}

