// as the page loads, call these scripts
jQuery(document).ready(function($) {
  // On page load make correct tab active 
  $(function(){
    var sessionTab = sessionStorage.getItem('currentTab');
    if(sessionTab == 'bde-edit'){
      $('li[data-id="bde-add"]').removeClass('selected-tab');
      $('li[data-id="bde-edit"]').addClass('selected-tab');
      $('li[data-id="bde-delete"]').removeClass('selected-tab');
      $('#bde-edit').removeClass('hidden-content');
      $('#bde-add').addClass('hidden-content');
      $('#bde-delete').addClass('hidden-content');


    } else if(sessionTab == 'bde-delete'){
      $('li[data-id="bde-add"]').removeClass('selected-tab');
      $('li[data-id="bde-edit"]').removeClass('selected-tab');
      $('li[data-id="bde-delete"]').addClass('selected-tab');
      $('#bde-edit').addClass('hidden-content');
      $('#bde-add').addClass('hidden-content');
      $('#bde-delete').removeClass('hidden-content');
    } else {
      $('li[data-id="bde-add"]').addClass('selected-tab');
      $('li[data-id="bde-edit"]').removeClass('selected-tab');
      $('li[data-id="bde-delete"]').removeClass('selected-tab');
      $('#bde-edit').addClass('hidden-content');
      $('#bde-add').removeClass('hidden-content');
      $('#bde-delete').addClass('hidden-content');
    }
  });
  
  // function for bioflyer add query
  $(function() {
    $(".bfa-msg").hide();

    $(".bfa-submit").click(function() {
      // on form submit check to make sure all required fields are filled out
      $(".bfa-msg").hide();
      var title = $("input#bf-title-add").val();
      var area_id = $("#bf-area-add").val();
      var file_under = $("#bf-fu-add").val();
      var content;
      var editor = tinyMCE.get('bf-body-add');
      if (editor) {
          // Ok, the active tab is Visual
          content = editor.getContent();
      } else {
          // The active tab is HTML, so just query the textarea
          content = $('#bf-body-add').val();
      }
      if (area_id == "-1" || title == "" || content == "") {
        var err_msg = ""
        $(".bfa-msg").toggleClass("error")
        if (area_id == "-1") {
          err_msg = err_msg + "<p>Please select an area for the bioflyer you are editing.</p>"
        }
        if (title == "") {
          err_msg = err_msg + "<p>Please enter a title for the bioflyer you are editing.</p>"
        }
        if (content == "") {
          err_msg = err_msg + "<p>Please enter content for the bioflyer you are editing.</p>"
        }
        $(".bfa-msg").html(err_msg).show();
        return false;
      }
      // construct data object for php script
      var dataObj = { 'area_id' : area_id,
                      'title' : title,
                      'body' : content,
                      'file_under' : file_under,
                      'form' : "addbioflyer"
                    }
      // send data to php script with ajax and get back JSON on request
      // error if php script runs into a problem
      $.ajax({
        type: "POST",
        url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
        data: dataObj,
        success: function(data) {
          switch (data.status) {
            case "success":
              $(".bfa-msg").toggleClass("updated")
                          .html("<p>Success! The bioflyer has been added to the database.</p>")
                          .show();
              break;
            case "connect_err":
              $(".bfa-msg").toggleClass("error")
                          .html("<p>Uh oh! Something went wrong when contacting the database. Please inform someone from the web team about this error.</p>")
                          .show();
              break;
            case "bf_exists_err":
              $(".bfa-msg").toggleClass("error")
                          .html("<p>A bioflyer with that name already exists. Please choose a different name and try again.</p>")
                          .show();
              break;
            default:
              $(".bfa-msg").toggleClass("error")
                          .html("<p>Uh oh! Something went wrong. Please inform someone from the web team about this error.</p>")
                          .show();
              break;
          }
        }
      });
      // prevent our form from submitting, avoiding a page refresh
      return false;
    });
  });

  // function for area add query
  $(function() {
    $(".aa-msg").hide();
    $(".aa-submit").click(function() {
      // on form submit check to make sure all required fields are filled out
      $(".aa-msg").hide();
      var title = $("input#area-title-add").val().toLowerCase().replace(/ /g,'');
        if (title == "") {
          $(".aa-msg").toggleClass("error")
                      .html("<p>Please enter a title for the new area and try again.</p>")
                      .show();
          $("input#area-title-add").focus();
          return false;
        }
      // construct data string for php script
      var dataString = 'title=' + title + '&dir_url=uifoundation.org/scholarships/' + title + '&form=addarea';
      // send data to php script with ajax and get back JSON on request
      // error if php script runs into a problem
      $.ajax({
        type: "POST",
        url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
        data: dataString,
        success: function(data) {
          switch (data.status) {
            case "success":
              $(".aa-msg").toggleClass("updated")
                          .html("<p>Success! The new area has been added to the database.</p>")
                          .show();
              break;
            case "connect_err":
              $(".aa-msg").toggleClass("error")
                          .html("<p>Uh oh! Something went wrong when contacting the database. Please inform someone from the web team about this error.</p>")
                          .show();
              break;
            case "area_exists_err":
              $(".aa-msg").toggleClass("error")
                          .html("<p>An area with that name already exists. Please choose a different name and try again.</p>")
                          .show();
              break;
            default:
              $(".aa-msg").toggleClass("error")
                          .html("<p>Uh oh! Something went wrong. Please inform someone from the web team about this error.</p>")
                          .show();
              break;
          }
        }
      });
      // prevent our form from submitting, avoiding a page refresh
      return false;
    });
  });

  // function for bioflyer edit query
  $(function() {
    $(".bfe-msg").hide();
    $("#bf-info-edit").hide();

    $(".bfe-submit").click(function() {
      // on form submit check to make sure all required fields are filled out
      $(".bfe-msg").hide();
      var title = $("input#bf-title-edit").val();
      var area_id = $("#bf-area-edit").val();
      var bf_id = $("#bf-edit-select").val();
      var file_under = $("#bf-file-under").val();
      var content;
      var editor = tinyMCE.get('bf-body-edit');
      if (editor) {
          // Ok, the active tab is Visual
          content = editor.getContent();
      } else {
          // The active tab is HTML, so just query the textarea
          content = $('#bf-body-edit').val();
      }
      if (area_id == "-1" || title == "" || content == "") {
        var err_msg = ""
        $(".bfe-msg").toggleClass("error")
        if (area_id == "-1") {
          err_msg = err_msg + "<p>Please select an area for the bioflyer you are editing.</p>"
        }
        if (title == "") {
          err_msg = err_msg + "<p>Please enter a title for the bioflyer you are editing.</p>"
        }
        if (content == "") {
          err_msg = err_msg + "<p>Please enter content for the bioflyer you are editing.</p>"
        }
        $(".bfe-msg").html(err_msg).show();
        return false;
      }
      // construct data object for php script
      var dataObj = { 'area_id' : area_id,
                      'bf_id' : bf_id,
                      'title' : title,
                      'body' : content,
                      'file_under' : file_under,
                      'form' : "editbioflyer"
                    }
      // send data to php script with ajax and get back JSON on request
      // error if php script runs into a problem
      $.ajax({
        type: "POST",
        url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
        data: dataObj,
        success: function(data) {
          handle_error_message(".bfe-msg", data.status);
        }
      });
      // prevent our form from submitting, avoiding a page refresh
      return false;
    });
  });

  // dynamic bioflyer search on edit page
  $(function() {
    $('#bf-title-search').keyup(function(e) {
      if (e.keyCode == 13) {
        e.preventDefault();
        var dataString = 'search_query=' + $("#bf-title-search").val() + '&area_id=' + $('#bf-area-search').val() + '&form=dbsearch';
        $.ajax({
          type: "POST",
          url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
          data: dataString,
          success: function(data) {
            var $sel = $("#bf-edit-select");
            $sel.empty();
            $.each(data.results, function(index, entry) {
               $.each(entry, function(key, value){
                $sel.append( $("<option></option>").attr("value", value).text(key) );
                });
            });
          }
        });
        return false;
      } else {
        var q = $("#bf-title-search").val();
        var dataString = 'search_query=' + q + '&area_id=' + $('#bf-area-search').val() + '&form=dbsearch';

        $.ajax({
          type: "POST",
          url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
          data: dataString,
          success: function(data) {
            console.log('success!');
            var $sel = $("#bf-edit-select");
            $sel.empty();
            $.each(data.results, function(index, entry) {
               $.each(entry, function(key, value){
                $sel.append($("<option></option>").attr("value", value).text(key));

               });

            });
          }
        });
      }
    });

    $('#bf-area-search').change(function() {
      var q = $("#bf-title-search").val();
      var dataString = 'search_query=' + q + '&area_id=' + $(this).val() + '&form=dbsearch';
        $.ajax({
          type: "POST",
          url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
          data: dataString,
          success: function(data) {
            console.log('success!');
            var $sel = $("#bf-edit-select");
            $sel.empty();
            $.each(data.results, function(index, entry) {
               $.each(entry, function(key, value){
                $sel.append($("<option></option>").attr("value", value).text(key));
               });
            });
          }
        });
    });

    $(document).on('click', '#bf-edit-select option', function() {
      var bf_id = $(this).val();
      var title = $(this).text()
      var dataString = 'bf_id=' + bf_id + '&title=' + title + '&form=getbf';
      if ($("#bf-info-edit").css('display') === 'none') $("#bf-info-edit").slideDown();
      // send data to php script with ajax and get back JSON on request
      $.ajax({
        type: "POST",
        url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
        data: dataString,
        success: function(data) {
          console.log(data);
          $("#bf-area-edit").val(data.area_id);
          $("#bf-title-edit").val(data.title);
          $("#bf-body-edit").val(data.body);
          $("#bf-file-under").val(data.file_under);
          var editor = tinyMCE.get('bf-body-edit');
          if (editor) editor.setContent(data.body);
        }
      });
    });
  });

  // function for area edit query
  $(function() {
    $(".ae-msg").hide();
    $(".ae-submit").click(function() {
      // on form submit check to make sure all required fields are filled out
      $(".ae-msg").hide();
      var area_id = $("select#area-edit-select").val();
      var title = $("#area-title-edit").val().toLowerCase().replace(/ /g,'');
      if (area_id == "-1" || title == "") {
        var err_msg = ""
        $(".ae-msg").toggleClass("error")
        if (area_id == "-1") {
          err_msg = err_msg + "<p>Please select an area to edit and try again.</p>"
        }
        if (title == "") {
          err_msg = err_msg + "<p>Please enter a new area title and try again.</p>"
        }
        $(".ae-msg").html(err_msg).show();
        return false;
      }
      // construct data string for php script
      var dataString = 'area_id=' + area_id + '&title=' + title + '&dir_url=uifoundation.org/scholarships/' + title + '&form=editarea';
      // send data to php script with ajax and get back JSON on request
      // error if php script runs into a problem
      $.ajax({
        type: "POST",
        url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
        data: dataString,
        success: function(data) {
          switch (data.status) {
            case "success":
              $(".ae-msg").toggleClass("updated")
                          .html("<p>Success! The designated area has been renamed.</p>")
                          .show();
              break;
            case "connect_err":
              $(".ae-msg").toggleClass("error")
                          .html("<p>Uh oh! Something went wrong when contacting the database. Please inform someone from the web team about this error.</p>")
                          .show();
              break;
            case "area_exists_err":
              $(".ae-msg").toggleClass("error")
                          .html("<p>An area with that name already exists. Please choose a different name and try again.</p>")
                          .show();
              break;
            default:
              $(".ae-msg").toggleClass("error")
                          .html(data)
                          //.html("<p>Uh oh! Something went wrong. Please inform someone from the web team about this error.</p>")
                          .show();
              break;
          }
        }
      });
      // prevent our form from submitting, avoiding a page refresh
      return false;
    });
  });

  // function for bioflyer delete query
  $(function() {
    $(".bfd-msg").hide();
    $(".bfd-submit").click(function() {
      // on form submit check to make sure all required fields are filled out
      $(".bfd-msg").hide();
      var bf_id = $("select#bf-delete-select").val() || "";
      if (bf_id == "") {
        $(".bfd-msg").toggleClass("error")
                    .html("<p>Please select a bioflyer to delete and try again.</p>")
                    .show();
        $("select#bf-delete-select").focus();
        return false;
      }
      var dcheck = confirm("Are you sure you want to delete the following Bioflyer?\n" + $("select#bf-delete-select").text());
      if (dcheck) {
        // construct data string for php script
        var dataString = 'bf_id=' + bf_id + '&form=deletebioflyer';
        // send data to php script with ajax and get back JSON on request
        // error if php script runs into a problem
        $.ajax({
          type: "POST",
          url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
          data: dataString,
          success: function(data) {
              console.log(data)
            switch (data.status) {
              case "success":
                $(".bfd-msg").toggleClass("updated")
                            .html("<p>Success! The bioflyer has been removed from the database.</p>")
                            .show();
                break;
              case "connect_err":
                $(".bfd-msg").toggleClass("error")
                            .html("<p>Uh oh! Something went wrong when contacting the database. Please inform someone from the web team about this error.</p>")
                            .show();
                break;
              default:
                $(".bfd-msg").toggleClass("error")
                            .html("<p>Uh oh! Something went wrong. Please inform someone from the web team about this error.</p>")
                            .show();
                break;
            }
          }
        });
      }
      // prevent our form from submitting, avoiding a page refresh
      return false;
    });
  });

  // function for area delete query
  $(function() {
    $(".da-msg").hide();
    $(".da-submit").click(function() {
      // on form submit check to make sure all required fields are filled out
      $(".da-msg").hide();
      var area_id = $("select#area-title-delete").val();
      if (area_id == "-1") {
        $(".da-msg").toggleClass("error")
                    .html("<p>Please select an area to delete and try again.</p>")
                    .show();
        $("select#area-title-delete").focus();
        return false;
      }
      // construct data string for php script
      var dataString = 'area_id=' + area_id + '&form=deletearea';
      // send data to php script with ajax and get back JSON on request
      // error if php script runs into a problem
      $.ajax({
        type: "POST",
        url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
        data: dataString,
        success: function(data) {
          switch (data.status) {
            case "success":
              $(".da-msg").toggleClass("updated")
                          .html("<p>Success! The designated area has been deleted from the database.</p>")
                          .show();
              break;
            case "connect_err":
              $(".da-msg").toggleClass("error")
                          .html("<p>Uh oh! Something went wrong when contacting the database. Please inform someone from the web team about this error.</p>")
                          .show();
              break;
            default:
              $(".da-msg").toggleClass("error")
                          .html("<p>Uh oh! Something went wrong. Please inform someone from the web team about this error.</p>")
                          .show();
              break;
          }
        }
      });
      // prevent our form from submitting, avoiding a page refresh
      return false;
    });
  });


  // dynamic bioflyer search on delete page
  $(function() {
    $('#bf-title-delete').keyup(function(e) {
      if (e.keyCode == 13) {
        e.preventDefault();
        var dataString = 'search_query=' + $("#bf-title-delete").val() + '&area_id=' + $('#bf-area-delete').val() + '&form=dbsearch';
         $.ajax({
          type: "POST",
          url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
          data: dataString,
          success: function(data) {
            var $sel = $("#bf-delete-select");
            $sel.empty();
            $.each(data.results, function(index, entry) {
               $.each(entry, function(key, value){
                $sel.append( $("<option></option>").attr("value", value).text(key) );
                });
            });
          }
        });
        return false;
      } else {
        var q = $("#bf-title-delete").val();
        var dataString = 'search_query=' + q + '&area_id=' + $('#bf-area-delete').val() + '&form=dbsearch';
        $.ajax({
          type: "POST",
          url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
          data: dataString,
          success: function(data) {
            var $sel = $("#bf-delete-select");
            $sel.empty();
            $.each(data.results, function(index, entry) {
               $.each(entry, function(key, value){
                $sel.append( $("<option></option>").attr("value", value).text(key) );
                });
            });
          }
        });
      }
    });

    $('#bf-area-delete').change(function() {
      var q = $("#bf-title-delete").val();
      var dataString = 'search_query=' + q + '&area_id=' + $(this).val() + '&form=dbsearch';
      $.ajax({
          type: "POST",
          url: "http://www.uifoundation.org/scholarships/wp-content/plugins/bioflyer-editor2/bde-process.php",
          data: dataString,
          success: function(data) {
            console.log(data);
            var $sel = $("#bf-delete-select");
            $sel.empty();
            $.each(data.results, function(index, entry) {
               $.each(entry, function(key, value){
                $sel.append( $("<option></option>").attr("value", value).text(key) );
                });
            });
          }
        });
    });
  });


  // function for show/hide different tabs
  $(function() {
    $("#bde-menu li.tab").click(function(e) {
      e.preventDefault();
      var $id = $(this).data("id");
      $("#bde-menu li.tab").removeClass("selected-tab");
      $(this).addClass("selected-tab");
      $("#bde-main > div:not(#" + $id + ")").addClass("hidden-content");
      $("#bde-main #" + $id).removeClass("hidden-content");
      
      // set session variable to keep tab after page refresh
      var currentTab = document.querySelector('.selected-tab').dataset;
      sessionStorage.setItem('currentTab', currentTab.id)
    });
  });

  // functions for show/hide different forms
  $(".add-form-2").hide();
  $(".edit-form-2").hide();
  $(".delete-form-2").hide();

  $(function() {
    $("[name=add-form-toggle]").click(function(){
      $(".add-form-1").hide();
      $(".add-form-2").hide();
      $(".add-form-"+$(this).val()).show();
    });
  });

  $(function() {
    $("[name=edit-form-toggle]").click(function(){
      $(".edit-form-1").hide();
      $(".edit-form-2").hide();
      $(".edit-form-"+$(this).val()).show();
    });
  });

  $(function() {
    $("[name=delete-form-toggle]").click(function(){
      $(".delete-form-1").hide();
      $(".delete-form-2").hide();
      $(".delete-form-"+$(this).val()).show();
    });
  });

// function to handle error messages 
 function handle_error_message(messageClass, status){
  // Remove any error messages that have already been applied
  document.getElementById('target-bfe-message').className = "";
  document.getElementById('target-bfe-message').className = "bfe-msg";
  
  switch (status) {
    case "success":
        $(messageClass).addClass("updated")
        .html("<p>Success! The bioflyer has been updated.</p>")
        .show();
    break;
    case "connect_err":
      $(messageClass).addClass("error")
      .html("<p>Uh oh! Something went wrong when contacting the database. Please inform someone from the web team about this error.</p>")
      .show();
    break;
    case "bf_exists_err":
      $(messageClass).addClass("error")
      .html("<p>A bioflyer with that name already exists. Please choose a different name and try again.</p>")
      .show();
    break;
    case "no_change":
      $(messageClass).addClass("error")
      .html("<p>No changes to the bioflyer have been made. Please make a change and try again.</p>")
      .show();
    break;
    default:
      $(messageClass).addClass("error")
      .html("<p>Uh oh! Something went wrong. Please inform someone from the web team about this error.</p>")
      .show();
    break;
  }
 }
 
}); /* end of as page load scripts */


