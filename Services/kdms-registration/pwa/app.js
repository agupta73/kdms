(function () {
  'use strict';

  const HIGH = 0.7;
  const MED = 0.4;
  const ID_HINTS = {
    Aadhaar: '12-digit number',
    'PAN Card': 'e.g. ABCDE1234F',
    Passport: 'e.g. A1234567',
    'Voter ID': '3 letters + 7 digits',
    'Driving License': 'State format varies',
    Other: ''
  };

  let csrfToken = '';
  let idStagingPath = '';
  let selfieGcsPath = '';

  const $ = (sel) => document.querySelector(sel);
  const form = $('#reg-form');
  const spinner = $('#spinner');

  function showSpinner(on) {
    spinner.hidden = !on;
  }

  async function loadCsrf() {
    const res = await fetch('/api/csrf-token');
    const data = await res.json();
    csrfToken = data.token || '';
  }

  function setFieldConfidence(input, confidence) {
    input.classList.remove('conf-high', 'conf-med');
    let verify = input.parentElement.querySelector('.verify');
    if (verify) verify.remove();
    if (confidence >= HIGH) {
      input.classList.add('conf-high');
    } else if (confidence >= MED) {
      input.classList.add('conf-med');
      const s = document.createElement('small');
      s.className = 'verify';
      s.textContent = 'Please verify';
      input.after(s);
    }
  }

  function applyOcrField(name, field) {
    const input = form.elements.namedItem(name);
    if (!input || !field) return;
    const conf = Number(field.confidence) || 0;
    const val = field.value;
    if (conf >= MED && val) {
      input.value = val;
      setFieldConfidence(input, conf);
    }
  }

  $('#btn-scan').addEventListener('click', () => $('#id-file').click());

  $('#id-file').addEventListener('change', async (e) => {
    const file = e.target.files && e.target.files[0];
    if (!file) return;
    const status = $('#scan-status');
    status.hidden = false;
    status.textContent = 'Reading ID…';
    showSpinner(true);
    try {
      const fd = new FormData();
      fd.append('id_image', file);
      const res = await fetch('/api/ocr-extract', { method: 'POST', body: fd });
      const data = await res.json();
      idStagingPath = data.id_staging_gcs_path || '';
      applyOcrField('Devotee_First_Name', data.Devotee_First_Name);
      applyOcrField('Devotee_Last_Name', data.Devotee_Last_Name);
      applyOcrField('Devotee_ID_Number', data.Devotee_ID_Number);
      applyOcrField('Devotee_DOB', data.Devotee_DOB);
      applyOcrField('Devotee_Station', data.Devotee_Station);
      status.textContent = 'ID scanned. Please check and complete the form below.';
    } catch (err) {
      status.textContent = 'Could not read ID. Please enter details manually.';
    } finally {
      showSpinner(false);
      e.target.value = '';
    }
  });

  form.elements.Devotee_ID_Type.addEventListener('change', () => {
    const t = form.elements.Devotee_ID_Type.value;
    $('#id-hint').textContent = ID_HINTS[t] || '';
  });

  $('#btn-selfie').addEventListener('click', () => $('#selfie-file').click());

  $('#selfie-file').addEventListener('change', async (e) => {
    const file = e.target.files && e.target.files[0];
    if (!file) return;
    showSpinner(true);
    try {
      const urlRes = await fetch('/api/selfie-upload-url');
      const urlData = await urlRes.json();
      if (!urlData.upload_url) throw new Error('no url');
      const put = await fetch(urlData.upload_url, {
        method: 'PUT',
        headers: { 'Content-Type': 'image/jpeg' },
        body: file
      });
      if (!put.ok) throw new Error('upload failed');
      selfieGcsPath = urlData.selfie_gcs_path || '';
      const preview = $('#selfie-preview');
      preview.src = URL.createObjectURL(file);
      preview.hidden = false;
    } catch (err) {
      alert('Photo upload failed. You can still register without a photo.');
    } finally {
      showSpinner(false);
      e.target.value = '';
    }
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    $('#form-error').hidden = true;
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    const btn = $('#btn-submit');
    btn.disabled = true;
    showSpinner(true);
    const payload = {
      Devotee_First_Name: form.elements.Devotee_First_Name.value.trim(),
      Devotee_Last_Name: form.elements.Devotee_Last_Name.value.trim(),
      Devotee_ID_Type: form.elements.Devotee_ID_Type.value,
      Devotee_ID_Number: form.elements.Devotee_ID_Number.value.trim(),
      Devotee_Cell_Phone_Number: form.elements.Devotee_Cell_Phone_Number.value.trim(),
      Devotee_DOB: form.elements.Devotee_DOB.value,
      Devotee_Station: form.elements.Devotee_Station.value.trim(),
      id_staging_gcs_path: idStagingPath,
      selfie_gcs_path: selfieGcsPath,
      csrf_token: csrfToken
    };
    try {
      const res = await fetch('/api/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success && data.Devotee_Key) {
        $('#scan-section').hidden = true;
        form.hidden = true;
        $('#success-screen').hidden = false;
        $('#ref-key').textContent = data.Devotee_Key;
        return;
      }
      if (res.status === 429) {
        showError(data.error || 'Too many requests. Please wait.');
        return;
      }
      showError(data.error || 'Registration failed. Please try again.');
    } catch (err) {
      showError('Network error. Please check your connection and try again.');
    } finally {
      btn.disabled = false;
      showSpinner(false);
    }
  });

  function showError(msg) {
    $('#error-message').textContent = msg;
    $('#error-screen').hidden = false;
    form.hidden = true;
    $('#scan-section').hidden = true;
  }

  $('#btn-retry').addEventListener('click', () => {
    $('#error-screen').hidden = true;
    form.hidden = false;
    $('#scan-section').hidden = false;
  });

  loadCsrf().catch(() => {});
})();
