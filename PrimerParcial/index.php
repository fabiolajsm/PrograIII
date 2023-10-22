<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'alta':
            include('ClienteAlta.php');
            break;
        case 'consulta':
            include('ConsultarCliente.php');
            break;
        default:
            echo 'Acción inválida';
    }
} else {
    echo 'Método no permitido';
}
?>