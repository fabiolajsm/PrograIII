<?php
require_once 'ManejadorArchivos.php';

class Cliente
{
    private $clientes;
    private $manejadorArchivos;
    private $archivoHoteles = './datos/hoteles.json';

    public function __construct()
    {
        $this->manejadorArchivos = new ManejadorArchivos($this->archivoHoteles);
        $this->clientes = $this->manejadorArchivos->leer();
    }

    public function getClienteById($id)
    {
        foreach ($this->clientes as $cliente) {
            if ($cliente['id'] == $id) {
                return $cliente;
            }
        }
        return null;
    }

    public function alta($datos)
    {
        $nombre = $datos["nombre"];
        $apellido = $datos["apellido"];
        $tipoDocumento = $datos["tipoDocumento"];
        $nroDocumento = $datos["nroDocumento"];
        $email = $datos["email"];
        $tipo = $datos["tipo"]; // individual o corporativo
        $pais = $datos["pais"];
        $ciudad = $datos["ciudad"];
        $telefono = $datos["telefono"];

        $noExisteCliente = true;
        $clienteID = 1;
        if (!empty($this->clientes)) {
            foreach ($this->clientes as $cliente) {
                if ($cliente["nombre"] == $nombre && $cliente["apellido"] == $apellido && $cliente["tipo"] == $tipo) {
                    $noExisteCliente = false;
                    return 'Error: Ya existe el cliente que quiere dar de alta.';
                }
            }
            $clienteID = intval(end($this->clientes)['id']) + 1;
        }
        $idFormateado = str_pad($clienteID, 6, '0', STR_PAD_LEFT); // formatear para que tenga 6 digitos
        if ($noExisteCliente) {
            $nuevoCliente = [
                "id" => $idFormateado,
                "nombre" => $nombre,
                "apellido" => $apellido,
                "tipoDocumento" => $tipoDocumento,
                "nroDocumento" => $nroDocumento,
                "email" => $email,
                "tipo" => $tipo,
                "pais" => $pais,
                "ciudad" => $ciudad,
                "telefono" => $telefono
            ];
            $this->clientes[] = $nuevoCliente;
        }

        if ($this->manejadorArchivos->guardar($this->clientes)) {
            $imagenID = $idFormateado . $tipo;
            $carpetaImagenes = './datos/ImagenesDeClientes/2023/';
            $rutaImagen = $carpetaImagenes . strtoupper($imagenID) . '.jpg';
            if ($this->manejadorArchivos->subirImagen($rutaImagen)) {
                return "Cliente registrado exitosamente.";
            } else {
                return "Cliente registrado exitosamente, pero hubo un problema al guardar la imagen.";
            }
        } else {
            return "Error: No se pudo registrar el cliente.";
        }
    }
    public function consultar($tipo, $nroCliente)
    {
        if (!empty($this->clientes)) {
            $encontrado = false;
            foreach ($this->clientes as $cliente) {
                if ($cliente["tipo"] == $tipo && $cliente["id"] == $nroCliente) {
                    $encontrado = true;
                    return "Pais: {$cliente['pais']}. Ciudad: {$cliente['ciudad']}. Teléfono: {$cliente['telefono']}";
                }
                if ($cliente["tipo"] !== $tipo && $cliente["id"] == $nroCliente) {
                    return "Tipo de cliente incorrecto";
                }
            }
            if (!$encontrado) {
                return "No existe la combinación de tipo y número de cliente.";
            }
        } else {
            return "No existen clientes registrados.";
        }
    }
    //revisar
    public function modificar($datos)
    {
        $numeroCliente = $datos["numeroCliente"];
        $tipo = $datos["tipo"];

        if (!empty($this->clientes)) {
            foreach ($this->clientes as $cliente) {
                if ($cliente["id"] == $numeroCliente && $cliente["tipo"] == $tipo) {
                    foreach ($datos as $key => $value) {
                        $cliente[$key] = $value;
                    }
                    return 'Modificacion exitosa.';
                }
            }
        }
        return 'Error: No existe el cliente que intenta modificar.';
    }
}
?>