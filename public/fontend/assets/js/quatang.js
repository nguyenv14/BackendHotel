$(document).ready(function(){
    $('.giftlist-slider').owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        dots:false,
        autoplay:true,
        autoplayTimeout:2000,
        smartSpeed:1200,
        touchDrag:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:2
            },
            1000:{
                items:4
            }
        }
    })
  });

  $(document).ready(function(){

    // $(window).scroll(function(event){
    //     var pos_body = $('html,body').scrollTop();
    //     console.log(pos_body);
    // });

    $('#quatang').on('click',function(event){
        event.preventDefault(); /* Hủy sự kiện thẻ a */
        $('html,body').animate({scrollTop:1},340);
    });

    $('#thele').on('click',function(event){
        event.preventDefault();
        $('html,body').animate({scrollTop:720},400);
    });

    $('#bangxephang').on('click',function(event){
        event.preventDefault();
        $('html,body').animate({scrollTop:1158},400);
    });

  });