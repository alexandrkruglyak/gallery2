<?php

class myFunction {
    
    //Преобразование байтов в килобайты
    public function getSize($size) {
        $size = round($size/1024);
        return $size;
    }
    
    //Текущая дата
    public function getDate($date = false) {
        if (!$date)
            $date = time();
        return strftime(config::FORMAT_DATE, $date);
    }
    
    //Безопасная строка
    public function clear($var) {
        $var = mysql_real_escape_string(strip_tags($var));
        return $var;
    }
    
    //Преобразование имени картинки в транслит
    public function translit($str) {
        $tr = array(
            "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
            "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
            "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
            "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
            "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
            "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
            "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
            "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
            "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
            "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
            "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
            "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
            "ы"=>"yi","ь"=>"'","э"=>"e","ю"=>"yu","я"=>"ya",
            " "=>"_","?"=>"_","/"=>"_","\\"=>"_",
       "*"=>"_",":"=>"_","*"=>"_","\""=>"_","<"=>"_",
       ">"=>"_","|"=>"_"
        );
        return strtr($str,$tr);
    }
    
    //Загрузка картинки
    public function uploadfile($tmp_name, $name, $dir_load, $dir_tumb) {
        $baseimgExt = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $name));
        move_uploaded_file($tmp_name, $dir_load);
        $this->resize($dir_load, $dir_tumb, 120, 185, $baseimgExt);
    }
    
    //Ресайз картинок
    private function resize($target, $dest, $wmax, $hmax, $ext) {
        /*
          $target - путь к оригинальному файлу
          $dest - путь сохранения обработанного файла
          $wmax - максимальная ширина
          $hmax - максимальная высота
          $ext - расширение файла
         */
        list($w_orig, $h_orig) = getimagesize($target);
        $ratio = $w_orig / $h_orig; // =1 - квадрат, <1 - альбомная, >1 - книжная

        if (($wmax / $hmax) > $ratio) {
            $wmax = $hmax * $ratio;
        } else {
            $hmax = $wmax / $ratio;
        }

        $img = "";
        // imagecreatefromjpeg | imagecreatefromgif | imagecreatefrompng
        switch ($ext) {
            case("png"):
                $img = imagecreatefrompng($target);
                break;
            default:
                $img = imagecreatefromjpeg($target);
        }
        $newImg = imagecreatetruecolor($wmax, $hmax); // создаем оболочку для новой картинки

        if ($ext == "png") {
            imagesavealpha($newImg, true); // сохранение альфа канала
            $transPng = imagecolorallocatealpha($newImg, 0, 0, 0, 127); // добавляем прозрачность
            imagefill($newImg, 0, 0, $transPng); // заливка  
        }

        imagecopyresampled($newImg, $img, 0, 0, 0, 0, $wmax, $hmax, $w_orig, $h_orig); // копируем и ресайзим изображение
        switch ($ext) {
            case("png"):
                imagepng($newImg, $dest);
                break;
            default:
                imagejpeg($newImg, $dest);
        }
        imagedestroy($newImg);
    }
    //Удаление 
    public function unlinkfile($dir_load=null, $dir_tumb=null) {
        if($dir_load) unlink($dir_load);
        if($dir_tumb) unlink($dir_tumb);
    }
    
    //Уничтожение сессии
    public function sessDestroy() {
        return session_destroy();
    }
    
}