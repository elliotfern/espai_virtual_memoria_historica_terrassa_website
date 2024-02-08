$(document).ready(function () {});

// COMPANY SUPPLY FUNCTIONS
// INPUT OPEN MODAL FORM - CREATE SUPPLY COMPANY
function btnCreateSupplyCompany() {
  let urlAjax = devDirectory + "/accounting/supply/new";

  $.ajax({
    url: urlAjax, //the page containing php script
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

    let urlAjax = devDirectory + "/accounting/process/supply/new";
    // Stop form from submitting normally
    event.preventDefault();

    $.ajax({
      type: "POST",
      url: urlAjax,
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
  let urlAjax = devDirectory + "/accounting/supply/invoice/new";
  $.ajax({
    url: urlAjax, //the page containing php script
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
    let urlAjax = devDirectory + "/accounting/process/supply/invoice/new";
    $.ajax({
      type: "POST",
      url: urlAjax,
      data: {
        facEmpresa: $("#facEmpresa").val(),
        facConcepte: $("#facConcepte").val(),
        facData: $("#facData").val(),
        facSubtotal: $("#facSubtotal").val(),
        facImportIva: $("#facImportIva").val(),
        facTotal: $("#facTotal").val(),
        facIva: $("#facIva").val(),
        facPagament: $("#facPagament").val(),
        clientVinculat: $("#clientVinculat").val(),
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
  let urlAjax = devDirectory + "/accounting/customer/new";
  $.ajax({
    url: urlAjax, //the page containing php script
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
  let urlAjax = devDirectory + "/accounting/process/customer/new";
  $("#btnAddCustomer").click(function () {
    // check values
    $("#createCustomerMessageOk").hide();
    $("#createCustomerMessageErr").hide();

    // Stop form from submitting normally
    event.preventDefault();
    $.ajax({
      type: "POST",
      url: urlAjax,
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
  let urlAjax = devDirectory + "/accounting/invoice-customer/new";
  $.ajax({
    url: urlAjax, //the page containing php script
    type: "post", //request type,
    data: {
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#bodyModalCustomerInvoice").html(response);
      $("#bodyModalCustomerInvoice").show();
      $("#btnAddNewCustomerInvoice").show();
    },
  });
}

// AJAX PROCESS > PHP - MODAL FORM - CREATE NEW INVOICE CUSTOMER - ELLIOT FERNANDEZ SOLE TRADE
$(function () {
  let urlAjax = devDirectory + "/accounting/process/invoice-customer/new";
  $("#btnAddNewCustomerInvoice").click(function () {
    // check values
    $("#createCustomerInvoiceMessageOk").hide();
    $("#createCustomerInvoiceMessageErr").hide();

    // Stop form from submitting normally
    event.preventDefault();
    $.ajax({
      type: "POST",
      url: urlAjax,
      data: {
        idUser: $("#idUser").val(),
        num: $("#num").val(),
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

// INVOICE INFO MODAL
// INPUT OPEN MODAL FORM - VIEW INVOICE INFO
function viewDetailInvoicec(idInvoice) {
  var idInvoice = idInvoice;
  let urlAjax = devDirectory + "/accounting/invoice-customer/info";
  $.ajax({
    url: urlAjax, //the page containing php script
    type: "post", //request type,
    data: {
      idInvoice: idInvoice,
      registration: "yes",
    },
    success: function (response) {
      // Add response in Modal body
      $("#modalViewInvoiceCostumer").html(response);
      $("#modalViewInvoiceCostumer").show();
    },
  });
}

// AJAX CREATE PDF INVOICE

function btnCreatePDFInvoice(id) {
  // Send an AJAX request to the server-side script with the invoice ID
  var xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    `https://gestio.elliotfern.com/accounting/invoice/pdf/${id}`,
    true
  );
  xhr.responseType = "blob";

  xhr.onload = function (e) {
    if (this.status === 200) {
      // Create a blob URL from the received blob data
      var blob = new Blob([this.response], { type: "application/pdf" });
      var url = URL.createObjectURL(blob);

      // Open the PDF in a new tab or window
      window.open(url);
    }
  };

  xhr.send();
}
