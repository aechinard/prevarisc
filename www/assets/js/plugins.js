(function($) {

  /*
  # MonkeyPatch for Jquery hide() and show() to work with Bootstrap 3
  */
  var hide, show;
  show = $.fn.show;
  $.fn.show = function() {
    this.removeClass("hidden hide");
    return show.apply(this, arguments);
  };
  hide = $.fn.hide;
  $.fn.hide = function() {
    this.addClass("hidden hide");
    return hide.apply(this, arguments);
  };

})(jQuery);
