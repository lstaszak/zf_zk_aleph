$(document).ready(function () {
  if (!$("#dialog_msg").length)
    $('<div id="dialog_msg" title=""><p></p></div>').appendTo("body").hide();
  if (!$("#ajax_overlay").length)
    var $ajax_overlay = $('<div id="ajax_overlay"><p>Proszę czekać, trwa ładowanie danych...</p><div class="waiting"><img src="/skins/default/gfx/ajax_loader.gif" alt="" /></div></div>').css("height", "150px").appendTo("body").hide();
  else
    $ajax_overlay = $("#ajax_overlay");
  $ajax_overlay.dialog({
    autoOpen: false,
    autoResize: true,
    width: 300,
    modal: true,
    minHeight: 169,
    closeOnEscape: false,
    resizable: false,
    open: function (event, ui) {
      $ajax_overlay.css("height", "150px");
      $ajax_overlay.prev(".ui-dialog-titlebar-close").hide();
      $ajax_overlay.prev(".ui-dialog-titlebar").hide();
    }
  });
  $(".ui-dialog").css({
    "z-index": "999999"
  });
  $(document).on("ajaxStart.mine", function () {
    $ajax_overlay.css("height", "150px");
    $ajax_overlay.dialog("open");
  });
  $(document).on("ajaxStop.mine", function () {
    $ajax_overlay.css("height", "150px");
    $ajax_overlay.dialog("close");
  });
  $(".topnav li").eq(3).after("<li><a target=\"_blank\" href=\"http://tceu-primo.hosted.exlibrisgroup.com/primo_library/libweb/action/search.do?vid=48WAT_VIEW\" id=\"menu-0\">Złóż nowe zamówienia</a></li>");
});
