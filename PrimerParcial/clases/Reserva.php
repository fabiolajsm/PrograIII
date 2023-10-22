<?php
require 'ManejadorArchivos.php';
require './clases/Cliente.php';

class Reserva
{
    private $reservas;
    private $manejadorArchivos;
    private $archivo = './datos/reservas.json';

    public function __construct()
    {
        $this->manejadorArchivos = new ManejadorArchivos($this->archivo);
        $this->reservas = $this->manejadorArchivos->leer();
    }

    public function getReservaById($id)
    {
        foreach ($this->reservas as $reserva) {
            if ($reserva['id'] == $id) {
                return $reserva;
            }
        }
        return null;
    }
    public function reservarHabitacion($datos)
    {
        $numeroCliente = $datos["numeroCliente"];
        $tipoCliente = $datos["tipoCliente"];
        $fechaEntrada = $datos["fechaEntrada"];
        $fechaSalida = $datos["fechaSalida"];
        $tipoHabitacion = $datos["tipoHabitacion"];
        $total = $datos["total"];
        $cliente = new Cliente();

        if ($cliente->getClienteById($numeroCliente)) {
            $reservaID = 1;
            if (!empty($this->reservas)) {
                $reservaID = intval(end($this->reservas)['id']) + 1;
            }
            $nuevaReserva = [
                "id" => $reservaID,
                "numeroCliente" => $numeroCliente,
                "tipoCliente" => $tipoCliente,
                "fechaEntrada" => $fechaEntrada,
                "fechaSalida" => $fechaSalida,
                "tipoHabitacion" => $tipoHabitacion,
                "total" => $total,
            ];
            $this->reservas[] = $nuevaReserva;

            if ($this->manejadorArchivos->guardar($this->reservas)) {
                $imagenOrigen = 'reservaExitosa.jpg';
                $carpetaDestino = './datos/ImagenesDeReservas2023';
                $nuevoNombre = $tipoCliente . strval($numeroCliente) . strval($reservaID) . ".jpg";
                $rutaCompletaDestino = $carpetaDestino . '/' . strtoupper($nuevoNombre);
                if (!file_exists($carpetaDestino)) {
                    mkdir($carpetaDestino, 0777, true);
                }
                if (copy($imagenOrigen, $rutaCompletaDestino)) {
                    return "Reserva registrada exitosamente.";
                } else {
                    return "Reserva registrada exitosamente, pero hubo un problema al guardar la imagen.";
                }
            } else {
                return "Error: No se pudo registrar la reserva.";
            }
        } else {
            return "Error: El cliente no existe";
        }
    }
    /**6- CancelarReserva.php: (por POST) se recibe el Tipo de Cliente, Nro de Cliente, y el Id de Reserva a cancelar.
     *  Si el cliente existe en hoteles.json y la reserva en reservas.json, 
     * se marca como cancelada en el registro de reservas. Si el cliente o la reserva no existen, informar el tipo de error.  */
    public function actualizarReserva($reservaActualizada)
    {
        $idReserva = $reservaActualizada['id'];
        foreach ($this->reservas as $clave => $reserva) {
            if ($reserva['id'] == $idReserva) {
                $this->reservas[$clave] = $reservaActualizada;
                break;
            }
        }
        if ($this->manejadorArchivos->guardar($this->reservas)) {
            return true;
        } else {
            return false;
        }
    }

    function cancelar($datos)
    {
        $numeroCliente = $datos["numeroCliente"];
        $idReserva = $datos["idReserva"];
        $tipoCliente = $datos["tipoCliente"];

        $cliente = new Cliente();
        $reserva = $this->getReservaById($idReserva);
        $clienteIngresado = $cliente->getClienteById($numeroCliente);

        if ($clienteIngresado && $clienteIngresado["tipo"] == $tipoCliente && $reserva) {
            $reserva["cancelada"] = true;
            if ($this->actualizarReserva($reserva)) {
                return 'Reserva cancelada con éxito.';
            } else {
                return 'Error al cancelar la reserva';
            }
        } else {
            return 'Error: Cliente o reserva no encontrados.';
        }
    }
}
?>