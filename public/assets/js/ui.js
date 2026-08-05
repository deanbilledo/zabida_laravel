/**
 * ZABIDA shared UI helpers — replaces the browser's native confirm() and
 * bare "disable the button" loading states with an animated, on-brand
 * modal and a full-screen loading overlay that:
 *   - blocks interaction with the rest of the page while an action runs
 *   - shows a real progress bar for file uploads (not just a spinner)
 *   - offers a working Cancel button (aborts the actual upload for
 *     XHR-based forms; best-effort window.stop() for plain form posts)
 *
 * Include once, after the DOM markup from partials/ui-overlay.blade.php
 * is present on the page (it is, once you add it to layouts/app.blade.php).
 */
window.ZUI = (function () {
  var confirmRoot = document.getElementById('z-confirm');
  var confirmMessage = document.getElementById('z-confirm-message');
  var confirmYes = document.getElementById('z-confirm-yes');
  var confirmNo = document.getElementById('z-confirm-no');
  var confirmPanel = confirmRoot ? confirmRoot.querySelector('.z-modal-panel') : null;

  var loadingRoot = document.getElementById('z-loading');
  var loadingPanel = loadingRoot ? loadingRoot.querySelector('.z-modal-panel') : null;
  var loadingLabel = document.getElementById('z-loading-label');
  var loadingBarFill = document.getElementById('z-loading-bar-fill');
  var loadingBarWrap = document.getElementById('z-loading-bar-wrap');
  var loadingPercent = document.getElementById('z-loading-percent');
  var loadingCancelBtn = document.getElementById('z-loading-cancel');
  var loadingErrorBox = document.getElementById('z-loading-error');

  var activeXhr = null;
  var lastFocused = null;

  function trapFocus(panel) {
    if (!panel) return;
    var focusable = panel.querySelectorAll('button, [href], input, select, textarea');
    if (focusable.length) focusable[0].focus();
  }

  // --- Confirm modal ------------------------------------------------

  function confirm(message, opts) {
    opts = opts || {};
    return new Promise(function (resolve) {
      if (!confirmRoot) { resolve(window.confirm(message)); return; }

      lastFocused = document.activeElement;
      confirmMessage.textContent = message;
      confirmYes.textContent = opts.confirmLabel || 'Yes, continue';
      confirmYes.className = 'px-5 py-2.5 text-sm uppercase tracking-wide text-paper transition-colors '
        + (opts.danger !== false ? 'bg-clay hover:bg-clay/90' : 'bg-ink hover:bg-ink/90');

      confirmRoot.classList.remove('hidden');
      requestAnimationFrame(function () {
        confirmRoot.classList.add('z-modal-open');
      });
      document.body.classList.add('overflow-hidden');
      trapFocus(confirmPanel);

      function cleanup(result) {
        confirmRoot.classList.remove('z-modal-open');
        setTimeout(function () {
          confirmRoot.classList.add('hidden');
          document.body.classList.remove('overflow-hidden');
          if (lastFocused) lastFocused.focus();
        }, 150);
        confirmYes.removeEventListener('click', onYes);
        confirmNo.removeEventListener('click', onNo);
        document.removeEventListener('keydown', onKey);
        resolve(result);
      }
      function onYes() { cleanup(true); }
      function onNo() { cleanup(false); }
      function onKey(e) {
        if (e.key === 'Escape') cleanup(false);
      }

      confirmYes.addEventListener('click', onYes);
      confirmNo.addEventListener('click', onNo);
      document.addEventListener('keydown', onKey);
    });
  }

  // --- Loading overlay ------------------------------------------------

  function showLoading(opts) {
    opts = opts || {};
    if (!loadingRoot) return;

    loadingLabel.textContent = opts.label || 'Working on it\u2026';
    loadingErrorBox.classList.add('hidden');
    loadingErrorBox.textContent = '';

    if (opts.showProgress) {
      loadingBarWrap.classList.remove('hidden');
      setProgress(0);
    } else {
      loadingBarWrap.classList.add('hidden');
    }

    if (opts.onCancel) {
      loadingCancelBtn.classList.remove('hidden');
      loadingCancelBtn.onclick = opts.onCancel;
    } else {
      loadingCancelBtn.classList.add('hidden');
      loadingCancelBtn.onclick = null;
    }

    loadingRoot.classList.remove('hidden');
    requestAnimationFrame(function () {
      loadingRoot.classList.add('z-modal-open');
    });
    document.body.classList.add('overflow-hidden');
  }

  function setProgress(percent) {
    if (!loadingBarFill) return;
    percent = Math.max(0, Math.min(100, Math.round(percent)));
    loadingBarFill.style.width = percent + '%';
    if (loadingPercent) loadingPercent.textContent = percent + '%';
  }

  function showLoadingError(message) {
    loadingErrorBox.textContent = message;
    loadingErrorBox.classList.remove('hidden');
    loadingCancelBtn.textContent = 'Close';
    loadingCancelBtn.classList.remove('hidden');
    loadingCancelBtn.onclick = hideLoading;
  }

  function hideLoading() {
    if (!loadingRoot) return;
    loadingRoot.classList.remove('z-modal-open');
    setTimeout(function () {
      loadingRoot.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }, 150);
    loadingCancelBtn.textContent = 'Cancel';
  }

  // --- Wiring: delete/confirm forms ------------------------------------

  // Any <form data-confirm="Message here"> gets intercepted: the native
  // submit is blocked, our modal asks first, and only a real "yes" lets
  // the form actually submit.
  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.hasAttribute('data-confirm') || form.dataset.confirmed === 'true') return;

    event.preventDefault();
    confirm(form.dataset.confirm, { danger: form.dataset.confirmDanger !== 'false' })
      .then(function (ok) {
        if (!ok) return;
        form.dataset.confirmed = 'true';

        if (form.hasAttribute('data-loading-label')) {
          showLoading({ label: form.dataset.loadingLabel, onCancel: function () { window.stop(); hideLoading(); } });
        }
        form.submit();
      });
  });

  // --- Wiring: plain forms that just need a blocking loading state -----

  // <form data-loading-label="Signing in\u2026"> shows the overlay right
  // before a normal (non-AJAX) submit. Cancel here is best-effort —
  // window.stop() halts the browser's page load for that navigation,
  // which works in practice for most browsers but isn't guaranteed the
  // instant the request has already been fully sent server-side.
  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.hasAttribute('data-loading-label')) return;
    if (form.hasAttribute('data-confirm')) return; // handled above
    if (form.hasAttribute('data-async-upload')) return; // handled below

    showLoading({
      label: form.dataset.loadingLabel,
      onCancel: function () { window.stop(); hideLoading(); },
    });
  });

  // --- Wiring: file-upload forms with real progress + real cancel ------

  // <form data-async-upload data-loading-label="Uploading\u2026"> is
  // submitted via XMLHttpRequest instead of a normal page post, so we get
  // real upload-progress events and a Cancel button that genuinely aborts
  // the in-flight request. On success we navigate to the final URL the
  // server redirected to (same place a normal form submit would land),
  // so flash messages / validation errors still show up normally.
  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-async-upload')) return;

    event.preventDefault();

    var xhr = new XMLHttpRequest();
    activeXhr = xhr;
    xhr.open(form.method || 'POST', form.action, true);
    // Deliberately NOT setting X-Requested-With here. That header makes
    // Laravel treat the request as "AJAX", which changes how validation
    // failures are returned (a raw JSON 422 instead of the normal
    // redirect-back-with-errors) — and this UI wasn't reading that JSON,
    // so a failed save just silently looked like nothing happened.
    // Leaving this as a plain request means Laravel's normal
    // redirect-with-flashed-errors behavior works exactly like any other
    // form, and the XHR below still follows that redirect either way.

    showLoading({
      label: form.dataset.loadingLabel || 'Uploading\u2026',
      showProgress: true,
      onCancel: function () {
        xhr.abort();
        hideLoading();
      },
    });

    xhr.upload.onprogress = function (e) {
      if (e.lengthComputable) setProgress((e.loaded / e.total) * 100);
    };

    xhr.onload = function () {
      activeXhr = null;
      if (xhr.status >= 200 && xhr.status < 400) {
        // Follow the server's final redirect target so the destination
        // page (with its normal flash message / validation errors) loads.
        window.location.href = xhr.responseURL || form.dataset.fallbackRedirect || '/';
      } else {
        showLoadingError('Upload failed (server responded with status ' + xhr.status + '). Please try again, or use a smaller file.');
      }
    };

    xhr.onerror = function () {
      activeXhr = null;
      showLoadingError('Upload failed — check your connection and try again.');
    };

    xhr.send(new FormData(form));
  });

  return { confirm: confirm, showLoading: showLoading, hideLoading: hideLoading, setProgress: setProgress, showLoadingError: showLoadingError };
})();