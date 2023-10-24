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
        if (empty($this->clientes)) {
            return null;
        }
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
    public function modificar($datos)
    {
        if (empty($this->clientes)) {
            return 'Error: No existen el clientes';
        }
        $numeroCliente = $datos["numeroCliente"];
        $tipo = $datos["tipo"];

        foreach ($this->clientes as &$cliente) {
            if ($cliente["id"] == $numeroCliente && $cliente["tipo"] == $tipo) {
                foreach ($datos as $key => $value) {
                    if ($key !== "numeroCliente" && $key !== "id") {
                        $cliente[$key] = $value;
                    }
                }
            }
        }

        if ($this->manejadorArchivos->guardar($this->clientes)) {
            return 'Modificacion exitosa.';
        } else {
            return 'Error: No existe el cliente que intenta modificar.';
        }
    }
    public function borrar($numeroCliente, $tipoCliente, $numeroDNI)
    {
        if (empty($this->clientes)) {
            return "No existen clientes";
        }
        $clienteEncontrado = $this->getClienteById($numeroCliente);

        if ($clienteEncontrado) {
            $palabraCompletaTipoCliente = "INDIVIDUAL";
            if ($tipoCliente != "INDI") {
                $palabraCompletaTipoCliente = "CORPORATIVO";
            }
            if ($clienteEncontrado['nroDocumento'] !== $numeroDNI || $clienteEncontrado['tipo'] !== strtolower($palabraCompletaTipoCliente)) {
                return 'Cliente no encontrado';
            }
            $idFormateado = str_pad($numeroCliente, 6, '0', STR_PAD_LEFT);
            $nombreDeLaImagenCliente = $idFormateado . $palabraCompletaTipoCliente;
            $imagenCliente = './datos/ImagenesDeClientes/2023/' . $nombreDeLaImagenCliente . '.jpg';
            $carpetaRespaldo = './ImagenesBackupClientes/2023/';

            $clienteEncontrado["borrado"] = true;
            foreach ($this->clientes as $clave => $reserva) {
                if ($clienteEncontrado['id'] == $numeroCliente) {
                    $this->clientes[$clave] = $clienteEncontrado;
                    $borradoExitoso = true;
                    break;
                }
            }
            if ($this->manejadorArchivos->guardar($this->clientes)) {
                if (file_exists($imagenCliente)) {
                    if (!file_exists($carpetaRespaldo)) {
                        mkdir($carpetaRespaldo, 0777, true);
                    }
                    $nuevaRuta = $carpetaRespaldo . strtoupper($numeroCliente) . 'ELIMINADO' . '.jpg';
                    if (rename($imagenCliente, $nuevaRuta)) {
                        return 'Cliente borrado exitosamente.';
                    }
                } else {
                    return "Cliente borrado exitosamente, pero hubo un problema al guardar la imagen";
                }
            } else {
                return 'Error al borrar Cliente';
            }
        } else {
            return "Error: Cliente no encontrado.";
        }
    }
}
?>