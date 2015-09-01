$(document).ready(function () {
var imgObject = Object();
    imgObject.id = null;
    imgObject.comment = null;
    imgObject.children_comment = null;
    imgObject.last_id = null;
    imgObject.date = getDate();

//Текущее время в удобном формате    
function getDate() {
    var date = new Date();
    var month_num = date.getMonth()
    var day = date.getDate();
    if (day <= 9) day = "0" + day;
    if (month_num <= 9) month_num = "0" + month_num;
    var now = day+"."+month_num+"."+date.getFullYear();
    return now;
}    
// кнопка загрузка картинки + интервал ожидания(гиф анимация)
    var button = $("#butUpload"), interval; 
    new AjaxUpload(button, {
        action: './',
        name: 'userfile',
        data: {id: 5},
        onSubmit: function (file, ext) { // Проверка на расширение
            if (!(ext && /^(jpg|png|jpeg)$/i.test(ext))) {
                $(function () {
                    $("#message-img-type").dialog({
                        modal: true,
                        buttons: {
                            Ok: function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                });
                return false;
            }
            button.fadeOut(300);

            interval = window.setInterval(function () {
                $('.loading').css('display', 'inline-block')
            }, 300);
        },
        onComplete: function (file, response) {
            $('.loading').css('display', 'none')
            window.clearInterval(interval);
            //this.enable();
            var res = JSON.parse(response);
            var name = res.name;
            var size = res.size;
            var type = res.type;
            var tmp_name = res.tmp_name;
            if (size > 1050000) { // Проверка на размер файла
                $(function () {
                    $("#message-img-size").dialog({
                        modal: true,
                        buttons: {
                            Ok: function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                });
                $.ajax({    // если не прошла то удаляем сессию
                    url: "./",
                    type: "POST",
                    data: {destroy_ses: 1},
                    success: function (res) {
                    },
                });
                $("#butUpload").css('display', 'block');
                return false;
            }
            // если ок, то устанавливаем значения
            $("#image-block").css('display', 'block');
            $("#left").html("<table class='info'><tr><td rowspan='2'><img id='' src='img/tumb/" + name + "'></td></tr></table>");
            $(".name-img").html("<strong>Name: </strong><span>" + name + "</span>");
            $(".size-img").html("<strong>Size: </strong>" + Math.round(size / 1024) + " KB ");
            $("#right").css('display', 'block');
            $("#tmp_name").val(tmp_name);
            $("#type").val(type);
            $("#img-name").val(name);
            $("#img-size").val(size);
        }
    });
    // Сохранениея в бд
    $('#save').click(function (eventObject) {
        eventObject.preventDefault();
        var name = $("#img-name").val();
        var tmp_name = $("#tmp_name").val();
        var type = $("#type").val();
        var size = $("#img-size").val();
        var comment = $("#comment").val();
        if (comment.length > 200 || comment.length < 5) { //проверка на длину символов
            $(function () {
                $("#dialog-message-comm").dialog({
                    modal: true,
                    buttons: {
                        Ok: function () {
                            $(this).dialog("close");
                        }
                    }

                });
            });
            return false;
        }
        if (name == '') { //проверка на загрузку картинки
            $(function () {
                $("#dialog-message-img").dialog({
                    modal: true,
                    buttons: {
                        Ok: function () {
                            $(this).dialog("close");
                        }
                    }

                });
            });
            return false;
        }
        $.ajax({ //Если ок - отправляем параметры.
            url: "./",
            type: "POST",
            data: {name: name, tmp_name: tmp_name, type: type, comment: comment, size: size},
            success: function (res) {
                var arr = JSON.parse(res);
                imgObject.last_id = arr.last_id;
                $("#image-block").css('display', 'none');
                $("#butUpload").css('display', 'block');
                $("#comment").val('');
                if (arr.res == 1) {
                    $("#img-name").val('');
                    $("#success-save").css('display', 'block')
                    $("#success-save").fadeOut(3000);
                    var block = $('.span3:first-child').clone();
                    block.css('display', 'block');
                    var block_img = block.children('.thumbnail').children('#block-img');
                    imgObject.children_comment = block_img.children('.comment');
                    block_img.children('.comment').text(comment);
                    block_img.children('img').attr("src", "img/load/"+name);
                    block_img.children('span').text(imgObject.date);
                    $(".thumbnails").append(block);
                    
                    var btn_del = block.children('.thumbnail').children('.btn-danger');
                    var bedit = block.children('.thumbnail').children('.btn-link');
                    btn_del.click(function(eventObject){
                        eventObject.preventDefault();
                        $(function() {
                            $("#dialog-confirm").dialog({
                                resizable: false,
                                height: 140,
                                modal: true,
                                buttons: {
                                    Подтверждаю: function () {
                                        $(this).dialog("close");
                                        $.ajax({
                                            url: "./",
                                            type: "POST",
                                            data: {del_row: imgObject.last_id, img_name: name},
                                            success: function (res) {
                                                if (res == 1)
                                                    block.fadeOut(500);
                                                else
                                                    alert("Ошибка!")
                                            },
                                            error: function () {
                                                alert("Error");
                                                return false;
                                            }
                                        });
                                    },
                                    Отмена: function () {
                                        $(this).dialog("close");
                                        return false;
                                    }
                                }
                            });
                        });
                    });
                    bedit.click(function(eventObject){
                        eventObject.preventDefault();
                        dialog.dialog("open");
                    });
                    
                }
            },
            error: function () {
                alert("Error");
            }
        });
    });
    
    //Удаление превьюшки
    $('#del-t').click(function (eventObject) {
        eventObject.preventDefault();
        $('#butUpload').fadeIn(300);
        var name = $(".name-img span").text();
        $.ajax({
            url: "./",
            type: "POST",
            data: {unlink_file: name},
            success: function (res) {
                $('#image-block').css('display', 'none');
                $('.success-ico').css('display', 'none');
            },
            error: function () {
                alert("Error");
            }
        });
    })


    // удаление бллока (картинки, коммента, числа)
    $(".btn-danger").click(function (eventObject) {
        eventObject.preventDefault();
        var id = $(this).next('.btn-link').next('.img-id').val();
        var name = $(this).next('.btn-link').next('.img-id').next('.img-name').val();
        var blockImg = $(this).parent(".thumbnail").parent(".span3");
        
        $(function() {
            $("#dialog-confirm").dialog({
                resizable: false,
                height: 140,
                modal: true,
                buttons: {
                    Подтверждаю: function () {
                        $(this).dialog("close");
                        $.ajax({
                            url: "./",
                            type: "POST",
                            data: {del_row: id, img_name: name},
                            success: function (res) {
                                if (res == 1)
                                    blockImg.fadeOut(500);
                                else
                                    alert("Ошибка!")
                            },
                            error: function () {
                                alert("Error");
                                return false;
                            }
                        });
                    },
                    Отмена: function () {
                        $(this).dialog("close");
                        return false;
                    }
                }
            });
        });
    });
//Функция работы скрипта UI-Dialog
    var dialog, form,
                texterea = $("#comm"),
                tips = $(".validateTips");
        
        function updateTips(t) { //Добавляет и Удаляет класс для ошибок
            tips
                    .text(t)
                    .addClass("ui-state-highlight");
            setTimeout(function () {
                tips.removeClass("ui-state-highlight", 1500);
            }, 500);
        }

        function checkLength(str) { //Проверка на длинну
            if (str.length > 200 || str.length < 5) {
                texterea.addClass("ui-state-error");
                updateTips("Допускается не более 200 и не менее 5 символов.");
                return false;
            } else {
                dialog.dialog("close");
                return true;
            }
        }

        function addComment() { //Изменяем коммент
            if(imgObject.id !=null) {
                var id = imgObject.id;
            }
            else {
                var id = imgObject.last_id;
            }
            var valid = true;
            texterea.removeClass("ui-state-error");
            var str = texterea.val();
            valid = valid && checkLength(str);
            if (valid) {
                $.ajax({
                    url: "./",
                    type: "POST",
                    data: {edit_comment: str, id: id},
                    success: function(res) {
                        if (res) {
                            if(imgObject.id !=null) {
                                $("."+id).text(str);
                                dialog.dialog("close");
                             }
                            else {
                                imgObject.children_comment.text(str);
                            }
                            
                        }
                        else alert("Ошибка!");
                    },
                    error: function () {
                        alert("Error");
                    }
                });
            }
        }

        dialog = $("#dialog-form").dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            modal: true,
            buttons: {
                Изменить: addComment,
                Отменить: function () {
                    dialog.dialog("close");
                }
            },
            close: function () {
                form[ 0 ].reset();
            }
        });

        form = dialog.find("form").on("submit", function (event) {
            event.preventDefault();
            addComment();
        });

        $(".btn-link").button().on("click", function(eventObject) {
            eventObject.preventDefault();
            var id = $(this).next('.img-id').val();
            imgObject.id = id;
            dialog.dialog("open");
        });

});