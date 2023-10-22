<?php
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        handlePost();
        break;
    case 'PUT':
        include('ModificarCliente.php');
        break;
    default:
        echo 'Método no permitido';
        break;
}

function handlePost()
{
    $action = $_POST['action'];
    switch ($action) {
        case 'alta':
            include('ClienteAlta.php');
            break;
        case 'consulta':
            include('ConsultarCliente.php');
            break;
        case 'reservaHabitacion':
            include('ReservaHabitacion.php');
            break;
        case 'cancelarReserva':
            include('CancelarReserva.php');
            break;
        case 'ajusteReserva':
            include('AjusteReserva.php');
            break;
        default:
            echo 'Acción inválida';
            break;
    }
}
?>