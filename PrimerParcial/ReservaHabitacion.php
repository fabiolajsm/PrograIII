<?php
require './clases/Reserva.php';

if (empty($_POST['tipoCliente']) || empty($_POST['numeroCliente']) || empty($_POST['fechaEntrada']) || empty($_POST['fechaSalida']) || empty($_POST['tipoHabitacion']) || empty($_POST['total'])) {
    echo "Error: tiene que ingresar todos los campos requeridos.";
    return;
}
if ($_POST['tipoCliente'] !== "individual" && $_POST['tipoCliente'] !== "corporativo") {
    echo "Error: El Tipo de Cliente debe ser 'individual' o 'corporativo'.";
    return;
}
if ($_POST['tipoHabitacion'] !== "Simple" && $_POST['tipoHabitacion'] !== "Doble" && $_POST['tipoHabitacion'] !== "Suite") {
    echo "Error: El Tipo de Habitacion debe ser 'Simple', 'Doble' o 'Suite'.";
    return;
}
if (!strtotime($_POST['fechaEntrada']) || !strtotime($_POST['fechaSalida']) || strtotime($_POST['fechaEntrada']) > strtotime($_POST['fechaSalida'])) {
    echo "Fechas de entrada y salida no válidas. Asegúrese de que sean fechas válidas y que la fecha de entrada sea anterior a la fecha de salida. (formato: DD-MM-AAAA, ejemplo: 12-10-2023)";
    return;
}
if (!is_numeric($_POST['total']) || $_POST['total'] <= 0) {
    echo "El campo Total debe ser un número mayor a 0.";
    return;
}
$reserva = new Reserva();
$resultado = $reserva->reservarHabitacion($_POST);
echo $resultado;
?>