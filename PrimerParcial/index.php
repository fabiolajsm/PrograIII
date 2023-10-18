<?php
/**Se debe realizar una aplicación para dar de ingreso con foto del usuario/cliente.
Los datos se persistirán en archivos (ej. txt, json, csv, etc.)
Se deben respetar los nombres de los archivos y de las clases.
Se debe crear una clase en PHP por cada entidad y los archivos PHP solo deben llamar
a métodos de las clases. */

/**A- index.php: Recibe todas las peticiones que realiza el cliente (utilizaremos Postman),
y administra a qué archivo se debe incluir. */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'alta':
            include('ClienteAlta.php');
            break;
        default:
            echo 'Acción inválida';
    }
} else {
    echo 'Método no permitido';
}
?>