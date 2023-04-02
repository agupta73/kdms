function dragNdrop(event) {
    let number_of_files = event.target.files.length;
    for (let each_file_index = 0; each_file_index < number_of_files; each_file_index++) {
        let file = event.target.files[each_file_index];
        let file_name = event.target.files[each_file_index].name;
        let reader_file = new FileReader();
        reader_file.readAsDataURL(file);
        reader_file.onload = () => {
            const base64String = reader_file.result;
            // call to the image upload API
            $.ajax({
                url: '../api/manage_kdms_ocr_image_bucket.php',
                method: 'POST',
                data: {image: base64String, image_name: file_name}
            }).done(function (response) {
                $('#loading').show();
                if (each_file_index === (number_of_files-1)) {
                    let url = window.location.href;
                    window.location = url;
                    window.location.reload();
                }
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

function get_address_object(address_lines) {
    address_array = address_lines.split('\n');
    address_line_1 = ""
    address_line_2 = ""
    station = ""
    state = ""
    pin = ""
    pin_match = address_lines.match(/\d{6}/i);
    try {
        if (pin_match !== null) {
            pin = pin_match[0]
        }
        if (address_array.length == 4) {
            console.log('length4');
            address_line_1 = address_array[0]
            address_line_2 = address_array[1]
            station = address_array[2].split(',')[1].trim()
            state = address_array[3].split('-')[0].trim()
        } else if (address_array.length == 3) {
            console.log('length3');
            address_line_1 = address_array[0]
            address_line_2 = address_array[1]
            station = address_array[1].split(',')[1].trim()
            state = address_array[2].split('-')[0]
        } else if (address_array.length == 2) {
            address_line_1 = address_array[0]
            address_line_2 = address_array[1]
            station = address_array[0].split(',')[1].trim()
            state = address_array[1].split('-')[0]
        } else {
            address_line_1 = address_array[0].split(',')[0]
            station = address_array[0].split(',')[1].trim()
            state = address_array[0].split(',')[1].trim()
        }
    } catch (error) {
        
    }
    return {
        "address_line_1": address_line_1,
        "address_line_2": address_line_2,
        "station": station,
        "state": state,
        "pin": pin
    }
}

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

function update_image_status() {
    const image_name = document.getElementById("active-preview-image").getAttribute('data-image-name');
    $.ajax({
        url: '../api/manage_kdms_ocr_image_bucket.php',
        method: 'POST',
        data: {image_name: image_name, api_type: 4}
    }).done(function () {
        console.log('image process flag updated.')
    });
}

function remove_all_image_from_temp_bucket(image_name_list) {
    let deletePrompt = confirm("Are you sure you want to delete all the unused images?");
    if (deletePrompt) {
        $.ajax({
            url: '../api/manage_kdms_ocr_image_bucket.php',
            method: 'POST',
            data: {api_type: 3}
        }).done(function (response) {
            let url = window.location.href;
            window.location = url;
            window.location.reload();
        });
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

        let devotee_image_tag = '<img src="../assets/img/faces/devotee.ico" alt="Devotee Image" height="70px" width="75px"></img>';

        if (devotee_photo != "") {
            devotee_image_tag = `<img class="search-result-image-scale" src="data:image/jpeg;base64,${devotee_photo}" height="50px" width="50px" alt="devotee_image"/>`;
        }
        innerTableHTML += `
            <tr>
                <th scope="row">${i+1}</td>
                <td>
                   ${devotee_image_tag}
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
                        Edit
                    </button>
                </a>

                <button class="btn btn-success pull-right" onclick="merge_record('${key}')">
                    <i class="material-icons">call_merge</i>
                    Merge
                </button>
                </td>
            </tr>
        `;
    }
    devotee_matched_records_table_body.innerHTML = innerTableHTML;
    show_modal  ();
}

function upsert_devotee_record(
    first_name,
    last_name,
    gender,
    dob,
    id_number,
    address,
    is_update=false,
    devotee_key=undefined,
) {
    const request_type = 'upsertDevotee';
    const devotee_id_type = 'Aadhaar';
    const devotee_type = 'T';
    const eventId = get_event_id();
    const devotee_first_name = first_name;
    const devotee_last_name = last_name;
    const devotee_gender = gender;
    const devotee_dob = dob;
    const devotee_id_number = id_number;
    const devotee_address_obj = get_address_object(address);
    const devotee_address_1 = devotee_address_obj['address_line_1'];
    const devotee_address_2 = devotee_address_obj['address_line_2'];
    const devotee_zip = devotee_address_obj['pin'];
    const devotee_state= devotee_address_obj['state'];
    const devotee_station= devotee_address_obj['station'];

    const request_data = {
        devotee_id_type: devotee_id_type,
        requestType: request_type,
        devotee_type: devotee_type,
        eventId: eventId,
        devotee_first_name: devotee_first_name,
        devotee_last_name: devotee_last_name,
        devotee_gender: devotee_gender,
        devotee_dob: devotee_dob,
        devotee_id_number: devotee_id_number,
        devotee_address_1: devotee_address_1,
        devotee_address_2: devotee_address_2,
        devotee_zip: devotee_zip, 
        devotee_state: devotee_state, 
        devotee_station: devotee_station,
    }
    if (is_update){
        request_data['devotee_key']=devotee_key;
    }
    $.ajax({
        url: '../api/upsertDevotee.php',
        method: 'POST',
        data: request_data
    }).done(function (response) {
        const response_data = JSON.parse(response);
        update_scan_image(response_data.info);
        update_image_status();
        if (is_update) {
            alert('Record updated successfully!');
            let redirect_url = `addDevoteeI.php?devotee_key=${response_data.info}`;
            window.open(redirect_url,'_blank');
        } else {
            alert('Devotee Record created successfully!');
            let redirect_url = `addDevoteeI.php?devotee_key=${response_data.info}`;
            window.open(redirect_url, '_blank');
        }
        hide_modal();
    });
}

function update_scan_image(devotee_key) {
    let image_data = document.getElementById("active-preview-image").getAttribute('src');
    $.ajax({
        url: '../api/managePhoto.php',
        method: 'POST',
        data: {image: image_data, api_type: 4, devotee_key: devotee_key}
    }).done(function (response) {
        console.log(response);
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
    if (ocr_form_devotee_name === null || ocr_form_devotee_name === "") {
        alert('No first/ last name to proceed registration, \
please parse the ID or use the traditional way to create the record!');
    } else {
        let name_array = ocr_form_devotee_name.split(" ");
        const first_name = name_array[0].trim();
        let last_name = ""
        let last_name_with_middle_name = ""
        if (name_array.length > 1) {
            last_name = name_array[1].trim();
            last_name_with_middle_name = last_name
        }
        if (name_array.length > 2) {
            // for e.g., JUGAL DUMKA can be split as JUGAL as First name and DUMKA as last name
            // e.g.,2 - BACCHI SINGH RAWAT - can be split as Bacchi as first name and rawat as last name we can 
            // ignore the middle name here for search then use FIRST and LAST NAME 
            last_name = name_array[name_array.length-1].trim();
            last_name_with_middle_name = [name_array.shift(), name_array.join(' ')][1];
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
                                upsert_devotee_record(
                                    first_name,
                                    last_name_with_middle_name,
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
            upsert_devotee_record(
                first_name,
                last_name_with_middle_name,
                ocr_form_devotee_gender,
                ocr_form_devotee_dob,
                ocr_form_devotee_id_number,
                ocr_form_devotee_address
            );
        }
    }
}

function merge_record(devotee_key) {
    const form_fields = get_form_fields();
    const ocr_form_devotee_name = form_fields['ocr_form_devotee_name'].value;
    const ocr_form_devotee_gender = form_fields['ocr_form_devotee_gender'].value;
    const ocr_form_devotee_dob = form_fields['ocr_form_devotee_dob'].value;
    const ocr_form_devotee_id_number = form_fields['ocr_form_devotee_id_number'].value;
    const ocr_form_devotee_address = form_fields['ocr_form_devotee_address'].innerHTML;
    
    let name_array = ocr_form_devotee_name.split(" ");
    const first_name = name_array[0].trim();
    let last_name = ""
    let last_name_with_middle_name = ""
    if (name_array.length > 1) {
        last_name = name_array[1].trim();
        last_name_with_middle_name = last_name
    }
    if (name_array.length > 2) {
        // for e.g., JUGAL DUMKA can be split as JUGAL as First name and DUMKA as last name
        // e.g.,2 - BACCHI SINGH RAWAT - can be split as Bacchi as first name and rawat as last name we can 
        // ignore the middle name here for search then use FIRST and LAST NAME 
        last_name = name_array[name_array.length-1].trim();
        last_name_with_middle_name = [name_array.shift(), name_array.join(' ')][1];
    }
    upsert_devotee_record(
        first_name,
        last_name_with_middle_name,
        ocr_form_devotee_gender,
        ocr_form_devotee_dob,
        ocr_form_devotee_id_number,
        ocr_form_devotee_address,
        is_update=true,
        devotee_key=devotee_key
    );

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
    let preview = document.getElementById('ocr_selected_image_preview');
    preview.innerHTML = "";
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

function set_image(base64_image_data, image_name) {
    let preview = document.getElementById('ocr_selected_image_preview');
    let previewImg = document.createElement("img");
    previewImg.setAttribute("src", `data:image/png;base64,${base64_image_data}`);
    previewImg.setAttribute("class", "ocr-selected-parser-preview-image");
    previewImg.setAttribute("id", "active-preview-image");
    previewImg.setAttribute("data-image-name", image_name);
    preview.innerHTML = "";
    preview.appendChild(previewImg);
}

function parse_image(ele) {
    let image_data = ele.getAttribute('data-image');
    let image_name = ele.getAttribute('data-image-name');
    const kdms_ocr_url = "http://localhost:5001/api/v1/kdms-ocr/";
    $('#loading').show();
    set_image(image_data, image_name);
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
                        ${status==0?'close':'check'}
                        </i>
                    </td>
                    <td>${image_uploaded_at}</td>
                    <td class="action-btns">
                        <button class="btn btn-success pull-right"
                            data-image-name="${image_name}"
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

function show_modal() {
    return $("#myModal").modal("show");
}
function hide_modal() {
    return $("#myModal").modal("hide");
}

$(document).ready(function () {
    $('#loading').hide();
    get_data();
});
