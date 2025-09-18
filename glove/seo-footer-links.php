<!--<div class="modal fade" id="myModal_popup" role="dialog">-->
<!--    <div class="modal-dialog mv1" style="">-->
<!--      <div class="modal-content">-->
        <!--<div class="modal-header">-->
        
        <!--</div>-->
<!--        <div class="modal-body">-->
<!--              <button type="button" class="close" data-dismiss="modal">&times;</button>-->
<!--          <img src="../img/add-popup.jpg" alt="popup" style="width: 100%"  />-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>      -->
        <!--Scroll-up-->
        <a id="scroll-up"><i class="fa fa-angle-up"></i></a>
        <!-- jequery-->
        <script src="../assets\js\vendor\jquery-1.12.0.min.js"></script>
        <!-- Bootstrap min.js  -->
        <script src="../assets\bootstrap\js\bootstrap.min.js"></script>
        <!-- slick slider js  -->
        <script src="../assets\js\slick.min.js"></script>
        <!-- isotope min.js  -->
        <script src="../assets\js\isotope.min.js"></script>
        <!-- imageloaded js-->
        <script src="../assets\js\imagesloaded.pkgd.min.js"></script>
        <!--meanmenu js -->
        <script src="../assets\js\jquery.meanmenu.js"></script>
        <!-- magnific-popup js  -->
        <script src="../assets\js\jquery.magnific-popup.min.js"></script>
        <!-- counterup js-->
        <script src="../assets\js\jquery.counterup.min.js"></script>
        <script src="../assets\js\waypoints.min.js"></script>
        <!--validate js -->
        <script src="../assets\js\jquery.validate.js"></script>
        <!--main js-->
        <script src="../assets/js/main.js"></script>
        <script src="../js/main.js"></script>
        <script src="https://cwc.livserv.in/chat.js?lid=19608" id="lp_cwc_xqzyihjdskw" ></script>
<script src="https://cw1.livserv.in?did=19608&amp;pid=1"></script>


	<script type="text/javascript">
                $('.translation-links a').on('click', function() {
                    
                    //alert("ooo");
            		//$(".skiptranslate").hide();
            		//$("body").css("top", "0px");
            		
            	//var lang =	$(this).find(':selected').data('lang');
            		
                  var lang = $(this).data('lang');
                  
                  var $frame = $('.goog-te-menu-frame:first');
                  if (!$frame.size()) {
                    alert("Error: Could not find Google translate frame.");
                    return false;
                  }
                  $frame.contents().find('.goog-te-menu2-item span.text:contains('+lang+')').get(0).click();
                  return false;
            	  
            	  
                });
            </script> 