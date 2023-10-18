<?php
/**B- ClienteAlta.php: (por POST) se ingresa Nombre y Apellido, Tipo Documento, Nro.
Documento, Email, Tipo de Cliente (individual o corporativo), País, Ciudad y Teléfono.

Se guardan los datos en el archivo hoteles.json, tomando un id autoincremental de 6
dígitos como Nro. de Cliente (emulado). Si el nombre y tipo ya existen , se actualiza la
información y se agrega al registro existente.
completar el alta con imagen/foto del cliente, guardando la imagen con Número y Tipo
de Cliente (ej.: NNNNNNTT) como identificación en la carpeta:
/ImagenesDeClientes/2023.
*/
require './clases/Cliente.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
}
?>
