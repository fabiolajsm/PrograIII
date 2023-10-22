<?php
require './clases/Reserva.php';

if (empty($_POST['motivo']) || empty($_POST['idReserva'])) {
    echo "Error: tiene que ingresar todos los campos requeridos.";
    return;
}
$reserva = new Reserva();
$resultado = $reserva->ajustar($_POST);
echo $resultado;
?>