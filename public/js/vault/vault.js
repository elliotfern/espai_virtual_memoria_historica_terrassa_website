// INPUT OPEN MODAL FORM - CREATE VAULT
function btnCreateVault() {
  var server = window.location.hostname;
  var urlAjax = "https://" + server + "/vault/new";
  $.ajax({
    url: urlAjax, //the page containing php script
    type: "post", //request type,
    data: {
      registration: "success",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalCreateVault").html(response);
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - INSERT VAULT
$(function () {
  $("#btnCreateVault").click(function () {
    // check values
    $("#createVaultMessageErr").hide();
    $("#modalFormAddVault").show();

    // Stop form from submitting normally
    event.preventDefault();
    var server = window.location.hostname;
    var urlAjax = "https://" + server + "/vault/process/new";

    $.ajax({
      type: "POST",
      url: urlAjax,
      data: {
        serveiNom: $("#serveiNom").val(),
        serveiUsuari: $("#serveiUsuari").val(),
        serveiPas: $("#serveiPas").val(),
        serveiType: $("#serveiType").val(),
        serveiWeb: $("#serveiWeb").val(),
        client: $("#client").val(),
        project: $("#project").val(),
        dateCreated: $("#dateCreated").val(),
        dateModified: $("#dateModified").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#createVaultMessageOk").show();
          $("#createVaultMessageErr").hide();
          $("#modalFormAddVault").hide();
          $("#btnCreateVault").hide();
        } else {
          $("#createVaultMessageErr").show();
          $("#createVaultMessageOk").hide();
        }
      },
    });
  });
});

// INPUT OPEN MODAL FORM - UPDATE VAULT
function updateVault(id) {
  var id = id;
  var server = window.location.hostname;
  var urlAjax = "https://" + server + "/vault/update";
  $.ajax({
    url: urlAjax, //the page containing php script
    type: "post", //request type,
    data: {
      registration: "success",
      id: id,
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalUpdateVault").html(response);
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - UPDATE VAULT
$(function () {
  $("#btnUpdateVault").click(function () {
    // check values
    $("#createVaultMessageErr").hide();

    // Stop form from submitting normally
    event.preventDefault();
    var server = window.location.hostname;
    var urlAjax = "https://" + server + "/vault/process/update";

    $.ajax({
      type: "POST",
      url: urlAjax,
      data: {
        serveiNom: $("#serveiNom").val(),
        serveiUsuari: $("#serveiUsuari").val(),
        serveiPas: $("#serveiPas").val(),
        serveiType: $("#serveiType").val(),
        serveiWeb: $("#serveiWeb").val(),
        client: $("#client").val(),
        project: $("#project").val(),
        dateModified: $("#dateModified").val(),
        id: $("#id").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#updateVaultMessageOk").show();
          $("#updateVaultMessageErr").hide();
          $("#bodyModalCreateVault").hide();
          $("#btnCreateVault").hide();
        } else {
          $("#updateVaultMessageErr").show();
          $("#updateVaultMessageOk").hide();
        }
      },
    });
  });
});
