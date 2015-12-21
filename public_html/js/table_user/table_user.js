var table_user = {
  init: function(num_rows) {
    this.option.module = "";
    this.option.baseurl = "";
    this.option.$table = $(".paginated");
    this.option.$pager = $("<div class=\"pager\"></div>");
    this.option.num_rows = num_rows;
    this.option.num_row_per_page = 100;
    this.option.max_pager_count = 20;
    this.option.curr_pager_part = 0;
    this.option.curr_page = 0;
    this.set_num_pages();
    this.add_num_row_per_page();
    this.add_filter_button();
    this.add_reset_button();
    this.add_paginator();
    this.add_sortable();
    this.add_filterable();
    this.add_detete_button();
    this.check_paginator();
    if (num_rows === 0) {
      $(".table_button").hide();
      $("#num_row_per_page-button").hide();
      this.option.$pager.hide();
      this.option.$table.hide();
    }
    this.load_data(false);
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
    $sortable_th: null,
    sort_method: null,
    sort_column: null,
    sort_column_eq: null
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
      that.add_paginator();
      that.check_paginator();
      var data = {};
      $.each($(".checkbox:checked"), function(index, value) {
        if ($(value).attr("name") !== "check_all_top")
          data[index] = parseInt($(value).attr("id").split("-")[1]);
      });
      dialog_confirm_del_user.init(data);
      dialog_confirm_del_user.display(translator.get("dialog_potwierdzenie"), translator.get("dialog_wykonanie_akcji") + " <strong>" + $(this).html() + "</strong>?");
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
  add_other_buttons: function() {
    var that = this;
    var $enable_button = $(".enable_user");
    var $disable_button = $(".disable_user");
    var $edit_button = $(".edit_user");
    var $delete_button = $(".delete_user");
    $enable_button.on("click", function() {
      var $t = $(this).parents("tr");
      var data = {};
      data["id"] = $t.attr("id").split("_")[1];
      $.ajax({
        async: true,
        url: that.option.baseurl + "/admin/settings/enableuser",
        type: "post",
        dataType: "json",
        data: data,
        success: function(resp) {
          if (resp)
            window.location = window.location.href;
        },
        error: function(resp) {
          dialog_msg.init();
          dialog_msg.error();
        }
      });
    });
    $disable_button.on("click", function() {
      var $t = $(this).parents("tr");
      var data = {};
      data["id"] = $t.attr("id").split("_")[1];
      $.ajax({
        async: true,
        url: that.option.baseurl + "/admin/settings/disableuser",
        type: "post",
        dataType: "json",
        data: data,
        success: function(resp) {
          if (resp)
            window.location = window.location.href;
        },
        error: function(resp) {
          dialog_msg.init();
          dialog_msg.error();
        }
      });
    });
    $edit_button.on("click", function() {
      var $t = $(this).parents("tr");
      var user_id = $t.attr("id").split("_")[1];
      $("#user_edit_id").val(user_id);
      $("#user_picture_id").empty();
      var url = that.option.baseurl + "/admin/settings/getuserimage";
      var data = {};
      data["add_photo_user_id"] = user_id;
      $.ajax({
        async: false,
        url: url,
        type: "POST",
        dataType: "json",
        data: data,
        success: function(resp) {
          if (resp !== null) {
            $("#role_id").selectmenu("value", resp["role_id"]);
            $("#user_fb_id").val(resp["user_fb_id"]);
            $("#first_name").val(resp["first_name"]);
            $("#last_name").val(resp["last_name"]);
            $("#email_address").val(resp["email_address"]);
            $("#phone_number").val(resp["phone_number"]);
            if (resp["is_active"] === 1) {
              $("#is_active").attr("checked", true);
            }
            if (resp["image"] !== null) {
              var new_photo = "<img alt=\"\" title=\"\" src=\"" + that.option.baseurl + "/user/upload/user_id_" + user_id + "/normal_" + resp["image"] + "\">";
              $("#user_picture_id").empty().append(new_photo);
            }
            $("#user_fb_id, #email_address, #email_address_confirm, #password").attr("disabled", true);
            if (!$("#" + $(".strong").eq(0).attr("class").split(" ")[0] + "_wrapper").is(":visible"))
              $(".strong").eq(0).trigger("click");
          }
        },
        error: function(resp) {
          dialog_msg.init();
          dialog_msg.error();
        }
      });
      $("html, body").animate({
        scrollTop: 0
      }, "slow");
    });
    $delete_button.on("click", function() {
      var $t = $(this).parents("tr");
      dialog_confirm_delete.init($t);
      dialog_confirm_delete.display(translator.get("dialog_potwierdzenie"), translator.get("dialog_usuniecie_uzytkownika"));
    });
    $("#user_picture_id").on("click", function() {
      var user_id = parseInt($("#user_edit_id").val());
      if (user_id) {
        $("#add_photo_user_id").val(user_id);
        add_file.init();
        add_file.display();
        postformrender();
        $("#dialog_add_file dt").css({
          "width": "120px"
        });
      }
    });
    var add_file = {
      window: null,
      title: null,
      msg: null,
      init: function() {
        this.window = $("#dialog_add_file");
      },
      display: function() {
        this.window.attr("title", translator.get("dialog_dodaj_zdjecie"));
        this.window.dialog({
          autoOpen: false,
          width: 785,
          height: "auto",
          position: [(($(window).width() / 2) - (785 / 2) + 10), (($(window).height() / 3))],
          modal: true,
          closeOnEscape: true,
          resizable: false,
          buttons: {
            "Zamknij": function() {
              $(this).dialog("close");
            }
          },
          open: function(event, ui) {
            $(".ui-widget-overlay").remove();
            $(".ui-dialog-titlebar-close").show();
            $(".ui-dialog-titlebar").show();
          },
          beforeOpen: function(event, ui) {

          },
          beforeClose: function(event, ui) {

          }
        });
        this.window.dialog("open");
        return false;
      }
    };
  },
  load_data: function(reinit_pager) {
    var that = this;
    var data = {};
    var url = that.option.baseurl + "/admin/settings/loaduserdata";
    if (reinit_pager) {
      if (that.option.$table.find("tbody tr").length === 0)
        that.option.curr_page = that.option.curr_page - 1;
    }
    data["num_row_per_page"] = parseInt(that.option.num_row_per_page);
    data["curr_page"] = parseInt(that.option.curr_page + 1);
    data["sort_column"] = that.option.sort_column !== null ? that.option.sort_column : "user_id";
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
              var user_id = value["user_id"];
              var image = "";
              if (value["image"] !== null) {
                image = "<img alt=\"\" title=\"\" src=\"" + that.option.baseurl + "/user/upload/user_id_" + user_id + "/min_" + value["image"] + "\">";
              }
              var user_name = $.trim(value["first_name"] + " " + value["last_name"]);
              var email_address = value["email_address"];
              var user_category_name = value["user_category_name"] !== null ? value["user_category_name"] : "";
              var is_active = parseInt(value["is_active"]);
              tr += "<tr id=\"user_" + user_id + "\">";
              tr += "<td><input class=\"checkbox\" type=\"checkbox\" value=\"1\" id=\"checked-" + user_id + "\" name=\"checkbox\"><p class=\"col-0\"></p></td>";
              if (is_active) {
                tr += "<td><p class=\"col-1\">" + image + "</p></td>";
                tr += "<td><p class=\"col-2\">" + user_name + "</p></td>";
                tr += "<td><p class=\"col-3\">" + email_address + "</p></td>";
                tr += "<td><p class=\"col-4\">" + user_category_name + "</p></td>";
                tr += "<td><span title=\"wyłącz\" class=\"disable_user\"></span></td>";
              }
              else {
                tr += "<td><p style=\"color:#C4C0C2\" class=\"col-1\">" + image + "</p></td>";
                tr += "<td><p style=\"color:#C4C0C2\" class=\"col-2\">" + user_name + "</p></td>";
                tr += "<td><p style=\"color:#C4C0C2\" class=\"col-3\">" + email_address + "</p></td>";
                tr += "<td><p style=\"color:#C4C0C2\" class=\"col-4\">" + user_category_name + "</p></td>";
                tr += "<td><span title=\"włącz\" class=\"enable_user\"></span></td>";
              }
              tr += "<td><span title=\"edytuj\" class=\"edit_user\"></span></td>";
              tr += "<td><span title=\"usuń\" class=\"delete_user\"></span></td>";
              tr += "</tr>";
            });
            var $tr = $(tr);
            var table_width = that.option.$table.width();
            $.each($tr, function(index, value) {
              var $sortable = that.option.$sortable_th;
              $.each($(value).find("[class^=col-]"), function(index, value) {
                var col_id = $(value).attr("class").split("-")[1];
                var width = (table_width * $sortable.eq(col_id).data("width") / 100);
                $(value).css({
                  "width": width - 5,
                  "word-wrap": "break-word",
                  "overflow": "hidden"
                });
              });
            });
            $tbody.empty().append($tr);
            that.add_other_buttons();
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
            $("a[rel^='prettyPhoto']").prettyPhoto({
              animation_speed: "normal",
              slideshow: 3000,
              autoplay_slideshow: false,
              overlay_gallery: false
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
              $.each(filter, function(index, value) {
                $("#" + index).val(value);
              });
              dialog_msg.init();
              dialog_msg.display(translator.get("dialog_brak_wynikow"), translator.get("dialog_brak_wynikow_dla_kryterium"));
            }
          }
        }
      },
      error: function(resp) {

      }
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
  },
  reload_data: function() {
    var that = this;
    that.clear_filterable();
    that.check_paginator();
    that.load_data(true);
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