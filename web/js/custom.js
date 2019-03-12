$(document).ready(function () {
    /**
     * после закрытия модального окна возвращаем ему дефолтный размер
     */
    $('#modal-default').on('hidden.bs.modal', function () {
        $(this).children(0).removeClass('modal-lg').removeClass('modal-sm').removeClass('modal-md').addClass('modal-md');
        $('.modal-footer').show();
    });

    function processTrackClick(url, taskId)
    {
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                'taskId': taskId
            },
            success: function (result) {
                $('#save-changes-modal').attr('data-url', '/ajax/track');
                $('#modalContent').html(result);
                $('#modal-default').modal('show');
            }
        });
    }

    /**
     * Выводит alert бутстраповский
     * @param msg
     * @param type может быть info, warning, danger, success
     */
    function showAlert(msg, type = 'success')
    {
        var icon = '';

        switch (type) {
            case 'info':
                icon = 'fa-info';
                break;

            case 'success':
                icon = 'fa-check';
                break;

            case 'warning':
                icon = 'fa-warning';
                break;

            case 'danger':
                icon = 'fa-ban';
                break;
        }

        var newDiv = document.createElement("div");
        newDiv.className = "alert alert-" + type + " alert-dismissible";
        newDiv.innerHTML =
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
            '<h4><i class="icon fa ' + icon + '"></i> Внимание!</h4>' +
            msg;

        var alert = document.getElementById('alert-js');
        alert.append(newDiv);
    }

    /**
     * Выводит alert бутстраповский
     * @param msg
     * @param type может быть info, warning, danger, success
     */
    function showAlertInModal(msg, type = 'success')
    {
        var icon = '';

        switch (type) {
            case 'info':
                icon = 'fa-info';
                break;

            case 'success':
                icon = 'fa-check';
                break;

            case 'warning':
                icon = 'fa-warning';
                break;

            case 'danger':
                icon = 'fa-ban';
                break;
        }

        var newDiv = document.createElement("div");
        newDiv.className = "alert alert-" + type + " alert-dismissible";
        newDiv.innerHTML =
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
            '<h4><i class="icon fa ' + icon + '"></i> Внимание!</h4>' +
            msg;

        var alert = document.getElementById('alert-js-modal');
        alert.append(newDiv);
        setTimeout(function() {
            $('#alert-js-modal').fadeOut(500, function() {
                $(this).html('').show();
            })
        }, 1000);
    }

    /**
     * клик по колокольчку
     * читаем все непрочитанные уведомления
     * на этом пользователе
     */
    $('#bell-notify').click(function() {
        $.ajax({
            type: 'POST',
            url: '/ajax/notify-read',
            success: function (result) {
                showAlert(result.msg, result.type);
            }
        });
    });

    /**
     * клик по иконке смены статуса
     */
    $('#change-status').click(function() {
        var id = $(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '/ajax/status',
            data: {
                'taskId': id
            },
            success: function (result) {
                $('#save-changes-modal').attr('data-url', '/ajax/status');
                $('#modalContent').html(result);
                $('#modal-default').modal('show');
            }
        });
    });

    /**
     * клик по иконке смены пользователя на кого назначена задача
     */
    $('#change-assigned').click(function() {
        var id = $(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '/ajax/assigned',
            data: {
                'taskId': id
            },
            success: function (result) {
                $('#save-changes-modal').attr('data-url', '/ajax/assigned');
                $('#modalContent').html(result);
                $('#modal-default').modal('show');
            }
        });
    });

    /**
     * клик по кнопке сохранить в модальном окне
     */
    $('#save-changes-modal').click(function() {
        var url = $(this).attr('data-url');

        $.ajax({
            type: 'POST',
            url: url,
            data: $("#modalContent :input").serialize(),
            success: function (result) {
                $('#modal-default').modal('hide');
                showAlert(result.msg, result.type);
            }
        });
    });

    /**
     * клик по иконке трудозатрат
     */
    $('.fa-clock-o').click(function() {
        var url = $(this).parent().attr('data-url');
        var id = $(this).parent().attr('data-id');

        processTrackClick(url, id);
    });

    /**
     * клик по иконке удаления файла в задаче
     */
    $('.js-remove-document').click(function () {
        var self = $(this);
        $.post(
            $(self).attr('href'),
            function (data) {
                if (data.status == 1) {
                    $(self).parent().remove();
                } else {
                    alert(data.message);
                }
            }
        );
        return false;
    });

    /**
     * клик по иконке трудозатрат в задаче
     */
    $('.fa-info-circle').click(function() {
        var url = $(this).attr('data-url');
        var id = $(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                'taskId': id
            },
            success: function (result) {
                $('#modalContent').html(result);
                $('#modal-default').modal('show').children(0).removeClass('modal-md').addClass('modal-lg');
                $('.modal-footer').hide();
                /**
                 * клик по иконке удаления трудозатрат в модальном окне трудозатрат
                 */
                $(document.body).find('.fa-trash').click(function() {
                    var url = $(this).attr('data-url');

                    $.ajax({
                        /** ВНИМАНИЕ GET, вместо POST **/
                        type: 'GET',
                        url: url,
                        context: $(this),
                        success: function (result) {
                            $(this).parent().parent().hide();
                            showAlertInModal(result.msg, result.type);
                        }
                    });
                });
            }
        });
    });

    $('.toggle-label').click(function () {
        var parent = $(this).parent();
        if (parent.hasClass('hidden-toggle')) {
            parent.removeClass('hidden-toggle');
        } else {
            parent.addClass('hidden-toggle');
        }
    });

    $('#save-comment-form').submit(function () {
        var self = $(this);
        var data = {
            TaskComment: {
                text: self.find('#taskcomment-text').val(),
                author_id: self.find('#taskcomment-author_id').val(),
                task_id: self.find('#taskcomment-task_id').val()
            }
        };
        $.post(self.attr('action'), data, function (data) {
            if (data.status != 1) {
                alert(data.message);
            } else {
                alert('Комментарий добавлен')
            }
            return false;
        });

        console.log(data);
        return false;
    });

    $('body').on('click', '.js-ajax-link', function (e) {
        var url = $(this).attr('href');

        $.get(url, function (data) {
            $('#mymodal').html(data);
            $('#modal').modal('show');
        });

        return false;
    });

    $('body').on('click', '.js-insert-files', function () {
        var val = $(this).parent().find('#js-select-file').val();
        var name = $(this).parent().find('#js-select-file option:selected').text();
        console.log(name);
        var text = " [file id=" + val + " " + "name=" + name + "] ";
        var editorInstance = CKEDITOR.instances['taskcomment-text'];
        editorInstance.insertHtml( text );
    });
});