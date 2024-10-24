<?php
require "todo.php";
require "DB.php";

function return_response($status, $statusMessage, $data) {
    header("HTTP/1.1 $status $statusMessage");
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data);
}

$bodyRequest = file_get_contents("php://input");

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $db = new DB();
        $new_todo = new Todo;

        // Construir el nuevo todo desde el JSON recibido
        $new_todo->jsonConstruct($bodyRequest);

        // Insertar la nueva tarea en la base de datos
        if ($new_todo->insert($db->connection)) {
            // Obtener la lista actualizada de tareas
            $todo_list = Todo::DB_selectAll($db->connection);
            // Convertir la lista en JSON y devolverla
            return_response(200, "OK", $todo_list);
        } else {
            return_response(500, "Error al insertar tarea", null);
        }

        break;

    default:
        return_response(405, "Method Not Allowed", null);
        break;
}

