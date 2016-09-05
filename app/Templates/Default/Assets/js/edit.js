$(document).ready(function () {
    
    //activate
    $('.file-delete').click(function() { 
        
        $(this).siblings('a').addClass('file-deleted');
        $(this).hide(); 
        $(this).siblings('.file-activate').css('display','inline-block');
        $(this).siblings('input').prop('disabled', false);
    });
    
    //delete
    $('.file-activate').click(function() { 
        
        $(this).siblings('a').removeClass('file-deleted').removeClass('hover-delete');
        $(this).hide(); 
        $(this).siblings('.file-delete').show();
        $(this).siblings('input').prop('disabled', true);
    });
    
    $('.file-activate, .file-delete').hover(
    function () {
        
        $(this).siblings('a').addClass('hover-delete');
    },
    
    function () {
        
        $(this).siblings('a').removeClass('hover-delete');
    });
    
});