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
                $rutaCompletaDestino = $carpetaDestino . $nuevoNombre;

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
}
?>