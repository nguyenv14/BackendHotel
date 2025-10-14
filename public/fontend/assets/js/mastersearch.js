
  $(document).ready(function(){ 
    $(window).scroll(function(){
        if($(this).scrollTop()){
            $('.BoxSearch-infohotel').addClass('BoxSearch-fixed');
            $('.BoxSearch-End').addClass('BoxSearch-End-Show');
        }else{
            $('.BoxSearch-infohotel').removeClass('BoxSearch-fixed');
            $('.BoxSearch-End').removeClass('BoxSearch-End-Show');
        }
    });   
});

