<?php
require './clases/Cliente.php';

if (empty($_POST['tipo']) || empty($_POST['numeroCliente'])) {
    echo "Error: tiene que ingresar todos los campos requeridos.";
    return;
}
if ($_POST['tipo'] !== "individual" && $_POST['tipo'] !== "corporativo") {
    echo "Error: El Tipo de Cliente debe ser 'individual' o 'corporativo'.";
    return;
}
$cliente = new Cliente();
$resultado = $cliente->consultar($_POST['tipo'], $_POST['numeroCliente']);
echo $resultado;
?>