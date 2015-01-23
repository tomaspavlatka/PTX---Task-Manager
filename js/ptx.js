jQuery(document).ready(function() {  
    var $action_window = jQuery('#action-window');
    
    function action_window(width, height, content) {
        $action_window.css({'width': width, 'height': height}).html(content).show();
    }

    if(jQuery('.new-task').length > 0) {
        jQuery('.new-task').click(function(event) {
            event.preventDefault();

            var form = jQuery('#form-new-task');
            action_window(form.data('width'), form.data('height'), form.html());
        })
    }   

    function load_taks_table() {

        var postData = {
        }
        var dataString = JSON.stringify(postData);

        jQuery.ajax({
            url: '/json/tasks',
            type: 'POST',
            dataType: 'html',
            data: {data: dataString}
        }).done(function(data) {
            console.log(data)
        }).fail(function(data) {
            console.log(data)
        }).always(function() {
            console.log('sent');
        })
    }

    load_taks_table();
});