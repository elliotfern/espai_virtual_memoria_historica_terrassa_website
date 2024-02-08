// LINKS 
function btnCreateLink() {
    // Reset the modal content and messages
    $("#bodyModalCreateLink").empty();
    $("#bodyModalNewLink").show();
    $("#createLinkMessageOk").hide();
    $("#createLinkMessageErr").hide();

    let urlAjax = devDirectory + "/form/links/new";
    $.ajax({
        url: urlAjax, //the page containing php script
        type: "post", //request type,
        data: {
            registration: "yes",
        },
        success: function (response) {
            // Add response in Modal body
            $("#bodyModalCreateLink").html(response);
            $("#btnAddLink").show();
        },
    });
}

// AJAX PROCESS > PHP - MODAL FORM - CREATE NEW LINK
$(function () {
    $("#btnAddLink").click(function () {
        // check values
        $("#createLinkMessageErr").hide();

        // Stop form from submitting normally
        event.preventDefault();
        let urlAjax = devDirectory + "/api/links/post";

        $.ajax({
            type: "POST",
            url: urlAjax,
            data: {
                nom: $("#nom").val(),
                web: $("#web").val(),
                cat: $("#cat").val(),
                lang: $("#lang").val(),
                tipus: $("#tipus").val(),
                linkCreated: $("#linkCreated").val(),
            },
            success: function (response) {
                if (response.status == "success") {
                    // Add response in Modal body
                    $("#createLinkMessageOk").show();
                    $("#createLinkMessageErr").hide();
                    // Reset the form fields
                    $("#bodyModalNewLink").trigger("reset");
                } else {
                    $("#createLinkMessageErr").show();
                    $("#createLinkMessageOk").hide();
                }

                // Hide the messages after 5 seconds
                setTimeout(function () {
                    $("#createLinkMessageOk").hide();
                    $("#createLinkMessageErr").hide();
                }, 5000);  // 5000 milliseconds = 5 seconds

            },
        });
    });
});

// LINKS

// AJAX PROCESS > PHP - MODAL FORM - UPDATE LINK
function btnUpdateLink(event) {
    console.log("clic en la funcion de actualizar link")
    // Stop form from submitting normally
    event.preventDefault();
    let urlAjax = devDirectory + "/api/links/put";
    console.log(urlAjax)
    $.ajax({
        type: "POST",
        url: urlAjax,
        data: {
            id: $("#id").val(),
            nom: $("#nom").val(),
            web: $("#web").val(),
            cat: $("#catTopicsLinks").val(),
            lang: $("#lang").val(),
            tipus: $("#tipusLinks").val(),
        },
        success: function (response) {
            console.log(response)
            if (response.status == "success") {
                // Add response in Modal body
                $("#updateLinkMessageOk").show();
                $("#updateLinkMessageErr").hide();
                $("#botoSave").hide();
            } else {
                $("#updateLinkMessageErr").show();
                $("#updateLinkMessageOk").hide();
            }
        },
    });
}