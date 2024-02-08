$(document).ready(function () {
  loadTableCourses();
  loadTableArticles();
});

function loadTableCourses() {
  $("#courseTable").DataTable({
    ajax: {
      url: "../inc/route.php?type=history-courses",
      dataSrc: "",
    },
    order: [[1, "asc"]],
    columns: [
      { data: "nameEng" },
      { data: "ordre" },
      { data: "descripEng" },
      { data: "wpIdEng" },
      { data: "id" },
      { data: "id" },
    ],

    columnDefs: [
      {
        // # action controller (edit,delete)
        targets: [0],
        orderable: true,
        // # column rendering
        // https://datatables.net/reference/option/columns.render
        render: function (data, type, row, meta) {
          return (
            '<a href="" onclick="btnViewCourse(' +
            row.id +
            ')" id="btnTableViewCourse" data-bs-toggle="modal" data-bs-target="#modalViewCourse" data-id="' +
            row.id +
            '" value="' +
            row.id +
            '" data-title="' +
            row.titol +
            '" data-slug="' +
            row.slug +
            '" data-text="' +
            row.text +
            '">' +
            row.nameEng +
            "</a>"
          );
        },
      },

      {
        // # action controller (edit,delete)
        targets: [3],
        orderable: false,
        // # column rendering
        // https://datatables.net/reference/option/columns.render
        render: function (data, type, row, meta) {
          return (
            '<a href=http://elliotfern.com/"?p=' +
            row.wpIdEng +
            '" target=_blank>View</a>'
          );
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
            '<button type="button" onclick="btnUpdateCourse(' +
            row.id +
            ')" id="btnTableUpdateCourse" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdateCourse" data-id="' +
            row.id +
            '" value="' +
            row.id +
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
            row.id +
            ')" id="btnUpdateCourse" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalUpdateCourse" data-id="' +
            row.id +
            '" value="' +
            row.id +
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

function loadTableArticles() {
  $("#articlesTable").DataTable({
    ajax: {
      url: "../inc/route.php?type=history-articles",
      dataSrc: "",
    },
    columns: [
      { data: "titleCat" },
      { data: "id" },
      { data: "id" },
      { data: "nameEng" },
      { data: "id" },
    ],
    columnDefs: [
      {
        // # hide the first column
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [0],
        visible: true,
        render: function (data, type, row, meta) {
          let str = escape(row.nameEng);
          return (
            '<a id="' +
            row.id +
            '" title="Show book details" data-bs-toggle="modal" data-bs-target="#modalViewBook" href="#" onclick="viewDetailBook(' +
            row.id +
            ",'" +
            str +
            "');return false;\">" +
            row.titleCat +
            "</a>"
          );
        },
      },
    ],
  });
}

// INPUT OPEN MODAL FORM - UPDATE COURSE
function btnUpdateCourse(idCourse) {
  var idCourse = idCourse;
  $.ajax({
    url: "./forms/course-update.php", //the page containing php script
    type: "post", //request type,
    data: {
      idCourse: idCourse,
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalUpdateCourse").html(response);
      $("#bodyModalUpdateCourse").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - UPDATE COURSE
$(function () {
  $("#btnUpdateCourse").click(function () {
    // check values
    $("#updateCourseMessageErr").hide();
    $("#btnUpdateCourse").show();
    $("#modalFormCourse").show();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/course-update-process-form.php",
      data: {
        nameCat: $("#nameCat").val(),
        nameCast: $("#nameCast").val(),
        nameEng: $("#nameEng").val(),
        nameIt: $("#nameIt").val(),
        nameFr: $("#nameFr").val(),
        descripCat: $("#descripCat").val(),
        descripCast: $("#descripCast").val(),
        descripEng: $("#descripEng").val(),
        descripIt: $("#descripIt").val(),
        descripFr: $("#descripFr").val(),
        wpIdCat: $("#wpIdCat").val(),
        wpIdCast: $("#wpIdCast").val(),
        wpIdEng: $("#wpIdEng").val(),
        wpIdIt: $("#wpIdIt").val(),
        wpIdFr: $("#wpIdFr").val(),
        img: $("#img").val(),
        ordre: $("#ordre").val(),
        id: $("#id").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#updateCourseMessageOk").show();
          $("#updateCourseMessageErr").hide();
          $("#courseTable").DataTable().ajax.reload();
          $("#modalFormCourse").hide();
          $("#btnUpdateCourse").hide();
        } else {
          $("#updateCourseMessageErr").show();
          $("#updateCourseMessageOk").hide();
        }
      },
    });
  });
});

// INPUT OPEN MODAL FORM - VIEW COURSE
function btnViewCourse(idCourse) {
  var idCourse = idCourse;
  $.ajax({
    url: "./forms/course-view.php", //the page containing php script
    type: "post", //request type,
    data: {
      idCourse: idCourse,
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalViewCourse").html(response);
      $("#bodyModalViewCourse").show();
    },
  });
}

// INPUT OPEN MODAL FORM - UPDATE ARTICLES-COURSE Wordpress
function btnUpdateHistoryArticle(idWP) {
  var idWP = idWP;
  $.ajax({
    url: "./forms/articles-course-update.php", //the page containing php script
    type: "post", //request type,
    data: {
      idWP: idWP,
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalUpdateHistoryCourse").html(response);
      $("#bodyModalUpdateHistoryCourse").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - UPDATE ARTICLES-COURSE Wordpress
$(function () {
  $("#btnUpdateArticleWPCourse").click(function () {
    // check values
    $("#updateArticleCourseMessageErr").hide();
    $("#modalFormArticleCourse").show();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/article-course-update-process-form.php",
      data: {
        dateModified: $("#dateModified").val(),
        cursId: $("#cursId").val(),
        wpCat: $("#wpCat").val(),
        wpCast: $("#wpCast").val(),
        wpEng: $("#wpEng").val(),
        wpIt: $("#wpIt").val(),
        wpFr: $("#wpFr").val(),
        ordre: $("#ordre").val(),
        id: $("#id").val(),
        idBibl: $("#idBibl").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#updateArticleCourseMessageOk").show();
          $("#updateArticleCourseMessageErr").hide();
          $("#modalFormArticleCourse").hide();
        } else {
          $("#updateArticleCourseMessageErr").show();
          $("#updateArticleCourseMessageOk").hide();
        }
      },
    });
  });
});
