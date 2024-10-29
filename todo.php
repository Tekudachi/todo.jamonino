<?php

class Todo implements \JsonSerializable {

    private int $item_id;
    private string $content;

    public function parametersConstruct(int $item_id, string $content){
        $this->item_id = $item_id;
        $this->content = $content;
    }

    public function jsonConstruct($json) {
        foreach (json_decode($json, true) AS $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }

    public function getContent(){       
        return $this->content;  
    }

    public function getItem_id(){       
        return $this->item_id;
    }

    public static function DB_selectAll($dbconn){
        $todo_list = array();
        foreach($dbconn->query("SELECT item_id, content FROM todo_list") as $row) {
            $new_todo = new Todo;
            $new_todo->parametersConstruct($row['item_id'], $row['content']);
            $todo_list[] = $new_todo;
        }
        return $todo_list;
    }

    public function insert($dbconn) {
        $sql = "INSERT INTO todo_list (content) VALUES (:content)";
        $stmt = $dbconn->prepare($sql);
        $stmt->bindParam(':content', $this->content);
        return $stmt->execute();
    }

    public function delete($dbconn, $item_id) {
        $sql = "DELETE FROM todo_list WHERE item_id = :item_id";
        $stmt = $dbconn->prepare($sql);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}