jQuery(document).ready(function() {  
    function build_table(data) {

        var table = '<table class="table_detail" width="100%">';
        table += '<thead>';
            table += '<tr role="row" class="heading">';
                table += '<th>ID</th><th>Task Name</th><th>Description</th><th>Status</th><th>Time Spent</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th>';
            table += '</tr>';
        table += '</thead>';
        table += '<tbody>';
            jQuery.each(data, function(key, value) {
                table += build_table_tr(value);
            });
        table += '</tbody>';
        table += '</table>';

        jQuery('#tasks-table').html(table);
    }

    function build_table_tr(data) {
        var tr = '<tr id="record+"' + data.id + '>';
            tr += '<td>' + data.id + '</td>';
            tr += '<td>' + data.name_clean + '</td>';
            tr += '<td>' + data.content_clean + '</td>';
            tr += '<td>' + data.status + '</td>';
            tr += '<td>' + data.time_spent + '</td>';
            tr += '<td><a href="#" class="task-time" data-id="' + data.id + '">Report Time</a></td>';
            tr += '<td><a href="#" class="task-edit" data-id="' + data.id + '">Edit</a></td>';
            tr += '<td><a href="#" class="task-close" data-id="' + data.id + '">Close</a></td>';
        tr += '</tr>';

        return tr;
    }

    function dialog(element, action) {
        if(action == 'hide') {
            jQuery('#' + element).hide();
            popup('hide');
        } else {
            var element = jQuery('#' + element);
            var element_width = element.data('width');
            var element_height = element.data('height');

            popup('show');
            var css = {
                'width': element_width, 'height': element_height, 
                top: (window.innerHeight / 2 - element_height / 2 - 120) + 'px',
                left: (window.innerWidth / 2 - element_width / 2) + 'px'}
            element.css(css).show();      
        }
    }

    function form_error(element, msg) {
        var parent = jQuery('#' + element).parents('.form-group', 0);
        parent.find('.input-error').text(msg);
    }

    function load_tasks_table() {
        var overlay = jQuery('#tasks-table-overlay');
        
        overlay.show();
        jQuery.ajax({
            url: '/json/tasks',
            type: 'POST',
            dataType: 'json'
        }).done(function(data) {    
            if(data.data != 'undefined' && data.data != undefined) {        
                build_table(data.data);
            }

            overlay.hide();
        }).fail(function(data) {
            console.log(data)
            overlay.hide();
        });
    }

    function is_blank(str) {
        return (!str || /^\s*$/.test(str));
    }

    function notification(msg) {
        jQuery('#dialog-notification').text(msg).show().fadeOut(3000);
    }

    function popup(action) {
        if(action == 'show') {
            jQuery('#page-overlay').show();
        } else {
            jQuery('#page-overlay').hide();
        }
    }

    jQuery('#form-new-task').submit(function(event) {
        event.preventDefault();

        var correct = true;

        var task_name = jQuery('#TaskName').val();
        if(is_blank(task_name)) {
            correct = false;
            form_error('TaskName', 'You forgot to insert task name');
        } else {
            form_error('TaskName', '');
        }

        if(correct) {
            var task_id = jQuery('#TaskId').val();
            var task_content = jQuery('#TaskContent').val();

            var post_data = {name: task_name, content: task_content, id: task_id}
            post_data = JSON.stringify(post_data);

            jQuery.ajax({
                url: '/json/task_add',
                type: 'POST',
                dataType: 'json',
                data: {data: post_data}
            }).done(function(data) { 
                if(data.result == 1 || data.result == '1') {
                    dialog('form-new-task', 'hide');

                    notification('New task has been stored.');
                    
                    load_tasks_table();
                } else if(data.error != undefined) {
                    notification_erros(data.errors);                    
                }
            }).fail(function(data) {
                
            }).always(function(data) {
                
            })
        }
    });

    if(jQuery('.new-task').length > 0) {
        jQuery('.new-task').click(function(event) {
            event.preventDefault();
            dialog('form-new-task', 'show');
        })
    }

    // Load Tasks table.
    load_tasks_table();
});