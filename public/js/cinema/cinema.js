function loadTableTVShows() {
  let urlAjax = devDirectory + "/api/cinema/get/?type=tvshows";
  $.ajax({
    url: urlAjax,
    method: "GET",
    dataType: "json",
    beforeSend: function (xhr) {
      // Obtener el token del localStorage
      let token = localStorage.getItem("token");

      // Incluir el token en el encabezado de autorización
      xhr.setRequestHeader("Authorization", "Bearer " + token);
    },

    success: function (data) {
      try {
        let html = "";
        for (let i = 0; i < data.length; i++) {
          html += "<tr>";
          html +=
            '<td class="text-center"><a id="' +
            data[i].id +
            '" title="Author page" href="./' +
            data[i].slug +
            '"><img src="../../public/img/library-author/' +
            data[i].img +
            '.jpg" style="height:70px"></a></td>';

            html +=
            '<td><a id="' +
            data[i].id +
            '" title="TV serie page" href="./tvshows/' +
            data[i].id +
            '">' +
            data[i].name +
            "</a></td>";

          html +=
            '<td><a id="' +
            data[i].id +
            '" title="Author page" href="./' +
            data[i].slug +
            '">' +
            data[i].startYear + "-" + data[i].endYear
            "</a></td>";

          html +=
            '<td><a id="' +
            data[i].idCountry +
            '" title="Authors by country" href="./by-country/' +
            data[i].idCountry +
            '">' +
            data[i].nomDirector + " " + data[i].lastName + 
            "</a></td>";

            html +=
            '<td><a id="' +
            data[i].idCountry +
            '" title="Authors by country" href="./by-country/' +
            data[i].idCountry +
            '">' +
            data[i].country + 
            "</a></td>";

          html +=
            '<td><a href="./update/' +
            data[i].slug +
            '"><button type="button" class="btn btn-sm btn-warning">Update</button></a></td>';

          html +=
            '<td><button type="button" class="btn btn-sm btn-danger">Delete</button></td>';
          html += "</tr>";
        }
        $("#tvshowTable tbody").html(html);
      } catch (error) {
        console.error("Error al parsear JSON:", error); // Muestra el error de parsing
      }
    },
  });
}

function loadTableMovies() {
  $("#moviesTable").DataTable({
    destroy: true,
    autoWidth: false,
    ajax: {
      url: "../inc/route.php?type=movies",
      dataSrc: "data",
    },
    columns: [
      { data: "nameMovie" },
      { data: "yearMovie" },
      { data: "nomDirector" },
      { data: "countryName" },
      { data: "id" },
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
            '" title="Show book details" data-bs-toggle="modal" data-bs-target="#modalViewBook" href="#" onclick="viewDetailBook(' +
            row.id +
            "');return false;\">" +
            row.nameMovie +
            "</a>"
          );
        },
      },
      {
        // # hide the first column
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [1],
        visible: true,
        render: function (data, type, row, meta) {
          return row.yearMovie;
        },
      },

      {
        // # hide the first column
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [2],
        visible: true,
        render: function (data, type, row, meta) {
          return row.nomDirector + " " + row.lastName;
        },
      },

      {
        // # disable search for column number 2
        // https://datatables.net/reference/option/columns.searchable
        targets: [3],
        searchable: true,
        // # disable orderable column
        // https://datatables.net/reference/option/columns.orderable
        orderable: true,
        render: function (data, type, row, meta) {
          return row.countryName;
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
            '<button type="button" onclick="btnUpdateBook(' +
            row.id +
            ')" id="btnUpdateBook" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdateBook" data-id="' +
            row.id +
            '" value="' +
            row.id +
            '" data-title="' +
            row.id +
            '" data-slug="' +
            row.id +
            '" data-text="' +
            row.id +
            '">Update</button>'
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
            '<button type="button" onclick="btnDeleteBook(this)" id="btnDeleteBook" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteBook" data-id="' +
            row.id +
            '" value="' +
            row.id +
            '" data-title="' +
            row.id +
            '" data-slug="' +
            row.id +
            '" data-text="' +
            row.id +
            '">Delete</button>'
          );
        },
      },
    ],
  });
}

