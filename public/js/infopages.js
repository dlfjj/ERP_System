$(document).ready(function() {
    $(function(){
        $('textarea.froala-editor').froalaEditor({
			key: '8sxslestE4gjk==',
			imageUploadParam: 'image_param',
			imageUploadURL: '/webshop_settings/froala-upload-image',
			imageUploadParams: {id: 'my_editor'},
			imageUploadMethod: 'POST',
			imageMaxSize: 5 * 1024 * 1024,
			imageAllowedTypes: ['jpeg', 'jpg', 'png']
		})
      .on('froalaEditor.image.beforeUpload', function (e, editor, images) {
        // Return false if you want to stop the image upload.
      })
      .on('froalaEditor.image.uploaded', function (e, editor, response) {
        // Image was uploaded to the server.
		console.log("Uploaded");
      })
      .on('froalaEditor.image.inserted', function (e, editor, $img, response) {
        // Image was inserted in the editor.
		console.log("Inserted");
      })
      .on('froalaEditor.image.replaced', function (e, editor, $img, response) {
        // Image was replaced in the editor.
		console.log("Replaced");
      })
      .on('froalaEditor.image.error', function (e, editor, error, response) {
        // Bad link.
        if (error.code == 1) {
			console.log("Error 1");
		}

        // No link in upload response.
        else if (error.code == 2) {
			console.log("Error 2");
		}

        // Error during image upload.
        else if (error.code == 3) {
			console.log("Error 3");
		}

        // Parsing response failed.
        else if (error.code == 4) {
			console.log("Error 4");
		}

        // Image too text-large.
        else if (error.code == 5) {
			console.log("Error 5");
		}

        // Invalid image type.
        else if (error.code == 6) {
			console.log("Error 6");
		}

        // Image can be uploaded only to same domain in IE 8 and IE 9.
        else if (error.code == 7) {
			console.log("Error 7");
		}

        // Response contains the original server response to the request if available.
      })


      .on('froalaEditor.image.removed', function (e, editor, $img) {
        $.ajax({
          // Request method.
          method: "POST",

          // Request URL.
          url: "/infopages/froala-image-delete",

          // Request params.
          data: {
            src: $img.attr('src')
          }
        })
        .done (function (data) {
          console.log ('image was deleted');
        })
        .fail (function () {
          console.log ('image delete problem');
        })
      })
	;


    });


});
