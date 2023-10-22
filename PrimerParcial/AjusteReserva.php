<?php
require './clases/Reserva.php';

$reserva = new Reserva();

if (empty($_POST['motivo']) || empty($_POST['idReserva'])) {
    echo "Error: tiene que ingresar todos los campos requeridos.";
    return;
}
$resultado = $reserva->ajustar($_POST);
echo $resultado;
?>