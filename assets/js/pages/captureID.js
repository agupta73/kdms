(function () {
    function getBase64(file) {
        return new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onload = () => resolve(reader.result);
          reader.onerror = error => reject(error);
        });
    }
    function resolveReservedKey() {
        const modal = document.getElementById('devotee_key_modal');
        const main = document.getElementById('devotee_key');
        let key = (modal && modal.value) ? modal.value.trim() : '';
        if (key === '' && main && main.value) {
            key = main.value.trim();
            if (modal) {
                modal.value = key;
            }
        }
        return key;
    }

    // upload devotee id image
    function uploadIDImage(base64_image_data) {
        const devoteeID = resolveReservedKey();
        if (devoteeID === '') {
            alert('Devotee ID is not reserved yet. Refresh the Add Devotee page and try again.');
            return;
        }
        $.ajax({
            url: '../api/managePhoto.php',
            method: 'POST',
            data: {image: base64_image_data, api_type: 4, devotee_key: devoteeID}
        }).done(function (resp) {
            var parsed = typeof resp === 'string' ? JSON.parse(resp) : resp;
            if (!parsed || parsed.status !== true) {
                alert(parsed && parsed.message ? parsed.message : 'ID image upload failed.');
                return;
            }
            alert('ID image saved for key ' + devoteeID);
            let photo_id_preview_div = document.getElementById('photo-id-preview_div');
            const preview_image = `<img class="photo-id-preview" src="${base64_image_data}" alt="devotee ID" height="400px" width="200px"></img>`;
            photo_id_preview_div.innerHTML = preview_image;
        }).fail(function () {
            alert('ID image upload request failed.');
        });
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
        const devoteeID = resolveReservedKey();
        if (devoteeID === '') {
            alert('Devotee ID is not reserved yet. Refresh the Add Devotee page and try again.');
            return;
        }
        $.ajax({
            url: '../api/managePhoto.php',
            method: 'POST',
            data: {image: base64_image_data, api_type: 3, devotee_key: devoteeID}
        }).done(function (resp) {
            var parsed = typeof resp === 'string' ? JSON.parse(resp) : resp;
            if (!parsed || parsed.status !== true) {
                alert(parsed && parsed.message ? parsed.message : 'Photo upload failed.');
                return;
            }
            alert('Devotee image saved for key ' + devoteeID);
            let photo_mobile_preview_div = document.getElementById('photo-mobile-preview_div');
            const preview_image = `<img class="devoteeImage" id="devoteeImage" src="${base64_image_data}" alt="devotee image"></img>`;
            photo_mobile_preview_div.innerHTML = preview_image;
        }).fail(function () {
            alert('Photo upload request failed.');
        });
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
