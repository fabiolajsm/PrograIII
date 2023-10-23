<?php
require './clases/Reserva.php';

if (empty($_GET['tipoHabitacion']) || empty($_GET['numeroCliente']) || empty($_GET['fechaDesde']) || empty($_GET['fechaHasta'])) {
    echo "Error: tiene que ingresar todos los campos requeridos.";
    return;
}
if ($_GET['tipoHabitacion'] !== "Simple" && $_GET['tipoHabitacion'] !== "Doble" && $_GET['tipoHabitacion'] !== "Suite") {
    echo "Error: El Tipo de Habitacion debe ser 'Simple', 'Doble' o 'Suite'.";
    return;
}
if (!strtotime($_GET['fechaDesde']) || !strtotime($_GET['fechaHasta']) || strtotime($_GET['fechaDesde']) > strtotime($_GET['fechaHasta'])) {
    echo "Fechas de Desde y Hasta no válidas. Asegúrese de que sean fechas válidas y que la fecha Desde sea anterior a la fecha de Hasta. (formato: DD-MM-AAAA, ejemplo: 12-10-2023)";
    return;
}
$reserva = new Reserva();
$resultado = $reserva->consultar($_GET);
echo $resultado;
?>