$(document).ready(function () {
    
    $('.thumb').click(function() {

        $('#haze').show();
        $('.preview').hide();
        $(this).next('.preview').show();
        
        $('#file-options').show();
        $('#file-options .download a').removeClass('active');
        $('#file-options .download a[file-id=' + $(this).parent('.file').attr('file-id') + ']').addClass('active');
                
        return false;
    });
    
    $('.preview, #haze').click(function() {
        
        $('#haze').hide();
        $('#file-options').hide();
        $('.preview').hide();
    });
});