/*
 * jQuery File Upload Plugin JS Example 7.0
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */

$(document).ready(function() {
  $("#fileupload").fileupload({});
  $("#fileupload").bind("fileuploadstart", function(e, data) {
    $(document).unbind(".mine");
  });
  $("#fileupload").bind("fileuploadstop", function(e, data) {
    $(document).bind(".mine");
  });
  $("#fileupload").bind("fileuploaddone", function(e, data) {
    if ($("#reset").length) {
      $("#reset").trigger("click");
    }
    if ($("#message").length) {
      var upload_file_url = data.result["files"][0].url;
      var message_tmp = "";
      if ($("#message").hasClass("ckeditor")) {
        message_tmp = CKEDITOR.instances["message"].getData();
        CKEDITOR.instances["message"].setData(message_tmp + " " + "<a target=\"_blank\" href=\"" + upload_file_url + "\">" + upload_file_url + "</a>");
      } else {
        message_tmp = $("#message").val();
        $("#message").val(message_tmp + " " + upload_file_url);
      }
    }
    var add_photo_user_id = parseInt($("#add_photo_user_id").val());
    if (add_photo_user_id) {
      var url = baseurl + "/admin/settings/getuserimage";
      var data = {};
      data["add_photo_user_id"] = add_photo_user_id;
      $.ajax({
        async: false,
        url: url,
        type: "POST",
        dataType: "json",
        data: data,
        success: function(resp) {
          if (resp !== null) {
            var user_id = parseInt($("#user_edit_id").val());
            var new_photo = "<a title=\"\" href=\"" + host + baseurl + "/user/upload/user_id_" + add_photo_user_id + "/normal_" + resp["image"] + "\" rel=\"prettyPhoto[gallery]\"><img width=\"90%\" alt=\"\" title=\"\" src=\"" + host + baseurl + "/user/upload/user_id_" + add_photo_user_id + "/min_" + resp["image"] + "\"></a>";
            $("#user_" + add_photo_user_id).find("span.image").empty().append(new_photo);
            if (add_photo_user_id === user_id) {
              new_photo = "<img alt=\"\" title=\"\" src=\"" + host + baseurl + "/user/upload/user_id_" + user_id + "/normal_" + resp["image"] + "\">";
              $("#user_picture_id").empty().append(new_photo);
            }
          }
        },
        error: function(resp) {
          dialog_msg.init();
          dialog_msg.display("Błąd", "Wystąpił błąd");
        }
      });
    }
  });
  $.ajax({
    global: false,
    url: $("#fileupload").fileupload("option", "url"),
    dataType: "json",
    context: $("#fileupload")[0]
  }).done(function(result) {
    $(this).fileupload("option", "done").call(this, null, {
      result: result
    });
  });
});