$(document).ready(function () {
    
    $('.addfilter').click(function () {
    
        $('#filterbar-clones .filter').clone(true, true).insertBefore('#filterbar form input[type=submit]').show(); 
    });
    
    $('.removefilter').click(function () {
    
        $(this).parents('.filter').remove();
    });
    
    $('.filter select').change(function () {
        
        var filter = $(this).parents('.filter'); //console.log(filter.css('border','1px solid black'));
        
        //current input
        var current = filter.find('.value-fields input');
        
        //remove current input
        filter.find('.value-fields').html('');
        
        //clone correct input
        var newInput = $('#filterbar-clones .value-fields span[filtername=' + $(this).val() + ']')
                .clone(true, true)
                .appendTo(filter.find('.value-fields'))
                .find('input');
        
        
        if (newInput.attr('type') === current.attr('type')) {
            
            newInput.val(current.val());
        }
        
        
    });

    
});