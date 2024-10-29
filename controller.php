<?php
require "todo.php";
require "DB.php";

// Habilitar CORS para todas las solicitudes
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

function return_response($status, $statusMessage, $data) {
    header("HTTP/1.1 $status $statusMessage");
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data);
}

// Responder a solicitudes OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
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
            $todo_list = Todo::DB_selectAll($db->connection);
            return_response(200, "OK", $todo_list);
        } else {
            return_response(500, "Error al insertar tarea", null);
        }
        break;

    case 'DELETE':
        parse_str($_SERVER['QUERY_STRING'], $query);
        $item_id = $query['id'];

        $db = new DB();
        $todo = new Todo;

        // Eliminar la tarea de la base de datos
        if ($todo->delete($db->connection, $item_id)) {
            $todo_list = Todo::DB_selectAll($db->connection);
            return_response(200, "OK", ["success" => true, "todo_list" => $todo_list]);
        } else {
            return_response(500, "Error al eliminar la tarea", ["success" => false]);
        }
        break;

    default:
        return_response(405, "Method Not Allowed", null);
        break;
}