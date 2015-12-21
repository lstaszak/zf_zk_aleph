var table_payments = {
  init: function(module, num_rows) {
    this.option.module = module;
    this.option.baseurl = "";
    this.option.$table = $("#order_payment");
    this.option.$pager = $("<div class=\"pager\"></div>");
    this.option.num_rows = num_rows;
    this.option.num_row_per_page = 100;
    this.option.max_pager_count = 20;
    this.option.curr_pager_part = 0;
    this.option.curr_page = 0;
    this.set_num_pages();
    this.add_num_row_per_page();
    this.add_paginator();
    this.add_sortable();
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
  add_num_row_per_page: function() {
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
    }).width(60).height(35).css({
      "cursor": "pointer",
      "margin": "5px 0px 5px 0px"
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
        var name = $sortable.data("id");
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
    var url = that.option.baseurl + "/" + that.option.module + "/payment/loaddataorderspayment";
    if (reinit_pager) {
      if (that.option.$table.find("tbody tr").length === 0)
        that.option.curr_page = that.option.curr_page - 1;
    }
    data["num_row_per_page"] = parseInt(that.option.num_row_per_page);
    data["curr_page"] = parseInt(that.option.curr_page + 1);
    data["sort_column"] = that.option.sort_column !== null ? that.option.sort_column : "order_payment_id";
    data["sort_method"] = that.option.sort_method !== null ? that.option.sort_method : "desc";
    $.ajax({
      url: url,
      type: "POST",
      dataType: "json",
      data: data,
      success: function(resp) {
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
            var order_payment_id = value["order_payment_id"];
            tr += "<tr id=\"" + order_payment_id + "\">";
            tr += "<td><p class=\"col-0\">" + order_payment_id + "</p></td>";
            tr += "<td><p class=\"col-1\">" + (roundnumber(value["order_payment_total_amount"]) / 100) + " PLN</p></td>";
            tr += "<td><p class=\"col-2\">" + (value["user_date_is_starting"] === null ? "" : value["user_date_is_starting"]) + "</p></td>";
            tr += "<td><p class=\"col-3\">" + (value["user_date_is_ending"] === null ? "" : value["user_date_is_ending"]) + "</p></td>";
            if (value["payment_type"] === 1) {
              tr += "<td><p class=\"col-4\">PayU</p></td>";
            }
            else {
              tr += "<td><p class=\"col-4\">YetiPay</p></td>";
            }
            if (value["is_ending"] === 1) {
              if (that.option.module === "borrower") {
                tr += "<td><p class=\"col-5\">Zakończona</p><p class=\"action\">Poproś o fakturę</p></td>";
              } else {
                tr += "<td><p class=\"col-5\">Zakończona</p></td>";
              }
            }
            else {
              tr += "<td><p class=\"col-6\">Nowa</p></td>";
            }
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
          $(".action").on("click", function() {
            var order_payment_id = parseInt($(this).parents("tr").attr("id"));
            rpc_order_settings.init("/borrower/ordersettings");
            $("#dialog_invoice_details").empty().append(rpc_order_settings.render_invoice_form(order_payment_id));
            invoice_details.init();
            invoice_details.display();
            $("body").off("click", "#order_submit_get_invoice").on("click", "#order_submit_get_invoice", function(event) {
              var result = rpc_order_settings.send_invoice();
              if (result === true) {
                dialog_msg.init();
                dialog_msg.display(translator.get("dialog_potwierdzenie"), translator.get("dialog_faktura"));
              }
              event.preventDefault();
              event.stopPropagation();
            });
          }).css({
            "text-decoration": "underline",
            "cursor": "pointer",
            "font-style": "normal"
          });
          $.each($tbody.find("tr").find("td:eq(0)"), function(index, value) {
            $(value).on("click", function() {
              that.get_payment_details(parseInt($(value).parent().attr("id")));
            }).find("p").css({
              "text-decoration": "none",
              "cursor": "pointer",
              "font-style": "normal",
              "color": "#0000e8"
            }).hover(function() {
              $(this).css({
                "text-decoration": "underline",
                "cursor": "pointer",
                "font-style": "normal",
                "color": "#0000e8"
              });
            }, function() {
              $(this).css({
                "text-decoration": "none",
                "cursor": "pointer",
                "font-style": "normal",
                "color": "#0000e8"
              });
            });
          });
          that.option.$table.alternateRowColors();
        } else {
          that.option.$pager.hide();
          that.option.$table.hide();
        }
      },
      error: function(resp) {

      }
    });
  },
  get_payment_details: function(order_payment_id) {
    var that = this;
    var top = "";
    var details = "";
    var name = "";
    var email_address = "";
    rpc_order_settings.init("/" + that.option.module + "/ordersettings");
    var is_get = rpc_order_settings.get_user_payment_details(order_payment_id);
    if (is_get) {
      details = "<p class=\"strong\">Numer płatności: " + order_payment_id + "</p>";
      $.each(is_get, function(index, value) {
        name = "<p><strong>Imię i nazwisko zamawiającego:</strong> " + value.first_name + " " + value.last_name + "</p>";
        email_address = "<p><strong>Adres e-mail: </strong> " + value.email_address + "</p>";
        details += "<p><strong>Sygnatura:</strong> " + value.call_id + "</p>";
        details += "<p><strong>Tytuł książki / czasopisma:</strong> " + value.journal_title + "</p>";
        details += "<p><strong>Nazwa rozdziału / artykułu:</strong> " + value.article_title + "</p>";
        details += "<p><strong>Strony od:</strong> " + value.page_from + "</p>";
        details += "<p><strong>Strony do:</strong> " + value.page_until + "</p>";
        details += "<p><strong>Numer zamówienia:</strong> " + value.order_journal_id + "</p>";
        details += "<p><strong>Cena:</strong> " + (parseFloat(value.order_journal_amount) / 100) + " PLN</p>";
        details += "<br />";
      });
      if (that.option.module === "user") {
        top = name + email_address;
      }
      $("#dialog_order_payment_details").empty().append(top + details);
      order_payment_details.init();
      order_payment_details.display();
    }
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
    that.get_count_tabs();
    that.clear_filterable();
    that.check_paginator();
    that.load_data(true);
  },
  reload_count: function() {
    var that = this;
    that.get_count_tabs();
  }
};
$(document).ready(function() {
  jQuery.fn.alternateRowColors = function() {
    $("tbody tr:odd", this).removeClass("even").addClass("odd");
    $("tbody tr:even", this).removeClass("odd").addClass("even");
    return this;
  };
});
var order_payment_details = {
  window: null,
  title: null,
  msg: null,
  init: function() {
    this.window = $("#dialog_order_payment_details");
  },
  display: function() {
    this.window.attr("title", "Szczegóły płatności");
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
        postformrender();
      },
      beforeClose: function(event, ui) {
        $("#dialog_order_payment_details").empty();
      }
    });
    this.window.dialog("open");
    return false;
  }
};
var invoice_details = {
  window: null,
  title: null,
  msg: null,
  init: function() {
    this.window = $("#dialog_invoice_details");
  },
  display: function() {
    this.window.attr("title", "Dane do faktury");
    this.window.dialog({
      autoOpen: false,
      width: 985,
      height: "auto",
      position: [(($(window).width() / 2) - (985 / 2) + 10), (($(window).height() / 3))],
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: {
        "Zamknij": function() {
          $(this).dialog("close");
        }
      },
      open: function(event, ui) {
        postformrender();
      },
      beforeClose: function(event, ui) {
        $("#dialog_invoice_details").empty();
      }
    });
    this.window.dialog("open");
    return false;
  }
};