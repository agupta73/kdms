(function () {
  'use strict';

  const HIGH = 0.7;
  const MED = 0.4;

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

  function $(sel) {
    return document.querySelector(sel);
  }

  function toTitleCase(str) {
    if (!str) return '';
    return str.toLowerCase().replace(/\b([a-zà-ÿ])/g, (m) => m.toUpperCase());
  }

  function isoToStaffDob(iso) {
    if (!iso || !/^\d{4}-\d{2}-\d{2}$/.test(iso)) return iso || '';
    const p = iso.split('-');
    return p[2] + '-' + p[1] + '-' + p[0];
  }

  function normalizeDobForStaff(val) {
    val = (val || '').trim();
    if (!val) return '';
    if (/^\d{4}-\d{2}-\d{2}$/.test(val)) return isoToStaffDob(val);
    const m = val.match(/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{4})$/);
    if (m) {
      return m[1].padStart(2, '0') + '-' + m[2].padStart(2, '0') + '-' + m[3];
    }
    return val;
  }

  function clearFieldHints(input) {
    input.style.borderLeft = '';
    const hint = input.parentElement && input.parentElement.querySelector('.ocr-verify-hint');
    if (hint) hint.remove();
  }

  function setFieldConfidence(input, confidence) {
    clearFieldHints(input);
    if (confidence >= HIGH) {
      input.style.borderLeft = '4px solid #4caf50';
    } else if (confidence >= MED) {
      input.style.borderLeft = '4px solid #ff9800';
      const s = document.createElement('small');
      s.className = 'ocr-verify-hint text-warning';
      s.textContent = 'Please verify';
      input.parentElement.appendChild(s);
    }
  }

  function applyOcrField(ocrName, field) {
    const elId = FIELD_MAP[ocrName];
    if (!elId) return;
    const input = document.getElementById(elId);
    if (!input || !field) return;
    const conf = Number(field.confidence) || 0;
    let val = field.value != null ? String(field.value) : '';
    if (conf < MED || !val) return;
    if (ocrName === 'Devotee_DOB') {
      val = normalizeDobForStaff(val);
    }
    if (ocrName === 'Devotee_Gender') {
      const g = val.toUpperCase().charAt(0);
      val = g === 'F' ? 'F' : g === 'M' ? 'M' : '';
      if (!val) return;
    }
    input.value = val;
    if (TITLE_CASE_IDS.indexOf(elId) >= 0 && val) {
      input.value = toTitleCase(input.value.trim());
    }
    setFieldConfidence(input, conf);
  }

  function init() {
    const btn = $('#btn-scan-id');
    const fileInput = $('#id-scan-file');
    const status = $('#id-scan-status');
    const ocrUrl = (window.kdmsWebRoot || '/').replace(/\/?$/, '/') + 'Logic/staffOcrExtractProxy.php';
    const keyInput = document.getElementById('devotee_key');

    if (!btn || !fileInput || !keyInput) return;

    if (!window.matchMedia('(pointer: coarse)').matches) {
      fileInput.removeAttribute('capture');
    }

    btn.addEventListener('click', function () {
      fileInput.click();
    });

    fileInput.addEventListener('change', async function () {
      const file = fileInput.files && fileInput.files[0];
      fileInput.value = '';
      if (!file) return;

      const devoteeKey = (keyInput.value || '').trim().toUpperCase();
      if (!devoteeKey || !/^P[0-9A-Z]+$/.test(devoteeKey)) {
        alert('Devotee key is missing. Refresh the page and try again.');
        return;
      }

      if (status) {
        status.hidden = false;
        status.textContent = 'Reading ID…';
      }

      const fd = new FormData();
      fd.append('id_image', file);
      fd.append('Devotee_Key', devoteeKey);

      try {
        const res = await fetch(ocrUrl, {
          method: 'POST',
          body: fd,
          credentials: 'same-origin'
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok && res.status !== 200) {
          throw new Error(data.error || 'Scan failed');
        }
        OCR_FIELDS.forEach((name) => applyOcrField(name, data[name]));
        const hiddenPath = document.getElementById('id_gcs_path');
        if (hiddenPath && data.id_gcs_path) {
          hiddenPath.value = data.id_gcs_path;
        }
        if (status) {
          status.textContent = 'ID scanned. Please check and complete the form.';
        }
      } catch (err) {
        if (status) {
          status.textContent = 'Could not read ID. Please enter details manually.';
        }
      }
    });

    TITLE_CASE_IDS.forEach((id) => {
      const el = document.getElementById(id);
      if (!el) return;
      el.addEventListener('blur', function () {
        if (el.value.trim()) {
          el.value = toTitleCase(el.value.trim());
        }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
