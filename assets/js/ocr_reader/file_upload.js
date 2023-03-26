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
            // console.log(base64String); // Step 4: Display or save the base64 string
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
        // Todo:
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

function get_event_id() {
    const ocr_panel = document.getElementById("ocr_panel");
    const event_id = ocr_panel.getAttribute('data-event-id');
    return event_id;
}

function render_matched_records(response_data) {
    const devotee_matched_records_table_body = document.getElementById("devotee_matched_records_table_body");
    let innerTableHTML = "";
    for(let i=0; i<response_data.length; i++) {
        let key = response_data[i]['devotee_key'];
        let name = response_data[i]['Devotee_Name'];
        let cell_phone_number = response_data[i]['devotee_cell_phone_number'];
        let station = response_data[i]['devotee_station'];
        let status = response_data[i]['devotee_status'];
        let devotee_photo = response_data[i]['Devotee_Photo'];
        innerTableHTML += `
            <tr>
                <th scope="row">${i+1}</td>
                <td>
                    <img class="search-result-image-scale" src="data:image/jpeg;base64,${devotee_photo}" height="50px" width="50px" alt="devotee_image"/>
                </td>
                <td>${key}</td>'
                <td>${name}</td>
                <td>${cell_phone_number}</td>
                <td>${station}</td>
                <td>${status}</td>
                <td class="action-btns">
                <a href="addDevoteeI.php?devotee_key=${key}" target="_blank">
                    <button class="btn btn-info pull-right">
                        <i class="material-icons">edit</i>
                    </button>
                </a>
                </td>
            </tr>
        `;
    }
    devotee_matched_records_table_body.innerHTML = innerTableHTML;
    get_bootstrap_modal().show();
}

function validateDate(isoDate) {
    if (isNaN(Date.parse(isoDate))) {
        return false;
    } else {
        if (isoDate != (new Date(isoDate)).toISOString().substr(0, 10)) {
            return false;
        }
    }
    return true;
}

function create_devotee_record(
    first_name,
    last_name,
    gender,
    dob,
    id_number,
    address
) {
    alert('Record created, click to edit the record in new tab for add addional data!');
    const request_type = 'upsertDevotee';
    const devotee_id_type = 'Aadhar';
    const devotee_type = 'T';
    const eventId = get_event_id();
    const devotee_first_name = first_name;
    const devotee_last_name = last_name;
    const devotee_gender = gender;
    const devotee_dob = dob;
    if (validateDate(devotee_dob)) {
        alert('Invalid DOB');
    }
    const devotee_id_number = id_number.replace(" ", "");
    const devotee_address_1 = address;

    const request_data = {
        request_type: request_type,
        devotee_id_type: devotee_id_type,
        devotee_type: devotee_type,
        eventId: eventId,
        devotee_first_name: devotee_first_name,
        devotee_last_name: devotee_last_name,
        devotee_gender: devotee_gender,
        devotee_dob: devotee_dob,
        devotee_id_number: devotee_id_number,
        devotee_address_1: devotee_address_1
    }
    $.ajax({
        url: '../api/upsertDevotee.php',
        method: 'POST',
        data: request_data
    }).done(function (response) {
        alert('Record created, click to edit the record in new tab for add addional data!');
    });
}

