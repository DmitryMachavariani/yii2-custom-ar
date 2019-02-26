$(document).ready(function () {

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
     * клик по колокольчку
     * читаем все непрочитанные уведомления
     * на этом пользователе
     */
    $('#bell-notify').click(function() {
        $.ajax({
            type: 'POST',
            url: '/ajax/notify-read',
            success: function (result) {
                if (result.type == 'success') {
                    showAlert(result.msg, 'info');
                } else {
                    showAlert(result.msg, 'danger');
                }
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
                if (result.type == 'success') {
                    showAlert(result.msg, 'info');
                } else {
                    showAlert(result.msg, 'danger');
                }
            }
        });
    });

});