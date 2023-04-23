(function () {
    function getBase64(file) {
        return new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onload = () => resolve(reader.result);
          reader.onerror = error => reject(error);
        });
    }
    // upload devotee id image
    function uploadIDImage(base64_image_data) {
        const devoteeID = document.getElementById('devotee_key_modal').value;
        if (devoteeID != "") {
            $.ajax({
                url: '../api/managePhoto.php',
                method: 'POST',
                data: {image: base64_image_data, api_type: 4, devotee_key: devoteeID}
            }).done(function (resp) {
                alert('Devotee Image updated!!');
                let photo_id_preview_div = document.getElementById('photo-id-preview_div');
                const preview_image = `<img class="photo-id-preview" src="${base64_image_data}" alt="devotee ID" height="400px" width="200px"></img>`;
                photo_id_preview_div.innerHTML = preview_image;
            });
        } else {
            alert('please add the data related to devotee first!');
        }
    }

    document
    .getElementById("cameraIDFileInput")
    .addEventListener("change", function () {
        getBase64(this.files[0]).then(
        base64_image_data => {
            uploadIDImage(base64_image_data);
        }
    );
    });
    // upload devotee photo.
    function uploadDevoteeImage(base64_image_data) {
        const devoteeID = document.getElementById('devotee_key_modal').value;
        if (devoteeID != "") {
            $.ajax({
                url: '../api/managePhoto.php',
                method: 'POST',
                data: {image: base64_image_data, api_type: 3, devotee_key: devoteeID}
            }).done(function (resp) {
                alert('Devotee Image updated!!');
                let photo_mobile_preview_div = document.getElementById('photo-mobile-preview_div');
                const preview_image = `<img class="devoteeImage" id="devoteeImage" src="${base64_image_data}" alt="devotee image"></img>`;
                photo_mobile_preview_div.innerHTML = preview_image;
            });
        } else {
            alert('please add the data related to devotee first!');
        }
    }

    document
    .getElementById("cameraMobilePhotoFileInput")
    .addEventListener("change", function () {
        getBase64(this.files[0]).then(
        base64_image_data => {
            uploadDevoteeImage(base64_image_data);
        }
    );
    });
})();
