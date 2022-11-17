var ASSET_URL = $("#asset_url").val();
$('document').ready(function () {
    $("#file-input").on('change', function (e) {
        $("#preview").html("");
        var file = e.target.files,
        imagefiles = $(this)[0].files;
        var i = 0;
        $.each(imagefiles, function(index, value){
            var f = file[i];
            var fileReader = new FileReader();
            fileReader.onload = (function(e) {
                if (f['type'].split('/')[0] == "image") {
                    $("#preview").append('<div class="pip col-sm-3 col-4 boxDiv" align="center" style="margin-bottom: 20px;">' +
                    '<img style="width: 120px; height: 100px;" src="' + e.target.result + '" class="prescriptions">'+
                    '<p style="word-break: break-all;">' + value.name + '</p>'+
                    '<input type="hidden" name="attachmentName" value="' + value.name + '">' +
                    '</div>');
                } else {
                    $("#preview").append('<div class="pip col-sm-3 col-4 boxDiv" align="center" style="margin-bottom: 20px;">' +
                    '<img style="width: 120px; height: 100px;" src="'+ASSET_URL+'/assets/img/icons/unicons/file.png" class="prescriptions">'+
                    '<p style="word-break: break-all;">' + value.name + '</p>'+
                    '<input type="hidden" name="attachmentName" value="' + value.name + '">' +
                    '</div>');
                }
            });
            fileReader.readAsDataURL(f);
            i++;

        });
    });
});
