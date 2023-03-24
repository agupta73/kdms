function dragNdrop(event) {
    let number_of_files = event.target.files.length;
    for (let each_file_index = 0; each_file_index < number_of_files; each_file_index++) {
        // let fileName = URL.createObjectURL(event.target.files[each_file_index]);
        // let preview = document.getElementById("ocr_image_preview");
        // let previewImg = document.createElement("img");
        // previewImg.setAttribute("src", fileName);
        // previewImg.setAttribute("class", "ocr-image-upload-parser-preview");
        // preview.appendChild(previewImg);
        let file = event.target.files[each_file_index];
        let file_name = event.target.files[each_file_index].name;
        let reader_file = new FileReader();
        reader_file.readAsDataURL(file);
        reader_file.onload = () => {
            // const base64String = reader_file.result.replace('data:', '').replace(/^.+,/, '');
            const base64String = reader_file.result;
            console.log(base64String); // Step 4: Display or save the base64 string
            // call to the image upload API
            // Todo: error response changes.
            $.ajax({
                url: '../api/manage_kdms_ocr_image_bucket.php',
                method: 'POST',
                data: {image: base64String, image_name: file_name}
            }).done(function (response) {
                alert('Selected Images uploaded sucessfully!');
                let url = window.location.href;
                window.location = url;
                window.location.reload();
            });
        };
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

function remove_image(image_name) {
    let deletePrompt = confirm("Are you sure you want to delete "+image_name+" ?");
    if (deletePrompt) {
        $.ajax({
            url: '../api/manage_kdms_ocr_image_bucket.php',
            method: 'POST',
            data: {image_name: image_name, api_type: 2}
        }).done(function (response) {
            let url = window.location.href;
            window.location = url;
            window.location.reload();
        });
    }
}

function remove_all_image_from_temp_bucket(image_name_list) {
    let deletePrompt = confirm("Are you sure you want to delete all images?");
    if (deletePrompt) {
        // $.ajax({
        //     url: '../api/manage_kdms_ocr_image_bucket.php',
        //     method: 'POST',
        //     data: {image_name: image_name, api_type: 2}
        // }).done(function (response) {
        //     let url = window.location.href;
        //     window.location = url;
        //     window.location.reload();
        // });
    }
}

function get_form_fields() {
    const ocr_form_devotee_name = document.getElementById("ocr_form_devotee_name");
    const ocr_form_devotee_gender = document.getElementById("ocr_form_devotee_gender");
    const ocr_form_devotee_dob = document.getElementById("ocr_form_devotee_dob");
    const ocr_form_devotee_id_number = document.getElementById("ocr_form_devotee_id_number");
    const ocr_form_devotee_address = document.getElementById("ocr_form_devotee_address");

    return {
        'ocr_form_devotee_name': ocr_form_devotee_name,
        'ocr_form_devotee_gender': ocr_form_devotee_gender,
        'ocr_form_devotee_dob': ocr_form_devotee_dob,
        'ocr_form_devotee_id_number': ocr_form_devotee_id_number,
        'ocr_form_devotee_address': ocr_form_devotee_address
    }
}

function kdms_ocr_submit_btn() {
    const form_fields = get_form_fields();
    const ocr_form_devotee_name = form_fields['ocr_form_devotee_name'].getAttribute('value');
    const ocr_form_devotee_gender = form_fields['ocr_form_devotee_gender'].getAttribute('value');
    const ocr_form_devotee_dob = form_fields['ocr_form_devotee_dob'].getAttribute('value');
    const ocr_form_devotee_id_number = form_fields['ocr_form_devotee_id_number'].getAttribute('value');
    const ocr_form_devotee_address = form_fields['ocr_form_devotee_address'].innerHTML;
    // todo:
    // call form sumit api 
    // if record found with the name 
    // then take it to search page 
    // else
    // create a new record with the above details and save the image as well and create a link which facilates user to redirect to the 
    // registration edit form where he can add the remaining details of the devotee
}

function kdms_ocr_clear_btn() {
    const form_fields = get_form_fields();
    form_fields['ocr_form_devotee_name'].setAttribute('value', '');
    form_fields['ocr_form_devotee_name'].parentNode.classList.remove('is-filled');
    form_fields['ocr_form_devotee_gender'].setAttribute('value', '');
    form_fields['ocr_form_devotee_gender'].parentNode.classList.remove('is-filled');
    form_fields['ocr_form_devotee_dob'].setAttribute('value', '');
    form_fields['ocr_form_devotee_dob'].parentNode.classList.remove('is-filled');
    form_fields['ocr_form_devotee_id_number'].setAttribute('value', '');
    form_fields['ocr_form_devotee_id_number'].parentNode.classList.remove('is-filled');
    form_fields['ocr_form_devotee_address'].innerHTML = "";
}

function update_form(response_data) {
    const name = response_data['data']['name'];
    const gender = response_data['data']['"Male"'];
    const date_of_birth = response_data['data']['date_of_birth'];
    const uid = response_data['data']['uid'];
    const address = response_data['data']['address'];

    const form_fields = get_form_fields();
    form_fields['ocr_form_devotee_name'].setAttribute('value', name);
    form_fields['ocr_form_devotee_name'].parentNode.classList.add('is-filled');
    let gender_alias = '-';
    if (gender === 'Male') {
        gender_alias = 'M'
    } else if (gender === 'Female') {
        gender_alias = 'F';
    }
    form_fields['ocr_form_devotee_gender'].value='M';
    form_fields['ocr_form_devotee_gender'].parentNode.classList.add('is-filled');
    form_fields['ocr_form_devotee_dob'].setAttribute('value', date_of_birth);
    form_fields['ocr_form_devotee_dob'].parentNode.classList.add('is-filled');
    form_fields['ocr_form_devotee_id_number'].setAttribute('value', uid);
    form_fields['ocr_form_devotee_id_number'].parentNode.classList.add('is-filled');
    form_fields['ocr_form_devotee_address'].innerHTML = address;
    form_fields['ocr_form_devotee_address'].parentNode.classList.add('is-filled');
}

function set_image(base64_image_data) {
    let preview = document.getElementById('ocr_selected_image_preview');
    let previewImg = document.createElement("img");
    previewImg.setAttribute("src", `data:image/png;base64,${base64_image_data}`);
    previewImg.setAttribute("class", "ocr-selected-parser-preview-image");
    previewImg.setAttribute("id", "active-preview-image");
    preview.innerHTML = "";
    preview.appendChild(previewImg);
}

function parse_image(ele) {
    let image_data = ele.getAttribute('data-image');
    const kdms_ocr_url = "http://localhost:5001/api/v1/kdms-ocr/";
    $('#loading').show();
    set_image(image_data);
    $.ajax({
        url: kdms_ocr_url,
        method: 'POST',
        data: {image_data: image_data, image_data_type: 'BASE64', card_type: 'AADHAR'}
    }).done(function (response) {
        update_form(response['data']);
        $('#loading').hide();
    });
}

const get_data = () => {
    const tempbucket_table_body = document.getElementById("tempbucket_table_body");
    $.ajax({
        url: '../api/manage_kdms_ocr_image_bucket.php',
        method: 'POST',
        data: { api_type: 1}
    }).done(function (response) {
        response = JSON.parse(response);
        let innerTableHTML = "";
        for(let i=0; i<response.data.length; i++) {
            let image_name = response.data[i]['image_name'];
            let status = response.data[i]['status'];
            let image_data = response.data[i]['image'];
            let image_uploaded_at = response.data[i]['image_uploaded_at'];
            innerTableHTML += `
                <tr>
                    <th scope="row">${i+1}</td>
                    <td>${image_name}</td>'
                    <td>
                        <i class="material-icons">
                        ${status?'close':'check'}
                        </i>
                    </td>
                    <td>${image_uploaded_at}</td>
                    <td class="action-btns">
                        <button class="btn btn-success pull-right"
                            data-image="${image_data}"
                            onClick="parse_image(this)"
                        >
                            <i class="material-icons">upload_file</i>
                        </button>
                        <button class="btn btn-danger pull-right" onClick="remove_image('${image_name}')"">
                            <i class="material-icons">delete</i>
                        </button>
                    </td>
                </tr>
            `;
        }
        tempbucket_table_body.innerHTML = innerTableHTML;
    });
}

$(document).ready(function () {
    $('#loading').hide();
    get_data();
});
