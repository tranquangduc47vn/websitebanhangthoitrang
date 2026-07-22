$(document).ready(function(){
  $(".scroll-link").on('click', function(event) {
    if (this.hash !== "") {
      event.preventDefault();
      var hash = this.hash;
      $('html, body').animate({
        scrollTop: $(hash).offset().top - 30 
      }, 800, function(){
        window.location.hash = hash;
      });
    }
  });
});