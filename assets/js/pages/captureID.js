(function () {
    // The width and height of the captured photoID. We will set the
    // width to the value defined here, but the height will be
    // calculated based on the aspect ratio of the input stream.

    var width = 620;    // We will scale the photoID width to this
    var height = 0;     // This will be computed based on the input stream

    // |streaming| indicates whether or not we're currently streaming
    // video from the camera. Obviously, we start at false.

    var streaming = false;
    var stream = null;

    // The various HTML elements we need to configure or control. These
    // will be set by the startup() function.

    var video = null;
    var IDcanvas = null;
    //var canvas2 = null;
    var photoID = null;
    //var photo2 = null;
    var devoteeID = null;
    var uploadbutton = null;
    var camera = null;

    function startup() {
        video = document.getElementById('video');
        IDcanvas = document.getElementById('canvasID');
        //canvas2 = document.getElementById('canvas2');
        photoID = document.getElementById('photoID');
      //  photo2 = document.getElementById('photo2');
        devoteeID = document.getElementById('devotee_key_modal');
        uploadbutton = document.getElementById('upload-doc');

        navigator.getMedia = (navigator.getUserMedia ||
                navigator.webkitGetUserMedia ||
                navigator.mozGetUserMedia ||
                navigator.msGetUserMedia);

        navigator.mediaDevices.getUserMedia({video: true, audio: false})
                .then(function (stream) {
                    video.srcObject = stream;
                    video.play();
                    camera = stream.getTracks()[0];
                    //console.log(stream.getTracks());

                })
                .catch(function (err) {
                    console.log("An error occurred! " + err);
                });


        video.addEventListener('canplay', function (ev) {
            if (!streaming) {
                height = video.videoHeight / (video.videoWidth / width);

                if (isNaN(height)) {
                    height = width / (4 / 3);
                    //height = width;
                }
                video.setAttribute('width', width);
                video.setAttribute('height', height);
                IDcanvas.setAttribute('width', width);
                IDcanvas.setAttribute('height', height);
                streaming = true;
            }
        }, false);

        uploadbutton.addEventListener('click', function (ev) {
            uploadIDImage();
            ev.preventDefault();
        }, false);

        clearphoto();
    }

    // Fill the photoID with an indication that none has been
    // captured.

    function clearphoto() {
        var context = IDcanvas.getContext('2d');
        context.fillStyle = "#AAA";
        context.fillRect(0, 0, IDcanvas.width, IDcanvas.height);
        var data = IDcanvas.toDataURL('image/png');
        photoID.setAttribute('src', data);
        //photo2.setAttribute('src', data);
    }

    // Capture a photoID by fetching the current contents of the video
    // and drawing it into a canvas, then converting that to a PNG
    // format data URL. By drawing it on an offscreen IDcanvas and then
    // drawing that to the screen, we can change its size and/or apply
    // other changes before drawing it.
    function isCanvasBlank(IDcanvas) {
        return !IDcanvas.getContext('2d')
            .getImageData(0, 0, IDcanvas.width, IDcanvas.height).data
            .some(channel => channel !== 0);
    }

    function drawIDImage(base64_image_data) {
        var context = IDcanvas.getContext('2d');
        if (isCanvasBlank(IDcanvas)) {
            var image = new Image();
            image.src = base64_image_data;
            image.onload = function() {
                context.drawImage(image, 0, 0, width/2, height);
            }
            IDcanvas.width = width;
            IDcanvas.height = height;
        } else {
            var second_image = new Image();
            var first_image = new Image();
            second_image.src = base64_image_data;
            console.log(base64_image_data);
            first_image.src = photoID.getAttribute('src');
            second_image.onload = function() {
                context.drawImage(first_image, 0, 0, width/2, height);
                context.drawImage(second_image, width/2, 0, width/2, height);
            }
            IDcanvas.width = width;
            IDcanvas.height = height;
        }
        photoID.setAttribute('src', base64_image_data);
    }

    function uploadIDImage() {
        var dataUrl = IDcanvas.toDataURL();
        if (devoteeID.value != "") {
            $.ajax({
                url: '../api/managePhoto.php',
                method: 'POST',
                data: {image: dataUrl, api_type: 4, devotee_key: devoteeID.value}
            }).done(function (resp) {
                var url = window.location.href;
                alert('Devotee Image updated!!');
                $('#CameraModalLong').modal('hide');
                window.location = url;
                window.location.reload();
            });
        } else {
            $.ajax({
                url: '../api/managePhotoID.php',
                method: 'POST',
                data: {image: dataUrl, api_type: 4}
            }).done(function (data) {
                data = $.parseJSON(data);
                var newId = data.message;
                var url = window.location.href;
                url = url + '?devotee_key=' + newId;
                alert('Image uploaded to new Devotee record!!');
                $('#CameraModalLong').modal('hide');
                window.location = url;
            });
        }
    }

    function getBase64(file) {
        return new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onload = () => resolve(reader.result);
          reader.onerror = error => reject(error);
        });
    }

    document
    .getElementById("cameraIDFileInput")
    .addEventListener("change", function () {
        getBase64(this.files[0]).then(
        base64_image_data => {
            drawIDImage(base64_image_data)
        }
        );
    });
    
    // $('#CameraModalLong').on('show.bs.modal', function () {
    //     startup();
    // });
    startup();
    // $('#CameraModalLong').on('hidden.bs.modal', function () {
    //     camera.stop();
    // });
})();

// code for devotee image scaling.
// $('#zoomInDevoteeImage').on('click', function (event) {
//     let zoomOutDevoteeImage = document.getElementById("zoomOutDevoteeImage");
//     let devotee_image_element = document.getElementById("devoteeImage");
//     this.style.display = "none";
//     zoomOutDevoteeImage.style.display = "block";
//     devotee_image_element.style.transform = "scale(1.5)";
//     devotee_image_element.style.transition = "transform 0.25s ease";
// });

// $('#zoomOutDevoteeImage').on('click', function (event) {
//     this.style.display = 'none';
//     let zoomInDevoteeImage = document.getElementById("zoomInDevoteeImage");
//     let devotee_image_element = document.getElementById("devoteeImage");
//     this.style.display = "none";
//     zoomInDevoteeImage.style.display = "block";
//     devotee_image_element.style.transform = "scale(1)";
//     devotee_image_element.style.transition = "transform 0.25s ease";
// });
