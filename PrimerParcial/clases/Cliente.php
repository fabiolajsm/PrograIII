<?php
require 'ManejadorArchivos.php';

class Cliente {
    private $clientes;
    private $manejadorArchivos;
    private $archivoHoteles = './datos/hoteles.json';

    public function __construct() {
        $this->manejadorArchivos = new ManejadorArchivos($this->archivoHoteles);
        $this->clientes = $this->manejadorArchivos->leer();
    }

    public function alta($datos) {
        $nombre = $datos["nombre"];
        $apellido = $datos["apellido"];
        $tipoDocumento = $datos["tipoDocumento"];
        $nroDocumento = $datos["nroDocumento"];
        $email = $datos["email"];
        $tipoCliente = $datos["tipoCliente"]; // individual o corporativo
        $pais = $datos["pais"];
        $ciudad = $datos["ciudad"];
        $telefono = $datos["telefono"];

        $clienteExistente = null;
        if (!empty($this->clientes)) {
            foreach ($this->clientes as &$cliente) {
                if ($cliente["nombre"] == $nombre && $cliente["tipoCliente"] == $tipoCliente) {
                    $clienteExistente = $cliente;
                    $cliente["nombre"] = $nombre;          
                    $cliente["apellido"] = $apellido;
                    $cliente["tipoDocumento"] = $tipoDocumento;          
                    $cliente["nroDocumento"] = $nroDocumento;          
                    $cliente["email"] = $email;          
                    $cliente["pais"] = $pais;          
                    $cliente["ciudad"] = $ciudad;          
                    $cliente["telefono"] = $telefono;
                    break;
                }
            } 
        }
        $clienteID = $clienteExistente !== null ? $clienteExistente["id"] : mt_rand(100000, 999999);

        if (!$clienteExistente) {
            $nuevoCliente = [
                "id" => $clienteID,
                "nombre" => $nombre,
                "apellido" => $apellido,
                "tipoDocumento" => $tipoDocumento,
                "nroDocumento" => $nroDocumento,
                "email" => $email,
                "tipoCliente" => $tipoCliente,
                "pais" => $pais,
                "ciudad" => $ciudad,
                "telefono" => $telefono
            ];
            $this->clientes[] = $nuevoCliente;
        }

        if($this->manejadorArchivos->guardar($this->clientes)){
            $imagenID = strval($clienteID) . $tipoCliente;
            $carpetaImagenes = './datos/ImagenesDeClientes/2023/';
            $rutaImagen = $carpetaImagenes . strtoupper($imagenID) . '.jpg';
            if($this->manejadorArchivos->subirImagen($rutaImagen)){
                return "Cliente registrado/actualizado exitosamente.";
            } else {
                return "Cliente registrado/actualizado exitosamente, pero hubo un problema al guardar la imagen.";
            }
        } else {
            return "Error: No se pudo registrar u actualizar el cliente.";
        }
    }
}
?>
