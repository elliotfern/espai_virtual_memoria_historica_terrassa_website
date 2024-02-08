export function nameUser(idUser) {
    let urlAjax = devDirectory + "/api/auth/?type=user&id=" + idUser;

    $.ajax({
        url: urlAjax,
        type: 'GET',
        beforeSend: function (xhr) {
            // Obtener el token del localStorage
            let token = localStorage.getItem('token');

            // Incluir el token en el encabezado de autorización
            xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        },
        success: function (data) {
            // Modifica el contenido de un div con el resultado de la API
            let responseData = JSON.parse(data);  // Parsea la respuesta JSON
            let welcomeMessage = `Benvingut: ${responseData.nom}`;
            $('#userDiv').html(welcomeMessage);  // Muestra el mensaje en tu página
        },
        error: function (error) {
            console.error('Error: ' + JSON.stringify(error));
        }
    });
}

