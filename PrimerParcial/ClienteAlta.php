<?php
require './clases/Cliente.php';

$clienteAlta = new Cliente();

if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['tipoDocumento']) || empty($_POST['nroDocumento']) || empty($_POST['email']) || empty($_POST['tipoCliente']) || empty($_POST['pais']) || empty($_POST['ciudad']) || empty($_POST['telefono'])) {
    echo "Error: tiene que ingresar todos los campos requeridos.";
    return;
}
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo "Error: La dirección de correo electrónico no es válida.";
    return;
}
if ($_POST['tipoCliente'] !== "individual" && $_POST['tipoCliente'] !== "corporativo") {
    echo "Error: El Tipo de Cliente debe ser 'individual' o 'corporativo'.";
    return;
}
if (empty($_FILES['imagen']['tmp_name'])) {
    echo "Error: Debe proporcionar una imagen válida.";
    return;
}
$resultado = $clienteAlta->alta($_POST);
echo $resultado;
?>