function categoriesLinks() {
  let urlAjax = devDirectory + "/api/links/get/?type=categories";
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
          html += '<td><a id="' + data[i].id + '" title="Show category" href="category/' + data[i].id + '">' + data[i].genre + '</a></td>';
          html += '<td><button type="button" onclick="btnUpdateBook(' + data[i].id + ')" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdateBook" data-id="' + data[i].id + '">Update</button>';
          html += '<button type="button" onclick="btnDeleteBook(' + data[i].id + ')" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteBook" data-id="' + data[i].id + '">Delete</button></td>';
          html += '</tr>';
        }
        $('#categoriesLinks tbody').html(html);
      } catch (error) {
        console.error('Error al parsear JSON:', error);  // Muestra el error de parsing
      }
    }
  })
}

// Pàgina categoria > id - aquesta funcio mostra una taula
function categoriaAllTopics(idCategoria) {
  let urlAjax = devDirectory + "/api/links/get/?type=categoria&id=" + idCategoria;
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
      const newContent = "Categoria: " + data[0].genre;
      const h2Element = document.getElementById('titolCategoria');
      h2Element.innerHTML = newContent;
      try {

        let html = '';
        for (let i = 0; i < data.length; i++) {
          html += '<tr>';
          html += '<td><a id="' + data[i].id + '" title="Show category" href="../topic/' + data[i].idTema + '">' + data[i].tema + '</a></td>';
          html += '<td><button type="button" onclick="btnUpdateBook(' + data[i].id + ')" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdateBook" data-id="' + data[i].id + '">Update</button>';
          html += '<button type="button" onclick="btnDeleteBook(' + data[i].id + ')" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteBook" data-id="' + data[i].id + '">Delete</button></td>';
          html += '</tr>';
        }
        $('#categoriaLinks tbody').html(html);
      } catch (error) {
        console.error('Error al parsear JSON:', error);  // Muestra el error de parsing
      }
    }
  })
}

// Pàgina topic > id - aquesta funcio mostra una taula amb tots els links
function categoriaAllLinksByTopic(idTopic, page = 1, itemsPerPage = 10) {
  let urlAjax = `${devDirectory}/api/links/get/?type=topic&id=${idTopic}&page=${page}&itemsPerPage=${itemsPerPage}`;
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
      const totalPages = Math.ceil(data[0].total_count / itemsPerPage);

      const newContent = "Topic: " + data[0].tema;
      const h2Element = document.getElementById('titolTopic');
      h2Element.innerHTML = newContent;

      const newContent2Link = data[0].idCategoria;
      const newContent2 = `Category: <a href="../category/${newContent2Link}">${data[0].genre}</a>`;
      const h2Element2 = document.getElementById('titolTopicCategoria');
      h2Element2.innerHTML = newContent2;

      try {

        let html = '';
        for (let i = 0; i < data.length; i++) {
          html += '<tr>';
          html += '<td><a href="' + data[i].url + '" target="_blank">' + data[i].nom + '</a></td>';
          html += '<td>';
          let langInt = data[i].lang;
          if (langInt == 1) {
            html += 'English';
          } else if (langInt == 2) {
            html += 'Catalan';
          } else if (langInt == 3) {
            html += 'Spanish';
          } else if (langInt == 4) {
            html += 'Italian';
          } else {
            html += 'None';
          }
          html += '</td>';
          html += '<td>' + data[i].type + '</td>';

          html += '<td><a class="btn btn-primary btn-sm" href="../update/' + data[i].linkId + '" role="button">Update</a>';

          html += '<td><a class="btn btn-danger btn-sm" href="../update/' + data[i].linkId + '" role="button">Delete</a>';

          html += '</tr>';
        }
        $('#topicsLinks tbody').html(html);

        // Llamar a una función para crear los enlaces de paginación
        createPaginationLinks(page, totalPages, idTopic);

      } catch (error) {
        console.error('Error al parsear JSON:', error);  // Muestra el error de parsing
      }
    }
  })
}

