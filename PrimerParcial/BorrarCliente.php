<?php
require './clases/Cliente.php';

$numeroCliente = $_GET['numeroCliente'];
$tipoCliente = $_GET['tipoCliente'];
$numeroDNI = $_GET['numeroDNI'];

if (empty($numeroCliente) || empty($tipoCliente) || empty($numeroDNI)) {
    echo "Error: Debe proporcionar el número de cliente, el tipo de cliente y el numero de DNI.";
    return;
}
if ($tipoCliente !== "INDI" && $tipoCliente !== "CORP") {
    echo "Error: El Tipo de Cliente debe ser 'INDI' o 'CORP'.";
    return;
}
$cliente = new Cliente();
$resultado = $cliente->borrar($numeroCliente, $tipoCliente, $numeroDNI);
echo $resultado;
?>