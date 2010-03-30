// Dom Ready
jQuery(document).ready(function($) {
      
    // Dropdown menu support for IE
    $('.dropdown li').hover(function(){
        $(this).addClass('hover');
    }, function(){
        $(this).removeClass('hover');
    });

});