// Función para crear los enlaces de paginación
function createPaginationLinks(currentPage, totalPages, idTopic) {
  const paginationContainer = document.getElementById('pagination');
  paginationContainer.innerHTML = '';

  for (let i = 1; i <= totalPages; i++) {
    const pageLink = document.createElement('a');
    pageLink.href = '#';
    pageLink.textContent = i;
    // Prevenir el comportamiento predeterminado del enlace
    pageLink.addEventListener('click', (event) => {
      event.preventDefault();
      categoriaAllLinksByTopic(idTopic, i);
    });

    // Agregar una clase para resaltar la página actual
    if (i === currentPage) {
      pageLink.classList.add('current-page');
    }

    paginationContainer.appendChild(pageLink);
  }
}

// Pàgina allTopics
function allTopicsList() {
  let urlAjax = devDirectory + "/api/links/get/?type=all-topics";
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
          html += '<td><a href="./topic/' + data[i].idTema + '">' + data[i].tema + '</a></td>';
          html += '<td><a href="./category/' + data[i].idGenre + '">' + data[i].genre + '</a></td>';
          html += '<td><button type="button" onclick="modalUpdateLink(' + data[i].linkId + ')" id="btnUpdateLink" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdateLink" data-id="' + data[i].linkId + '" value="' + data[i].linkId + '" data-title="' + data[i].linkId + '" data-slug="' + data[i].linkId + '" data-text="' + data[i].linkId + '">Update</button></td> ';
          html += '<td><button type="button" onclick="btnRemoveLink(' + data[i].linkId + ')" id="btnRemoveLink" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalRemoveLink" data-id="' + data[i].linkId + '" value="' + data[i].linkId + '" data-title="' + data[i].linkId + '" data-slug="' + data[i].linkId + '" data-text="' + data[i].linkId + '">Delete</button></td>';
          html += '</tr>';
        }
        $('#allTopicsList tbody').html(html);
      } catch (error) {
        console.error('Error al parsear JSON:', error);  // Muestra el error de parsing
      }
    }
  })
}

// Pàgina UpdateLink
function formUpdateLink(idLink) {
  let urlAjax = devDirectory + "/api/links/get/?type=link&id=" + idLink;
  console.log(idLink)
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
      document.getElementById('id').value = data.id;
      document.getElementById("nom").value = data.nom;
      document.getElementById('web').value = data.web;
      document.getElementById('lang').value = data.lang;

      const newContent = "Link: " + data.nom;
      const h2Element = document.getElementById('titolLinkUpdate');
      h2Element.innerHTML = newContent;

      // Ahora llenar el select con las opciones y seleccionar la opción adecuada
      formTopicsLinksList(data.idTema, true);
      formTypeLinksList(data.tipus, true);
    }
  })
}

function formTopicsLinksList(topicId) {
  let urlAjax = devDirectory + "/api/links/get/?type=all-topics";
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
        const select = document.getElementById('catTopicsLinks');
        select.innerHTML = '';  // Limpiar el contenido previo

        if (Array.isArray(data) && data.length > 0) {
          data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.idTema;
            option.textContent = item.tema;
            select.appendChild(option);
          });

          // Seleccionar la opción por valor (topicId)
          select.value = topicId;
        } else {
          console.error('Error: La estructura de datos no es válida.');
        }

      } catch (error) {
        console.error('Error al parsear JSON:', error);
      }
    }
  });
}

function formTypeLinksList(tipus) {
  let urlAjax = devDirectory + "/api/links/get/?type=all-types";
  $.ajax({
    url: urlAjax,
    method: "GET",
    dataType: "json",
    beforeSend: function (xhr) {
      let token = localStorage.getItem('token');
      xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    },
    success: function (data) {
      console.log(data)
      try {
        const select = document.getElementById('tipusLinks');
        select.innerHTML = '';  // Limpiar el contenido previo

        if (Array.isArray(data) && data.length > 0) {
          data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.idType;
            option.textContent = item.type;
            select.appendChild(option);
          });

          // Seleccionar la opción por valor (tipus)
          select.value = tipus;
        } else {
          console.error('Error: La estructura de datos no es válida.');
        }

      } catch (error) {
        console.error('Error al parsear JSON:', error);
      }
    }
  });
}
