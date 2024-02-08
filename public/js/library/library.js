// AUTHOR object
// authors table
function authorsTableLibrary() {
  let urlAjax = devDirectory + "/api/library/authors/allAuthors";
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
        let html = '';
        for (let i = 0; i < data.length; i++) {
          html += '<tr>';
          html += '<td class="text-center"><a id="' + data[i].id + '" title="Author page" href="./' + data[i].slug + '"><img src="../../public/img/library-author/' + data[i].nameImg + '.jpg" style="height:70px"></a></td>';

          html += '<td><a id="' + data[i].id + '" title="Author page" href="./' + data[i].slug + '">' + data[i].AutNom + " " + data[i].AutCognom1 + '</a></td>';

          html += '<td><a id="' + data[i].idCountry + '" title="Authors by country" href="./by-country/' + data[i].idCountry + '">' + data[i].country + '</a></td>';

          html += '<td><a id="' + data[i].idProfession + '" title="Authors by profession" href="./by-profession/' + data[i].idProfession + '">' + data[i].profession + '</a></td>';

          if (data[i].yearDie === 0) {
            html += '<td>' + data[i].yearBorn + '</td>';
          } else {
            html += '<td>' + data[i].yearBorn + " - " + data[i].yearDie + '</td>';
          }

          html += '<td><a href="./update/' + data[i].slug + '"><button type="button" class="btn btn-sm btn-warning">Update</button></a></td>';

          html += '<td><button type="button" class="btn btn-sm btn-danger">Delete</button></td>';
          html += '</tr>';
        }
        $('#authorsTable tbody').html(html);
      } catch (error) {
        console.error('Error al parsear JSON:', error);  // Muestra el error de parsing
      }
    }
  })
}

// author page info
function authorPageInfoLibrary(slug) {
  let urlAjax = devDirectory + "/api/library/author/" + slug;
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
        console.log(data)
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

// author book
function authorBookListLibrary(idAuthor) {
  let urlAjax = devDirectory + "/api/library/author/books/" + idAuthor;
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
        let html = '';
        for (let i = 0; i < data.length; i++) {
          html += '<tr>';
          html += '<td><a id="' + data[i].id + '" title="Book page" href="../book/' + data[i].slug + '">' + data[i].titol + '</a></td>';

          html += '<td>' + data[i].any + '</td>';

          html += '<td><button type="button" onclick="btnUpdateBook(' + data[i].id + ')" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdateBook" data-id="' + data[i].id + '">Update</button></td>';

          html += '<td><button type="button" onclick="btnDeleteBook(' + data[i].id + ')" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteBook" data-id="' + data[i].id + '">Delete</button></td>';
          html += '</tr>';
        }
        $('#booksAuthor tbody').html(html);
      } catch (error) {
        console.error('Error al parsear JSON:', error);  // Muestra el error de parsing
      }
    }
  })
}


// info book
function bookInfoLibrary(slug) {
  let urlAjax = devDirectory + "/api/library/book/" + slug;
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

        const newContent = "Book: " + data.titol;
        const h2Element = document.getElementById('titolBook');
        h2Element.innerHTML = newContent;
      } catch (error) {
        console.error('Error al parsear JSON:', error);  // Muestra el error de parsing
      }
    }
  })
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

