//**All config js of theme
(function ($) {
    'use strict';
    jQuery(document).ready(function ($) {
        ab_avada();
    });
    //Image map
    function ab_avada(){
        $(document).on('click','.fusion-selector-down', function(){
        	console.log("OK");
        	$(this).parent().find('.nav-mobile').slideToggle();
        });
    };
})(jQuery);