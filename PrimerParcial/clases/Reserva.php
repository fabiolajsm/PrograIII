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
        if (empty($this->reservas)) {
            return null;
        }
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
            try {
                $fechaEntradaObj = new DateTime($fechaEntrada);
                $fechaSalidaObj = new DateTime($fechaSalida);
            } catch (Exception $e) {
                return "Error: Las fechas proporcionadas no son válidas.";
            }
            $fechaEntradaFormateada = $fechaEntradaObj->format('d-m-Y');
            $fechaSalidaFormateada = $fechaSalidaObj->format('d-m-Y');

            $nuevaReserva = [
                "id" => $reservaID,
                "numeroCliente" => $numeroCliente,
                "tipoCliente" => $tipoCliente,
                "fechaEntrada" => $fechaEntradaFormateada,
                "fechaSalida" => $fechaSalidaFormateada,
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
    public function actualizarReserva($reservaActualizada)
    {
        if (empty($this->reservas)) {
            return "No existen reservas";
        }
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
        if (empty($this->reservas)) {
            return "No existen reservas";
        }
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
    function ajustar($datos)
    {
        if (empty($this->reservas)) {
            return "No existen reservas";
        }
        $idReserva = $datos['idReserva'];
        $motivo = $datos['motivo'];
        $ajuste = $datos['ajuste'];
        $reserva = $this->getReservaById($idReserva);

        if ($reserva) {
            $datosAjuste = [
                "idReserva" => $idReserva,
                "motivo" => $motivo,
                "ajuste" => $ajuste
            ];

            $archivoAjustes = './datos/ajustes.json';
            $manejadorAjustes = new ManejadorArchivos($archivoAjustes);
            $ajustes = $manejadorAjustes->leer();
            $ajustes[] = $datosAjuste;

            if ($manejadorAjustes->guardar($ajustes)) {
                $reserva["total"] = $reserva["total"] + $ajuste;
                if ($this->actualizarReserva($reserva)) {
                    return 'Ajuste registrado y reserva actualizada exitosamente.';
                } else {
                    return 'Ajuste registrado, pero error al actualizar la reserva.';
                }
            } else {
                return 'Error al guardar el ajuste.';
            }
        } else {
            return 'Error: La reserva no existe.';
        }
    }
    /**a- El total cancelado (importe) por tipo de cliente y fecha en un día en particular (se
envía por parámetro), si no se pasa fecha, se muestran las del día anterior.
b- El listado de cancelaciones para un cliente en particular.
c- El listado de cancelaciones entre dos fechas ordenado por fecha.
d- El listado de cancelaciones por tipo de cliente.
e- El listado de todas las operaciones (reservas y cancelaciones) por usuario.
f- El listado de Reservas por tipo de modalidad. */
    public function consultar($datos)
    {
        if (empty($this->reservas)) {
            return "No existen reservas";
        }
        $tipoHabitacion = $datos["tipoHabitacion"];
        $fechaReserva = $datos["fechaReserva"];
        $numeroCliente = $datos["numeroCliente"];
        $fechaDesde = $datos["fechaDesde"];
        $fechaHasta = $datos["fechaHasta"];

        $puntoA = 'A - Total de reservas(importe) por tipo de habitacion : ' . strval($this->getImporte($tipoHabitacion, $fechaReserva, false));
        $puntoB = 'B - Listado de reservas para cliente ' . $numeroCliente . ': ' . json_encode($this->getReservasByCliente($numeroCliente));
        $puntoC = 'C - Listado de reservas entre dos fechas ordenado por fecha: ' . json_encode($this->getReservasByFechas($fechaDesde, $fechaHasta));
        $puntoD = 'D - Listado de reservas por tipo de habitación: ' . json_encode($this->getReservasPorTipoHabitacion($tipoHabitacion));

        $puntoE = 'E - El total cancelado (importe) por tipo de cliente y fecha en un día en particular: ' . strval($this->getImporte($tipoHabitacion, $fechaReserva, true));
        $parteUno = $puntoA . "\n" . $puntoB . "\n" . $puntoC . "\n" . $puntoD . "\n";
        $parteDos = $puntoE . "\n";
        return $parteUno . $parteDos;
    }
    private function getImporte($tipoHabitacion, $fecha, $traerCancelados)
    {
        $totalImporte = 0;
        if (!empty($fecha) && !strtotime($fecha)) {
            return 'Error: Fecha de reserva inválida';
        }
        $hoy = date('Y-m-d'); // Obtener la fecha actual en formato 'YYYY-MM-DD'

        foreach ($this->reservas as $reserva) {
            if ($reserva['tipoHabitacion'] == $tipoHabitacion) {
                if (!$traerCancelados) {
                    if ($fecha == $reserva['fechaEntrada'] || $fecha == $reserva['fechaSalida']) {
                        $totalImporte += $reserva['total'];
                    }
                    if (empty($fecha)) {
                        if (strtotime($reserva['fechaEntrada']) < strtotime($hoy) || strtotime($reserva['fechaSalida']) < strtotime($hoy)) {
                            $totalImporte += $reserva['total'];
                        }
                    }
                } else {
                    if ($fecha == $reserva['fechaEntrada'] || $fecha == $reserva['fechaSalida'] && isset($reserva['cancelada'])) {
                        $totalImporte += $reserva['total'];
                    }
                    if (empty($fecha) && isset($reserva['cancelada'])) {
                        if (strtotime($reserva['fechaEntrada']) < strtotime($hoy) || strtotime($reserva['fechaSalida']) < strtotime($hoy)) {
                            $totalImporte += $reserva['total'];
                        }
                    }
                }
            }
        }
        return $totalImporte;
    }
    public function getReservasByCliente($numeroCliente)
    {
        $reservasBuscadas = [];
        foreach ($this->reservas as $reserva) {
            if ($reserva['numeroCliente'] == $numeroCliente) {
                $reservasBuscadas[] = $reserva;
            }
        }
        return $reservasBuscadas;
    }
    public function getReservasByTipoHabitacion($tipoHabitacion)
    {
        $reservasBuscadas = [];
        foreach ($this->reservas as $reserva) {
            if ($reserva['tipoHabitacion'] == $tipoHabitacion) {
                $reservasBuscadas[] = $reserva;
            }
        }
        return $reservasBuscadas;
    }
    public function ordenarPorFecha(&$reservas)
    {
        usort($reservas, function ($a, $b) {
            return strtotime($a['fechaEntrada']) - strtotime($b['fechaEntrada']);
        });
    }
    public function getReservasByFechas($fechaInicio, $fechaFin)
    {
        $reservasBuscadas = [];
        foreach ($this->reservas as $reserva) {
            if (($fechaInicio) <= $reserva['fechaEntrada'] && $fechaFin >= $reserva['fechaSalida']) {
                $reservasBuscadas[] = $reserva;
            }
        }
        $this->ordenarPorFecha($reservasBuscadas);
        return $reservasBuscadas;
    }
    public function getReservasPorTipoHabitacion($tipoHabitacion)
    {
        $reservasPorTipo = [];
        foreach ($this->reservas as $reserva) {
            if ($reserva['tipoHabitacion'] == $tipoHabitacion) {
                $reservasPorTipo[] = $reserva;
            }
        }
        return $reservasPorTipo;
    }
}
?>