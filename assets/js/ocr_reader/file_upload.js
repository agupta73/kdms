function dragNdrop(event) {
    let number_of_files = event.target.files.length;
    for (let each_file_index = 0; each_file_index < number_of_files; each_file_index++) {
        let fileName = URL.createObjectURL(event.target.files[each_file_index]);
        let preview = document.getElementById("ocr_image_preview");
        let previewImg = document.createElement("img");
        previewImg.setAttribute("src", fileName);
        previewImg.setAttribute("class", "ocr-image-upload-parser-preview");
        // preview.innerHTML = "";
        preview.appendChild(previewImg);
    }
}
function drag() {
    document.getElementById('uploadFile').parentNode.className = 'draging dragBox';
}
function drop() {
    document.getElementById('uploadFile').parentNode.className = 'dragBox';
}
// (function($) {
//     $.fn.hasScrollBar = function() {
//         return this.get(0).scrollHeight > this.height();
//     }
// })(jQuery);

// if ($('#ocr_image_preview').hasScrollBar()) {
//     console.log('arrow');
// }
