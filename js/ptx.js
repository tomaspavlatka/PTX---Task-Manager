jQuery(document).ready(function() {  
    function build_table(data) {

        var table = '<table class="table_detail" width="100%">';
        table += '<thead>';
            table += '<tr role="row" class="heading">';
                table += '<th>ID</th><th>Task Name</th><th>Description</th><th>Status</th><th>Time Spent</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th>';
            table += '</tr>';
        table += '</thead>';
        table += '<tbody>';
            jQuery.each(data.data, function(key, value) {
                table += build_table_tr(value);
            });
        table += '</tbody>';
        table += '<tfoot>';
            table += '<tr><th colspan="2">' + data.paginator.records + ' records</th>';
            table += '<th colspan="6" class="paginator">';

                if(data.paginator.has_previous) {
                    table += '<a href="#" class="table-step" data-page="1">First</a> ';
                    table += '<a href="#" class="table-step" data-page="' + (data.paginator.active - 1) + '">Previous</a> ';
                }

                for(var i = 1; i <= data.paginator.pages; i++) {
                    if(i == data.paginator.active) {
                        table += '<a href="#" class="table-step active" data-page="' + i + '">' + i + '</a> ';
                    } else {
                        table += '<a href="#" class="table-step" data-page="' + i + '">' + i + '</a> ';
                    }
                }

                if(data.paginator.has_next) {
                    table += '<a href="#" class="table-step" data-page="' + (data.paginator.active + 1) + '">Next</a> ';
                    table += '<a href="#" class="table-step" data-page="' + data.paginator.pages + '">Last</a> ';
                }

            table += '</th>';
        table += '</tfoot>';
        table += '</table>';

        jQuery('#tasks-table').html(table);

        jQuery('.tasks-refresh').data('page', data.paginator.active);
    }

    function build_table_tr(data) {
        var tr = '<tr id="record_+' + data.id + '">';
            tr += '<td>' + data.id + '</td>';
            tr += '<td id="task-name-' + data.id + '">' + data.name_clean + '</td>';
            tr += '<td><small>' + data.content_clean + '</small></td>';
            tr += '<td>' + data.task_status + '</td>';
            tr += '<td>' + get_time_text(data.time_spent) + '</td>';
            tr += '<td><a href="#" class="task-time" data-id="' + data.id + '">Report Time</a></td>';
            tr += '<td><a href="#" class="task-edit" data-id="' + data.id + '">Edit</a></td>';
            if(data.task_status == 'closed') {
                tr += '<td><a href="#" class="task-open" data-id="' + data.id + '">Open</a></td>';
            } else {
                tr += '<td><a href="#" class="task-close" data-id="' + data.id + '">Close</a></td>';
            }
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

            element.find('.errors').html('');
            popup('show');
            var css = {
                'width': element_width, 'height': element_height, 
                top: (window.innerHeight / 2 - element_height / 2 - 120) + 'px',
                left: (window.innerWidth / 2 - element_width / 2) + 'px'}
            element.css(css).show();      
        }
    }

    function get_time_text(minutes) {
        var hour = parseInt(minutes / 60);
        var min = minutes % 60;

        return hour + 'h ' + min + 'm';
    }

    function form_error(element, msg) {
        var parent = jQuery('#' + element).parents('.form-group', 0);
        parent.find('.input-error').text(msg);
    }

    function load_tasks_table(page) {
        var overlay = jQuery('#tasks-table-overlay');
        
        if(page == null) {
            var original_page = jQuery('#tasks-table-container').attr('data-page');
            pattern = /^[0-9]+$/;
            if(pattern.test(original_page)) {
                page = original_page;
            } else {
                page = 1;
            }
        }

        var post_data = {page: page}        

        overlay.show();
        jQuery.ajax({
            url: '/json/tasks',
            type: 'POST',
            dataType: 'json',
            data: {data: JSON.stringify(post_data)}
        }).done(function(data) {    
            jQuery('#tasks-table-container').attr('data-page', page);
            build_table(data);
            overlay.hide();
        }).fail(function(data) {
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

    function show_errors(errors) {
        var error_text = '';
        jQuery.each(errors, function(key, value) {
            error_text += value;
        });

        jQuery('.dialog').each(function() {
            if(jQuery(this).is(':visible')) {
                jQuery(this).find('.errors').text(error_text);                
            }
        });
    }

    jQuery('.dialog-close').click(function(event) {
        event.preventDefault();
        popup('hide');
        jQuery('.dialog').hide();
    });

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

            jQuery.ajax({
                url: '/json/task_add',
                type: 'POST',
                dataType: 'json',
                data: {data: JSON.stringify(post_data)}
            }).done(function(data) { 
                if(data.errors == undefined || data.errors == 'undefined') {
                    dialog('form-new-task', 'hide');
                    notification('New task has been stored.');
                    load_tasks_table(null);           
                } else {
                    show_errors(data.errors);
                }
            }).fail(function(data) {
                
            }).always(function(data) {
                
            })
        }
    });

    jQuery('#form-report').submit(function(event) {
        event.preventDefault();

        var correct = true;

        var task_time = jQuery('#TaskReportTime').val();
        var pattern = /[0-9]*\.?[0-9]+/;
        if(!pattern.test(task_time)) {        
            correct = false;
            form_error('TaskReportTime', 'You forgot to insert time spent');
        } else {
            form_error('TaskReportTime', '');
        }

        if(correct) {
            var task_id = jQuery('#TaskReportId').val();
            var post_data = {time_spent: task_time, id: task_id}            

            jQuery.ajax({
                url: '/json/task_add_time',
                type: 'POST',
                dataType: 'json',
                data: {data: JSON.stringify(post_data)}
            }).done(function(data) { 
                if(data.errors == undefined || data.errors == 'undefined') {
                    dialog('form-report-container', 'hide');
                    notification('New time report has been stored.');
                    load_tasks_table(null);
                } else {
                    show_errors(data.errors);
                }
            }).fail(function() {
                var errors = {general: 'Upps, something weird just happened. Try again please.'}  
                show_errors(errors);
            })
        }
    });
    

    jQuery('.task-new').click(function(event) {
        event.preventDefault();

        jQuery('#TaskName, #TaskContent, #TaskId').val(''); // Clean fields.
        dialog('form-new-task', 'show');
    });

    jQuery('#tasks-table').on('click', '.task-edit', function(event) {
        event.preventDefault();

        var task_id = jQuery(this).data('id');
        var post_data = {id: task_id}

        jQuery.ajax({
            url: '/json/task',
            type: 'POST',
            dataType: 'json',
            data: {data: JSON.stringify(post_data)}
        }).done(function(data) {
            if(data.errors == undefined || data.errors == 'undefined') {
                jQuery('#TaskName').val(data.task_data.name);
                jQuery('#TaskContent').val(data.task_data.content);
                jQuery('#TaskId').val(data.task_data.id);
                dialog('form-new-task', 'show');
            } else {
                show_errors(data.errors);
            }
        }).fail(function(data) {
            var errors = {general: 'Upps, something weird just happened. Try again please.'}  
            show_errors(errors);
        });
    }).on('click', '.task-time', function(event) {
        event.preventDefault();

        var task_id = jQuery(this).data('id');
        task_name = jQuery('#task-name-' + task_id).text();
        jQuery('#TaskReportName').val(task_name);
        jQuery('#TaskReportId').val(task_id);
        jQuery('#TaskReportTime').val('');
        dialog('form-report-container', 'show');

    }).on('click', '.task-close', function(event) {
        event.preventDefault();

        var task_id = jQuery(this).data('id');

        task_name = jQuery('#task-name-' + task_id).text();
        jQuery('#task-close-name').text(task_name);

        jQuery('#task-close-confirm').data('id', task_id);
        dialog('task-close-container', 'show'); 
    }).on('click', '.task-open', function(event) {
        event.preventDefault();

        var task_id = jQuery(this).data('id');

        task_name = jQuery('#task-name-' + task_id).text();
        jQuery('#task-open-name').text(task_name);

        jQuery('#task-open-confirm').data('id', task_id);
        dialog('task-open-container', 'show'); 
    }).on('click', '.table-step', function(event) {
        event.preventDefault();
        load_tasks_table(jQuery(this).data('page'));
    })

    jQuery('#task-close-confirm').click(function(event) {
        event.preventDefault();

        var task_id = jQuery(this).data('id');
        var post_data = {id: task_id}
        jQuery.ajax({
            url: '/json/task_close',
            type: 'POST',
            dataType: 'json',
            data: {data: JSON.stringify(post_data)}
        }).done(function(data) {
            if(data.errors == undefined || data.errors == 'undefined') {
                dialog('task-close-container', 'hide');
                load_tasks_table(null);           
            } else {
                show_errors(data.errors);
            }
        }).fail(function() {
            var errors = {general: 'Upps, something weird just happened. Try again please.'}  
            show_errors(errors);
        });
    });

    jQuery('#task-open-confirm').click(function(event) {
        event.preventDefault();

        var task_id = jQuery(this).data('id');
        var post_data = {id: task_id}
        jQuery.ajax({
            url: '/json/task_open',
            type: 'POST',
            dataType: 'json',
            data: {data: JSON.stringify(post_data)}
        }).done(function(data) {
            if(data.errors == undefined || data.errors == 'undefined') {
                dialog('task-open-container', 'hide');
                load_tasks_table(null);           
            } else {
                show_errors(data.errors);
            }
        }).fail(function() {
            var errors = {general: 'Upps, something weird just happened. Try again please.'}  
            show_errors(errors);
        });
    });

    jQuery('.tasks-refresh').click(function(event) {
        event.preventDefault();

        load_tasks_table(jQuery(this).data('page'));
    });

    // Load Tasks table.
    load_tasks_table(1);
});