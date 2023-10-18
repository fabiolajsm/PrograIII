<?php
class ManejadorArchivos {
    private $urlArchivo;

    public function __construct($urlArchivo) {
        $this->urlArchivo = $urlArchivo; 
    }

    public function leer(){
        if (file_exists($this->urlArchivo)) {
            $jsonData = file_get_contents($this->urlArchivo); 
            return json_decode($jsonData, true);
        } else {
            return [];
        }
    }

    public function guardar($data) {
        $jsonData = json_encode($data);
        return file_put_contents($this->urlArchivo, $jsonData);
    }

    public function subirImagen($rutaImagen) {
        $directorio = dirname($rutaImagen); 
        if (!file_exists($directorio)) {
            // Si el directorio no existe, créalo
            if (!mkdir($directorio, 0777, true)) {
                return false; // No se pudo crear el directorio
            }
        }
        if (isset($_FILES['imagen']) && move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
            return true;
        } else {
            return false;
        }
    }    
}
?>
