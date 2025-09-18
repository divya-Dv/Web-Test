(function($) {
  'use strict'; 
 	 /*---------------------------------
        Preloader JS
    -----------------------------------*/ 
    var prealoaderOption = $(window);
    prealoaderOption.on("load", function () {
        var preloader = jQuery('.spinner');
        var preloaderArea = jQuery('.preloader-area');
        preloader.fadeOut();
        preloaderArea.delay(350).fadeOut('slow');
    });
    /*---------------------------------
        Preloader JS
    -----------------------------------*/
 	/*---------------------------------
		Google map js
 	----------------------------------*/
 	$(window).on('load', function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {
              lat: 23.810332,
              lng: 90.412518
            },
            zoom: 13
        });
          // Let's also add a marker while we're at it
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(23.810332, 90.412518),
            map: map,
            icon: {
              url: 'assets/img/marker.png',
            },
            animation: google.maps.Animation.BOUNCE
        });
    });
 	/*---------------------------------
		Google map js
 	----------------------------------*/
})(window.jQuery);  