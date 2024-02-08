$(document).ready(function () {
  loadTableArticlesElliotfern();
});

function loadTableArticlesElliotfern() {
  $("#articlesTable").DataTable({
    paging: false,
    ajax: {
      url: "../inc/route.php?type=wp-elliotfern-articles",
      dataSrc: "",
    },
    order: [[0, "asc"]],
    columns: [
      { data: "idWp" },
      { data: "post_title" },
      { data: "lang" },
      { data: "type" },
      { data: "idWp" },
      { data: "idWp" },
    ],

    columnDefs: [
      {
        // # action controller (edit,delete)
        targets: [1],
        orderable: true,
        // # column rendering
        // https://datatables.net/reference/option/columns.render
        render: function (data, type, row, meta) {
          return (
            "<a href='http://elliotfern.com/?p=" +
            row.idWp +
            "' target=_blank>" +
            row.post_title +
            "</a>"
          );
        },
      },

      {
        // # LANGUAGE
        targets: [2],
        orderable: true,
        // # column rendering
        // https://datatables.net/reference/option/columns.render
        render: function (data, type, row, meta) {
          if (row.lang == 1) {
            return "Catalan";
          } else if (row.lang == 2) {
            return "English";
          } else if (row.lang == 3) {
            return "Spanish";
          } else if (row.lang == 4) {
            return "Italian";
          } else if (row.lang == 7) {
            return "French";
          } else {
            return "No language";
          }
        },
      },

      {
        // # TYPE
        targets: [3],
        orderable: true,
        // # column rendering
        // https://datatables.net/reference/option/columns.render
        render: function (data, type, row, meta) {
          if (row.type == 1) {
            return "Elliotfern blog";
          } else if (row.type == 2) {
            return "History article";
          } else if (row.type == 3) {
            return "History course page";
          } else if (row.type == 4) {
            return "History timeline";
          } else if (row.type == 5) {
            return "History homepage";
          } else if (row.type == 6) {
            return "History event";
          } else if (row.type == 7) {
            return "History city";
          }
        },
      },

      {
        // # action controller (edit,delete)
        targets: [4],
        orderable: false,
        // # column rendering
        // https://datatables.net/reference/option/columns.render
        render: function (data, type, row, meta) {
          return (
            '<button type="button" onclick="btnUpdateArticleType(' +
            row.idWp +
            ')" id="btnTableUpdateArticlewp" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdateArticleWp" data-id="' +
            row.idWp +
            '" value="' +
            row.idWp +
            '" data-title="' +
            row.titol +
            '" data-slug="' +
            row.slug +
            '" data-text="' +
            row.text +
            '">Update</button>'
          );
        },
      },

      {
        // # action controller (edit,delete)
        targets: [5],
        orderable: false,
        // # column rendering
        // https://datatables.net/reference/option/columns.render
        render: function (data, type, row, meta) {
          return (
            '<button type="button" onclick="btnUpdateCourse(' +
            row.idWp +
            ')" id="btnUpdateCourse" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalUpdateCourse" data-id="' +
            row.idWp +
            '" value="' +
            row.idWp +
            '" data-title="' +
            row.titol +
            '" data-slug="' +
            row.slug +
            '" data-text="' +
            row.text +
            '">Delete</button>'
          );
        },
      },
    ],
  });
}

// INPUT OPEN MODAL FORM - UPDATE ARTICLE TYPE WP
function btnUpdateArticleType(idWP) {
  var idWP = idWP;
  $.ajax({
    url: "./forms/articleType-update.php", //the page containing php script
    type: "post", //request type,
    data: {
      idWP: idWP,
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalUpdateArticle").html(response);
      $("#bodyModalUpdateArticle").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - UPDATE ARTICLE TYPE WP
$(function () {
  $("#btnUpdateArticleWp").click(function () {
    // check values
    $("#updateArticleWpMessageErr").hide();
    $("#btnUpdateArticleWp").show();
    $("#modalFormArticle").show();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/articleType-update-process-form.php",
      data: {
        idPost: $("#idPost").val(),
        lang: $("#lang").val(),
        type: $("#type").val(),
        id: $("#id").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#updateArticleWpMessageOk").show();
          $("#updateArticleWpMessageErr").hide();
          $("#articlesTable").DataTable().ajax.reload();
          $("#modalFormArticle").hide();
        } else {
          $("#updateArticleWpMessageErr").show();
          $("#updateArticleWpMessageOk").hide();
        }
      },
    });
  });
});

// INPUT OPEN MODAL FORM - CREATE ARTICLE TYPE WP
function btnCreateArticle() {
  $.ajax({
    url: "./forms/articleType-create.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalCreateArticle").html(response);
      $("#bodyModalCreateArticle").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - CREATE ARTICLE TYPE WP
$(function () {
  $("#btnCreateArticleWp").click(function () {
    // check values
    $("#createArticleWpMessageOk").hide();
    $("#btnUpdateArticleWp").show();
    $("#modalFormCreateArticle").show();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/articleType-create-process-form.php",
      data: {
        idPost: $("#idPost").val(),
        lang: $("#lang").val(),
        type: $("#type").val(),
        id: $("#id").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#createArticleWpMessageOk").show();
          $("#createArticleWpMessageErr").hide();
          $("#articlesTable").DataTable().ajax.reload();
          $("#modalFormCreateArticle").hide();
        } else {
          $("#createArticleWpMessageErr").show();
          $("#createArticleWpMessageOk").hide();
        }
      },
    });
  });
});
