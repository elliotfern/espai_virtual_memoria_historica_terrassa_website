// INPUT OPEN MODAL FORM - CREATE USER
function btnCreateBook(idAuthor) {
  var idAuthor = idAuthor;
  $.ajax({
    url: "./forms/book-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      idAuthor: idAuthor,
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalCreateBook").html(response);
      $("#bodyModalCreateBook").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - CREATE BOOK
$(function () {
  $("#btnCreateBook").click(function () {
    // check values
    $("#createBookMessageErr").hide();
    $("#btnCreateBook").show();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/book-insert-process-form.php",
      data: {
        nomAutor: $("#nomAutor").val(),
        titol: $("#titol").val(),
        titolEng: $("#titolEng").val(),
        any: $("#any").val(),
        idEd: $("#idEd").val(),
        idGen: $("#idGen").val(),
        lang: $("#lang").val(),
        img: $("#img").val(),
        tipus: $("#tipus").val(),
        idBook: $("#idBook").val(),
        dateCreated: $("#dateCreated").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#createBookMessageOk").show();
          $("#createBookMessageErr").hide();
          $("#booksTable").DataTable().ajax.reload();
          $("#modalFormBook").hide();
          $("#btnCreateBook").hide();
        } else {
          $("#createBookMessageErr").show();
          $("#createBookMessageOk").hide();
        }
      },
    });
  });
});

