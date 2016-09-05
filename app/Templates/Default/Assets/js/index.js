$(document).ready(function () {
    
    $('#docslist.table > div:not(.head)').hover(function() { 
        
        $(this).find('img').toggle(); 
    });
    
});