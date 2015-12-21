var rpc_order_settings = {
  init: function(url) {
    this.option.url = baseurl + url;
  },
  option: {
    url: null
  },
  render_order_form: function(module, order_id) {
    var that = this;
    var data = {};
    var response = null;
    data.module = module;
    data.order_id = order_id;
    $.ajax({
      async: false,
      url: that.option.url + "/renderorderform",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp !== null) {
          response = resp;
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
    return response;
  },
  render_invoice_form: function(order_payment_id) {
    var that = this;
    var data = {};
    var response = null;
    data.module = "borrower";
    data.order_payment_id = order_payment_id;
    $.ajax({
      async: false,
      url: that.option.url + "/renderinvoiceform",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp !== null) {
          response = resp;
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
    return response;
  },
  get_settings: function(order_id) {
    var that = this;
    var data = {};
    var response = null;
    data.order_id = order_id;
    $.ajax({
      async: false,
      url: that.option.url + "/getsettings",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp !== null) {
          response = resp;
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
    return response;
  },
  set_settings: function(order_id, param) {
    var that = this;
    var data = {};
    var response = null;
    data.order_id = order_id;
    data.param = param;
    $.ajax({
      async: false,
      url: that.option.url + "/setsettings",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp !== null) {
          response = resp;
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
    return response;
  },
  get_fields: function(order_id) {
    var that = this;
    var data = {};
    var response = null;
    data.order_id = order_id;
    $.ajax({
      async: false,
      url: that.option.url + "/getfields",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp !== null) {
          response = resp;
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
    return response;
  },
  get_btn_action_name: function(order_status_id) {
    var that = this;
    var data = {};
    var response = null;
    if (order_status_id === 6) {
      data.order_status_id = order_status_id;
      $.ajax({
        async: false,
        url: that.option.url + "/getbtnactionname",
        type: "post",
        dataType: "json",
        data: data,
        success: function(resp) {
          if (resp !== null) {
            response = resp;
          }
        },
        error: function() {
          dialog_msg.init();
          dialog_msg.error();
        }
      });
      return response;
    }
  },
  make_action: function(order_status_id, order_id, is_canceled, param) {
    var that = this;
    var data = {};
    var response = null;
    data.order_status_id = order_status_id;
    data.order_id = order_id;
    data.is_canceled = is_canceled;
    data.param = param;
    $.ajax({
      async: false,
      url: that.option.url + "/makeaction",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp !== null) {
          response = resp;
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
    return response;
  },
  get_tabs_count: function() {
    var that = this;
    var response = null;
    $.ajax({
      async: true,
      url: that.option.url + "/gettabscount",
      type: "post",
      dataType: "json",
      success: function(resp) {
        if (resp !== null) {
          $.each(resp, function(index, value) {
            $("#order_status_id-" + value.id).find(".order_count").html("(" + value.count + ")");
          });
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
  },
  get_cart_total_amount: function() {
    var that = this;
    var response = null;
    $.ajax({
      async: false,
      url: that.option.url + "/getcarttotalamount",
      type: "post",
      dataType: "json",
      success: function(resp) {
        if (resp !== null) {
          response = parseInt(resp);
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
    return response;
  },
  get_session_success: function() {
    var that = this;
    $.ajax({
      async: true,
      url: that.option.url + "/getsessionsuccess",
      type: "post",
      dataType: "json",
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
  },
  clear_session_success: function() {
    var that = this;
    $.ajax({
      async: true,
      url: that.option.url + "/clearsessionsuccess",
      type: "post",
      dataType: "json",
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
  },
  get_user_payment_details: function(order_payment_id) {
    var that = this;
    var data = {};
    var response = null;
    data.order_payment_id = order_payment_id;
    $.ajax({
      async: false,
      url: that.option.url + "/getuserpaymentdetails",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp !== null) {
          response = resp;
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
    return response;
  },
  get_cart_journals: function(order_payment_id) {
    var that = this;
    var data = {};
    var response = null;
    data.order_payment_id = order_payment_id;
    $.ajax({
      async: false,
      url: that.option.url + "/getcarttotalamount",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp !== null) {
          response = resp;
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
    return response;
  },
  get_scanned_files_list: function() {
    var that = this;
    var response = null;
    $.ajax({
      async: false,
      url: that.option.url + "/getscannedfileslist",
      type: "post",
      dataType: "json",
      success: function(resp) {
        if (resp !== null) {
          response = resp;
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
    return response;
  },
  send_invoice: function() {
    var that = this;
    var data = {};
    var response = null;
    data.csrf_token = $('#csrf_token').val();
    data.id = $('#id').val();
    data.order_id = $('#order_id').val();
    data.amount = $('#amount').val();
    data.user_email_address = $('#user_email_address').val();
    data.user_name = $('#user_name').val();
    data.company_name = $('#company_name').val();
    data.user_nip = $('#user_nip').val();
    data.company_address = $('#company_address').val();
    data.forwarding_address = $('#forwarding_address').val();
    $.ajax({
      async: false,
      url: that.option.url + "/sendinvoice",
      type: "post",
      dataType: "json",
      data: data,
      success: function(resp) {
        if (resp !== null) {
          response = resp;
        }
      },
      error: function() {
        dialog_msg.init();
        dialog_msg.error();
      }
    });
    return response;
  }
};
