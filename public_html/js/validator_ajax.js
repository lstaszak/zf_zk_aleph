var dialog_msg = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  v: null,
  init: function(v) {
    this.window = $("#dialog_msg");
    this.v = v;
  },
  display: function(title, msg) {
    this.btn[translator.get("btn_zamknij")] = function() {
      $(this).dialog("close");
    };
    this.title = title;
    this.msg = msg;
    this.window.attr("title", this.title);
    this.window.find("p").html(this.msg);
    this.window.dialog({
      autoOpen: false,
      width: 300,
      height: 150,
      minHeight: 150,
      position: [(($(window).width() / 2) - (300 / 2) + 10), (($(window).height() / 5))],
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
      }
    });
    this.window.dialog("option", "title", this.title);
    this.window.dialog("open");
    return false;
  },
  error: function() {
    this.display(translator.get("dialog_blad"), translator.get("dialog_wystapil_blad"));
  }
};
dialog_msg.init();
var validate_ajax = {
  url: "",
  data: {},
  field_id: null,
  is_error: null,
  validate: function(field_id) {
    var that = this;
    that.field_id = field_id;
    that.data["form_name"] = that.field_id.parents("form").find("#form_name").val();
    that.data["hash"] = that.field_id.parents("form").find("#csrf_token").val();
    that.data["valid"] = that.field_id.parents("form").find("input, textarea, select").serializeArray();
    that.data["remove"] = {};
    that.data["subforms"] = [];
    that.data["subforms"].push({
      "name": "invoiceitem",
      "length": that.field_id.parents("form").find(".item_wrapper").length
    });
    $.each(that.field_id.parents("form").find("*:disabled"), function(index, value) {
      that.data["remove"][index] = {};
      that.data["remove"][index]["name"] = $(value).attr("id");
      that.data["remove"][index]["value"] = $(value).attr("value");
    });
    if ($("#id").length) {
      that.data["order_id"] = $("#id").val();
    }
    $.ajax({
      async: false,
      url: baseurl + validate_ajax.url,
      type: "POST",
      dataType: "json",
      data: that.data,
      success: function(resp) {
        if (resp !== null) {
          that.get_error_html(resp);
          $(".errors_tip").hide().eq(0).show();
        }
      },
      error: function(resp) {
        dialog_msg.error();
      }
    });
  },
  get_error_html: function(resp) {
    var that = this;
    $(".field_error").removeClass("field_error");
    $(".errors_tip").remove();
    $.each(resp, function(index, value) {
      if (index === "subforms") {
        $.each(value, function(inner_index, inner_value) {
          if (resp[inner_value])
            $.each(resp[inner_value], function(inner_inner_index, inner_inner_value) {
              that.mark_error_field(inner_inner_index, inner_inner_value);
            });
        });
      } else {
        that.mark_error_field(index, value);
      }
    });
    return false;
  },
  mark_error_field: function(index, value) {
    var $paragraf = $("<div class=\"errors_tip\"></div>");
    var output = "";
    this.is_error = null;
    var idx = 0;
    for (error_key in value) {
      if (value[error_key] !== true && !idx) {
        output = "<p class=\"" + index + "-error_text\" style=\"padding-left:5px;\"><strong style=\"font-size:15px; color:#FF3853; padding-right:1px;\"> ! </strong><i>" + value[error_key] + "</i></p><div class=\"clear\"></div>";
        $paragraf.append(output);
        $("#" + index).addClass("field_error");
        this.is_error = true;
        idx++;
      }
    }
    if (this.is_error) {
      var width = $("#" + index + "-element").width();
      var position = $("#" + index + "-element").position();
      if (width) {
        width = width + 5;
        $paragraf.css({
          "top": position.top,
          "left": position.left + width
        });
      }
      $("#" + index + "-element").append($paragraf);
    }
    $paragraf.find("p").css({
      "text-shadow": "none"
    }).end().hide();
  }
};
var dialog_confirm = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  v: null,
  init: function(v) {
    this.window = $("#dialog_msg");
    this.v = v;
  },
  display: function(title, msg) {
    this.btn[translator.get("btn_anuluj")] = function() {
      $(this).dialog("close");
    };
    this.btn[translator.get("btn_usun")] = function() {
      $(this).dialog("close");
      dialog_confirm.confirm_del_image();
    };
    this.title = title;
    this.msg = msg;
    this.window.attr("title", this.title);
    this.window.find("p").html(this.msg);
    this.window.dialog({
      v: this.v,
      autoOpen: false,
      width: 300,
      height: 150,
      minHeight: 150,
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
        $("#dialog_msg").css({
        });
      }
    });
    this.window.dialog("open");
    this.window.dialog("option", "title", this.title);
    return false;
  },
  confirm_del_image: function() {
    var $t = this;
    var data = {};
    data.id = parseInt(($t.v.attr("id")).split("_")[3]);
    $.ajax({
      async: true,
      url: baseurl + "/admin/media/deleteimage",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp === true) {
          $t.v.parents("tr").fadeOut("slow", function() {
            $(this).remove();
            $("tbody tr:odd", "#table_image").removeClass("even").addClass("odd");
            $("tbody tr:even", "#table_image").removeClass("odd").addClass("even");
            $("a[rel^=prettyPhoto]").prettyPhoto({
              animation_speed: "normal",
              slideshow: 3000,
              autoplay_slideshow: false
            });
            table_image.load_data(true);
            $("#ajax_overlay").show();
          });
        }
      },
      error: function(resp) {
        dialog_msg.error();
      }
    });
  }
};
var dialog_confirm_del_image = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  v: null,
  init: function(v) {
    this.window = $("#dialog_msg");
    this.v = v;
  },
  display: function(title, msg) {
    this.btn[translator.get("btn_anuluj")] = function() {
      $(this).dialog("close");
    };
    this.btn[translator.get("btn_usun")] = function() {
      $(this).dialog("close");
      dialog_confirm_del_image.confirm_del_image();
    };
    this.title = title;
    this.msg = msg;
    this.window.attr("title", this.title);
    this.window.find("p").html(this.msg);
    this.window.dialog({
      v: this.v,
      autoOpen: false,
      width: 300,
      height: 150,
      minHeight: 150,
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
        $("#dialog_msg").css({
        });
      }
    });
    this.window.dialog("open");
    this.window.dialog("option", "title", this.title);
    return false;
  },
  confirm_del_image: function() {
    var $t = this;
    $.each($t.v, function(index, value) {
      var data = {};
      data.id = value;
      $.ajax({
        async: true,
        url: baseurl + "/admin/media/deleteimage",
        type: "post",
        dataType: "json",
        data: data,
        success: function(resp) {
          if (resp === true) {
            $("tr[id=" + value + "]").fadeOut("slow", function() {
              $(this).remove();
              $("tbody tr:odd", "table[id^=table_]").removeClass("even").addClass("odd");
              $("tbody tr:even", "table[id^=table_]").removeClass("odd").addClass("even");
              $("a[rel^=prettyPhoto]").prettyPhoto({
                animation_speed: "normal",
                slideshow: 3000,
                autoplay_slideshow: false
              });
              table_image.load_data(true);
              $("#ajax_overlay").show();
            });
          }
        },
        error: function(resp) {
          dialog_msg.error();
        }
      });
    });
  }
};
var dialog_confirm_del_user = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  v: null,
  init: function(v) {
    this.window = $("#dialog_msg");
    this.v = v;
  },
  display: function(title, msg) {
    this.btn[translator.get("btn_anuluj")] = function() {
      $(this).dialog("close");
    };
    this.btn[translator.get("btn_usun")] = function() {
      $(this).dialog("close");
      dialog_confirm_del_user.confirm_del_user();
    };
    this.title = title;
    this.msg = msg;
    this.window.attr("title", this.title);
    this.window.find("p").html(this.msg);
    this.window.dialog({
      v: this.v,
      autoOpen: false,
      width: 300,
      height: 150,
      minHeight: 150,
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
        $("#dialog_msg").css({
        });
      }
    });
    this.window.dialog("open");
    this.window.dialog("option", "title", this.title);
    return false;
  },
  confirm_del_user: function() {
    var $t = this;
    $.each($t.v, function(index, value) {
      var data = {};
      data.id = value;
      $.ajax({
        async: true,
        url: baseurl + "/admin/settings/deleteuser",
        type: "post",
        dataType: "json",
        data: data,
        success: function(resp) {
          if (resp === true) {
            $("tr[id$=" + value + "]").fadeOut("slow", function() {
              $(this).remove();
              $("tbody tr:odd", "table[id^=table_]").removeClass("even").addClass("odd");
              $("tbody tr:even", "table[id^=table_]").removeClass("odd").addClass("even");
              table_user.load_data(true);
              $("#ajax_overlay").show();
            });
          }
        },
        error: function(resp) {
          dialog_msg.error();
        }
      });
    });
  }
};
var dialog_confirm_del_nav_element = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  v: null,
  init: function(v) {
    this.window = $("#dialog_msg");
    this.v = v;
  },
  display: function(title, msg) {
    this.btn[translator.get("btn_anuluj")] = function() {
      $(this).dialog("close");
    };
    this.btn[translator.get("btn_usun")] = function() {
      $(this).dialog("close");
      dialog_confirm_del_nav_element.confirm_del_nav_element();
    };
    this.title = title;
    this.msg = msg;
    this.window.attr("title", this.title);
    this.window.find("p").html(this.msg);
    this.window.dialog({
      v: this.v,
      autoOpen: false,
      width: 300,
      height: 150,
      minHeight: 150,
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
        $("#dialog_msg").css({
        });
      }
    });
    this.window.dialog("open");
    this.window.dialog("option", "title", this.title);
    return false;
  },
  confirm_del_nav_element: function() {
    var $t = this;
    var data = {};
    data.id = parseInt(($t.v.attr("id").split("_")[1]));
    data.element = $t.v.attr("id").split("_")[0];
    $.ajax({
      async: true,
      url: baseurl + "/admin/navigation/deletenavigationelement",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp === true) {
          $t.v.fadeOut("slow", function() {
            var $parent = $(this).parent();
            $(this).next().remove().end().remove();
            if ($parent.find("p").size() === 1) {
              $parent.remove();
            }
          });
        }
      },
      error: function(resp) {
        dialog_msg.error();
      }
    });
  }
};
var dialog_confirm_del_nav_menu = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  v: null,
  init: function(v) {
    this.window = $("#dialog_msg");
    this.v = v;
  },
  display: function(title, msg) {
    this.btn[translator.get("btn_anuluj")] = function() {
      $(this).dialog("close");
    };
    this.btn[translator.get("btn_usun")] = function() {
      $(this).dialog("close");
      dialog_confirm_del_nav_menu.confirm_del_nav_menu();
    };
    this.title = title;
    this.msg = msg;
    this.window.attr("title", this.title);
    this.window.find("p").html(this.msg);
    this.window.dialog({
      v: this.v,
      autoOpen: false,
      width: 300,
      height: 150,
      minHeight: 150,
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
        $("#dialog_msg").css({
        });
      }
    });
    this.window.dialog("open");
    this.window.dialog("option", "title", this.title);
    return false;
  },
  confirm_del_nav_menu: function() {
    var $t = this;
    var data = {};
    data.id = parseInt(($t.v.attr("id").split("_")[1]));
    $.ajax({
      async: true,
      url: baseurl + "/admin/navigation/deletenavigationmenu",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp === true) {
          $t.v.fadeOut("slow", function() {
            var $parent = $(this).parent();
            $(this).next().remove().end().remove();
            if ($parent.find("p").size() === 1) {
              $parent.remove();
            }
          });
        }
        if (resp)
          window.location = window.location.href;
      },
      error: function(resp) {
        dialog_msg.error();
      }
    });
  }
};
var dialog_confirm_del_nav_submenu = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  v: null,
  init: function(v) {
    this.window = $("#dialog_msg");
    this.v = v;
  },
  display: function(title, msg) {
    this.btn[translator.get("btn_anuluj")] = function() {
      $(this).dialog("close");
    };
    this.btn[translator.get("btn_usun")] = function() {
      $(this).dialog("close");
      dialog_confirm_del_nav_submenu.confirm_del_nav_submenu();
    };
    this.title = title;
    this.msg = msg;
    this.window.attr("title", this.title);
    this.window.find("p").html(this.msg);
    this.window.dialog({
      v: this.v,
      autoOpen: false,
      width: 300,
      height: 150,
      minHeight: 150,
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
        $("#dialog_msg").css({
        });
      }
    });
    this.window.dialog("open");
    this.window.dialog("option", "title", this.title);
    return false;
  },
  confirm_del_nav_submenu: function() {
    var $t = this;
    var data = {};
    data.id = parseInt(($t.v.attr("id").split("_")[1]));
    $.ajax({
      async: true,
      url: baseurl + "/admin/navigation/deletenavigationsubmenu",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp === true) {
          $t.v.fadeOut("slow", function() {
            var $parent = $(this).parent();
            $(this).next().remove().end().remove();
            if ($parent.find("p").size() === 1) {
              $parent.remove();
            }
          });
        }
        if (resp)
          window.location = window.location.href;
      },
      error: function(resp) {
        dialog_msg.error();
      }
    });
  }
};
var dialog_confirm_del_nav_subsubmenu = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  v: null,
  init: function(v) {
    this.window = $("#dialog_msg");
    this.v = v;
  },
  display: function(title, msg) {
    this.btn[translator.get("btn_anuluj")] = function() {
      $(this).dialog("close");
    };
    this.btn[translator.get("btn_usun")] = function() {
      $(this).dialog("close");
      dialog_confirm_del_nav_subsubmenu.confirm_del_nav_subsubmenu();
    };
    this.title = title;
    this.msg = msg;
    this.window.attr("title", this.title);
    this.window.find("p").html(this.msg);
    this.window.dialog({
      v: this.v,
      autoOpen: false,
      width: 300,
      height: 150,
      minHeight: 150,
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
        $("#dialog_msg").css({
        });
      }
    });
    this.window.dialog("open");
    this.window.dialog("option", "title", this.title);
    return false;
  },
  confirm_del_nav_subsubmenu: function() {
    var $t = this;
    var data = {};
    data.id = parseInt(($t.v.attr("id").split("_")[1]));
    $.ajax({
      async: true,
      url: baseurl + "/admin/navigation/deletenavigationsubsubmenu",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp === true) {
          $t.v.fadeOut("slow", function() {
            var $parent = $(this).parent();
            $(this).next().remove().end().remove();
            if ($parent.find("p").size() === 1) {
              $parent.remove();
            }
          });
        }
        if (resp)
          window.location = window.location.href;
      },
      error: function(resp) {
        dialog_msg.error();
      }
    });
  }
};
var dialog_confirm_del_user_role = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  v: null,
  init: function(v) {
    this.window = $("#dialog_msg");
    this.v = v;
  },
  display: function(title, msg) {
    this.btn[translator.get("btn_anuluj")] = function() {
      $(this).dialog("close");
    };
    this.btn[translator.get("btn_usun")] = function() {
      $(this).dialog("close");
      dialog_confirm_del_user_role.confirm_del_user_role();
    };
    this.title = title;
    this.msg = msg;
    this.window.attr("title", this.title);
    this.window.find("p").html(this.msg);
    this.window.dialog({
      v: this.v,
      autoOpen: false,
      width: 300,
      height: 150,
      minHeight: 150,
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
        $("#dialog_msg").css({
        });
      }
    });
    this.window.dialog("open");
    this.window.dialog("option", "title", this.title);
    return false;
  },
  confirm_del_user_role: function() {
    var $t = this;
    var data = {};
    data.id = parseInt(($t.v.attr("id").split("_")[1]));
    $.ajax({
      async: true,
      url: baseurl + "/admin/settings/deleteuserrole",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp === true) {
          $t.v.fadeOut("slow", function() {
            var $parent = $(this).parent();
            $(this).next().remove().end().remove();
            if ($parent.find("p").size() === 1) {
              $parent.remove();
            }
          });
        }
      },
      error: function(resp) {
        dialog_msg.error();
      }
    });
  }
};
var dialog_confirm_delete = {
  window: null,
  btn: {},
  title: null,
  msg: null,
  v: null,
  init: function(v) {
    this.window = $("#dialog_msg");
    this.v = v;
  },
  display: function(title, msg) {
    this.btn[translator.get("btn_anuluj")] = function() {
      $(this).dialog("close");
    };
    this.btn[translator.get("btn_usun")] = function() {
      $(this).dialog("close");
      dialog_confirm_delete.confirm_delete();
    };
    this.title = title;
    this.msg = msg;
    this.window.attr("title", this.title);
    this.window.find("p").html(this.msg);
    this.window.dialog({
      v: this.v,
      autoOpen: false,
      width: 300,
      height: 150,
      minHeight: 150,
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: this.btn,
      open: function(event, ui) {
        $("#dialog_msg").css({
        });
      }
    });
    this.window.dialog("open");
    this.window.dialog("option", "title", this.title);
    return false;
  },
  confirm_delete: function() {
    var $t = this;
    var data = {};
    data.model = $t.v.attr("id").split("_")[0];
    data.id = parseInt(($t.v.attr("id").split("_")[1]));
    $.ajax({
      async: true,
      url: baseurl + "/admin/validator/delete",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp === true) {
          if ($(".news_issue_order, .portfolio_issue_order, .faq_issue_order, .address_issue_order, .order_email_notification_issue_order").length) {
            $t.v.fadeOut("slow", function() {
              var $parent = $(this).parents("div[id$=_wrapper]");
              $(this).remove();
              if ($parent.find("li").size() === 0) {
                $parent.prev("p").remove();
                $parent.remove();
              }
            });
          }
          else if ($("#table_user").length) {
            $t.v.fadeOut("slow", function() {
              table_user.load_data(true);
            });
          }
          else {
            $t.v.fadeOut("slow", function() {
              var $parent = $(this).parent();
              $(this).next().remove().end().remove();
              if ($parent.find("p").size() === 1) {
                $parent.remove();
              }
            });
          }
        }
      },
      error: function(resp) {
        dialog_msg.error();
      }
    });
  }
};
var dialog_confirm_del_order = {
  module: null,
  window: null,
  title: null,
  msg: null,
  v: null,
  init: function(module, v) {
    this.window = $("#dialog_msg");
    this.v = v;
    this.module = module;
  },
  display: function(title, msg) {
    this.title = title;
    this.msg = msg;
    this.window.attr("title", this.title);
    this.window.find("p").html(this.msg);
    this.window.dialog({
      v: this.v,
      autoOpen: false,
      width: 300,
      height: "auto",
      position: [(($(window).width() / 2) - 985 / 2), 10],
      modal: true,
      closeOnEscape: true,
      resizable: false,
      buttons: {
        "Anuluj": function() {
          $(this).dialog("close");
        },
        "Usuń": function() {
          $(this).dialog("close");
          dialog_confirm_del_order.confirm_del_order();
        }
      },
      open: function(event, ui) {

      }
    });

    this.window.dialog("open");
    return false;
  },
  confirm_del_order: function() {
    var $t = this;
    dialog_msg.init();
    $.each($t.v, function(index, value) {
      var data = {};
      data.id = value;
      $.ajax({
        async: true,
        url: baseurl + "/" + $t.module + "/orders/deleteorder",
        type: "post",
        dataType: "json",
        data: data,
        success: function(resp) {
          if (resp === true) {
            $("tr[id=" + value + "]").fadeOut("slow", function() {
              $(this).remove();
              $("#reset").trigger("click");
            });
          }
        },
        error: function(resp) {
          dialog_msg.display("Błąd", "Wystąpił błąd");
        }
      });
    });
    $("#check_all_top").attr("checked", false);
  }
};
$(document).ready(function() {
  (function($) {
    $.fn.UItoTop = function(options) {
      var defaults = {
        text: "",
        min: 30,
        inDelay: 600,
        outDelay: 400,
        containerID: "container_arrow_top",
        containerHoverID: "container_arrow_top",
        scrollSpeed: 1200,
        easingType: "linear"
      };
      var settings = $.extend(defaults, options);
      var containerIDhash = "#" + settings.containerID;
      var containerHoverIDHash = "#" + settings.containerHoverID;
      $("body").append('<a href="#" id="' + settings.containerID + '">' + settings.text + '</a>');
      var right = (($(window).width() - 980) / 2) - 64 - 20;
      $("#" + settings.containerID).css({
        "right": right
      });
      $(containerIDhash).hide().click(function() {
        $("html, body").animate({
          scrollTop: 0
        }, settings.scrollSpeed, settings.easingType);
        $("#" + settings.containerHoverID, this).stop().animate({
          "opacity": 0
        }, settings.inDelay, settings.easingType);
        return false;
      }).prepend('<span id="' + settings.containerHoverID + '"></span>').hover(function() {
        $(containerHoverIDHash, this).stop().animate({
          "opacity": 1
        }, 600, "linear");
      }, function() {
        $(containerHoverIDHash, this).stop().animate({
          "opacity": 0
        }, 700, "linear");
      });
      $(window).scroll(function() {
        var sd = $(window).scrollTop();
        if (typeof document.body.style.maxHeight === "undefined") {
          $(containerIDhash).css({
            "position": "absolute",
            "top": $(window).scrollTop() + $(window).height() - 50
          });
        }
        if (sd > settings.min)
          $(containerIDhash).fadeIn(settings.inDelay);
        else
          $(containerIDhash).fadeOut(settings.Outdelay);
      });
    };
  })(jQuery);
  $().UItoTop({
    easingType: "easeOutQuart"
  });
});