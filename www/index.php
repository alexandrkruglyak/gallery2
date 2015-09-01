<?php
session_start();

//подключение к конфигурационному файлу
require_once 'config.php';

//подключение к конфигурационному файлу
require_once 'myfunction.php';

//подключение к бд
require_once 'database.php';

//Создаем объект к бд
$db = new DataBase(config::DB_HOST, config::DB_USER, config::DB_PASSWORD, config::DB_NAME);
$my_function = new myFunction();

//Сортировка
$order_p = array(
                'sized' => array('от больших размеров файла', 'size DESC'),
                'sizea' => array('от маленьких размеров файла', 'size ASC'),
                'dated' => array('по дате добавления - с последних', 'date DESC'),
                'datea' => array('по дате добавления - к последним', 'date ASC'),
                );
$order = $order_p['dated'][0];
$order_db = $order_p['dated'][1];

//Массив данных из таблицы "image"
$arr = $db->getAll($order_db);

// получаем возможный параметр сортировки
if($_GET['order']) {
$order_get = ($_GET['order']); 
if(array_key_exists($order_get, $order_p)){
    $order = $order_p[$order_get][0];
    $order_db = $order_p[$order_get][1];
}else{
    // по умолчанию сортировка по первому элементу массива order_p
    $order = $order_p['namea'][0];
    $order_db = $order_p['namea'][1];
}
//Массив данных из таблицы "image"
$arr = $db->getAll($order_db);       
}

//Данные для удаления  сессии
if ($_POST['destroy_ses']) {
    $my_function->sessDestroy();
}

//Принимаем данные на сохранение в бд
if ($_POST['name'] && $_POST['tmp_name']) {
    $my_function->sessDestroy();
    $name = $my_function->translit($_POST['name']);
    $type = $_POST['type'];
    $comment = $_POST['comment'];
    $size = $_POST['size'];
    $tmp_name = $_POST['tmp_name'];
    $date = time();
    $res = $db->saveData($comment, $name, $size, $date);
    $dir_tumb = config::DIR_IMG . "tumb/" . trim($name);
    $my_function->unlinkfile(null, $dir_tumb);
    exit(json_encode($res));
    $db->__destruct();
}

//Принимаем данные на изменение коммента
if ($_POST['edit_comment']) {
   $comm = $_POST['edit_comment'];
   $id = $_POST['id'];
   $res = $db->editRow($comm, $id);
   exit(json_encode($res));
}


//Принимаем данные на сохранение картинки
if ($_FILES['userfile']) {
    $userfile = $_FILES['userfile'];
    $type = $userfile['type'];
    $name = $my_function->translit($userfile['name']);
    $size = $userfile['size'];
    $size = $my_function->getSize($size);
    $tmp_name = $userfile['tmp_name'];
    $dir_load = config::DIR_IMG . "load/" . $name;
    $dir_tumb = config::DIR_IMG . "tumb/" . $name;
    $_SESSION['userfile']['name'] = $name;
    $_SESSION['userfile']['size'] = $size;
    $_SESSION['userfile']['tmp_name'] = $tmp_name;
    $_SESSION['userfile']['type'] = $type;
    $my_function->uploadfile($tmp_name, $name, $dir_load, $dir_tumb);
    $userfile['name'] = $name;
      exit(json_encode($userfile));
}

//Принимаем данные на удаление верхней превьюшки
if ($_POST['unlink_file']) {
    $my_function->sessDestroy();
    $name = $_POST['unlink_file'];
    $dir_load = config::DIR_IMG . "load/" . trim($name);
    $dir_tumb = config::DIR_IMG . "tumb/" . trim($name);
    $my_function->unlinkfile($dir_load, $dir_tumb);
}

//Принимаем данные на удаление строки (блока картинки).
if ($_POST['del_row']) {
    (int)$id = $_POST['del_row'];
    $name = $_POST['img_name'];
    $delRow = $db->delRow($id);
    $dir_load = config::DIR_IMG . "load/" . trim($name);
    $my_function->unlinkfile($dir_load, $dir_tumb=null);
    exit(json_encode($delRow));
}

//Подключение вида
require_once 'view/index.php';
?>