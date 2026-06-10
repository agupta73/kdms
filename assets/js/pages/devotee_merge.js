(function () {
  'use strict';

  const cfg = window.kdmsMergeUtility || {};
  const contextUrl = cfg.contextUrl || '../Logic/devoteeMergeContextProxy.php';
  const mergeUrl = cfg.mergeUrl || '../Logic/adminMergeProxy.php';
  const photoProxy = cfg.photoProxy || '../Logic/devoteePhotoProxy.php';
  const eventId = cfg.eventId || '';

  const state = {
    anchorKey: '',
    survivorKey: '',
    tbmKey: '',
    tbmScore: 0,
    matches: [],
    anchor: null,
  };

  const el = {
    anchorInput: document.getElementById('merge-anchor-input'),
    loadBtn: document.getElementById('merge-load-anchor'),
    alert: document.getElementById('merge-alert'),
    stepDuplicates: document.getElementById('merge-step-duplicates'),
    stepCompare: document.getElementById('merge-step-compare'),
    anchorCard: document.getElementById('merge-anchor-card'),
    matchesBody: document.getElementById('merge-matches-body'),
    noMatches: document.getElementById('merge-no-matches'),
    survivorCol: document.getElementById('merge-survivor-col'),
    tbmCol: document.getElementById('merge-tbm-col'),
    previewFields: document.getElementById('merge-preview-fields'),
    previewImages: document.getElementById('merge-preview-images'),
    previewChildren: document.getElementById('merge-preview-children'),
    mergeBtn: document.getElementById('merge-confirm-btn'),
    backBtn: document.getElementById('merge-back-btn'),
  };

  if (!el.anchorInput) {
    return;
  }

  function esc(s) {
    const d = document.createElement('div');
    d.textContent = s == null ? '' : String(s);
    return d.innerHTML;
  }

  function photoSrc(key, type) {
    if (!key) {
      return '../assets/img/faces/devotee.ico';
    }
    return photoProxy + '?devotee_key=' + encodeURIComponent(key) + '&type=' + encodeURIComponent(type);
  }

  function signalLabel(signal, score) {
    if (score >= 100 || signal === 1) {
      return 'Same ID';
    }
    if (signal === 3) {
      return 'Name + DOB';
    }
    if (signal === 4) {
      return 'Name + phone';
    }
    if (signal === 5) {
      return 'Name + station';
    }
    return 'Possible match';
  }

  function showAlert(type, message) {
    if (!el.alert) {
      return;
    }
    el.alert.className = 'alert alert-' + type;
    el.alert.textContent = message;
    el.alert.style.display = message ? 'block' : 'none';
  }

  function summaryHtml(summary, keyOverride) {
    const key = keyOverride || summary.devotee_key;
    return (
      '<div class="merge-summary">' +
      '<div class="merge-summary-photo"><img src="' + esc(photoSrc(key, 'photo')) + '" alt="Photo" width="72" height="72"></div>' +
      '<div><strong>' + esc(summary.name || '—') + '</strong><br>' +
      '<span class="text-muted">' + esc(key) + '</span><br>' +
      esc(summary.station || '') +
      (summary.phone ? ' · ' + esc(summary.phone) : '') +
      '<br><small>Updated: ' + esc(summary.updated_at || '—') + '</small></div></div>'
    );
  }

  function childCountsText(counts) {
    if (!counts) {
      return '—';
    }
    return (
      'Seva ' + (counts.seva || 0) +
      ', Amenities ' + (counts.amenities || 0) +
      ', Active acco ' + (counts.accommodation_active || 0) +
      ', Attendance ' + (counts.attendance || 0)
    );
  }

  function setCompareVisible(visible) {
    if (el.stepCompare) {
      el.stepCompare.style.display = visible ? 'block' : 'none';
    }
    if (el.stepDuplicates) {
      el.stepDuplicates.style.display = visible ? 'none' : 'block';
    }
  }

  function renderAnchor() {
    if (!state.anchor || !el.anchorCard) {
      return;
    }
    el.anchorCard.innerHTML = summaryHtml(state.anchor);
  }

  function renderMatches() {
    if (!el.matchesBody) {
      return;
    }
    el.matchesBody.innerHTML = '';
    if (!state.matches.length) {
      if (el.noMatches) {
        el.noMatches.style.display = 'block';
      }
      return;
    }
    if (el.noMatches) {
      el.noMatches.style.display = 'none';
    }

    state.matches.forEach(function (m) {
      const tr = document.createElement('tr');
      const summary = m.summary || {};
      tr.innerHTML =
        '<td><input type="radio" name="merge_pick" value="' + esc(m.devotee_key) + '" data-score="' + esc(String(m.score)) + '"></td>' +
        '<td>' + esc(m.devotee_key) + '</td>' +
        '<td>' + esc(summary.name || '') + '</td>' +
        '<td>' + esc(String(m.score)) + '</td>' +
        '<td>' + esc(signalLabel(m.signal, m.score)) + '</td>' +
        '<td>' + esc(summary.station || '') + '</td>' +
        '<td>' + esc(summary.phone || '') + '</td>' +
        '<td>' + childCountsText(summary.child_counts) + '</td>' +
        '<td><button type="button" class="btn btn-sm btn-primary merge-review-btn" data-key="' + esc(m.devotee_key) + '" data-score="' + esc(String(m.score)) + '">Review</button></td>';
      el.matchesBody.appendChild(tr);
    });

    el.matchesBody.querySelectorAll('.merge-review-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const key = btn.getAttribute('data-key');
        const score = parseInt(btn.getAttribute('data-score') || '0', 10);
        selectTbm(key, score);
      });
    });

    el.matchesBody.querySelectorAll('input[name="merge_pick"]').forEach(function (radio) {
      radio.addEventListener('change', function () {
        if (radio.checked) {
          selectTbm(radio.value, parseInt(radio.getAttribute('data-score') || '0', 10));
        }
      });
    });
  }

  function selectTbm(tbmKey, score) {
    state.tbmKey = tbmKey;
    state.tbmScore = score;
    if (!state.survivorKey) {
      state.survivorKey = state.anchorKey;
    }
    loadPreview();
  }

  function loadPreview() {
    if (!state.survivorKey || !state.tbmKey) {
      return;
    }
    showAlert('info', 'Loading merge preview…');
    const params = new URLSearchParams({
      survivor: state.survivorKey,
      tbm: state.tbmKey,
      eventId: eventId,
    });
    fetch(contextUrl + '?' + params.toString(), { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data || data.status !== true || !data.preview) {
          showAlert('danger', (data && data.message) || 'Preview failed');
          return;
        }
        showAlert('info', '');
        renderPreview(data.preview);
        setCompareVisible(true);
      })
      .catch(function () {
        showAlert('danger', 'Preview request failed');
      });
  }

  function renderPreview(preview) {
    const survivor = preview.survivor;
    const tbm = preview.tbm;

    if (el.survivorCol) {
      el.survivorCol.innerHTML =
        '<label class="d-block mb-2"><input type="radio" name="merge_survivor" value="' + esc(survivor.devotee_key) + '"' +
        (state.survivorKey === survivor.devotee_key ? ' checked' : '') + '> Keep as survivor</label>' +
        summaryHtml(survivor);
    }
    if (el.tbmCol) {
      el.tbmCol.innerHTML =
        '<label class="d-block mb-2"><input type="radio" name="merge_survivor" value="' + esc(tbm.devotee_key) + '"' +
        (state.survivorKey === tbm.devotee_key ? ' checked' : '') + '> Keep as survivor</label>' +
        '<p class="small text-warning mb-2">This record will be merged away.</p>' +
        summaryHtml(tbm);
    }

    document.querySelectorAll('input[name="merge_survivor"]').forEach(function (radio) {
      radio.addEventListener('change', function () {
        if (!radio.checked) {
          return;
        }
        const newSurvivor = radio.value;
        const newTbm = newSurvivor === survivor.devotee_key ? tbm.devotee_key : survivor.devotee_key;
        state.survivorKey = newSurvivor;
        state.tbmKey = newTbm;
        loadPreview();
      });
    });

    if (el.previewFields) {
      const rows = (preview.fields || []).filter(function (f) {
        return f.survivor || f.tbm;
      });
      let html = '<table class="table table-sm table-bordered"><thead><tr><th>Field</th><th>Survivor</th><th>Duplicate</th><th>After merge</th></tr></thead><tbody>';
      rows.forEach(function (f) {
        const label = f.field.replace(/^Devotee_/, '').replace(/_/g, ' ');
        html += '<tr><td>' + esc(label) + '</td><td>' + esc(f.survivor || '—') + '</td><td>' + esc(f.tbm || '—') + '</td><td><strong>' + esc(f.merged || '—') + '</strong></td></tr>';
      });
      html += '</tbody></table>';
      el.previewFields.innerHTML = html;
    }

    if (el.previewImages) {
      const photo = preview.images && preview.images.photo ? preview.images.photo : {};
      const idImg = preview.images && preview.images.id ? preview.images.id : {};
      el.previewImages.innerHTML =
        '<p><strong>Photo:</strong> keeps ' + esc(photo.source || 'none') + ' image</p>' +
        '<p><strong>ID scan:</strong> keeps ' + esc(idImg.source || 'none') + ' image (latest when both exist)</p>';
    }

    if (el.previewChildren) {
      el.previewChildren.innerHTML =
        '<p><strong>Survivor child records:</strong> ' + esc(childCountsText(preview.child_counts && preview.child_counts.survivor)) + '</p>' +
        '<p><strong>Duplicate child records (will move to survivor):</strong> ' + esc(childCountsText(preview.child_counts && preview.child_counts.tbm)) + '</p>' +
        '<p class="small text-muted">If both records have an active accommodation for the same event, the duplicate allocation is marked Departed.</p>';
    }
  }

  function loadAnchor(key) {
    key = (key || '').trim().toUpperCase();
    if (!key) {
      showAlert('warning', 'Enter a devotee key');
      return;
    }
    state.anchorKey = key;
    state.survivorKey = key;
    state.tbmKey = '';
    state.tbmScore = 0;
    setCompareVisible(false);
    showAlert('info', 'Searching for duplicates…');

    const params = new URLSearchParams({ anchor: key, eventId: eventId });
    fetch(contextUrl + '?' + params.toString(), { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data || data.status !== true) {
          showAlert('danger', (data && data.message) || 'Could not load devotee');
          return;
        }
        state.anchor = data.anchor;
        state.matches = data.matches || [];
        state.survivorKey = key;
        renderAnchor();
        renderMatches();
        if (state.matches.length) {
          showAlert('success', 'Found ' + state.matches.length + ' possible duplicate(s). Pick one to review and merge.');
        } else {
          showAlert('warning', 'No duplicate candidates found for this record. You can still search again with another key.');
        }
        if (el.stepDuplicates) {
          el.stepDuplicates.style.display = 'block';
        }
        if (window.history && window.history.replaceState) {
          const url = new URL(window.location.href);
          url.searchParams.set('anchor', key);
          window.history.replaceState({}, '', url.toString());
        }
      })
      .catch(function () {
        showAlert('danger', 'Request failed');
      });
  }

  function confirmMerge() {
    if (!state.survivorKey || !state.tbmKey) {
      showAlert('warning', 'Select a duplicate record first');
      return;
    }
    const msg = 'Merge ' + state.tbmKey + ' into survivor ' + state.survivorKey + '? The duplicate record will be removed.';
    if (!window.confirm(msg)) {
      return;
    }
    if (el.mergeBtn) {
      el.mergeBtn.disabled = true;
    }
    fetch(mergeUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        base_devotee_key: state.survivorKey,
        tbm_devotee_keys: [state.tbmKey],
        eventId: eventId,
        merge_mode: 'utility',
        merge_score: state.tbmScore,
      }),
    })
      .then(function (r) {
        return r.text().then(function (text) {
          let data = null;
          try {
            data = text ? JSON.parse(text) : null;
          } catch (e) {
            data = null;
          }
          return { ok: r.ok, data: data };
        });
      })
      .then(function (res) {
        if (el.mergeBtn) {
          el.mergeBtn.disabled = false;
        }
        if (res.data && res.data.status) {
          showAlert('success', 'Merged successfully. Survivor: ' + res.data.Devotee_Key + '. Loading remaining duplicates…');
          state.anchorKey = res.data.Devotee_Key;
          state.survivorKey = res.data.Devotee_Key;
          state.tbmKey = '';
          setCompareVisible(false);
          loadAnchor(state.anchorKey);
          return;
        }
        showAlert('danger', (res.data && res.data.message) || 'Merge failed');
      })
      .catch(function () {
        if (el.mergeBtn) {
          el.mergeBtn.disabled = false;
        }
        showAlert('danger', 'Merge request failed');
      });
  }

  el.loadBtn.addEventListener('click', function () {
    loadAnchor(el.anchorInput.value);
  });
  el.anchorInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      loadAnchor(el.anchorInput.value);
    }
  });
  if (el.mergeBtn) {
    el.mergeBtn.addEventListener('click', confirmMerge);
  }
  if (el.backBtn) {
    el.backBtn.addEventListener('click', function () {
      state.tbmKey = '';
      setCompareVisible(false);
      showAlert('info', '');
    });
  }

  const initialAnchor = (cfg.initialAnchor || '').trim().toUpperCase();
  if (initialAnchor) {
    el.anchorInput.value = initialAnchor;
    loadAnchor(initialAnchor);
  }
})();
