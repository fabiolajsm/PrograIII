<?php
require './clases/Cliente.php';

$cliente = new Cliente();
$data = json_decode(file_get_contents("php://input"));
echo $data . 'wdasd';

if (empty($data['numeroCliente']) || empty($data['nombre']) || empty($data['apellido']) || empty($data['tipoDocumento']) || empty($data['nroDocumento']) || empty($data['email']) || empty($data['tipo']) || empty($data['pais']) || empty($data['ciudad']) || empty($data['telefono'])) {
    echo "Error: tiene que ingresar todos los campos requeridos.";
    return;
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo "Error: La dirección de correo electrónico no es válida.";
    return;
}
if ($data['tipo'] !== "individual" && $data['tipo'] !== "corporativo") {
    echo "Error: El Tipo de Cliente debe ser 'individual' o 'corporativo'.";
    return;
}
$resultado = $cliente->modificar($data);
echo $resultado;
?>