

(function ($) {
    
 
        var url = window.location.hash.substr(14);
        $('#access_token').val(url);
        var arr = url.split('.'); 
        //console.log(arr);
       $('#instagram_page_id').val(arr[0]);
    
       
   
    
    //console.log(url);
    $(function () {
        $('.colorpick').wpColorPicker();
    });
    $('#save_btn').serialize();
   $('#save_btn').on("click",function(){
   // $('#save_btn').click(function(){    
        var data = $('#wpsf-setting').serialize();
	// We can also pass the url value separately from ajaxurl for front end AJAX implementations
         jQuery.ajax({
            url: ajaxurl,
            data: {action: 'save_feed', data: data},
            type: 'REQUEST',
            async: false,
            dataType: 'json',
            //cache: false,
            success: function (data, textStatus, jqXHR) {
                console.log(data);
            },
            complete: function (data, textStatus, jqXHR) {
                //console.log(data);
            }
        });
     
	
        return false;
    });

    $('#image_width').closest('tr').hide();
    $('#image_height').closest('tr').hide();

    $('#image_size').on('change', function () {
        if ($(this).attr('checked') === 'checked') {
            $('#image_width').closest('tr').show();
            $('#image_height').closest('tr').show();
           
        } else {
            $('#image_width').closest('tr').hide('title');
            $('#image_height').closest('tr').hide();
        }
        console.log($(this).attr('checked'));
    });
    
}(jQuery));

