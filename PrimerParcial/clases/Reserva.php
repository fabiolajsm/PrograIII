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
    function ajustar($datos)
    {
        $idReserva = $datos['idReserva'];
        $motivo = $datos['motivo'];
        $reserva = $this->getReservaById($idReserva);

        if ($reserva) {
            $ajuste = [
                "idReserva" => $idReserva,
                "motivo" => $motivo
            ];

            $archivoAjustes = './datos/ajustes.json';
            $manejadorAjustes = new ManejadorArchivos($archivoAjustes);
            $ajustes = $manejadorAjustes->leer();
            $ajustes[] = $ajuste;

            if ($manejadorAjustes->guardar($ajustes)) {
                $reserva["ajustada"] = true;
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
/**a- El total de reservas (importe) por tipo de habitación 
 * y fecha en un día en particular (se envía por parámetro), 
 * si no se pasa fecha, se muestran las del día anterior. 
 * b- El listado de reservas para un cliente en particular. 
 * c- El listado de reservas entre dos fechas ordenado por fecha. 
 * d- El listado de reservas por tipo de habitación. 
 */
    public function consultar($datos){
        $tipoHabitacion = $datos["tipoHabitacion"];
        $fechaReserva = $datos["fechaReserva"];
        if (!empty($_GET['fechaReserva']) && !strtotime($_GET['fechaReserva'])){
            return 'Error: Fecha de reserva invalida';
        } else {
            return 'A - Total de reservas(importe) por tipo de habitacion : ' . strval($this->getImporte($tipoHabitacion, $fechaReserva));
        }
    }
    private function getImporte($tipoHabitacion, $fecha)
    {
        $totalImporte = 0;
        foreach ($this->reservas as $reserva) {
            if ($reserva['tipoHabitacion'] == $tipoHabitacion) {
                if ($fecha == $reserva['fechaEntrada'] || $fecha == $reserva['fechaSalida']) {
                    $totalImporte += $reserva['total'];
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
}
?>