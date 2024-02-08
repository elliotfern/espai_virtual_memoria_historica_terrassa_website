$(document).ready(function () {
  loadTableCustomers();
  loadTableInvoicesCustomers();
  loadTableInvoicesSupplies();
});

function loadTableCustomers() {
  var server = window.location.hostname;
  var urlAjax = "https://" + server + "/accounting/supply/new";
  $("#customersTable").dataTable({
    ajax: {
      url: "../inc/route.php?type=accounting-customers",
      type: "POST",
      dataSrc: "",
    },
    order: [[2, "desc"]],
    columns: [
      { data: "clientNom" },
      { data: "clientEmpresa" },
      { data: "clientRegistre" },
      { data: "estatNom" },
      { data: "id" },
    ],
    columnDefs: [
      {
        // # NAME COSTUMER
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [0],
        visible: true,
        render: function (data, type, row, meta) {
          if (row.clientCognoms == null) {
            return "" + row.clientNom + " ";
          } else {
            return "" + row.clientNom + " " + row.clientCognoms + "";
          }
        },
      },

      {
        // # NAME COMPANY
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [1],
        visible: true,
        render: function (data, type, row, meta) {
          if (row.clientEmpresa == null) {
            return "";
          } else {
            return "" + row.clientEmpresa + " ";
          }
        },
      },

      {
        // # STATUS
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [3],
        visible: true,
        render: function (data, type, row, meta) {
          if (row.clientStatus == 1) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estatNom +
              "</button>"
            );
          } else if (row.clientStatus == 2) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estatNom +
              "</button>"
            );
          } else if (row.clientStatus == 3) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estatNom +
              "</button>"
            );
          } else if (row.clientStatus == 4) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estatNom +
              "</button>"
            );
          } else if (row.clientStatus == 5) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estatNom +
              "</button>"
            );
          } else if (row.clientStatus == 6) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estatNom +
              "</button>"
            );
          } else if (row.clientStatus == 7) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estatNom +
              "</button>"
            );
          } else if (row.clientStatus == 8) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estatNom +
              "</button>"
            );
          } else if (row.clientStatus == 9) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estatNom +
              "</button>"
            );
          } else if (row.clientStatus == 10) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estatNom +
              "</button>"
            );
          } else if (row.clientStatus == 11) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estatNom +
              "</button>"
            );
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
            '<button type="button" onclick="btnUpdateBook(' +
            row.id +
            ')" id="btnUpdateBook" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdateBook" data-id="' +
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
            '<button type="button" onclick="btnUpdateBook(' +
            row.id +
            ')" id="btnUpdateBook" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalUpdateBook" data-id="' +
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

