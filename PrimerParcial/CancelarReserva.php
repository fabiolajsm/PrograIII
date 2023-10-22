<?php
require './clases/Reserva.php';

if (empty($_POST['tipoCliente']) || empty($_POST['numeroCliente']) || empty($_POST['idReserva'])) {
    echo "Error: tiene que ingresar todos los campos requeridos.";
    return;
}
if ($_POST['tipoCliente'] !== "individual" && $_POST['tipoCliente'] !== "corporativo") {
    echo "Error: El Tipo de Cliente debe ser 'individual' o 'corporativo'.";
    return;
}
$reserva = new Reserva();
$resultado = $reserva->cancelar($_POST);
echo $resultado;
?>