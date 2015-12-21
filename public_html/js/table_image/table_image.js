var table_image = {
  init: function(num_rows) {
    this.option.baseurl = "";
    this.option.$table = $(".paginated");
    this.option.$pager = $("<div class=\"pager\"></div>");
    this.option.num_rows = num_rows;
    this.option.num_row_per_page = 100;
    this.option.max_pager_count = 20;
    this.option.curr_pager_part = 0;
    this.option.curr_page = 0;
    this.translate();
    this.set_num_pages();
    this.add_num_row_per_page();
    this.add_filter_button();
    this.add_reset_button();
    this.add_detete_button();
    this.add_upload_file_button();
    this.add_paginator();
    this.add_sortable();
    this.add_filterable();
    this.check_paginator();
    if (num_rows === 0) {
      $(".table_button").hide();
      $("#num_row_per_page-button").hide();
      this.option.$pager.hide();
      this.option.$table.hide();
    }
    this.load_data(false);
    this.save_image_addto();
    this.save_image_settings();
  },
  option: {
    baseurl: null,
    $table: null,
    $pager: null,
    num_rows: null,
    num_row_per_page: null,
    max_pager_count: null,
    curr_pager_part: null,
    curr_page: null,
    num_pages: null,
    last_page: null,
    sortable_th: null,
    sort_method: null,
    sort_column: null,
    sort_column_eq: null,
    $filterable_th: null
  },
  set_num_rows: function(num_rows) {
    this.option.num_rows = num_rows;
  },
  get_num_rows: function() {
    return this.option.num_rows;
  },
  set_num_pages: function() {
    this.option.num_pages = Math.ceil(this.option.num_rows / this.option.num_row_per_page);
    this.option.last_page = this.option.num_pages - 1;
  },
  get_num_pages: function() {
    return this.option.num_pages;
  },
  translate: function() {
  },
  add_filter_button: function() {
    var that = this;
    var $button = $("<button id=\"choose\" class=\"table_button\">" + translator.get("btn_filtruj") + "</button>").on("click", function() {
      $(".pager").empty();
      that.option.curr_page = 0;
      that.add_paginator();
      that.check_paginator();
      that.load_data(false);
    }).button();
    $button.insertBefore(that.option.$table);
  },
  add_reset_button: function() {
    var that = this;
    var $button = $("<button id=\"reset\" class=\"table_button\">" + translator.get("btn_resetuj") + "</button>").on("click", function() {
      $(".pager").empty();
      that.option.curr_page = 0;
      that.clear_filterable();
      that.add_paginator();
      that.check_paginator();
      that.load_data(false);
    }).button().css({
      "margin-left": "0px"
    });
    $button.insertBefore(that.option.$table);
  },
  add_detete_button: function() {
    var that = this;
    var $button = $("<button id=\"delete\" class=\"table_button\">" + translator.get("btn_usun") + "</button>").on("click", function() {
      $(".pager").empty();
      that.option.curr_page = 0;
      that.clear_filterable();
      that.add_paginator();
      that.check_paginator();
      var data = {};
      $.each($(".checkbox:checked"), function(index, value) {
        if ($(value).attr("name") !== "check_all_top")
          data[index] = parseInt($(value).attr("id").split("-")[1]);
      });
      dialog_confirm_del_image.init(data);
      dialog_confirm_del_image.display(translator.get("dialog_potwierdzenie"), translator.get("dialog_wykonanie_akcji") + " <strong>" + $(this).html() + "</strong>?");
    }).button().css({
      "margin-left": "0px"
    });
    $button.insertBefore(that.option.$table);
  },
  add_upload_file_button: function() {
    var that = this;
    var $button = $("<button id=\"upload_new_file\">" + translator.get("btn_dodaj") + "</button>").on("click", function() {
      add_file.init();
      add_file.display();
      postformrender();
    }).button().css({
      "margin-left": "0px"
    });
    $button.insertBefore(that.option.$table);
  },
  add_num_row_per_page: function() {
    var that = this;
    var select = "";
    select += "<select id=\"num_row_per_page\" name=\"num_row_per_page\">";
    select += "<option value=\"20\">20</option>";
    select += "<option value=\"50\">50</option>";
    select += "<option value=\"100\" selected>100</option>";
    select += "<option value=\"200\">200</option>";
    select += "</select>";
    select += "<div class=\"clear\"></div>";
    $(select).width(60).css({
      "cursor": "pointer",
      "margin": "5px 0px 5px 0px"
    }).insertBefore(that.option.$table);
    $("#num_row_per_page").on("change", function(event) {
      $(".pager").empty();
      that.option.curr_pager_part = 0;
      that.option.curr_page = 0;
      that.option.num_row_per_page = parseInt($(this).find("option:selected").html());
      that.set_num_pages();
      that.add_paginator();
      that.check_paginator();
      that.load_data(false);
      $(this).selectmenu();
    }).selectmenu();
  },
  add_paginator: function() {
    var that = this;
    for (var page = 0; page < that.option.num_pages; page++) {
      $("<span class=\"page_number\"></span>").text(page + 1).on("click", {
        new_page: page
      }, function(event) {
        var $t = $(this);
        if ($t.hasClass("click_able")) {
          $t.parent(".pager").find("span").removeClass("active").end().end().addClass("active");
          that.option.curr_page = event.data["new_page"];
          that.check_paginator();
          that.load_data(false);
        }
      }).appendTo(that.option.$pager).addClass("click_able");
    }
    that.option.$pager.insertBefore(that.option.$table);
    that.option.$pager.find(".page_number").eq(that.option.curr_page).addClass("active");
    $("<span class=\"prev\"><< " + translator.get("btn_poprzedni") + "</span>").addClass("click_able").prependTo(that.option.$pager);
    $("<span class=\"next\">" + translator.get("btn_nastepny") + " >></span>").addClass("click_able").appendTo(that.option.$pager);
    $(".prev").on("click", function() {
      if (!$(this).hasClass("click_not_able")) {
        that.nav("prev");
      }
    });
    $(".next").on("click", function() {
      if (!$(this).hasClass("click_not_able")) {
        that.nav("next");
      }
    });
    $(".pager span.page_number").hide().slice(that.option.curr_pager_part * that.option.max_pager_count, (that.option.curr_pager_part + 1) * that.option.max_pager_count).show();
  },
  check_paginator: function() {
    var that = this;
    if (that.option.curr_page === 0) {
      if (that.option.num_pages === 1) {
        $(".prev").addClass("click_not_able");
        $(".next").addClass("click_not_able");
      } else {
        $(".prev").addClass("click_not_able");
        $(".next").removeClass("click_not_able");
      }
    } else if (that.option.curr_page === that.option.last_page) {
      $(".prev").removeClass("click_not_able");
      $(".next").addClass("click_not_able");
    } else {
      $(".prev").removeClass("click_not_able");
      $(".next").removeClass("click_not_able");
    }
  },
  add_sortable: function() {
    var that = this;
    var table_width = that.option.$table.width();
    that.option.$sortable_th = that.option.$table.find("#sort_able th");
    $.each(that.option.$sortable_th, function(index, value) {
      $(value).width((table_width * $(value).data("width")) / 100);
    });
    $.each(that.option.$sortable_th, function(index, value) {
      if ($(value).hasClass("sort")) {
        var $header = $(this);
        $header.addClass("click_able").hover(function() {
          $header.addClass("hover");
        }, function() {
          $header.removeClass("hover");
        }).click(function() {
          var $t = $(this);
          var sort_direction = 1;
          if ($header.hasClass("sort_asc")) {
            sort_direction = -1;
          }
          that.option.$sortable_th.removeClass("sort_asc").removeClass("sort_desc");
          $.each(that.option.$sortable_th.find("span"), function(index, value) {
            if ($(value).html() === "" && $(value).parent().hasClass("sort")) {
              $(value).removeClass("sort_arrow_asc").removeClass("sort_arrow_desc").addClass("sort_arrow");
            }
          });
          if (sort_direction === 1)
            $header.addClass("sort_asc").children("span").eq(1).removeClass("sort_arrow").removeClass("sort_arrow_desc").addClass("sort_arrow_asc");
          else
            $header.addClass("sort_desc").children("span").eq(1).removeClass("sort_arrow").removeClass("sort_arrow_asc").addClass("sort_arrow_desc");
          if ($header.hasClass("sort_asc"))
            that.option.sort_method = "asc";
          else if ($header.hasClass("sort_desc"))
            that.option.sort_method = "desc";
          that.option.sort_column = $t.attr("id");
          that.option.sort_column_eq = $t.index();
          $(".pager").empty();
          that.option.curr_page = 0;
          that.add_paginator();
          that.check_paginator();
          that.load_data(false);
        });
      }
    });
  },
  add_filterable: function() {
    var that = this;
    var table_width = that.option.$table.width();
    that.option.$filterable_th = that.option.$table.find("#filter_able th");
    $.each(that.option.$filterable_th, function(index, value) {
      var $t = $(this);
      if ($t.hasClass("filter")) {
        var idx = $t.index();
        var $sortable = that.option.$sortable_th.eq(idx);
        var width = $sortable.data("width");
        var name = $sortable.attr("id");
        var $input = $("<input>").attr("name", name).attr("id", "filter_" + name).css({
          "width": ((table_width * width) / 100) - 5,
          "padding-left": "0px"
        }).on("keypress", function(e) {
          if (e.keyCode === 13) {
            $(".pager").empty();
            that.option.curr_page = 0;
            that.add_paginator();
            that.check_paginator();
            that.load_data(false);
          }
        });
        $t.append($input);
        $("#filter_created_date").datepicker({
          dateFormat: 'yy-mm-dd',
          showOtherMonths: true,
          selectOtherMonths: true,
          numberOfMonths: 1
        });
      }
    });
  },
  clear_filterable: function() {
    var that = this;
    $.each(that.option.$filterable_th.find("input"), function(index, value) {
      $(value).val("");
    });
  },
  nav: function(id) {
    var that = this;
    var $button_curr_page = null;
    var sort_direction = null;
    id === "next" ? sort_direction = 1 : sort_direction = -1;
    if (that.option.curr_page >= 0 && that.option.curr_page <= that.option.num_pages) {
      if ((that.option.curr_page === (that.option.last_page)) && id === "next") {
        sort_direction = 0;
      }
      if (that.option.curr_page === 0 && id === "prev") {
        sort_direction = 0;
      }
      that.option.curr_page = that.option.curr_page + sort_direction;
      $button_curr_page = $(".pager span.page_number").eq(that.option.curr_page);
      if ($button_curr_page.is(":hidden")) {
        id === "next" ? that.option.curr_pager_part++ : that.option.curr_pager_part--;
      }
      $button_curr_page.end().removeClass("active");
      $button_curr_page.addClass("active");
      $(".pager span.page_number").hide().slice(that.option.curr_pager_part * that.option.max_pager_count, (that.option.curr_pager_part + 1) * that.option.max_pager_count).show();
      that.check_paginator();
      that.load_data(false);
    }
  },
  load_data: function(reinit_pager) {
    var that = this;
    var data = {};
    var url = baseurl + "/admin/media/loaddata";
    if (reinit_pager) {
      if (that.option.$table.find("tbody tr").length === 0)
        that.option.curr_page = that.option.curr_page - 1;
    }
    data["num_row_per_page"] = parseInt(that.option.num_row_per_page);
    data["curr_page"] = parseInt(that.option.curr_page + 1);
    data["sort_column"] = that.option.sort_column !== null ? that.option.sort_column : "created_date";
    data["sort_method"] = that.option.sort_method !== null ? that.option.sort_method : "desc";
    $.each($("input[id^=filter_]"), function(index, value) {
      data[$(value).attr("id")] = $(value).val();
    });
    $.ajax({
      url: url,
      type: "POST",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp !== null) {
          var $tbody = that.option.$table.find("tbody");
          var tr = "";
          var num_rows = resp["num_rows"];
          var rowset = resp["rowset"];
          $tbody.empty();
          if (num_rows !== that.get_num_rows()) {
            that.reinit(num_rows);
          }
          if (num_rows > 0) {
            $(".table_button").show();
            $("#num_row_per_page-button").show();
            that.option.$pager.show();
            that.option.$table.show();
            $.each(rowset, function(index, value) {
              var image_id = value["id"];
              var user_image_id = value["user_image_id"];
              if (user_image_id === null)
                user_image_id = value["user_id"];
              var image_src = host + baseurl + "/user/upload/user_id_" + user_image_id + "/normal_" + value["name"];
              var image_thumb = host + baseurl + "/user/upload/user_id_" + user_image_id + "/min_" + value["name"];
              tr += "<tr id=\"" + image_id + "\" class=\"image_id_" + image_id + "\">";
              tr += "<td><input class=\"checkbox\" type=\"checkbox\" value=\"1\" id=\"checked-" + image_id + "\" name=\"checkbox\"></td>";
              tr += "<td><a title=\"" + (value["descr"] !== null ? value["descr"] : "") + "\" href=\"" + image_src + "\" rel=\"prettyPhoto[gallery]\"><img alt=\"" + value["user_name"] + "\" title=\"" + (value["descr"] !== null ? value["descr"] : "") + "\" src=\"" + image_thumb + "\" /></a></td>";
              tr += "<td>";
              tr += "<p><strong>Nazwa: </strong><span class=\"user_name\">" + value["user_name"] + "</span></p>";
              tr += "<p><strong>Zasób: </strong><span class=\"image_type_name\">" + value["type_name"] + "</span></p>";
              tr += "<p><strong>Moduł: </strong><span class=\"image_module_name\">" + (value["module_name"] !== null ? value["module_name"] : "-") + "</span></p>";
              tr += "<p><strong>Galeria: </strong><span class=\"image_gallery_name\">" + (value["gallery_name"] !== null ? value["gallery_name"] : "-") + "</span></p>";
              tr += "<p><strong>Slider: </strong><span class=\"image_slider_name\">" + (value["slider_name"] !== null ? value["slider_name"] : "-") + "</span></p>";
              tr += "</td>";
              tr += "<td><p>" + value["user_created_date"] + "</p></td>";
              tr += "<td></td>";
              tr += "<td></td>";
              tr += "<td></td>";
              tr += "</tr>";
            });
            $tbody.empty().append(tr);
            $.each($tbody.find("tr").find("td:eq(4)"), function(index, value) {
              var $button = $("<span class=\"addto_img\" title=\"Dodaj do...\"></span>").on("click", function() {
                var rpc_ajaxrender = jQuery.Zend.jsonrpc({
                  url: baseurl + "/admin/media/ajaxrender"
                });
                var form = rpc_ajaxrender.renderForm(1);
                $("#dialog_form").empty().append(form);
                $(".errors_tip").remove();
                postformrender();
                $.each($(".form input:text"), function(index, value) {
                  if ($(value).hasClass("field_error"))
                    $(value).removeClass("field_error");
                });
                var image_id = $(this).parents("tr").attr("id");
                $("#addto_type_id").on("change", function(event) {
                  var selected_value = parseInt($(this).selectmenu("value")) + 1;
                  if (selected_value === 1) {
                    $("#addto_gallery_id").selectmenu("enable").parents("dl").show().next(".clear").show();
                    $("#addto_slider_id").selectmenu("disable").parents("dl").hide().next(".clear").hide();
                  } else if (selected_value === 2) {
                    $("#addto_gallery_id").selectmenu("disable").parents("dl").hide().next(".clear").hide();
                    $("#addto_slider_id").selectmenu("enable").parents("dl").show().next(".clear").show();
                  }
                });
                $(".form").append($("<p class=\"image_id\">" + image_id + "</p>").hide());
                $("#addto_slider_id").parents("dl").hide();
                $("#addto_type_id").trigger("change");
                image_addto_dialog.init();
                image_addto_dialog.display();
                $("#addto_slider_id").parents("dl").next().hide();
                $.each($(".form"), function(index, value) {
                  var form_height = parseInt($(value).actual("outerHeight", {
                    includeMargin: true
                  }));
                  $(value).parents(".form_wrapper").height(form_height + 80);
                  $(value).parents(".form_box").height(form_height + 80 - 20);
                });
              });
              $(value).append($button);
            });
            $.each($tbody.find("tr").find("td:eq(5)"), function(index, value) {
              var $button = $("<span class=\"edit_img\" title=\"Zmień\"></span>").on("click", function() {
                var rpc_ajaxrender = jQuery.Zend.jsonrpc({
                  url: baseurl + "/admin/media/ajaxrender"
                });
                var form = rpc_ajaxrender.renderForm(2);
                $("#dialog_form").empty().append(form);
                $(".errors_tip").remove();
                postformrender();
                $.each($(".form input:text"), function(index, value) {
                  if ($(value).hasClass("field_error"))
                    $(value).removeClass("field_error");
                });
                var image_id = $(this).parents("tr").attr("id");
                var rpc_image_settings = jQuery.Zend.jsonrpc({
                  url: baseurl + "/admin/media/imagesettings"
                });
                var image_settings = rpc_image_settings.getSettings(image_id);
                $("#image_src").val(image_settings.name).attr("readonly", true);
                $("#image_user_name").val(image_settings.user_name);
                $("#image_descr").val(image_settings.descr);
                $(".form").append($("<p class=\"image_id\">" + image_id + "</p>").hide());
                image_settings_dialog.init();
                image_settings_dialog.display();
                $.each($(".form"), function(index, value) {
                  var form_height = parseInt($(value).actual("outerHeight", {
                    includeMargin: true
                  }));
                  $(value).parents(".form_wrapper").height(form_height + 80);
                  $(value).parents(".form_box").height(form_height + 80 - 20);
                });
              });
              $(value).append($button);
            });
            $.each($tbody.find("tr").find("td:eq(6)"), function(index, value) {
              var image_id = $(value).parents("tr").attr("id");
              var $button = $("<span class=\"delete_img\" title=\"Usuń\"></span>").on("click", function(event) {
                var $v = $(this);
                dialog_confirm.init($v);
                dialog_confirm.display(translator.get("dialog_potwierdzenie"), translator.get("dialog_usuniecie_zdjecia"));
              }).attr("id", "image_delete_id_" + image_id);
              $(value).append($button);
            });
            $("a[rel^='prettyPhoto']").prettyPhoto({
              animation_speed: "normal",
              slideshow: 3000,
              autoplay_slideshow: false,
              overlay_gallery: false
            });
            $("#check_all_top").on("click", function() {
              $("input[type=checkbox]").attr("checked", $(this).is(":checked"));
              if ($(this).is(":checked"))
                $(":checkbox:not(#check_all_top)").parents("tr").addClass("is_checked ui-selected");
              else
                $(":checkbox:not(#check_all_top)").parents("tr").removeClass("is_checked ui-selected");
            });
            $(":checkbox:not(#check_all_top)").on("click", function() {
              var is_checked = $(this).is(":checked");
              if (is_checked)
                $(this).parents("tr").addClass("is_checked ui-selected");
              else
                $(this).parents("tr").removeClass("is_checked ui-selected");
            });
            that.option.$table.alternateRowColors();
          } else {
            $(".table_button").hide();
            $("#num_row_per_page-button").hide();
            that.option.$pager.hide();
            that.option.$table.hide();
            var filter = {};
            $.each($("input[id^=filter_]"), function(index, value) {
              if ($(value).val() !== "") {
                filter[$(value).attr("id")] = $(value).val();
              }
            });
            if (Object.keys(filter).length) {
              $("#reset").trigger("click");
            }
          }
        }
      },
      error: function(resp) {

      }
    });
  },
  save_image_addto: function() {
    $("body").on("click", "#image_addto_submit", function(event) {
      var image_id = parseInt($("p.image_id").html());
      var rpc_image_addto = jQuery.Zend.jsonrpc({
        url: baseurl + "/admin/media/imageaddto"
      });
      var selected_value = parseInt($("#addto_type_id").selectmenu("value")) + 1;
      var element_id = null, element_name = null;
      if (selected_value === 1) {
        element_id = $("#addto_gallery_id").selectmenu("value");
        element_name = $("#addto_gallery_id").find("option[value=" + element_id + "]").html();
      } else if (selected_value === 2) {
        element_id = $("#addto_slider_id").selectmenu("value");
        element_name = $("#addto_slider_id").find("option[value=" + element_id + "]").html();
      }
      var is_save = rpc_image_addto.setSettings(image_id, selected_value, element_id);
      if (is_save === true) {
        var $image_id = $("#" + image_id);
        if (selected_value === 1)
          $image_id.find("td:eq(2) span.image_gallery_name").html(element_name);
        else if (selected_value === 2)
          $image_id.find("td:eq(2) span.image_slider_name").html(element_name);
      }
      event.preventDefault();
      event.stopPropagation();
    });
  },
  save_image_settings: function() {
    $("body").on("click", "#image_settings_submit", function(event) {
      var image_id = parseInt($("p.image_id").html());
      var rpc_image_settings = jQuery.Zend.jsonrpc({
        url: baseurl + "/admin/media/imagesettings"
      });
      var user_name = $("#image_user_name").val();
      var descr = $("#image_descr").val();
      var is_save = rpc_image_settings.setSettings(image_id, user_name, descr);
      if (is_save === true) {
        var $image_id = $("#" + image_id);
        $image_id.find("td:eq(1) a").attr("title", descr).end().find("td:eq(1) img").attr("alt", user_name).end().find("td:eq(2) span.user_name").html(user_name);
        $("a[rel^='prettyPhoto']").prettyPhoto({
          animation_speed: "normal",
          slideshow: 3000,
          autoplay_slideshow: false
        });
      }
      event.preventDefault();
      event.stopPropagation();
    });
  },
  reinit: function(num_rows) {
    var that = this;
    $(".pager").empty();
    if (num_rows !== 0) {
      that.set_num_rows(num_rows);
      that.set_num_pages();
      that.add_paginator();
      that.check_paginator();
    }
  }
};
var image_addto_dialog = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  init: function() {
    this.window = $("#dialog_form");
  },
  display: function() {
    this.btn[translator.get("btn_zamknij")] = function() {
      $(this).dialog("close");
    };
    this.window.attr("title", translator.get("dialog_edytuj_zdjecie"));
    this.window.dialog({
      autoOpen: false,
      width: 785,
      height: "auto",
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
      },
      beforeClose: function(event, ui) {
      }
    });
    this.window.dialog("open");
  }
};
var image_settings_dialog = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  init: function() {
    this.window = $("#dialog_form");
  },
  display: function() {
    this.btn[translator.get("btn_zamknij")] = function() {
      $(this).dialog("close");
    };
    this.window.attr("title", translator.get("dialog_edytuj_zdjecie"));
    this.window.dialog({
      autoOpen: false,
      width: 785,
      height: "auto",
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
      },
      beforeClose: function(event, ui) {
      }
    });
    this.window.dialog("open");
  }
};
var add_file = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  init: function() {
    this.window = $("#dialog_add_file");
  },
  display: function() {
    this.btn[translator.get("btn_zamknij")] = function() {
      $(this).dialog("close");
    };
    this.window.attr("title", translator.get("dialog_dodaj_zdjecie"));
    this.window.dialog({
      autoOpen: false,
      width: 785,
      height: "auto",
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
      },
      beforeOpen: function(event, ui) {

      }
    });
    this.window.dialog("open");
  }
};
$(document).ready(function() {
  jQuery.fn.alternateRowColors = function() {
    $.each($("tbody tr"), function(index, value) {
      if (!(index % 2))
        $(value).removeClass("odd").addClass("even");
      else
        $(value).removeClass("even").addClass("odd");
    });
    return this;
  };
});