// INPUT OPEN MODAL FORM - CREATE TV SHOW
function btnFAddTVShow() {
  $.ajax({
    url: "./forms/tvshow-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalCreateTVShow").html(response);
      $("#bodyModalCreateTVShow").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - CREATE TV SHOW
$(function () {
  $("#btnCreateTVShow").click(function () {
    // check values
    $("#createTVShowMessageOk").hide();
    $("#btnCreateTVShow").show();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/tvshow-insert-process-form.php",
      data: {
        name: $("#name").val(),
        startYear: $("#startYear").val(),
        endYear: $("#endYear").val(),
        season: $("#season").val(),
        chapter: $("#chapter").val(),
        director: $("#director").val(),
        lang: $("#lang").val(),
        genre: $("#genre").val(),
        producer: $("#producer").val(),
        country: $("#country").val(),
        img: $("#img").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#createTVShowMessageOk").show();
          $("#createTVShowMessageErr").hide();
          $("#tvshowTable").DataTable().ajax.reload();
          $("#modalFormAddTVShow").hide();
          $("#btnCreateTVShow").hide();
        } else {
          $("#createTVShowMessageErr").show();
          $("#createTVShowMessageOk").hide();
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

// INPUT OPEN MODAL FORM - DETAIL TV SHOW
function viewDetailTVshow(id, name) {
  var id = id;
  var name = name;
  var name2 = unescape(name);
  $("#tvshowTitle").html("");
  $.ajax({
    url: "./forms/tvshow-view.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "success",
      idShow: id,
      name: name,
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalViewTVShow").html(response);
      $("#tvshowTitle").append(name2);
    },
  });
}

function loadTableActors() {
  $("#actorsTable").DataTable({
    destroy: true,
    autoWidth: false,
    ajax: {
      url: "../inc/route.php?type=actors",
      dataSrc: "data",
    },
    columns: [
      { data: "actorLastName" },
      { data: "birthYear" },
      { data: "country" },
      { data: "id" },
      { data: "id" },
    ],
    columnDefs: [
      {
        // # hide the first column
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [0],
        visible: true,
        render: function (data, type, row, meta) {
          let actorLastName2 = escape(row.actorLastName);
          let actorFirstName2 = escape(row.actorFirstName);
          return (
            '<a id="' +
            row.id +
            '" title="View actor details" data-bs-toggle="modal" data-bs-target="#modalViewActor" href="#" onclick="viewDetailActor(' +
            row.id +
            ",'" +
            actorLastName2 +
            "','" +
            actorFirstName2 +
            "');return false;\">" +
            row.actorLastName +
            ", " +
            row.actorFirstName +
            "</a>"
          );
        },
      },
      {
        // # hide the first column
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [1],
        visible: true,
        render: function (data, type, row, meta) {
          if (row.deadYear == null) {
            return row.birthYear + " - Present";
          } else {
            return row.birthYear + " - " + row.deadYear;
          }
        },
      },

      {
        // # hide the first column
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [2],
        visible: true,
        render: function (data, type, row, meta) {
          return row.country;
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
            '<button type="button" onclick="btnUpdateBook(' +
            row.id +
            ')" id="btnUpdateBook" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdateBook" data-id="' +
            row.id +
            '" value="' +
            row.id +
            '" data-title="' +
            row.id +
            '" data-slug="' +
            row.id +
            '" data-text="' +
            row.id +
            '">Update</button>'
          );
        },
      },
      {
        // # action controller (delete)
        targets: [4],
        orderable: false,
        // # column rendering
        // https://datatables.net/reference/option/columns.render
        render: function (data, type, row, meta) {
          return (
            '<button type="button" onclick="btnDeleteBook(this)" id="btnDeleteBook" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteBook" data-id="' +
            row.id +
            '" value="' +
            row.id +
            '" data-title="' +
            row.id +
            '" data-slug="' +
            row.id +
            '" data-text="' +
            row.id +
            '">Delete</button>'
          );
        },
      },
    ],
  });
}

// INPUT OPEN MODAL FORM - DETAIL ACTOR
function viewDetailActor(id, lastName, firstName) {
  var id = id;
  var firstName = firstName;
  var firstName2 = unescape(firstName);
  var lastName = lastName;
  var lastName2 = unescape(lastName);
  $("#actorTitle").html("");
  $.ajax({
    url: "./forms/actor-view.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "success",
      idActor: id,
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalViewActor").html(response);
      $("#actorTitle").append(firstName2 + " " + lastName2);
    },
  });
}

// INPUT OPEN MODAL FORM - CREATE ACTOR
function btnFAddActor() {
  $.ajax({
    url: "./forms/actor-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalCreateActor").html(response);
      $("#bodyModalCreateActor").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - CREATE ACTOR
$(function () {
  $("#btnCreateActor").click(function () {
    // check values
    $("#createActorMessageOk").hide();
    $("#btnCreateActor").show();
    $("#modalFormAddActor").show();

    // Stop form from submitting normally
    event.preventDefault();
    $.ajax({
      type: "POST",
      url: "./php-process/actor-insert-process-form.php",
      data: {
        actorLastName: $("#actorLastName").val(),
        actorFirstName: $("#actorFirstName").val(),
        actorCountry: $("#actorCountry").val(),
        birthYear: $("#birthYear").val(),
        deadYear: $("#deadYear").val(),
        img: $("#img").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#createActorMessageOk").show();
          $("#createActorMessageErr").hide();
          $("#actorsTable").DataTable().ajax.reload();
          $("#modalFormAddActor").hide();
          $("#btnCreateActor").hide();
        } else {
          $("#createActorMessageErr").show();
          $("#createActorMessageOk").hide();
        }
      },
    });
  });
});

// INPUT OPEN MODAL FORM - ASSOCIATE ACTOR TO TV SHOW
function btnAssActorTVshow(id) {
  var id = id;
  $.ajax({
    url: "./forms/actor-tvshow-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "success",
      idActor: id,
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalActorTVshow").html(response);
      $("#bodyModalActorTVshow").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - ASSOCIATE ACTOR TO TV SHOW
$(function () {
  $("#btnAddActorTVShow").click(function () {
    // check values
    $("#createActorTVshowMessageOk").hide();
    $("#btnAddActorTVShow").show();
    $("#modalFormAddActorTVShow").show();

    // Stop form from submitting normally
    event.preventDefault();
    $.ajax({
      type: "POST",
      url: "./php-process/actor-tvshow-insert-process-form.php",
      data: {
        idtvShow: $("#idtvShow").val(),
        idActor: $("#idActor").val(),
        role: $("#role").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#createActorTVshowMessageOk").show();
          $("#createActorTVshowMessageErr").hide();
          $("#actorsTable").DataTable().ajax.reload();
          $("#modalFormAddActorTVShow").hide();
          $("#btnAddActorTVShow").hide();
        } else {
          $("#createActorTVshowMessageErr").show();
          $("#createActorTVshowMessageOk").hide();
        }
      },
    });
  });
});


// PAGE TV SHOW INFO
// https://gestio.elliotfern.com/api/cinema/get/?type=tvshow&id=35
function tvshowPageInfo(slug) {
  let urlAjax = devDirectory + "/api/cinema/get/?type=tvshow&id=" + slug;
  $.ajax({
    url: urlAjax,
    method: "GET",
    dataType: "json",
    beforeSend: function (xhr) {
      // Obtener el token del localStorage
      let token = localStorage.getItem('token');

      // Incluir el token en el encabezado de autorización
      xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    },

    success: function (data) {
      try {
        const idAuthor = data.id;
        authorBookListLibrary(idAuthor)

        // DOM modifications
        document.getElementById('authorName').innerHTML = "Author: " + data.AutNom + " " + data.AutCognom1;
        document.getElementById("authorPhoto").src = `../../public/img/library-author/${data.nameImg}.jpg`;
        document.getElementById('authorCountry').innerHTML = data.country;

        if (data.yearDie === null || data.yearDie === "NULL" || data.yearDie === 0) {
          document.getElementById('authorYearBirth').innerHTML = data.yearBorn;
        } else {
          document.getElementById('authorYearBirth').innerHTML = data.yearBorn + " - " + data.yearDie;
        }

        document.getElementById('linkAuthor').href = `../country/${data.idPais}`;
        document.getElementById('authorprofession').innerHTML = data.name;
        document.getElementById('authorMovement').innerHTML = data.movement;
        document.getElementById('linkMovement').href = `../movement/${data.idMovement}`;
        document.getElementById('authorWeb').href = `${data.AutWikipedia}`;
        document.getElementById('authorCreated').innerHTML = data.dateCreated;
        document.getElementById('authorUpdated').innerHTML = data.dateModified;
        document.getElementById('authorDescrip').innerHTML = data.AutDescrip;

      } catch (error) {
        console.error('Error al parsear JSON:', error);  // Muestra el error de parsing
      }
    }
  })
}