function kdms_ocr_submit_btn(create_anyway=false) {

    const form_fields = get_form_fields();
    const ocr_form_devotee_name = form_fields['ocr_form_devotee_name'].value;
    const ocr_form_devotee_gender = form_fields['ocr_form_devotee_gender'].value;
    const ocr_form_devotee_dob = form_fields['ocr_form_devotee_dob'].value;
    const ocr_form_devotee_id_number = form_fields['ocr_form_devotee_id_number'].value;
    const ocr_form_devotee_address = form_fields['ocr_form_devotee_address'].innerHTML;

    // split name in first name and last name
    if (ocr_form_devotee_name === null) {
        alert('No first and last name to proceed registration, \
please parse the ID or use the traditional way to create the record!');
    } else {
        let name_array = ocr_form_devotee_name.split(" ");
        const first_name = name_array[0].trim();
        let last_name = ""
        if (name_array.length > 1) {
            last_name = name_array[1].trim();
        }
        if (name_array.length > 2) {
            // for e.g., JUGAL DUMKA can be split as JUGAL as First name and DUMKA as last name
            // e.g.,2 - BACCHI SINGH RAWAT - can be split as Bacchi as first name and rawat as last name we can 
            // ignore the middle name here for search then use FIRST and LAST NAME 
            last_name = name_array[name_array.length-1].trim();
        }
        if (create_anyway === false) {
            const event_id = get_event_id();
            let query_string = `devotee_first_name=${first_name},devotee_last_name=${last_name}`
            let response_data = undefined;
            $.ajax({
                url: `../api/searchDevotee.php?mode=CUS&eventId=${event_id}&key=${query_string}`,
                method: 'POST',
                data: {}
            }).done(function (response) {
                response_data = JSON.parse(response);
                if (Object.prototype.hasOwnProperty.call(response_data, 'status')) {
                    if (response_data['status'] === false) {
                        console.log('No record found with the first name and last name.');
                        let consent_prompt = confirm('No record found with the data, Do you want to create record with the current data.');
                        if (consent_prompt) {
                            if (ocr_form_devotee_id_number !== "") {
                                create_devotee_record(
                                    first_name,
                                    last_name,
                                    ocr_form_devotee_gender,
                                    ocr_form_devotee_dob,
                                    ocr_form_devotee_id_number,
                                    ocr_form_devotee_address
                                );
                            } else {
                                // check if enough data is available if not then error it.
                                alert("Error: provided data is not enough for registration process!");
                            }
                        }
                    }
                } else {
                    // show the matched records in modal.
                    render_matched_records(response_data)
                }
            });
        } else {
            // create devotee function call
            create_devotee_record();
        }
    }
}

function kdms_ocr_clear_btn() {
    const form_fields = get_form_fields();
    form_fields['ocr_form_devotee_name'].value = "";
    form_fields['ocr_form_devotee_name'].parentNode.classList.remove('is-filled');
    form_fields['ocr_form_devotee_gender'].value = "";
    form_fields['ocr_form_devotee_gender'].parentNode.classList.remove('is-filled');
    form_fields['ocr_form_devotee_dob'].value = "";
    form_fields['ocr_form_devotee_dob'].parentNode.classList.remove('is-filled');
    form_fields['ocr_form_devotee_id_number'].value = "";
    form_fields['ocr_form_devotee_id_number'].parentNode.classList.remove('is-filled');
    form_fields['ocr_form_devotee_address'].innerHTML = "";
}

function update_form(response_data) {
    const name = response_data['data']['name'];
    const gender = response_data['data']['gender'];
    const date_of_birth = response_data['data']['date_of_birth'];
    const uid = response_data['data']['uid'];
    const address = response_data['data']['address'];

    const form_fields = get_form_fields();
    form_fields['ocr_form_devotee_name'].value = name;
    form_fields['ocr_form_devotee_name'].parentNode.classList.add('is-filled');
    let gender_alias = '-';
    if (gender === 'Male') {
        gender_alias = 'M'
    } else if (gender === 'Female') {
        gender_alias = 'F';
    }
    form_fields['ocr_form_devotee_gender'].value=gender_alias;
    form_fields['ocr_form_devotee_gender'].parentNode.classList.add('is-filled');
    form_fields['ocr_form_devotee_dob'].value = date_of_birth;
    form_fields['ocr_form_devotee_dob'].parentNode.classList.add('is-filled');
    form_fields['ocr_form_devotee_id_number'].value = uid;
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

function get_bootstrap_modal() {
    return $("#myModal").modal();
}

$(document).ready(function () {
    $('#loading').hide();
    get_data();
});
