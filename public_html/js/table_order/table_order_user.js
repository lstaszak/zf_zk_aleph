var table_order_user = {
  init: function (order_status_id, num_rows) {
    this.option.module = "user";
    this.option.baseurl = "";
    this.option.$table = $("#order_status-" + order_status_id);
    this.option.$pager = $("<div class=\"pager\"></div>");
    this.option.order_status_id = parseInt(order_status_id);
    this.option.num_rows = num_rows;
    this.option.num_row_per_page = 100;
    this.option.max_pager_count = 20;
    this.option.curr_pager_part = 0;
    this.option.curr_page = 0;
    this.set_num_pages();
    this.add_num_row_per_page();
    this.add_filter_button();
    this.add_reset_button();
    this.add_detete_button();
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
    this.clear_filterable();
    this.set_order_settings();
    this.load_data(false);
  },
  option: {
    order_status_id: null,
    baseurl: null,
    module: null,
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
    $filterable_th: null,
    sort_method: null,
    sort_column: null,
    sort_column_eq: null
  },
  set_num_rows: function (num_rows) {
    this.option.num_rows = num_rows;
  },
  get_num_rows: function () {
    return this.option.num_rows;
  },
  set_num_pages: function () {
    this.option.num_pages = Math.ceil(this.option.num_rows / this.option.num_row_per_page);
    this.option.last_page = this.option.num_pages - 1;
  },
  get_num_pages: function () {
    return this.option.num_pages;
  },
  add_filter_button: function () {
    var that = this;
    var $button = $("<button id=\"choose\" class=\"table_button\">" + translator.get("btn_filtruj") + "</button>").on("click",function () {
      $(".pager").empty();
      that.option.curr_page = 0;
      that.add_paginator();
      that.check_paginator();
      that.load_data(false);
    }).button();
    $button.insertBefore(that.option.$table);
  },
  add_reset_button: function () {
    var that = this;
    var $button = $("<button id=\"reset\" class=\"table_button\">" + translator.get("btn_resetuj") + "</button>").on("click",function () {
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
  add_detete_button: function () {
    var that = this;
    if (that.option.order_status_id === 1) {
      var $button = $("<button id=\"delete\" class=\"table_button\">" + translator.get("btn_usun") + "</button>").on("click",function () {
        var data = {};
        $.each($(".checkbox:checked"), function (index, value) {
          if ($(value).attr("name") !== "check_all_top")
            data[index] = parseInt($(value).attr("id").split("-")[1]);
        });
        dialog_confirm_del_order.init("user", data);
        dialog_confirm_del_order.display(translator.get("dialog_potwierdzenie"), translator.get("dialog_wykonanie_akcji") + " <strong>" + $(this).html() + "</strong>?");
      }).button().css({});
      $button.insertBefore(that.option.$table);
    }
  },
  add_num_row_per_page: function () {
    var that = this;
    var select = "";
    select += "<select id=\"num_row_per_page\" name=\"num_row_per_page\">";
    select += "<option value=\"20\">20</option>";
    select += "<option value=\"50\">50</option>";
    select += "<option value=\"100\" selected>100</option>";
    select += "<option value=\"200\">200</option>";
    select += "</select>";
    //select += "<div class=\"clear\"></div>";
    $(select).insertBefore(that.option.$table);
    $("#num_row_per_page").on("change",function (event) {
      $(".pager").empty();
      that.option.curr_pager_part = 0;
      that.option.curr_page = 0;
      that.option.num_row_per_page = parseInt($(this).find("option:selected").html());
      that.set_num_pages();
      that.add_paginator();
      that.check_paginator();
      that.load_data(false);
      $(this).selectmenu();
    }).width(60).height(35).css({
      "cursor": "pointer",
      "margin": "5px 0px 5px 0px"
    }).selectmenu();
  },
  add_paginator: function () {
    var that = this;
    for (var page = 0; page < that.option.num_pages; page++) {
      $("<span class=\"page_number\"></span>").text(page + 1).on("click", {
        new_page: page
      },function (event) {
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
    $(".prev").on("click", function () {
      if (!$(this).hasClass("click_not_able")) {
        that.nav("prev");
      }
    });
    $(".next").on("click", function () {
      if (!$(this).hasClass("click_not_able")) {
        that.nav("next");
      }
    });
    $(".pager span.page_number").hide().slice(that.option.curr_pager_part * that.option.max_pager_count, (that.option.curr_pager_part + 1) * that.option.max_pager_count).show();
  },
  check_paginator: function () {
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
  add_sortable: function () {
    var that = this;
    var table_width = that.option.$table.width();
    that.option.$sortable_th = that.option.$table.find("#sort_able th");
    $.each(that.option.$sortable_th, function (index, value) {
      $(value).width((table_width * $(value).data("width")) / 100);
    });
    $.each(that.option.$sortable_th, function (index, value) {
      if ($(value).hasClass("sort")) {
        var $header = $(this);
        $header.addClass("click_able").hover(function () {
          $header.addClass("hover");
        },function () {
          $header.removeClass("hover");
        }).click(function () {
          var $t = $(this);
          var sort_direction = 1;
          if ($header.hasClass("sort_asc")) {
            sort_direction = -1;
          }
          that.option.$sortable_th.removeClass("sort_asc").removeClass("sort_desc");
          $.each(that.option.$sortable_th.find("span"), function (index, value) {
            if ($(value).html() === "" && $(value).parent().hasClass("sort")) {
              $(value).removeClass("sort_arrow_asc").removeClass("sort_arrow_desc").addClass("sort_arrow");
            }
          });
          if (sort_direction === 1)
            $header.addClass("sort_asc").children("span").eq(1).removeClass("sort_arrow").removeClass("sort_arrow_desc").addClass("sort_arrow_asc"); else
            $header.addClass("sort_desc").children("span").eq(1).removeClass("sort_arrow").removeClass("sort_arrow_asc").addClass("sort_arrow_desc");
          if ($header.hasClass("sort_asc"))
            that.option.sort_method = "asc"; else if ($header.hasClass("sort_desc"))
            that.option.sort_method = "desc";
          that.option.sort_column = $t.data("id");
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
  add_filterable: function () {
    var that = this;
    var table_width = that.option.$table.width();
    that.option.$filterable_th = that.option.$table.find("#filter_able th");
    $.each(that.option.$filterable_th, function (index, value) {
      var $t = $(this);
      if ($t.hasClass("filter")) {
        var idx = $t.index();
        var $sortable = that.option.$sortable_th.eq(idx);
        var width = $sortable.data("width");
        var name = $sortable.data("id");
        var $input = $("<input>").attr("name", name).attr("id", "filter_" + name).css({
          "width": ((table_width * width) / 100) - 5,
          "padding-left": "0px"
        }).on("keypress", function (e) {
          if (e.keyCode === 13) {
            if (name === "amount" && $(this).val().length)
              $(this).val(roundnumber($(this).val()));
            $(".pager").empty();
            that.option.curr_page = 0;
            that.add_paginator();
            that.check_paginator();
            that.load_data(false);
          }
        });
        $t.append($input);
        if (name === "amount") {
          $("#filter_amount").on("blur", function () {
            if ($(this).val().length)
              $(this).val(roundnumber($(this).val()));
          });
        }
      }
    });
  },
  clear_filterable: function () {
    var that = this;
    $.each(that.option.$filterable_th.find("input"), function (index, value) {
      $(value).val("");
    });
  },
  nav: function (id) {
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
  load_data: function (reinit_pager) {
    var that = this;
    var data = {};
    var url = that.option.baseurl + "/user/orders/loaddataordersnew";
    if (reinit_pager) {
      if (that.option.$table.find("tbody tr").length === 0)
        that.option.curr_page = that.option.curr_page - 1;
    }
    data["filter_order_status_id"] = that.option.order_status_id;
    data["num_row_per_page"] = parseInt(that.option.num_row_per_page);
    data["curr_page"] = parseInt(that.option.curr_page + 1);
    data["sort_column"] = that.option.sort_column !== null ? that.option.sort_column : "modified_date";
    data["sort_method"] = that.option.sort_method !== null ? that.option.sort_method : "desc";
    $.each($("input[id^=filter_]"), function (index, value) {
      data[$(value).attr("id")] = $(value).val();
    });
    $.ajax({
      url: url,
      type: "POST",
      dataType: "json",
      data: data,
      success: function (resp) {
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
          $.each(rowset, function (index, value) {
            var order_id = value["id"];
            var relationships = "";
            if (value["relationships_count"] !== 0) {
              relationships += "<span class=\"relationships_details_" + order_id + "\">";
              $.each(value["relationships"], function (index, order) {
                relationships += "<br />[" + order["id"] + "], [" + (order["page_from"] === null ? "" : order["page_from"]) + " - " + (order["page_until"] === null ? "" : order["page_until"]) + "], [" + order["order_status_user_name"] + "]";
              });
              relationships += "</span>";
            }
            tr += "<tr id=\"" + order_id + "\">";
            tr += "<td><input class=\"checkbox\" type=\"checkbox\" value=\"1\" id=\"checked-" + order_id + "\" name=\"checkbox\"></td>";
            tr += "<td><p class=\"col-1\">" + value["id"] + "</p></td>";
            tr += "<td><p class=\"col-2\">" + value["journal_title"] + "</p></td>";
            tr += "<td><p class=\"col-3\">" + (value["page_from"] === null ? "" : value["page_from"]) + " - " + (value["page_until"] === null ? "" : value["page_until"]) + "</p></td>";
            tr += "<td><p class=\"col-4\">" + roundnumber((parseFloat(value["amount"]) / 100)) + " PLN</p></td>";
            tr += "<td><p class=\"col-5\" style=\"margin-top:8px; margin-bottom:8px;\">" + "<span class=\"relationships_count_" + order_id + "\">Ilość zamówień: " + value["relationships_count"] + "</span>" + relationships + "</p></td>";
            tr += "</tr>";
          });
          var $tr = $(tr);
          var table_width = that.option.$table.width();
          $.each($tr, function (index, value) {
            var $sortable = that.option.$sortable_th;
            $.each($(value).find("[class^=col-]"), function (index, value) {
              var col_id = $(value).attr("class").split("-")[1];
              var width = (table_width * $sortable.eq(col_id).data("width") / 100) - 15;
              $(value).css({
                "width": width,
                "overflow": "hidden",
                "word-wrap": "break-word"
              });
            });
          });
          $tbody.empty().append($tr);
          $.each($tbody.find("tr").find("td:eq(2)"), function (index, value) {
            $(value).on("click",function () {
              that.get_order_settings(parseInt($(value).parent().attr("id")));
            }).find("p").css({
              "text-decoration": "none",
              "cursor": "pointer",
              "font-style": "normal",
              "color": "#0000e8"
            }).hover(function () {
              $(this).css({
                "text-decoration": "underline",
                "cursor": "pointer",
                "font-style": "normal",
                "color": "#0000e8"
              });
            }, function () {
              $(this).css({
                "text-decoration": "none",
                "cursor": "pointer",
                "font-style": "normal",
                "color": "#0000e8"
              });
            });
          });
          $.each(rowset, function (index, value) {
            var order_id = value["id"];
            $(".relationships_details_" + order_id).hide();
            $(".relationships_count_" + order_id).on("click",function () {
              if ($(".relationships_details_" + order_id).is(":hidden"))
                $(".relationships_details_" + order_id).show(); else
                $(".relationships_details_" + order_id).hide();
            }).css({
              "text-decoration": "none",
              "cursor": "pointer",
              "font-style": "normal",
              "color": "#0000e8"
            }).hover(function () {
              $(this).css({
                "text-decoration": "underline",
                "cursor": "pointer",
                "font-style": "normal",
                "color": "#0000e8"
              });
            }, function () {
              $(this).css({
                "text-decoration": "none",
                "cursor": "pointer",
                "font-style": "normal",
                "color": "#0000e8"
              });
            });
          });
          $("#check_all_top").on("click", function () {
            $("input[type=checkbox]").attr("checked", $(this).is(":checked"));
            if ($(this).is(":checked"))
              $(":checkbox:not(#check_all_top)").parents("tr").addClass("is_checked ui-selected"); else
              $(":checkbox:not(#check_all_top)").parents("tr").removeClass("is_checked ui-selected");
          });
          $(":checkbox:not(#check_all_top)").on("click", function () {
            var is_checked = $(this).is(":checked");
            if (is_checked)
              $(this).parents("tr").addClass("is_checked ui-selected"); else
              $(this).parents("tr").removeClass("is_checked ui-selected");
          });
        } else {
          $(".table_button").hide();
          $("#num_row_per_page-button").hide();
          that.option.$pager.hide();
          that.option.$table.hide();
          var filter = {};
          $.each($("input[id^=filter_]"), function (index, value) {
            if ($(value).val() !== "") {
              filter[$(value).attr("id")] = $(value).val();
            }
          });
          if (Object.keys(filter).length) {
            $("#reset").trigger("click");
            $.each(filter, function (index, value) {
              $("#" + index).val(value);
            });
            dialog_msg.init();
            dialog_msg.display(translator.get("dialog_brak_wynikow"), translator.get("dialog_brak_wynikow_dla_kryterium"));
          }
        }
        $.each($("tbody tr"), function (index, value) {
          if (!(index % 2))
            $(value).removeClass("odd").addClass("even"); else
            $(value).removeClass("even").addClass("odd");
        });
      },
      error: function (resp) {
      }
    });
    that.get_count_tabs();
  },
  get_order_settings: function (order_id) {
    var that = this;
    rpc_order_settings.init("/user/ordersettings");
    $("#dialog_order_setting").empty().append(rpc_order_settings.render_order_form("user", order_id));
    $("#order_submit_save_and_make_action").addClass("btn-success");
    var is_get = rpc_order_settings.get_settings(order_id);
    if (is_get.success === true) {
      $.each(is_get.row, function (index, value) {
        $("#" + index).val("");
        if (value) {
          if (index === "amount") {
            value = roundnumber((value / 100)) + " PLN";
          }
          $("#" + index).val(value);
        }
      });
      if (that.option.order_status_id === 4) {
        if (is_get.row.user_first_name.length && is_get.row.user_last_name.length)
          $("#footer_pdf").val("UŻYWANIE KOPII TYLKO W ZAKRESIE WŁASNEGO UŻYTKU OSOBISTEGO - " + is_get.row.user_first_name + " " + is_get.row.user_last_name + ", " + is_get.row.user_email_address);
      }
      $("#amount").on("blur change", function () {
        if ($(this).val().length) {
          var amount = $(this).val();
          $(this).val(roundnumber(amount.replace(/[^\d.,]/g, '')));
        }
      });
      order_setting.init();
      order_setting.display();
      $.each($(".form"), function (index, value) {
        var form_height = parseInt($(value).actual("outerHeight", {
          includeMargin: true
        }));
        $(value).parents(".form_wrapper").height(form_height + 80);
        $(value).parents(".form_box").height(form_height + 80 - 20);
      });
      $(".form_wrapper").show();
    }
  },
  set_order_settings: function () {
    var that = this;
    rpc_order_settings.init("/user/ordersettings");
    $("body").undelegate("#order_submit_save_and_make_action", "click").delegate("#order_submit_save_and_make_action", "click", function (event) {
      validate_ajax.url = "/admin/validator/validateform";
      validate_ajax.validate($(this));
      if (!$(".errors_tip").length) {
        var data = {};
        var order_id = parseInt($("#id").val());
        var order_fields = rpc_order_settings.get_fields(order_id);
        $.each(order_fields["write_able"], function (index, value) {
          data[value] = $("#" + value).val();
        });
        var is_save = rpc_order_settings.set_settings(order_id, data);
        if (is_save)
          that.btn_action(that.option.order_status_id, order_id, is_save);
        $("#dialog_order_setting").dialog("close");
      }
      event.preventDefault();
      event.stopPropagation();
    });
    $("body").undelegate("#order_submit_cancel", "click").delegate("#order_submit_cancel", "click", function (event) {
      var order_id = parseInt($("#id").val());
      if (that.option.order_status_id === 2 || that.option.order_status_id === 3) {
        if (rpc_order_settings.make_action(that.option.order_status_id, order_id, true)) {
          that.option.$table.find("tbody").find("tr[id=" + order_id + "]").fadeOut("slow").remove();
          that.reload_data();
        }
      }
      $("#dialog_order_setting").dialog("close");
      event.preventDefault();
      event.stopPropagation();
    });
    $("body").undelegate("#order_submit_outer_magazine", "click").delegate("#order_submit_outer_magazine", "click", function (event) {
      var order_id = parseInt($("#id").val());
      if (that.option.order_status_id === 2) {
        var param = {};
        param.outer_magazine = true;
        if (rpc_order_settings.make_action(that.option.order_status_id, order_id, false, param)) {
          that.option.$table.find("tbody").find("tr[id=" + order_id + "]").fadeOut("slow").remove();
          that.reload_data();
        }
      }
      $("#dialog_order_setting").dialog("close");
      event.preventDefault();
      event.stopPropagation();
    });
    $("body").undelegate("#order_submit_save_and_print", "click").delegate("#order_submit_save_and_print", "click", function (event) {
      window.open("/user/orders/print/no/" + $("#id").val(), "_blank");
      event.preventDefault();
      event.stopPropagation();
    });
    $("body").undelegate("#order_add_file", "click").delegate("#order_add_file", "click", function (event) {
      order_add_file.init();
      order_add_file.display();
    });
  },
  get_action_button_name: function () {
    var that = this;
    rpc_order_settings.init("/user/ordersettings");
    return rpc_order_settings.get_btn_action_name(that.option.order_status_id);
  },
  btn_action: function (order_status_id, order_id, is_requestable) {
    var that = this;
    rpc_order_settings.init("/user/ordersettings");
    dialog_msg.init();
    if (is_requestable === "not_requestable" && order_status_id === 1) {
      that.option.$table.find("tbody").find("tr[id=" + order_id + "]").fadeOut("slow").remove();
      dialog_msg.display(translator.get("dialog_niepowodzenie"), translator.get("dialog_zamowienie_nie_moze_zostac_zrealizowane"));
    } else {
      var is_save = rpc_order_settings.make_action(order_status_id, order_id);
      if (order_status_id === 1 && is_save) {
        that.option.$table.find("tbody").find("tr[id=" + order_id + "]").fadeOut("slow").remove();
        dialog_msg.display(translator.get("dialog_potwierdzenie"), translator.get("dialog_zamowienie_zostalo_utworzone_i_wyslane_do_wyceny"));
      } else if (order_status_id === 3 && is_save) {
        that.option.$table.find("tbody").find("tr[id=" + order_id + "]").fadeOut("slow").remove();
        dialog_msg.display(translator.get("dialog_potwierdzenie"), translator.get("dialog_zamowienie_zostalo_przekazane_do_realizacji"));
      } else if (order_status_id <= 5 && is_save) {
        that.option.$table.find("tbody").find("tr[id=" + order_id + "]").fadeOut("slow").remove();
      } else if (order_status_id === 6 && is_save) {
        window.open("/files_scanned/user_id_" + is_save, "_blank");
      }
    }
    that.reload_data();
  },
  get_count_tabs: function () {
    rpc_order_settings.init("/user/ordersettings");
    rpc_order_settings.get_tabs_count();
  },
  make_cart: function () {
    var that = this;
    rpc_order_settings.init("/user/ordersettings");
    if (rpc_order_settings.get_cart_total_amount() !== null) {
      var $tbody = that.option.$table.find("tbody");
      var $tbody_cart = $tbody.find("tr:last");
      var tr = "";
      tr += "<tr id=\"cart_total_amount\">";
      tr += "<td></td>";
      tr += "<td></td>";
      tr += "<td></td>";
      tr += "<td id=\"total_amount\"><p>Suma:<br /><span>" + roundnumber((cart_total_amount / 100)) + " PLN</span></p></td>";
      tr += "<td></td>";
      tr += "<td></td>";
      tr += "</tr>";
      tr += "<tr id=\"cart_button\">";
      tr += "<td></td>";
      tr += "<td></td>";
      tr += "<td></td>";
      tr += "<td id=\"payment_button\"></td>";
      tr += "<td></td>";
      tr += "<td></td>";
      tr += "</tr>";
      $tbody_cart.after(tr);
      $("#cart_total_amount").css({
        "height": "30px"
      });
      $("#cart_button td").css({
        "height": "30px",
        "background-color": "#ffffff"
      });
      $("#cart_total_amount td").css({
        "height": "30px",
        "border-top": "1px solid #dddddd",
        "border-bottom": "0px solid #dddddd",
        "background-color": "#ffffff"
      });
      $("#total_amount").css({
        "height": "30px",
        "border": "0px 1px 1px 1px solid #dddddd",
        "background-color": "#1d2e3f"
      }).find("p").css({
        "font-weight": "bold",
        "color": "#454255"
      }).find("span").css({
        "font-weight": "bold",
        "font-style": "italic",
        "color": "#454255"
      });
      $("#payment_button").css({
        "text-align": "left"
      }).find("button").css({
        "margin-left": "0px"
      });
      var $button = $("<button id=\"pay\">" + translator.get("btn_zaplac") + "</button>").on("click",function () {
        window.location = "/user/payment/newpayment";
      }).button().css({
        "margin-left": "0px"
      });
      $("#payment_button").append($button);
    }
  },
  reinit: function (num_rows) {
    var that = this;
    $(".pager").empty();
    if (num_rows !== 0) {
      that.set_num_rows(num_rows);
      that.set_num_pages();
      that.add_paginator();
      that.check_paginator();
    }
  },
  reload_data: function () {
    var that = this;
    $("#reset").trigger("click");
  }
};
var order_setting = {
  window: null,
  title: null,
  msg: null,
  init: function () {
    this.window = $("#dialog_order_setting");
  },
  display: function () {
    this.window.attr("title", "Szczegóły zamówienia");
    this.window.dialog({
      autoOpen: false,
      width: 985,
      height: "auto",
      position: [(($(window).width() / 2) - 985 / 2), 10],
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: [
        {
          text: "Anuluj",
          click: function () {
            $(this).dialog("close");
          }
        }
      ],
      open: function (event, ui) {
        postformrender();
      },
      befogClose: function (event, ui) {
        table_order_user.load_data(true);
      }
    });
    this.window.dialog("open");
    return false;
  }
};
var order_add_file = {
  window: null,
  title: null,
  msg: null,
  init: function () {
    this.window = $("#dialog_order_add_file");
  },
  display: function () {
    this.window.attr("title", "Dodaj zeskanowany plik");
    this.window.dialog({
      autoOpen: false,
      width: 785,
      height: "auto",
      position: [(($(window).width() / 2) - (785 / 2) + 10), (($(window).height() / 3))],
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: {
        "Zamknij": function () {
          $(this).dialog("close");
        }
      },
      open: function (event, ui) {
        $.each($(".form"), function (index, value) {
          var form_height = parseInt($(value).actual("outerHeight", {
            includeMargin: true
          }));
          $(value).parents(".form_wrapper").height(form_height + 80);
          $(value).parents(".form_box").height(form_height + 80 - 20);
        });
        $(".ui-widget-overlay").width($(document).width());
        $(".ui-widget-overlay").height($(document).height());
      },
      beforeClose: function (event, ui) {
        rpc_order_settings.init("/user/ordersettings");
        var $order_file_id = $("#order_file_id");
        var scanned_file = rpc_order_settings.get_scanned_files_list();
        var option = "";
        $.each(scanned_file, function (index, value) {
          option += "<option label=\"" + value + "\" value=\"" + index + "\">" + value + "</option>";
        });
        $order_file_id.selectmenu("destroy");
        $order_file_id.empty().append(option);
        $order_file_id.selectmenu().selectmenu("value", $order_file_id.find("option:eq(1)").val());
        $("#order_file_id-button").width(358);
        $(".fileupload-buttonbar .cancel").trigger("click");
      }
    });
    this.window.dialog("open");
    return false;
  }
};
$(document).ready(function () {
});