// INPUT OPEN MODAL FORM - UPDATE USER
function btnUpdateUser(idUser) {
  idUser = idUser;
  var server = window.location.hostname;
  var urlAjax = "https://" + server + "/users/update";
  $.ajax({
    url: urlAjax,
    type: "post", //request type,
    data: {
      idUser: idUser,
      registration: "success",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalUpdateUser").html(response);
      $("bodyModalUpdateUser").show();
      $("#botoSave").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - UPDATE USER FORM
$(function () {
  $("#botoUpdateUser").click(function () {
    var server = window.location.hostname;
    var urlAjax = "https://" + server + "/users/process/update";
    // check values
    $(".show_conversation2").hide();

    // Stop form from submitting normally
    event.preventDefault();
    $.ajax({
      type: "POST",
      url: urlAjax,
      data: {
        id: $("#id").val(),
        username: $("#username").val(),
        firstName: $("#firstName").val(),
        lastName: $("#lastName").val(),
        email: $("#email").val(),
        password: $("#password").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#updateUserMessageOk").show();
          $("#updateUserMessageErr").hide();
          $("#modalFormBook").hide();
          $("#botoSave").hide();
        } else {
          $("#updateUserMessageErr").show();
          $("#updateUserMessageOk").hide();
        }
      },
    });
  });
});

// INPUT OPEN MODAL FORM - DELETE BOOK
function btnDeleteBook(element) {
  id = element.value;
  $("#btnDelete").show();
  $.ajax({
    url: "./forms/book-delete.php", //the page containing php script
    type: "post", //request type,
    data: {
      idBook: id,
      registration: "success",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalDeleteBook").html(response);
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - DELETE FORM
$(function () {
  $("#btnDelete").click(function () {
    // check values
    $(".show_conversation2").hide();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/book-delete-process-form.php",
      data: {
        idBook: $("#idBook").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $(".show_conversation").show();
          $("#modalFormDelete").hide();
          $("#btnDelete").hide();
          $("#booksTable").DataTable().ajax.reload();
        } else {
          $(".show_conversation2").show();
          $(".show_conversation").hide();
        }
      },
    });
  });
});

// INPUT OPEN MODAL FORM - DETAIL BOOK
function viewDetailBook(id, title) {
  var id = id;
  var title = title;
  var title2 = unescape(title);
  $("#bookName").html("");
  $.ajax({
    url: "./forms/book-view.php", //the page containing php script
    type: "post", //request type,
    data: {
      idBook: id,
      title: title2,
      registration: "success",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalDetailBook").html(response);
      $("#bookName").append(title2);
    },
  });
}

// AUTHOR object
// authors table
function loadTableAuthors() {
  $("#authorsTable").DataTable({
    ajax: {
      url: "../inc/route.php?type=library_authors",
    },
    columns: [
      { data: "AutNom" },
      { data: "country" },
      { data: "name" },
      { data: "yearBorn" },
      { data: "id" },
    ],
    columnDefs: [
      {
        // # hide the first column
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [0],
        visible: true,
        render: function (data, type, row, meta) {
          return (
            '<a id="' +
            row.id +
            '" title="Show author details" data-bs-toggle="modal" data-bs-target="#modalViewAuthor" href="#" onclick="viewDetailAuthor(' +
            row.id +
            ",'" +
            row.AutNom +
            "','" +
            row.AutCognom1 +
            "');return false;\">" +
            row.AutCognom1 +
            ", " +
            row.AutNom +
            "</a>"
          );
        },
      },
      {
        // # disable search for column number 2
        // https://datatables.net/reference/option/columns.searchable
        targets: [3],
        searchable: false,
        // # disable orderable column
        // https://datatables.net/reference/option/columns.orderable
        orderable: true,
        render: function (data, type, row, meta) {
          if (row.yearDie == 0) {
            return row.yearBorn;
          } else {
            return row.yearBorn + " - " + row.yearDie;
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
            '<button type="button" onclick="updateAuthor(' +
            row.id +
            ')" id="btnUpdateAuthor" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdateAuthor">Update</button>'
          );
        },
      },
      {
        // # action controller (delete)
        targets: [5],
        orderable: false,
        // # column rendering
        // https://datatables.net/reference/option/columns.render
        render: function (data, type, row, meta) {
          return (
            '<button type="button" onclick="btnDeleteBook("' +
            row.id +
            '")" id="btnDeleteBook" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteBook" data-id="' +
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

// INPUT OPEN MODAL FORM - CREATE AUTHOR
function btnCreateAuthor() {
  $.ajax({
    url: "./forms/author-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "success",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalCreateAuthor").html(response);
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - INSERT AUTHOR
$(function () {
  $("#btnAddAuthor").click(function () {
    // check values
    $("#createAuthorMessageErr").hide();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/author-insert-process-form.php",
      data: {
        AutNom: $("#AutNom").val(),
        AutCognom1: $("#AutCognom1").val(),
        yearBorn: $("#yearBorn").val(),
        yearDie: $("#yearDie").val(),
        paisAutor: $("#paisAutor").val(),
        img: $("#img").val(),
        AutWikipedia: $("#AutWikipedia").val(),
        AutDescrip: $("#AutDescrip").val(),
        AutMoviment: $("#AutMoviment").val(),
        dateCreated: $("#dateCreated").val(),
        AutOcupacio: $("#AutOcupacio").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#authorsTable").DataTable().ajax.reload();
          $("#createAuthorMessageOk").show();
          $("#createAuthorMessageErr").hide();
          $("#modalFormDelete").hide();
          $("#modalFormBook").hide();
          $("#botoSave").hide();
        } else {
          $("#createAuthorMessageErr").show();
          $("#createAuthorMessageOk").hide();
        }
      },
    });
  });
});

// INPUT OPEN MODAL FORM - DETAIL AUTHOR
function viewDetailAuthor(id, nom, cognom) {
  var id = id;
  var nom = nom;
  var cognom = cognom;
  $("#authorName").html("");
  $.ajax({
    url: "./forms/author-view.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "success",
      idAuthor: id,
      nom: nom,
      cognom: cognom,
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalViewAuthor").html(response);
      $("#authorName").append(nom + " " + cognom);
    },
  });
}

// INPUT OPEN MODAL FORM - UPDATE AUTHOR
function updateAuthor(id) {
  var id = id;
  $.ajax({
    url: "./forms/author-update.php", //the page containing php script
    type: "post", //request type,
    data: {
      id: id,
      registration: "success",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalUpdateAuthor").html(response);
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - UPDATE AUTHOR
$(function () {
  $("#btnUpdateAuthor").click(function () {
    // check values
    $("#updateAuthorMessageErr").hide();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/author-update-process-form.php",
      data: {
        AutNom: $("#AutNom").val(),
        AutCognom1: $("#AutCognom1").val(),
        yearBorn: $("#yearBorn").val(),
        yearDie: $("#yearDie").val(),
        paisAutor: $("#paisAutor").val(),
        img: $("#img").val(),
        AutWikipedia: $("#AutWikipedia").val(),
        AutDescrip: $("#AutDescrip").val(),
        AutMoviment: $("#AutMoviment").val(),
        dateModified: $("#dateModified").val(),
        AutOcupacio: $("#AutOcupacio").val(),
        id: $("#id").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#authorsTable").DataTable().ajax.reload();
          $("#updateAuthorMessageOk").show();
          $("#updateAuthorMessageErr").hide();
          $("#modalFormAuthor").hide();
          $("#btnUpdateAuthor").hide();
        } else {
          $("#updateAuthorMessageErr").show();
          $("#updateAuthorMessageOk").hide();
        }
      },
    });
  });
});

// UPLOAD IMAGE FORM
function divUploadImg(idType) {
  var idType = idType;
  $.ajax({
    url: "./forms/img-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      image: "yes",
      idType: idType,
    },
    success: function (response) {
      // Add response in Modal body
      $("#uploadImg").html(response);
      $("#uploadImg").show();
    },
  });
}

// ADD PUBLISHER FORM
function divCreatePublisher() {
  $.ajax({
    url: "./forms/publisher-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      publisherForm: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#createPublisher").html(response);
      $("#createPublisher").show();
    },
  });
}

// ADD A SECOND AUTHOR TO A BOOK
function divAnotherAuthor(idBook) {
  var idBook = idBook;
  $.ajax({
    url: "./forms/author-second-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      image: "yes",
      idBook: idBook,
    },
    success: function (response) {
      // Add response in Modal body
      $("#addAnotherAuthor").html(response);
      $("#addAnotherAuthor").show();
    },
  });
}

// ADD A NEW COLLECTION TO A BOOK
function addCollectionBook(idBook) {
  var idBook = idBook;
  $.ajax({
    url: "./forms/book-collection-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      bookCollection: "yes",
      idBook: idBook,
    },
    success: function (response) {
      // Add response in Modal body
      $("#bookCollection").html(response);
      $("#bookCollection").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - ADD NEW COLLECTION TO BOOK
$(function () {
  $("#btnAddCollectionBook").click(function () {
    // check values
    $("#addCollectionBookMessageErr").hide();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/book-collection-insert-process-form.php",
      data: {
        ordre: $("#ordre").val(),
        idCollection: $("#idCollection").val(),
        idBook: $("#idBook").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#addCollectionBookMessageOk").show();
          $("#addCollectionBookMessageErr").hide();
          $("#addCollectionBookForm").hide();
          $("#btnAddCollectionBook").hide();
          $("#booksTable").DataTable().ajax.reload();
        } else {
          $("#addCollectionBookMessageErr").show();
          $("#addCollectionBookMessageOk").hide();
        }
      },
    });
  });
});

// ADD A NEW COLLECTION
function divCreateNewCollection() {
  var idBook = idBook;
  $.ajax({
    url: "./forms/book-create-collection-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      bookCollection: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#createNewCollection").html(response);
      $("#createNewCollection").show();
    },
  });
}
