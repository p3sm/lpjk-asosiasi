$(document).ready(function(){
  $('#btn-hapus').click(function(){
    var text = $(this).html()

    $(this).html("Loading...");

    var url = $(this).data("url");
    var id = $(this).data("id");

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax(
    {
        url: url,
        type: 'delete', // replaced from put
        dataType: "JSON",
        data: {
            "id": id // method and token not needed in data
        },
        success: function (response)
        {
          $(this).html(text);
          location.reload();
        },
        error: function(xhr) {
        console.log(xhr.responseText); // this line will save you tons of hours while debugging
        // do something here because of error
    }
    });
  });

  $(".delete").on("click", function(){
    $("#modal-konfirmasi").modal('show');

    $("#modal-konfirmasi").find("#btn-hapus").data("url", $(this).data("url"));
    $("#modal-konfirmasi").find("#btn-hapus").data("id", $(this).data("id"));
    $("#konfirmasi-body").text("Delete data " + $(this).data("name"));
  })
})