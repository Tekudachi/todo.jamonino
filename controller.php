<?php
require "todo.php";
require "DB.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

function return_response($status, $statusMessage, $data) {
    header("HTTP/1.1 $status $statusMessage");
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data);
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$bodyRequest = file_get_contents("php://input");

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $db = new DB();
        $new_todo = new Todo;
        $new_todo->jsonConstruct($bodyRequest);
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
        if ($todo->delete($db->connection, $item_id)) {
            $todo_list = Todo::DB_selectAll($db->connection);
            return_response(200, "OK", ["success" => true, "todo_list" => $todo_list]);
        } else {
            return_response(500, "Error al eliminar la tarea", ["success" => false]);
        }
        break;

    case 'PUT':
        parse_str($_SERVER['QUERY_STRING'], $query);
        $item_id = $query['id'];
        $db = new DB();
        $updated_todo = new Todo;
        $updated_todo->jsonConstruct($bodyRequest);
        if ($updated_todo->update($db->connection, $item_id)) {
            $todo_list = Todo::DB_selectAll($db->connection);
            return_response(200, "OK", ["success" => true, "todo_list" => $todo_list]);
        } else {
            return_response(500, "Error al actualizar la tarea", ["success" => false]);
        }
        break;

    default:
        return_response(405, "Method Not Allowed", null);
        break;
}
