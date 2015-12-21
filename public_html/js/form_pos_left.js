$(document).ready(function() {
  postformrender();
});
function postformrender() {
  $("input[id*=submit]").on("click", function(event) {
    validate_ajax.url = "/admin/validator/validateform";
    validate_ajax.validate($(this));
    if ($(".errors_tip").length) {
      event.preventDefault();
      event.stopPropagation();
    }
  });
  $.each($("select"), function(index, value) {
    if (!$(value).hasClass("multiselect") && !$(value).hasClass("chosen") && !$(value).hasClass("select2")) {
      $(value).selectmenu();
    } else if ($(value).hasClass("chosen")) {
      $(value).chosen().on("change", function() {
        $.each($(".form"), function(index, value) {
          var form_height = parseInt($(value).actual("outerHeight", {
            includeMargin: true
          }));
          $(value).parents(".form_wrapper").height(form_height + 80);
          $(value).parents(".form_box").height(form_height + 80 - 20);
        });
      });
    } else if ($(value).hasClass("select2")) {
      $(value).select2().on("change", function() {
        $.each($(".form"), function(index, value) {
          var form_height = parseInt($(value).actual("outerHeight", {
            includeMargin: true
          }));
          $(value).parents(".form_wrapper").height(form_height + 80);
          $(value).parents(".form_box").height(form_height + 80 - 20);
        });
      });
    }
  });
  if ($(".ckeditor").length) {
    $.each($(".ckeditor"), function(index, value) {
      var id = $(value).attr("id");
      $("#" + id + "-label").css({
        "margin-top": "-10px",
        "margin-bottom": "10px"
      });
      $(value).parents(".form_wrapper").find("#submit-label").remove();
    });
    CKEDITOR.on("instanceReady", function(e) {
      var editor_name = e.editor.name;
      var $form_wrapper = $("#" + editor_name).parents(".form_wrapper");
      var $form_box = $("#" + editor_name).parents(".form_box");
      $form_wrapper.height($form_wrapper.find(".form").actual("outerHeight", {
        includeMargin: true
      }) + 80);
      $form_box.height($form_box.parents(".form_wrapper").actual("outerHeight", {
        includeMargin: true
      }) - 30);
    });
  }
  $.each($(".form"), function(index, value) {
    var form_height = parseInt($(value).actual("outerHeight", {
      includeMargin: true
    }));
    $(value).parents(".form_wrapper").height(form_height + 80);
    $(value).parents(".form_box").height(form_height + 80 - 20);
  });
  $(".form_wrapper").show();
  $.each($(".strong"), function(index, value) {
    var name = $(value).attr("class").split(" ")[0];
    $("." + name).on("click", function() {
      $("#" + name + "_wrapper").toggle();
      if ($("#" + name + "_wrapper").is(":hidden"))
        $(this).find(".yellow").html(translator.get("btn_pokaz_view"));
      else
        $(this).find(".yellow").html(translator.get("btn_ukryj_view"));
    }).css({
      "cursor": "pointer"
    });
  });
  var $dl = $(".form_wrapper dl, #wrapper_upload dl");
  $.each($dl, function(index, value) {
    var width = 275;
    var is_clear = null;
    if ($(value).parent(".inline_element").length) {
      width = $(value).find("input, select").data("width");
      $(value).addClass("pos_left").css({
        "width": width + 8
      });
      $(value).find("dt, dd, dd input").css({
        "width": width
      });
    } else if ($(value).parent("fieldset").length || $(value).find("fieldset").length) {
      $(value).css({
        "position": "relative",
        "width": "900px"
      }).after("<div class=\"clear\"></div>");
      $(value).find("dt, dd").addClass("pos_left").end().find("dt").css({
        "width": width
      });
      is_clear = $(value).next().next();
      if (is_clear.hasClass("clear"))
        is_clear.remove();
    } else {
      $(value).css({
        "position": "relative",
        "width": "900px"
      }).after("<div class=\"clear\"></div>");
      $(value).find("dt, dd").addClass("pos_left").end().find("dt").css({
        "width": width,
        "padding-right": "5px"
      });
      is_clear = $(value).next().next();
      if (is_clear.hasClass("clear"))
        is_clear.remove();
    }
  });
  var $fieldset_dd = $("fieldset dd");
  $fieldset_dd.after("<div class=\"clear\"></div>");
  $.each($fieldset_dd, function(index, value) {
    var is_clear = $(value).next().next();
    if (is_clear.hasClass("clear"))
      is_clear.remove();
  });
  $("#select_user-label").show();
  $(".form").css({
    "display": "block"
  });
  $.each($("input[type=checkbox]"), function(index, value) {
    var id = $(value).attr("id").split("-")[0];
    $("#" + id + "-label label").css({
      "margin-top": "2px"
    });
    $(value).css({
      "cursor": "pointer"
    });
  });
  $("input[id*=submit][type=submit], input[type=submit]").button();
  $("input[id*=submit][type=submit], input[type=submit]").css({
    "cursor": "pointer",
    "width": "auto"
  });
  $("#prev").show();
  $("#next").show();
  $("#success").hide();
  $.each($("button"), function(index, value) {
    if ($(value).attr("id") !== "order_submit_save_and_make_action" && $(value).attr("id") !== "new_order") {
      $(value).button();
      if ($(value).attr("title") !== "close") {
        $(value).css({});
      }
    } else {
      $(value).css({
        //"height": "32px"
      });
    }
  });
  postformvalidator();
}
function postformvalidator() {
  var $input = $(".valid");
  $.each($input, function(index, value) {
    $(value).on("focus", function() {
      var $t = $(this);
      $(".errors_tip").hide();
      $t.parent().find(".errors_tip").show();
    });
  });
}
function extend(from, to) {
  if (from === null || typeof from !== "object")
    return from;
  if (from.constructor !== Object && from.constructor !== Array)
    return from;
  if (from.constructor === Date || from.constructor === RegExp || from.constructor === Function ||
          from.constructor === String || from.constructor === Number || from.constructor === Boolean)
    return new from.constructor(from);
  to = to || new from.constructor();
  for (var name in from) {
    to[name] = typeof to[name] === "undefined" ? extend(from[name], null) : to[name];
  }
  return to;
}
function roundnumber(number) {
  if (number === undefined)
    number = 0;
  number = Number(number.toString().replace(/\,/g, "."));
  if (isNaN(number)) {
    number = 0;
  }
  var rounded_number = number;
  rounded_number = parseFloat(Number(rounded_number)).toFixed(2);
  return rounded_number;
}