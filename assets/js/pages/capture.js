(function () {
    // The width and height of the captured photo. We will set the
    // width to the value defined here, but the height will be
    // calculated based on the aspect ratio of the input stream.

    var width = 320;    // We will scale the photo width to this
    var height = 0;     // This will be computed based on the input stream

    // |streaming| indicates whether or not we're currently streaming
    // video from the camera. Obviously, we start at false.

    var streaming = false;
    var stream = null;

    // The various HTML elements we need to configure or control. These
    // will be set by the startup() function.

    var video = null;
    var canvas = null;
    //var canvas2 = null;
    var photo = null;
    //var photo2 = null;
    var devoteeID = null;
    var startbutton = null;
    var uploadbutton = null;
    var camera = null;

    function startup() {
        video = document.getElementById('video');
        canvas = document.getElementById('canvas');
        //canvas2 = document.getElementById('canvas2');
        photo = document.getElementById('photo');
      //  photo2 = document.getElementById('photo2');
        devoteeID = document.getElementById('devotee_key_modal');
        startbutton = document.getElementById('click-pic');
        uploadbutton = document.getElementById('upload-pic');

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
                canvas.setAttribute('width', width);
                canvas.setAttribute('height', height);
                var context = canvas.getContext('2d');
                context.beginPath();
                context.fillStyle = "#AAA";
                 context.fillRect(0, 0, canvas.width, canvas.height);
                // var context = canvas.getContext('2d');
                // context.lineWidth = 117;
                // context.strokeStyle = '#c00';
                // context.fillStyle="#F9E5E5";
                // context.lineCap = 'round';
               // canvas2.setAttribute('width', width);
               // canvas2.setAttribute('height', height);
                streaming = true;
            }
        }, false);

        startbutton.addEventListener('click', function (ev) {
            takepicture();
            ev.preventDefault();
        }, false);
        uploadbutton.addEventListener('click', function (ev) {
            uploadImage();
            ev.preventDefault();
        }, false);

        clearphoto();
    }

    // Fill the photo with an indication that none has been
    // captured.

    function clearphoto() {
        var context = canvas.getContext('2d');
        context.fillStyle = "#AAA";
        context.fillRect(0, 0, canvas.width, canvas.height);

        var data = canvas.toDataURL('image/png');
        photo.setAttribute('src', data);
        //photo2.setAttribute('src', data);
    }

    // Capture a photo by fetching the current contents of the video
    // and drawing it into a canvas, then converting that to a PNG
    // format data URL. By drawing it on an offscreen canvas and then
    // drawing that to the screen, we can change its size and/or apply
    // other changes before drawing it.
    // context.drawImage(video, 0, 0);
    function takepicture() {
        var context = canvas.getContext('2d');
        if (width && height) {
            canvas.width = width;
            canvas.height = height;
            // drawImage(image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight)
            // context.drawImage(video, 220, 50, 210, 450, 40, 0, 320, 350);
            // context.drawImage(video, 220, 50, 210, 450, 0, 0, 320, 350);
            context.drawImage(video, 140, 0, 360, 770, 0, 0, 300, 400);
            // context.drawImage(video, 0, 0, width, height);
            var data = canvas.toDataURL('image/png');
            photo.setAttribute('src', data);
            uploadbutton.style.visibility = 'visible';
        } else {
            clearphoto();
        }
    }

    function draw(base64_image_data) {
        var context = canvas.getContext('2d');
        var image = new Image();
        image.onload = function() {
            context.drawImage(image, 0, 0, width, height);
        }
        canvas.width = width;
        canvas.height = height;
        image.src = base64_image_data;

        var data = canvas.toDataURL('image/png');
        photo.setAttribute('src', data);
        uploadbutton.style.visibility = 'visible';
    }

    function uploadImage() {
        var dataUrl = canvas.toDataURL();
        if (devoteeID.value != "") {
            $.ajax({
                url: '../api/managePhoto.php',
                method: 'POST',
                data: {image: dataUrl, api_type: 3, devotee_key: devoteeID.value}
            }).done(function (data) {
                var url = window.location.href;
                alert('Devotee Image updated!!');
                // window.location = url;
                // window.location.reload();
                let image_tag = document.getElementById('photo2');
                image_tag.innerHTML = `<img class="devoteeImage" id="devoteeImage" src="${dataUrl}" alt="devotee image"></img>`;
            });
        } else {
            $.ajax({
                url: '../api/managePhoto.php',
                method: 'POST',
                data: {image: dataUrl, api_type: 3}
            }).done(function (data) {
                data = $.parseJSON(data);
                var newId = data.message;
                var url = window.location.href;
                url = url + '?devotee_key=' + newId;
                alert('Image uploaded to new Devotee record!!');
                window.history.pushState('devotee', 'AddDevotee', url);
                let image_tag = document.getElementById('photo2');
                image_tag.innerHTML = `<img class="devoteeImage" id="devoteeImage" src="${dataUrl}" alt="devotee image"></img>`;
                // window.location = url;
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
    .getElementById("cameraFileInput")
    .addEventListener("change", function () {
        getBase64(this.files[0]).then(
        base64_image_data => {
            draw(base64_image_data)
        }
        );
    });
    
    $('#CameraModalLong').on('show.bs.modal', function () {
        startup();
    });
    startup();
    $('#CameraModalLong').on('hidden.bs.modal', function () {
        camera.stop();
    });
})();

// code for devotee image scaling.
$('#zoomInDevoteeImage').on('click', function (event) {
    let zoomOutDevoteeImage = document.getElementById("zoomOutDevoteeImage");
    let devotee_image_element = document.getElementById("devoteeImage");
    this.style.display = "none";
    zoomOutDevoteeImage.style.display = "block";
    devotee_image_element.style.transform = "scale(1.5)";
    devotee_image_element.style.transition = "transform 0.25s ease";
});

$('#zoomOutDevoteeImage').on('click', function (event) {
    this.style.display = 'none';
    let zoomInDevoteeImage = document.getElementById("zoomInDevoteeImage");
    let devotee_image_element = document.getElementById("devoteeImage");
    this.style.display = "none";
    zoomInDevoteeImage.style.display = "block";
    devotee_image_element.style.transform = "scale(1)";
    devotee_image_element.style.transition = "transform 0.25s ease";
});
