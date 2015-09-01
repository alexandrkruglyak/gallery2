<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>gallery</title>
        <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <script src="js/jquery-1.10.2.js" type="text/javascript"></script>  
        <script src="js/jquery-ui-1.9.2.custom.js" type="text/javascript"></script>
        <script src="js/bootstrap.js" type="text/javascript"></script>
        <script src="js/ajaxupload.js" type="text/javascript"></script>
        <script src="js/workscripts.js" type="text/javascript"></script>

    </head>
    <body>
        <div id="upload-form">
            <form>
                <legend>Online Gallery</legend>
                <!-- При обновлении страницы первоначальная превьюшка не сбрасывается --> 
                <?php if (isset($_SESSION['userfile'])): ?>
                    <a href="#" id="butUpload" style="display: none" class="btn btn-large"><i class="icon-picture"></i><span class="dawnload-img">Загрузить картинку</span></a>
                    <span id='upload-Files'><img class='loading' src="img/loading.gif"></span>
                    <div id='image-block' class="clearfix" style="display: block">
                        <div id="left"> 
                            <table class='info'>
                                <tr><td rowspan='2'><img id='' src="img/tumb/<?php echo $_SESSION['userfile']['name']; ?>"></td></tr>
                            </table>
                        </div>
                        <div id="right" style="display: block"> 
                            <table>
                                <tr>
                                    <td class='name-img'><strong>Name: </strong><span> <?php echo $_SESSION['userfile']['name']; ?></span></td>
                                </tr>
                                <tr>
                                    <td class='size-img'><strong>Size: </strong> <?php echo $_SESSION['userfile']['size']." "; ?>KB</td>
                                </tr>
                                <tr>
                                    <td><a id="del-t" href="#"><i class="icon-remove-sign"></i>Удалить</a></td>
                                </tr>
                            </table>
                        </div>
                        <input type="hidden" id="tmp_name" value="<?php echo $_SESSION['userfile']['tmp_name'] ?>" name="tmp_name">
                        <input type="hidden" id="type" value="<?php echo $_SESSION['userfile']['type'] ?>" name="type">
                        <input type="hidden" id="img-name" value="<?php echo $_SESSION['userfile']['name'] ?>" name="img-name">
                        <input type="hidden" id="img-size" value="<?php echo $_SESSION['userfile']['size'] ?>" name="img-size">
                    </div>
                <?php else: ?>
                    <a href="#" id="butUpload" class="btn btn-large"><i class="icon-picture"></i><span class="dawnload-img">Загрузить картинку</span></a>
                    <span id='upload-Files'><img class='loading' src="img/loading.gif"></span>
                    <div id='image-block' class="clearfix">
                        <div id="left"> </div>
                        <div id="right"> 
                            <table>
                                <tr>
                                    <td class='name-img'></td>
                                </tr>
                                <tr>
                                    <td class='size-img'></td>
                                </tr>
                                <tr>
                                    <td><a id='del-t' href='#'><i class='icon-remove-sign'></i>Удалить</a></td>
                                </tr>
                            </table>
                        </div>
                        <input type="hidden" id="tmp_name" value="" name="tmp_name">
                        <input type="hidden" id="type" value="" name="type">
                        <input type="hidden" id="img-name" value="" name="img-name">
                        <input type="hidden" id="img-size" value="" name="img-size">
                    </div>
                <?php endif ?>
                <textarea id="comment" class="comment" rows="3" placeholder="Введите комментарий"></textarea>
                <div id="dialog-message-comm" title="Ошибка!">
                    <p>
                        <span class="ui-icon ui-icon-circle-minus" style="float:left; margin:0 7px 50px 0;"></span>
                        Комментарий должен быть не более 200 и не менее 5 символов.
                    </p>
                </div>
                <div id="dialog-message-img" title="Ошибка!">
                    <p>
                        <span class="ui-icon ui-icon-circle-minus" style="float:left; margin:0 7px 50px 0;"></span>
                        Картинка не выбрана.
                    </p>
                </div>
                <div id="message-img-size" title="Ошибка!">
                    <p>
                        <span class="ui-icon ui-icon-circle-minus" style="float:left; margin:0 7px 50px 0;"></span>
                        Превышен допустимый размер файла. Максимальный размер: 1мб
                    </p>
                </div>
                <div id="message-img-type" title="Ошибка!">
                    <p>
                        <span class="ui-icon ui-icon-circle-minus" style="float:left; margin:0 7px 50px 0;"></span>
                        Неверный тип файла. Разрешается только: jpg,png,jpeg.
                    </p>
                </div>
                <button id="save" type="submit" class="btn">Сохранить</button>
            </form>
            <div id="success-save" class="alert alert-success"><span class="ui-icon ui-icon-check"></span>Сохранение прошло успешно!</div>
        </div>
        <div id="main">
                <legend>
                    <div class="btn-group dropup">
                        <button class="btn dropdown-toggle" data-toggle="dropdown">Сортировать картинки <?php echo $order ?></button>
                        <button class="btn dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach ($order_p as $key => $val): ?>
                            <?php if($order == $val[0]) continue; ?>
                            <li><a href="?order=<?= $key ?>"><?=$val[0]?></a></li>
                            <?php endforeach;  ?>
                        </ul>
                    </div>
                </legend>
                <ul class="thumbnails">
                    <?php if($arr): ?>
                    <?php foreach ($arr as $img): ?>
                        <li id='' class="span3">
                            <div class="thumbnail">
                                <div id="block-img">
                                <input type="hidden" id="img-size" value="<?php echo $img['title'] ?>" name="img-size">
                                <img src="img/load/<?php echo $img['title'] ?>" alt="">
                                <h5 class="comment <?php echo $img['id'] ?>"><?php echo $img['comment'] ?></h5>
                                <span><?php echo $my_function->getDate($img['date']) ?></span>
                                </div>
                                <a class="btn btn-danger" href="#">Удалить</a>
                                <a class="btn btn-link" href="#">Изменить</a>
                                <input type="hidden" class="img-id" id="img-id" value="<?php echo $img['id'] ?>" name="img-size">
                                <input type="hidden" class="img-name" id="" value="<?php echo $img['title'] ?>" name="img-size">
                                <div id="dialog-form" title="Изменить комментарий">
                                    <p class="validateTips"></p>
                                        <form>
                                          <fieldset>
                                            <input type="hidden" class="img-id" id="img-id" value="<?php echo $img['id'] ?>" name="img-size">
                                            <textarea id="comm" class='comm' rows="3" placeholder="Введите комментарий"></textarea>
                                            <!-- Allow form submission with keyboard without duplicating the dialog button -->
                                            <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
                                          </fieldset>
                                        </form>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                        <?php else: ?>
                        <li id='' class="span3" style="display: none">
                            <div class="thumbnail">
                                <div id="block-img">
                                <input type="hidden" id="img-size" value="<?php echo $img['title'] ?>" name="img-size">
                                <img src="img/load/<?php echo $img['title'] ?>" alt="">
                                <h5 class="comment <?php echo $img['id'] ?>"><?php echo $img['comment'] ?></h5>
                                <span><?php echo $my_function->getDate($img['date']) ?></span>
                                </div>
                                <a class="btn btn-danger" href="#">Удалить</a>
                                <a class="btn btn-link" href="#">Изменить</a>
                                <input type="hidden" class="img-id" id="img-id" value="<?php echo $img['id'] ?>" name="img-size">
                                <input type="hidden" class="img-name" id="" value="<?php echo $img['title'] ?>" name="img-size">
                                <div id="dialog-form" title="Изменить комментарий">
                                    <p class="validateTips"></p>
                                        <form>
                                          <fieldset>
                                            <input type="hidden" class="img-id" id="img-id" value="<?php echo $img['id'] ?>" name="img-size">
                                            <textarea id="comm" class='comm' rows="3" placeholder="Введите комментарий"></textarea>
                                            <!-- Allow form submission with keyboard without duplicating the dialog button -->
                                            <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
                                          </fieldset>
                                        </form>
                                </div>
                            </div>
                        </li>
                   <?php endif ?>
                </ul>
                <div id="dialog-confirm" title="Подтвердите удаление!">
                    <p><span class="ui-icon ui-icon-alert"></span>Удалить картинку?</p>
                </div>
            
        </div>
    </body>
</html>
