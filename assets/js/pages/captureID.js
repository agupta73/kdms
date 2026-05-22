(function () {
    'use strict';

    const HIGH = 0.7;
    const MED = 0.4;
    const MAX_ID_DIM = 1600;
    const MAX_OCR_BYTES = 5 * 1024 * 1024;
    const OCR_TIMEOUT_MS = 90000;
    const UPLOAD_TIMEOUT_MS = 120000;

    const FIELD_MAP = {
        Devotee_First_Name: 'devotee_first_name',
        Devotee_Last_Name: 'devotee_last_name',
        Devotee_ID_Number: 'devotee_id_number',
        Devotee_DOB: 'devotee_dob',
        Devotee_Gender: 'devotee_gender',
        Devotee_Email: 'devotee_email',
        Devotee_Address_1: 'devotee_address_1',
        Devotee_Address_2: 'devotee_address_2',
        Devotee_Station: 'devotee_station',
        Devotee_State: 'devotee_state',
        Devotee_Zip: 'devotee_zip'
    };

    const TITLE_CASE_IDS = [
        'devotee_first_name',
        'devotee_last_name',
        'devotee_address_1',
        'devotee_address_2',
        'devotee_station',
        'devotee_state'
    ];

    const OCR_FIELDS = Object.keys(FIELD_MAP);

    function getManagePhotoUrl() {
        return (typeof window.kdmsManagePhotoUrl === 'string' && window.kdmsManagePhotoUrl !== '')
            ? window.kdmsManagePhotoUrl
            : '../api/managePhoto.php';
    }

    function setIdUploadStatus(message, isError) {
        var el = document.getElementById('id-upload-status');
        if (!el) {
            return;
        }
        el.className = (isError ? 'text-danger' : 'text-info') + ' small mt-3 text-center px-2';
        el.textContent = message || 'Processing…';
    }

    function setIdUploadBusy(busy, statusText) {
        var input = document.getElementById('cameraIDFileInput');
        var spinner = document.getElementById('id-upload-spinner');
        if (input) {
            input.disabled = !!busy;
        }
        if (spinner) {
            spinner.style.display = busy ? 'flex' : 'none';
        }
        if (busy && statusText) {
            setIdUploadStatus(statusText, false);
        }
    }

    function ocrResponseHasFields(data) {
        if (!data || typeof data !== 'object') {
            return false;
        }
        return OCR_FIELDS.some(function (name) {
            var field = data[name];
            if (!field || field.value == null || String(field.value).trim() === '') {
                return false;
            }
            return (Number(field.confidence) || 0) >= MED;
        });
    }

    function getBase64(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () { resolve(reader.result); };
            reader.onerror = function (error) { reject(error); };
        });
    }

    /**
     * Resize large camera-roll photos before upload/OCR (keeps under PHP post limits).
     * @returns {Promise<{blob: Blob, previewUrl: string}>}
     */
    function compressImageFile(file, maxDim, quality) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = new Image();
                img.onload = function () {
                    var w = img.width;
                    var h = img.height;
                    var max = maxDim || MAX_ID_DIM;
                    if (w > max || h > max) {
                        if (w >= h) {
                            h = Math.round(h * max / w);
                            w = max;
                        } else {
                            w = Math.round(w * max / h);
                            h = max;
                        }
                    }
                    var canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                    var q = quality || 0.85;
                    canvas.toBlob(function (blob) {
                        if (!blob) {
                            reject(new Error('Could not compress image'));
                            return;
                        }
                        resolve({
                            blob: blob,
                            previewUrl: canvas.toDataURL('image/jpeg', q)
                        });
                    }, 'image/jpeg', q);
                };
                img.onerror = function () { reject(new Error('Invalid image file')); };
                img.src = e.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    function resolveReservedKey() {
        var modal = document.getElementById('devotee_key_modal');
        var main = document.getElementById('devotee_key');
        var key = (modal && modal.value) ? modal.value.trim() : '';
        if (key === '' && main && main.value) {
            key = main.value.trim();
            if (modal) {
                modal.value = key;
            }
        }
        return key;
    }

    function toTitleCase(str) {
        if (!str) {
            return '';
        }
        return str.toLowerCase().replace(/\b([a-zà-ÿ])/g, function (m) { return m.toUpperCase(); });
    }

    function normalizeDobForStaff(val) {
        val = (val || '').trim();
        if (!val) {
            return '';
        }
        if (/^\d{4}-\d{2}-\d{2}$/.test(val)) {
            var p = val.split('-');
            return p[2] + '-' + p[1] + '-' + p[0];
        }
        var m = val.match(/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{4})$/);
        if (m) {
            return m[1].padStart(2, '0') + '-' + m[2].padStart(2, '0') + '-' + m[3];
        }
        return val;
    }

    function setFieldConfidence(input, confidence) {
        if (!input || !input.parentElement) {
            return;
        }
        input.style.borderLeft = '';
        var hint = input.parentElement.querySelector('.ocr-verify-hint');
        if (hint) {
            hint.remove();
        }
        if (confidence >= HIGH) {
            input.style.borderLeft = '4px solid #4caf50';
        } else if (confidence >= MED) {
            input.style.borderLeft = '4px solid #ff9800';
            var s = document.createElement('small');
            s.className = 'ocr-verify-hint text-warning d-block';
            s.textContent = 'Please verify';
            input.parentElement.appendChild(s);
        }
    }

    function applyOcrField(ocrName, field) {
        var elId = FIELD_MAP[ocrName];
        if (!elId) {
            return;
        }
        var input = document.getElementById(elId);
        if (!input || !field) {
            return;
        }
        var conf = Number(field.confidence) || 0;
        var val = field.value != null ? String(field.value) : '';
        if (conf < MED || !val) {
            return;
        }
        if (ocrName === 'Devotee_DOB') {
            val = normalizeDobForStaff(val);
        }
        if (ocrName === 'Devotee_Gender') {
            var g = val.toUpperCase().charAt(0);
            val = g === 'F' ? 'F' : g === 'M' ? 'M' : '';
            if (!val) {
                return;
            }
        }
        input.value = val;
        if (TITLE_CASE_IDS.indexOf(elId) >= 0 && val) {
            input.value = toTitleCase(input.value.trim());
        }
        setFieldConfidence(input, conf);
    }

    function applyStaffOcrResponse(data) {
        if (!data || typeof data !== 'object') {
            return false;
        }
        var applied = false;
        OCR_FIELDS.forEach(function (name) {
            var before = document.getElementById(FIELD_MAP[name]);
            var beforeVal = before ? before.value : '';
            applyOcrField(name, data[name]);
            if (before && before.value !== beforeVal) {
                applied = true;
            }
        });
        return applied;
    }

    async function runStaffOcrPrefill(file) {
        var devoteeKey = resolveReservedKey().toUpperCase();
        if (!devoteeKey || !/^P[0-9A-Z]+$/.test(devoteeKey)) {
            return false;
        }
        var ocrUrl = (window.kdmsWebRoot || '/').replace(/\/?$/, '/') + 'Logic/staffOcrExtractProxy.php';
        var fd = new FormData();
        fd.append('id_image', file);
        fd.append('Devotee_Key', devoteeKey);

        var controller = new AbortController();
        var timer = setTimeout(function () { controller.abort(); }, OCR_TIMEOUT_MS);
        try {
            var res = await fetch(ocrUrl, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
                signal: controller.signal
            });
            var data = await res.json().catch(function () { return {}; });
            if (res.ok && ocrResponseHasFields(data)) {
                applyStaffOcrResponse(data);
                return 'filled';
            }
            if (res.ok) {
                return 'empty';
            }
        } catch (e) {
            /* OCR failure is non-fatal; upload still proceeds */
        } finally {
            clearTimeout(timer);
        }
        return 'failed';
    }

    function uploadIDImageMultipart(blob, previewUrl, ocrResult) {
        var devoteeID = resolveReservedKey();
        if (devoteeID === '') {
            alert('Devotee ID is not reserved yet. Refresh the Add Devotee page and try again.');
            return;
        }

        var fd = new FormData();
        fd.append('id_image', blob, 'id_image.jpg');
        fd.append('api_type', '4');
        fd.append('devotee_key', devoteeID);

        $.ajax({
            url: getManagePhotoUrl(),
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            timeout: UPLOAD_TIMEOUT_MS
        }).done(function (resp) {
            setIdUploadBusy(false);
            var parsed = typeof resp === 'string' ? JSON.parse(resp) : resp;
            if (!parsed || parsed.status !== true) {
                alert(parsed && parsed.message ? parsed.message : 'ID image upload failed.');
                return;
            }
            var msg = 'ID image saved for key ' + devoteeID + '.';
            if (ocrResult === 'filled') {
                msg += ' Form fields were prefilled from the ID — please verify.';
            } else if (ocrResult === 'empty') {
                msg += ' OCR did not return field data (registration service may be unavailable) — enter details manually.';
            }
            alert(msg);
            var previewContent = document.getElementById('photo-id-preview-content');
            if (previewContent) {
                previewContent.innerHTML =
                    '<img class="photo-id-preview" src="' + previewUrl + '" alt="devotee ID" height="400px" width="200px"></img>';
            }
            if (typeof window.kdmsRefreshDedupHints === 'function') {
                window.kdmsRefreshDedupHints();
            }
        }).fail(function (xhr) {
            setIdUploadBusy(false);
            var msg = 'ID image upload request failed.';
            if (xhr && xhr.responseText) {
                try {
                    var err = JSON.parse(xhr.responseText);
                    if (err && err.message) {
                        msg = err.message;
                    }
                } catch (ignore) { /* use default */ }
            }
            alert(msg);
        });
    }

    document.getElementById('cameraIDFileInput').addEventListener('change', async function () {
        var file = this.files && this.files[0];
        this.value = '';
        if (!file) {
            return;
        }

        setIdUploadBusy(true, 'Preparing image…');

        var compressed;
        try {
            compressed = await compressImageFile(file);
        } catch (e) {
            setIdUploadBusy(false);
            alert('Could not read the selected image.');
            return;
        }

        var ocrFile = file;
        if (file.size > MAX_OCR_BYTES) {
            ocrFile = new File([compressed.blob], 'id_ocr.jpg', { type: 'image/jpeg' });
        }

        setIdUploadBusy(true, 'Scanning ID (OCR)…');
        var ocrResult = 'failed';
        try {
            ocrResult = await runStaffOcrPrefill(ocrFile);
        } catch (e) {
            ocrResult = 'failed';
        }

        setIdUploadBusy(true, 'Uploading ID image…');
        uploadIDImageMultipart(compressed.blob, compressed.previewUrl, ocrResult);
    });

    function uploadDevoteeImage(base64_image_data) {
        var devoteeID = resolveReservedKey();
        if (devoteeID === '') {
            alert('Devotee ID is not reserved yet. Refresh the Add Devotee page and try again.');
            return;
        }

        $.ajax({
            url: getManagePhotoUrl(),
            method: 'POST',
            data: { image: base64_image_data, api_type: 3, devotee_key: devoteeID },
            timeout: UPLOAD_TIMEOUT_MS
        }).done(function (resp) {
            var parsed = typeof resp === 'string' ? JSON.parse(resp) : resp;
            if (!parsed || parsed.status !== true) {
                alert(parsed && parsed.message ? parsed.message : 'Photo upload failed.');
                return;
            }
            alert('Devotee image saved for key ' + devoteeID);
            var photoMobilePreviewDiv = document.getElementById('photo-mobile-preview_div');
            if (photoMobilePreviewDiv) {
                photoMobilePreviewDiv.innerHTML =
                    '<img class="devoteeImage" id="devoteeImage" src="' + base64_image_data + '" alt="devotee image"></img>';
            }
        }).fail(function () {
            alert('Photo upload request failed.');
        });
    }

    var mobileInput = document.getElementById('cameraMobilePhotoFileInput');
    if (mobileInput) {
        mobileInput.addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (!file) {
                return;
            }
            getBase64(file).then(function (base64_image_data) {
                uploadDevoteeImage(base64_image_data);
            });
        });
    }
})();