function loadTableInvoicesCustomers() {
  $("#customersInvoicesTable").dataTable({
    ajax: {
      url: "../inc/route.php?type=accounting-customers-invoices",
      type: "POST",
      dataSrc: "",
    },
    order: [[2, "desc"]],
    columns: [
      { data: "id" },
      { data: "clientEmpresa" },
      { data: "facData" },
      { data: "facConcepte" },
      { data: "facTotal" },
      { data: "estat" },
      { data: "id" },
      { data: "id" },
    ],

    columnDefs: [
      {
        // # ID INVOICE
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [0],
        visible: true,
        render: function (data, type, row, meta) {
          return "" + row.id + "/" + row.yearInvoice;
        },
      },
      {
        // # NAME COMPANY
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [1],
        visible: true,
        render: function (data, type, row, meta) {
          if (row.clientEmpresa == null) {
            return "" + row.clientNom + " " + row.clientCognoms + " ";
          } else {
            return "" + row.clientEmpresa + " ";
          }
        },
      },
      {
        // # AMOUNT INVOICE
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [4],
        visible: true,
        render: function (data, type, row, meta) {
          return "" + row.facTotal + "€ ";
        },
      },

      {
        // # STATUS
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [5],
        visible: true,
        render: function (data, type, row, meta) {
          if (row.facEstat == 1) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estat +
              "</button>"
            );
          } else if (row.facEstat == 2) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estat +
              "</button>"
            );
          } else if (row.facEstat == 3) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estat +
              "</button>"
            );
          } else if (row.facEstat == 4) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estat +
              "</button>"
            );
          } else if (row.facEstat == 5) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estat +
              "</button>"
            );
          } else if (row.facEstat == 6) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estat +
              "</button>"
            );
          } else if (row.facEstat == 7) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estat +
              "</button>"
            );
          } else if (row.facEstat == 8) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estat +
              "</button>"
            );
          } else if (row.facEstat == 9) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estat +
              "</button>"
            );
          } else if (row.facEstat == 10) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estat +
              "</button>"
            );
          } else if (row.facEstat == 11) {
            return (
              '<button type="button" class="btn btn-primary btn-sm">' +
              row.estat +
              "</button>"
            );
          }
        },
      },

      {
        // # action controller (edit,delete)
        targets: [6],
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
        // # action controller (edit,delete)
        targets: [7],
        orderable: false,
        // # column rendering
        // https://datatables.net/reference/option/columns.render
        render: function (data, type, row, meta) {
          return (
            '<button type="button" onclick="btnUpdateBook(' +
            row.id +
            ')" id="btnUpdateBook" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalUpdateBook" data-id="' +
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

function loadTableInvoicesSupplies() {
  $("#suppliesInvoicesTable").dataTable({
    ajax: {
      url: "../inc/route.php?type=accounting-supplies-invoices",
      type: "POST",
      dataSrc: "",
    },
    order: [[0, "desc"]],
    columns: [
      { data: "facData" },
      { data: "empresaNom" },
      { data: "facConcepte" },
      { data: "facSubtotal" },
      { data: "ivaPercen" },
      { data: "facTotal" },
      { data: "tipusNom" },
      { data: "id" },
      { data: "id" },
    ],
    columnDefs: [
      {
        // # ID INVOICE
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [0],
        visible: true,
        render: function (data, type, row, meta) {
          return "" + row.facData + "";
        },
      },
      {
        // # NAME COMPANY
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [1],
        visible: true,
        render: function (data, type, row, meta) {
          return "" + row.empresaNom + " ";
        },
      },
      {
        // # AMOUNT INVOICE
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [3],
        visible: true,
        render: function (data, type, row, meta) {
          return "€" + row.facSubtotal + "";
        },
      },

      {
        // # AMOUNT INVOICE
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [4],
        visible: true,
        render: function (data, type, row, meta) {
          return "" + row.ivaPercen + "%";
        },
      },

      {
        // # AMOUNT INVOICE
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [5],
        visible: true,
        render: function (data, type, row, meta) {
          return "€" + row.facTotal + "";
        },
      },

      {
        // # AMOUNT INVOICE
        // https://datatables.net/examples/advanced_init/column_render.html
        targets: [6],
        visible: true,
        render: function (data, type, row, meta) {
          return "" + row.tipusNom + "";
        },
      },

      {
        // # action controller (edit,delete)
        targets: [7],
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
        // # action controller (edit,delete)
        targets: [8],
        orderable: false,
        // # column rendering
        // https://datatables.net/reference/option/columns.render
        render: function (data, type, row, meta) {
          return (
            '<button type="button" onclick="btnUpdateBook(' +
            row.id +
            ')" id="btnUpdateBook" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalUpdateBook" data-id="' +
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

// COMPANY SUPPLY FUNCTIONS
// INPUT OPEN MODAL FORM - CREATE SUPPLY COMPANY
function btnCreateSupplyCompany() {
  $.ajax({
    url: "./forms/company-supply-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalCreateSupplyCompany").html(response);
      $("#bodyModalCreateSupplyCompany").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - CREATE SUPPLY COMPANY
$(function () {
  $("#btnAddSupplyCompany").click(function () {
    // check values
    $("#createSupplyCompanyMessageOk").hide();
    $("#createSupplyCompanyMessageErr").hide();

    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: "./php-process/supplyCompany-insert-process-form.php",
      data: {
        empresaNom: $("#empresaNom").val(),
        empresaNIF: $("#empresaNIF").val(),
        empresaDireccio: $("#empresaDireccio").val(),
        empresaPais: $("#empresaPais").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#createSupplyCompanyMessageOk").show();
          $("#createSupplyCompanyMessageErr").hide();
          $("#suppliesInvoicesTable").DataTable().ajax.reload();
          $("#modalFormBook").hide();
          $("#btnCreateBook").hide();
        } else {
          $("#createSupplyCompanyMessageErr").show();
          $("#createSupplyCompanyMessageOk").hide();
        }
      },
    });
  });
});

// INPUT OPEN MODAL FORM - CREATE SUPPLY INVOICE
function btnCreateSupplyInvoice() {
  $.ajax({
    url: "./forms/invoice-supply-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalAddSupplyInvoice").html(response);
      $("#bodyModalAddSupplyInvoice").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - CREATE SUPPLY INVOICE
$(function () {
  $("#btnAddSupplyInvoice").click(function () {
    // check values
    $("#createSupplyInvoiceMessageOk").hide();
    $("#createSupplyInvoiceMessageErr").hide();

    // Stop form from submitting normally
    event.preventDefault();
    $.ajax({
      type: "POST",
      url: "./php-process/supply-invoice-insert-process-form.php",
      data: {
        facEmpresa: $("#facEmpresa").val(),
        facConcepte: $("#facConcepte").val(),
        facData: $("#facData").val(),
        facSubtotal: $("#facSubtotal").val(),
        facImportIva: $("#facImportIva").val(),
        facTotal: $("#facTotal").val(),
        facIva: $("#facIva").val(),
        facPagament: $("#facPagament").val(),
        loanDirectors: $("#loanDirectors").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#createSupplyInvoiceMessageOk").show();
          $("#createSupplyInvoiceMessageErr").hide();
          $("#suppliesInvoicesTable").DataTable().ajax.reload();
          $("#modalFormAddSupplyInvoice").hide();
          $("#btnCreateBook").hide();
        } else {
          $("#createSupplyInvoiceMessageErr").show();
          $("#createSupplyInvoiceMessageOk").hide();
        }
      },
    });
  });
});

// INPUT OPEN MODAL FORM - CREATE NEW CUSTOMER
function btnCreateCustomer() {
  $.ajax({
    url: "./forms/customer-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalAddCustomer").html(response);
      $("#bodyModalAddCustomer").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - CREATE NEW CUSTOMER
$(function () {
  $("#btnAddCustomer").click(function () {
    // check values
    $("#createCustomerMessageOk").hide();
    $("#createCustomerMessageErr").hide();

    // Stop form from submitting normally
    event.preventDefault();
    $.ajax({
      type: "POST",
      url: "./php-process/customer-insert-process-form.php",
      data: {
        clientNom: $("#clientNom").val(),
        clientCognoms: $("#clientCognoms").val(),
        clientEmail: $("#clientEmail").val(),
        clientWeb: $("#clientWeb").val(),
        clientNIF: $("#clientNIF").val(),
        clientEmpresa: $("#clientEmpresa").val(),
        clientAdreca: $("#clientAdreca").val(),
        clientCiutat: $("#clientCiutat").val(),
        clientProvincia: $("#clientProvincia").val(),
        clientPais: $("#clientPais").val(),
        clientStatus: $("#clientStatus").val(),
        clientCP: $("#clientCP").val(),
        clientTelefon: $("#clientTelefon").val(),
        clientRegistre: $("#clientRegistre").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#createCustomerMessageOk").show();
          $("#createCustomerMessageErr").hide();
          $("#customersTable").DataTable().ajax.reload();
          $("#modalFormAddCustomer").hide();
          $("#btnAddCustomer").hide();
        } else {
          $("#createCustomerMessageErr").show();
          $("#createCustomerMessageOk").hide();
        }
      },
    });
  });
});

// INPUT OPEN MODAL FORM - CREATE CUSTOMER INVOICE
function btnCreateCustomInvoice() {
  $.ajax({
    url: "./forms/invoice-customer-add.php", //the page containing php script
    type: "post", //request type,
    data: {
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalCustomerInvoice").html(response);
      $("#bodyModalCustomerInvoice").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - CREATE NEW CUSTOMER
$(function () {
  $("#btnAddNewCustomerInvoice").click(function () {
    // check values
    $("#createCustomerInvoiceMessageOk").hide();
    $("#createCustomerInvoiceMessageErr").hide();

    // Stop form from submitting normally
    event.preventDefault();
    $.ajax({
      type: "POST",
      url: "./php-process/customer-invoice-insert-process-form.php",
      data: {
        idUser: $("#idUser").val(),
        facConcepte: $("#facConcepte").val(),
        facData: $("#facData").val(),
        facDueDate: $("#facDueDate").val(),
        facSubtotal: $("#facSubtotal").val(),
        facFees: $("#facFees").val(),
        facTotal: $("#facTotal").val(),
        facVAT: $("#facVAT").val(),
        facIva: $("#facIva").val(),
        facEstat: $("#facEstat").val(),
        facPaymentType: $("#facPaymentType").val(),
      },
      success: function (response) {
        if (response.status == "success") {
          // Add response in Modal body
          $("#createCustomerInvoiceMessageOk").show();
          $("#createCustomerInvoiceMessageErr").hide();
          $("#customersInvoicesTable").DataTable().ajax.reload();
          $("#modalFormAddCustomerInvoice").hide();
          $("#btnAddNewCustomerInvoice").hide();
        } else {
          $("#createCustomerInvoiceMessageErr").show();
          $("#createCustomerInvoiceMessageOk").hide();
        }
      },
    });
  });
});
