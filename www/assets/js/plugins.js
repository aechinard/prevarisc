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

  /*
  # MonkeyPatch pour traduire fullCalendar en français
  */
  var fullCalendar;
  fullCalendar = $.fn.fullCalendar;
  $.fn.fullCalendar = function(options) {

    options.buttonText = options.buttonText || {month: 'Mois', day: 'Jour', week: 'Semaine', today: 'Aujourd\'hui'};
    options.header = options.header || {left: 'prev,next today', center: 'title', right: 'month,agendaWeek,agendaDay'};
    options.titleFormat = options.titleFormat || {month: 'MMMM yyyy', week: "dd[ MMMM][ yyyy]{ '&#8212;' dd MMMM yyyy}", day: 'dddd dd MMMM yyyy'};
    options.firstDay = options.firstDay || 1;
    options.monthNames = options.monthNames || ['Janvier','F\u00e9vrier','Mars','Avril','Mai','Juin','Juillet','Ao\u00fbt','Septembre','Octobre','Novembre','D\u00e9cembre'];
    options.monthAbbrevs = options.monthAbbrevs || ['Jan','Fev','Mar','Avr','Mai','Juin','Juil','Aout','Sep','Oct','Nov','Dec'];
    options.dayNames = options.dayNames || ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
    options.dayNamesShort = options.dayNamesShort || ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
    options.weekMode = options.weekMode || 'liquid';
    options.timeFormat = options.timeFormat || 'H:mm{ - H:mm}';
    options.aspectRatio = options.aspectRatio || 2;
    options.axisFormat = options.axisFormat || 'H(:mm)';

    return fullCalendar.apply(this, arguments);
  };

  /*
  # MonkeyPatch pour traduire multiselect en français
  */
  var multiselect;
  multiselect = $.fn.multiselect;
  $.fn.multiselect = function(options) {

    var self = this;

    options.selectAllText = options.selectAllText || "Tout sélectionner";
    options.nSelectedText = options.nSelectedText || "sélectionné";

    options.buttonText = options.buttonText || function(options, select) {
        if (options.length == 0) {
            return this.nonSelectedText + ' <b class="caret"></b>';
        }
        else {
            if (options.length > this.numberDisplayed) {
                return options.length + ' ' + this.nSelectedText + (options.length > 1 ? 's' : '') + ' <b class="caret"></b>';
            }
            else {
                var selected = '';
                options.each(function() {
                    var label = ($(this).attr('label') !== undefined) ? $(this).attr('label') : $(this).html();
                    selected += label + ', ';
                });
                return selected.substr(0, selected.length - 2) + ' <b class="caret"></b>';
            }
        }
    };

    return multiselect.apply(this, arguments);
  };

})(jQuery);
