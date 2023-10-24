<?php
require './clases/Cliente.php';

if (empty($_POST['numeroCliente']) || empty($_POST['modalidadDePago']) || empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['tipoDocumento']) || empty($_POST['nroDocumento']) || empty($_POST['email']) || empty($_POST['tipo']) || empty($_POST['pais']) || empty($_POST['ciudad']) || empty($_POST['telefono'])) {
    echo "Error: tiene que ingresar todos los campos requeridos.";
    return;
}
if ($_POST['modalidadDePago'] !== "Efectivo" && $_POST['modalidadDePago'] !== "Tarjeta") {
    echo "Error: La modalidad de pago debe ser con 'Efectivo' o 'Tarjeta'.";
    return;
}
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo "Error: La dirección de correo electrónico no es válida.";
    return;
}
if ($_POST['tipo'] !== "INDI" && $_POST['tipo'] !== "CORP") {
    echo "Error: El Tipo de Cliente debe ser 'INDI' o 'CORP'.";
    return;
}
$tipoDeDocumento = $_POST['tipoDocumento'];
if ($tipoDeDocumento !== "DNI" && $tipoDeDocumento !== "LE" && $tipoDeDocumento !== "LC" && $tipoDeDocumento !== "PASAPORTE") {
    echo "Error: El Tipo de docuemento debe ser DNI, LE, LC, o PASAPORTE.";
    return;
}
if (empty($_FILES['imagen']['tmp_name'])) {
    echo "Error: Debe proporcionar una imagen válida.";
    return;
}
$cliente = new Cliente();
$resultado = $cliente->alta($_POST);
echo $resultado;
?>