// INPUT OPEN MODAL FORM - UPDATE BOOK
function btnUpdateBook(idBook) {
  idBook = idBook;
  $.ajax({
    url: "./forms/book-update.php", //the page containing php script
    type: "post", //request type,
    data: {
      idBook: idBook,
      registration: "success",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalUpdateBook").html(response);
      $("bodyModalUpdateBook").show();
      $("#botoSave").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - UPDATE FORM
$(function () {
  $("#botoSave").click(function () {
    // check values
    $(".show_conversation2").hide();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/book-update-process-form.php",
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
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $(".show_conversation").show();
          $("#booksTable").DataTable().ajax.reload();
          $("#modalFormDelete").hide();
          $("#modalFormBook").hide();
          $("#botoSave").hide();
        } else {
          $(".show_conversation2").show();
          $(".show_conversation").hide();
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


// AJAX PROCESS > PHP - MODAL FORM - INSERT AUTHOR
function createNewAuthor(event) {
  event.preventDefault();
  let urlAjax = devDirectory + "/api/library/new/author";

  $.ajax({
    type: "POST",
    url: urlAjax,
    dataType: "json",
    beforeSend: function (xhr) {
      // Obtener el token del localStorage
      let token = localStorage.getItem('token');

      // Incluir el token en el encabezado de autorización
      xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    },
    data: {
      AutNom: $("#AutNom").val(),
      AutCognom1: $("#AutCognom1").val(),
      slug: $("#slug").val(),
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
        $("#createAuthorMessageOk").show();
        $("#createAuthorMessageErr").hide();
      } else {
        $("#createAuthorMessageErr").show();
        $("#createAuthorMessageOk").hide();
      }
    },
  });
}


// AJAX PROCESS > PHP - MODAL FORM - UPDATE AUTHOR
function updateAuthor(event) {

  // Stop form from submitting normally
  event.preventDefault();
  let urlAjax = devDirectory + "/api/library/update/author";
  $.ajax({
    type: "POST",
    url: urlAjax,
    dataType: "JSON",
    headers: { 'X-HTTP-Method-Override': 'PUT' },
    beforeSend: function (xhr) {
      // Obtener el token del localStorage
      let token = localStorage.getItem('token');

      // Incluir el token en el encabezado de autorización
      xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    },
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
      console.log('Response received:', response);
      if (response.status == "success") {
        // Add response in Modal body
        $("#updateAuthorMessageOk").show();
        $("#updateAuthorMessageErr").hide();
      } else {
        $("#updateAuthorMessageErr").show();
        $("#updateAuthorMessageOk").hide();
      }
    },
  });
}

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


// AUTHOR NEW FORM 
function formProfessionAuthor(AutOcupacio) {
  let urlAjax = devDirectory + "/api/library/profession/profession";
  $.ajax({
    url: urlAjax,
    method: "GET",
    dataType: "json",
    beforeSend: function (xhr) {
      let token = localStorage.getItem('token');
      xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    },
    success: function (data) {
      try {
        const select = document.getElementById('AutOcupacio');
        select.innerHTML = '';  // Limpiar el contenido previo

        // Agregar el primer option como marcador de posición
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = 'Select an option:';
        select.appendChild(placeholderOption);

        if (Array.isArray(data) && data.length > 0) {
          data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            select.appendChild(option);
          });

          // Seleccionar la opción por valor (topicId)
          if (AutOcupacio) {
            select.value = AutOcupacio;
          }

        } else {
          console.error('Error: La estructura de datos no es válida.');
        }

      } catch (error) {
        console.error('Error al parsear JSON:', error);
      }
    }
  });
}

function formMovimentAuthor(idMovement) {
  let urlAjax = devDirectory + "/api/library/movement/movement";
  $.ajax({
    url: urlAjax,
    method: "GET",
    dataType: "json",
    beforeSend: function (xhr) {
      let token = localStorage.getItem('token');
      xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    },
    success: function (data) {
      try {
        const select = document.getElementById('AutMoviment');
        select.innerHTML = '';  // Limpiar el contenido previo

        // Agregar el primer option como marcador de posición
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = 'Select an option:';
        select.appendChild(placeholderOption);

        if (Array.isArray(data) && data.length > 0) {
          data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.movement;
            select.appendChild(option);
          });

          if (idMovement) {
            select.value = idMovement;
          }

        } else {
          console.error('Error: La estructura de datos no es válida.');
        }

      } catch (error) {
        console.error('Error al parsear JSON:', error);
      }
    }
  });
}

function formCountry(idPais) {
  let urlAjax = devDirectory + "/api/places/country";
  $.ajax({
    url: urlAjax,
    method: "GET",
    dataType: "json",
    beforeSend: function (xhr) {
      let token = localStorage.getItem('token');
      xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    },
    success: function (data) {
      try {
        const select = document.getElementById('paisAutor');
        select.innerHTML = '';  // Limpiar el contenido previo

        // Agregar el primer option como marcador de posición
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = 'Select an option:';
        select.appendChild(placeholderOption);

        if (Array.isArray(data) && data.length > 0) {
          data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.country;
            select.appendChild(option);
          });

          if (idPais) {
            select.value = idPais;
          }

        } else {
          console.error('Error: La estructura de datos no es válida.');
        }

      } catch (error) {
        console.error('Error al parsear JSON:', error);
      }
    }
  });
}

function formImageAuthor(idImg) {
  let urlAjax = devDirectory + "/api/library/image/author/imageAuthor";
  $.ajax({
    url: urlAjax,
    method: "GET",
    dataType: "json",
    beforeSend: function (xhr) {
      let token = localStorage.getItem('token');
      xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    },
    success: function (data) {
      try {
        const select = document.getElementById('img');
        select.innerHTML = '';  // Limpiar el contenido previo

        // Agregar el primer option como marcador de posición
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = 'Select an option:';
        select.appendChild(placeholderOption);

        if (Array.isArray(data) && data.length > 0) {
          data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nameImg;
            select.appendChild(option);
          });

          if (idImg) {
            select.value = idImg;
          }

        } else {
          console.error('Error: La estructura de datos no es válida.');
        }

      } catch (error) {
        console.error('Error al parsear JSON:', error);
      }
    }
  });
}

// Pagina Update Author
function formUpdateAuthor(slug) {
  let urlAjax = devDirectory + "/api/library/author/" + slug;
  console.log(slug)
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
      console.log(data)
      // Establecer valores en los campos del formulario
      document.getElementById('AutNom').value = data.AutNom;
      document.getElementById("AutCognom1").value = data.AutCognom1;
      document.getElementById('slug').value = data.slug;
      document.getElementById('AutWikipedia').value = data.AutWikipedia;
      document.getElementById('yearBorn').value = data.yearBorn;
      document.getElementById('yearDie').value = data.yearDie;
      document.getElementById('AutDescrip').value = data.AutDescrip;
      document.getElementById('id').value = data.id;

      const newContent = "Author: " + data.AutNom + " " + data.AutCognom1;
      const h2Element = document.getElementById('authorUpdateTitle');
      h2Element.innerHTML = newContent;

      // Ahora llenar el select con las opciones y seleccionar la opción adecuada
      formProfessionAuthor(data.AutOcupacio, true);
      formMovimentAuthor(data.idMovement, true);
      formCountry(data.idPais, true);
      formImageAuthor(data.idImg, true);
    }
  })
}