<?php
require_once 'myfunction.php';

class DataBase {

    private $mysqli;
    private $my_function;

    public function __construct($db_host, $db_user, $db_password, $db_name) {
        $this->mysqli = @new mysqli($db_host, $db_user, $db_password, $db_name);
        if ($this->mysqli->connect_errno)
            exit("Ошибка соединения с базой данных");
        $this->mysqli->query("SET lc_time_names = 'ru_RU'");
        $this->mysqli->set_charset("utf8");
        $this->my_function = new myFunction();
    }
    
    //Удаление строки
    public function delRow($id) {
        $id = (int)$id;
        $query = "DELETE FROM image WHERE id = $id";
        $this->mysqli->query($query) or die(mysql_error());
        return $this->mysqli->affected_rows;
    }
    //Изменение комментария
    public function editRow($comm, $id) {
        $comment = $this->my_function->clear(trim($comm));
        $query = "UPDATE image SET comment = '$comment' WHERE id = $id";
        $res = $this->mysqli->query($query) or die(mysql_error());
        return $res;
    }    
    //Выборка всей таблицы
    public function getAll($sort = null) {
        if($sort) $sort = "ORDER BY $sort";
        $query = "SELECT * FROM image $sort";
        $res = $this->mysqli->query($query) or die(mysql_error());
        while ($row = $res->fetch_assoc()){
            $arr[] = $row;
        };
        return $arr;
    }
    //Сохранение в бд
    public function saveData($comment, $name, $size, $date) {
        $size = trim((int)$size);
        $comment = $this->my_function->clear(trim($comment));
        $name = $this->my_function->clear(trim($name));
        $query = "INSERT INTO image (title, size, comment, date)
                        VALUES ('$name', '$size', '$comment', '$date')";
        $this->mysqli->query($query) or die(mysql_error());
        $res = $this->mysqli->affected_rows;
        $last_id = $this->mysqli->insert_id;
        $arr['res'] = $res;
        $arr['last_id'] = $last_id;
        return $arr;

    }
    //Закрыть подключение к бд
    public function __destruct() {
        if (($this->mysqli) && (!$this->mysqli->connect_errno))
            $this->mysqli->close();
    }

}

?>