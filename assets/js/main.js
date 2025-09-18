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
        sticky header JS
    -----------------------------------*/
    $(window).on('scroll',function() {    
        var scroll = $(window).scrollTop();
         if (scroll < 100) {
          $("#header-area").removeClass("sticky");
         }else{
          $("#header-area").addClass("sticky");
         }
    }); 
    /*---------------------------------  
        sticky header JS
    -----------------------------------*/
    /*---------------------------------  
        Meanmenu JS
    -----------------------------------*/ 
    $('.main-menu nav').meanmenu({
      meanMenuContainer: '.mobile-menu',
      meanScreenWidth: "767"
    });
    /*---------------------------------  
        Meanmenu JS
    -----------------------------------*/ 
    /*---------------------- 
        Scroll top js
    ------------------------*/
    $(window).on('scroll', function() {
      if ($(this).scrollTop() > 100) {
          $('#scroll-up').fadeIn();
      } else {
          $('#scroll-up').fadeOut();
      }
    });
    $('#scroll-up').on('click', function() {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });
    /*---------------------- 
        Scroll top js
    ------------------------*/
    /*---------------------- 
        Slick Slider js
    ------------------------*/
    $('.responsive').slick({
        dots: false,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: false,
        arrows:true
    });
    $(".regular").slick({
      dots: true,
      arrows: false,
      autoplay: false,
      infinite: true,
      slidesToShow: 2,
      speed: 400,
      slidesToScroll: 1,
      responsive: [
        {
          breakpoint: 600,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1
          }
        }
      ]
    });
    /*---------------------- 
        Slick Slider js
    ------------------------*/ 
    /*---------------------- 
        COnterup js
    ------------------------*/  
    $('.counter').counterUp({
        delay: 60,
        time: 6000
    });
    /*---------------------- 
        COnterup js
    ------------------------*/ 
    /*---------------------- 
        Isotope js
    ------------------------*/ 
    $('#portfolio').imagesLoaded( function() {
      var $grid = $('.grid').isotope({
        itemSelector: '.grid-item',
        percentPosition: true,
        masonry: {
          // use outer width of grid-sizer for columnWidth
          columnWidth: '.grid-item'
        }
      })
      // filter items on button click
      $('.portfolio-button').on( 'click', 'button', function() {
        var filterValue = $(this).attr('data-filter');
        $grid.isotope({ filter: filterValue });
      });
      // change is-checked class on buttons
      $('.portfolio-button').each(function (i, buttonGroup) {
        var $buttonGroup = $(buttonGroup);
        $buttonGroup.on('click', 'button', function () {
            $buttonGroup.find('.is-checked').removeClass('is-checked');
            $(this).addClass('is-checked');
        });
      });
    });
    /*---------------------- 
        Isotope js
    ------------------------*/
    /*---------------------- 
      magnific-Popup js
    ------------------------*/
    $('.portfolio-single').magnificPopup({
        type: 'image',
        gallery: {
            enabled: true
        }
    });
    /*---------------------- 
      magnific-Popup js
    ------------------------*/
    /*---------------------- 
    Form validation js
    ------------------------*/
    $("#commentForm").validate();
    /*---------------------- 
    Form validation js
    ------------------------*/
    /*---------------------- 
     Ajax Form
    ------------------------*/
    $("#contact-form").submit(function (event) {

        var successMail = '#success' ;
        var errorMail = '#error' ;

        event.preventDefault();



        var formData = $("#contact-form").serialize();

        $("#contact-form :input").prop("disabled", true);

        $.ajax({
            type: 'POST',
            url: $('#contact-form').attr('action'),
            data: formData
        })
            .done(function (response) {
                $(successMail).show();
                $(errorMail).hide();
                $('#contact-form input').val('');
                $('#contact-form textarea').val('');
                $("#contact-form :input").prop("disabled", false);
                console.log(response);
                $(successMail).text(response.success);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {

                var msg = JSON.parse(jqXHR.responseText) ;
                $(errorMail).show();
                $(successMail).hide();
                $("#contact-form :input").prop("disabled", false);
                $(errorMail).text(msg.error);
            });


        return false ;
    })
    /*---------------------- 
     Ajax Form
    ------------------------*/

})(window.jQuery);   