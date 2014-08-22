document.addEventListener('DOMContentLoaded', function() {

  // Tablesorter
  $(".tablesorter").tablesorter({
    theme: 'default'
  });

  // Affichage des établissements enfants
  $("ul.search-list").on("click", 'li.slide.etablissement', function() {
    var container = this;
    if( $(this).hasClass("active") ) {
      $(this).next().slideUp(400, function() {$(container).next().remove()});
      $(this).toggleClass("active");
    }
    else {
      $(container).find(".load").show();
      $.getJSON("/api/1.0/search/etablissements", {parent: $(this).attr("id"), count: 100}, function(data) {
        $(container).toggleClass("active").find(".load").hide();
        $.post("/search/display-ajax-search", {items: 'etablissement', data: data.response.results}, function(html) {
          $(container).after("<li class='hide child' style='overflow: auto; height: auto;' >" + html + "</li>").next().slideDown();
        });
      });
    }
  });

  // Affichage des établissements enfants
  $("ul.search-list").on("click", 'li.slide.dossier', function() {
    var container = this;
    if( $(this).hasClass("active") ) {
      $(this).next().slideUp(400, function() {$(container).next().remove()});
      $(this).toggleClass("active");
    }
    else {
      $(container).find(".load").show();
      $.getJSON("/api/1.0/search/dossiers", {parent: $(this).attr("id"), count: 100}, function(data) {
        $(container).toggleClass("active").find(".load").hide();
        $.post("/search/display-ajax-search", {items: 'dossier', data: data.response.results}, function(html) {
          $(container).after("<li class='hide child' style='overflow: auto; height: auto;'>" + html + "</li>").next().slideDown();
        });
      });
    }
  });

  // Gestion des boites modales
  $("a[data-toggle='modal']").click(function() {
      var target = $(this).attr("data-target");
      var url = $(this).attr("href");
      $(target).load(url);
  });

  // Marquee sur les listes de recherche
  $('.search-list li').each(function() {
    var li_width = $(this).width();
    var left_width = $(this).find('.pull-left').width();
    var right_width = $(this).find('.pull-right').width();
    if( (left_width + right_width) > li_width) {
      var free_width = li_width - right_width - 20;
      $(this).find('.pull-left').css('width', free_width + 'px').css('overflow', 'hidden').marquee({
        duplicated: true,
        duration: 7500
      });
    }
  });

    // Gestion de la boite de recherche
    $(".navbar.navbar-default.navbar-fixed-top input[name='label']").focus(function() {
        $('.nav.navbar-nav.navbar-left').removeClass('fadeIn').addClass('animated fadeOut');
        $(this).animate({width: "600px",}, 400 );
    }).blur(function() {
        $('.nav.navbar-nav.navbar-left').removeClass('fadeOut').addClass('animated fadeIn');
        $(this).animate({width: "150px",}, 400 );
    });

});
