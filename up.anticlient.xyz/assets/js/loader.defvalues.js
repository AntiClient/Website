$( document ).ready(function() {
  var mClick = false;
  $("body").removeClass("preload");
  $("#dropd").click(function(e) {
    if($(".dropdown-content").is(":hidden")) {
      $(".dropdown-content").slideDown();
      e.stopPropagation();
    } else {
      $(".dropdown-content").slideUp();
    }
  });
  $(document).click(function() {
    if($(".dropdown-content").not(":hidden")) {
      $(".dropdown-content").slideUp();
    }
  });
  $("#opm").click(function() {
    $(".vertical-wrapper").animate({width:'toggle'},350);
    mClick = false;
  });
  $("#clm").click(function() {
    $(".vertical-wrapper").animate({width:'toggle'},350);
    mClick = true;
  });
  $( window ).resize(function() {
    if($( window ).width() > 600) {
      if($(".vertical-wrapper").is(":hidden")) {
        $(".vertical-wrapper").animate({width:'toggle'},350);
      }
    }
  });
});
