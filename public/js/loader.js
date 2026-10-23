/**
 * EduPulse — Global Wi-Fi / Network Loading Overlay
 * Intercepts: page navigation, form submissions, fetch, XMLHttpRequest, axios
 * Minimum display time: 2000ms  |  Keeps running until real response arrives
 */
(function () {
    'use strict';

    // ── Config ──────────────────────────────────────────────
    var MIN_DISPLAY_MS = 600; // minimum loader visibility in ms
    var activeCount    = 0;    // number of concurrent requests
    var loaderShownAt  = 0;    // timestamp when loader was shown
    var hideTimer      = null; // debounce timer for hiding
    var progressTimer  = null; // progress bar animation timer
    var formSubmitting = false;// prevent double submit

    var MESSAGES = [
        { title: 'Loading',       sub: 'Fetching data...'      },
        { title: 'Please wait',   sub: 'Processing request...' },
        { title: 'Connecting',    sub: 'Reaching server...'    },
        { title: 'Loading',       sub: 'Preparing page...'     },
        { title: 'Please wait',   sub: 'Saving changes...'     },
        { title: 'Loading',       sub: 'Fetching records...'   },
    ];

    // ── DOM refs ────────────────────────────────────────────
    function el(id) { return document.getElementById(id); }

    function getOverlay()    { return el('global-loader'); }
    function getTopBar()     { return el('loader-topbar'); }
    function getTitleEl()    { return el('loader-msg-title'); }
    function getSubtitleEl() { return el('loader-msg-sub'); }
    function getProgressBar(){ return el('loader-progress-bar'); }

    // ── Message helpers ─────────────────────────────────────
    function setMessage(title, sub) {
        var t = getTitleEl();
        var s = getSubtitleEl();
        if (t) t.textContent = title || 'Loading';
        if (s) s.textContent = sub   || 'Please wait...';
    }

    function randomMessage() {
        return MESSAGES[Math.floor(Math.random() * MESSAGES.length)];
    }

    // ── Progress bar animation ──────────────────────────────
    var currentProgress = 0;

    function startProgress() {
        currentProgress = 0;
        var bar = getProgressBar();
        if (bar) bar.style.width = '0%';
        clearInterval(progressTimer);
        progressTimer = setInterval(function () {
            // Ease towards 90% — never complete until real done
            if (currentProgress < 90) {
                var increment = (90 - currentProgress) * 0.07 + 0.5;
                currentProgress = Math.min(90, currentProgress + increment);
                if (bar) bar.style.width = currentProgress.toFixed(1) + '%';
            }
        }, 120);
    }

    function finishProgress(cb) {
        clearInterval(progressTimer);
        currentProgress = 100;
        var bar = getProgressBar();
        if (bar) bar.style.width = '100%';
        setTimeout(cb, 300);
    }

    // ── Top bar ─────────────────────────────────────────────
    var topBarProgress = 0;
    var topBarTimer    = null;

    function startTopBar() {
        topBarProgress = 0;
        var bar = getTopBar();
        if (!bar) return;
        bar.style.width = '0%';
        bar.classList.add('bar-visible');
        clearInterval(topBarTimer);
        topBarTimer = setInterval(function () {
            if (topBarProgress < 85) {
                var inc = (85 - topBarProgress) * 0.08 + 0.3;
                topBarProgress = Math.min(85, topBarProgress + inc);
                bar.style.width = topBarProgress.toFixed(1) + '%';
            }
        }, 100);
    }

    function finishTopBar() {
        clearInterval(topBarTimer);
        topBarProgress = 100;
        var bar = getTopBar();
        if (!bar) return;
        bar.style.width = '100%';
        setTimeout(function () {
            bar.classList.remove('bar-visible');
            setTimeout(function () { bar.style.width = '0%'; }, 400);
        }, 350);
    }

    // ── Show / Hide ──────────────────────────────────────────
    function showLoader(title, sub) {
        clearTimeout(hideTimer);
        activeCount++;

        if (activeCount === 1) {
            loaderShownAt = Date.now();
            var msg = (title && sub) ? { title: title, sub: sub } : randomMessage();
            setMessage(msg.title, msg.sub);

            var overlay = getOverlay();
            if (overlay) overlay.classList.add('loader-visible');
            startProgress();
            startTopBar();
        }
    }

    function hideLoader() {
        activeCount = Math.max(0, activeCount - 1);
        if (activeCount > 0) return; // still waiting on other requests

        var elapsed  = Date.now() - loaderShownAt;
        var remaining = Math.max(0, MIN_DISPLAY_MS - elapsed);

        clearTimeout(hideTimer);
        hideTimer = setTimeout(function () {
            finishProgress(function () {
                var overlay = getOverlay();
                if (overlay) overlay.classList.remove('loader-visible');
                finishTopBar();
                activeCount    = 0;
                formSubmitting = false;
            });
        }, remaining);
    }

    function forceHide() {
        clearTimeout(hideTimer);
        activeCount = 0;
        finishProgress(function () {
            var overlay = getOverlay();
            if (overlay) overlay.classList.remove('loader-visible');
            finishTopBar();
            formSubmitting = false;
        });
    }

    // ── Expose globally ──────────────────────────────────────
    window.GlobalLoader = {
        show: showLoader,
        hide: hideLoader,
        forceHide: forceHide
    };

    // ── Page navigation (anchor clicks) ─────────────────────
    document.addEventListener('click', function (e) {
        var anchor = e.target.closest('a[href]');
        if (!anchor) return;

        var href = anchor.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript') || href.startsWith('mailto') || href.startsWith('tel')) return;
        if (anchor.hasAttribute('download')) return;
        if (anchor.getAttribute('target') === '_blank') return;

        // Same-origin navigation only
        try {
            var url = new URL(href, window.location.href);
            if (url.origin !== window.location.origin) return;
            // Skip if it's the same page
            if (url.href === window.location.href) return;
        } catch (err) { return; }

        showLoader('Loading', 'Preparing page...');
    });

    // ── Form submissions ─────────────────────────────────────
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM') return;

        // Skip forms with data-no-loader
        if (form.dataset.noLoader) return;

        // Prevent double submission
        if (formSubmitting) {
            e.preventDefault();
            return;
        }
        formSubmitting = true;

        // Determine message based on method / action
        var method  = (form.getAttribute('method') || 'get').toUpperCase();
        var action  = form.getAttribute('action') || '';
        var msg     = guessFormMessage(method, action, form);

        showLoader(msg.title, msg.sub);

        // Disable submit buttons to prevent double click
        var submitBtns = form.querySelectorAll('[type="submit"], button:not([type="button"])');
        submitBtns.forEach(function (btn) {
            btn.disabled = true;
            btn._loaderDisabled = true;
        });
    });

    function guessFormMessage(method, action, form) {
        if (method === 'GET') return { title: 'Searching', sub: 'Fetching results...' };

        var a = action.toLowerCase();
        if (a.includes('login'))    return { title: 'Signing in',   sub: 'Authenticating...'  };
        if (a.includes('register')) return { title: 'Registering',  sub: 'Creating account...' };
        if (a.includes('logout'))   return { title: 'Signing out',  sub: 'Ending session...'   };
        if (a.includes('delete') || form.querySelector('[name="_method"][value="DELETE"]'))
                                    return { title: 'Deleting',     sub: 'Removing record...'  };
        if (a.includes('pay') || a.includes('finance'))
                                    return { title: 'Processing',   sub: 'Completing payment...' };
        if (a.includes('setting'))  return { title: 'Saving',       sub: 'Applying settings...' };
        if (a.includes('mail') || a.includes('send'))
                                    return { title: 'Sending',      sub: 'Delivering message...' };
        if (a.includes('upload'))   return { title: 'Uploading',    sub: 'Transferring file...' };

        if (method === 'DELETE')    return { title: 'Deleting',     sub: 'Removing record...'   };
        if (method === 'PUT' || method === 'PATCH')
                                    return { title: 'Updating',     sub: 'Saving changes...'    };
        return { title: 'Please wait', sub: 'Processing request...' };
    }

    // ── XHR (Axios & jQuery) ─────────────────────────────────
    var _open    = XMLHttpRequest.prototype.open;
    var _send    = XMLHttpRequest.prototype.send;

    XMLHttpRequest.prototype.open = function (method, url) {
        this._loaderUrl    = url;
        this._loaderMethod = method;
        this._skipLoader   = isPollingUrl(url);
        return _open.apply(this, arguments);
    };

    XMLHttpRequest.prototype.send = function () {
        if (!this._skipLoader) {
            var msg = guessAjaxMessage(this._loaderMethod, this._loaderUrl);
            showLoader(msg.title, msg.sub);

            var xhr = this;
            function onDone() {
                hideLoader();
                xhr.removeEventListener('load',  onDone);
                xhr.removeEventListener('error', onDone);
                xhr.removeEventListener('abort', onDone);
            }
            this.addEventListener('load',  onDone);
            this.addEventListener('error', onDone);
            this.addEventListener('abort', onDone);
        }
        return _send.apply(this, arguments);
    };

    // ── Fetch API ────────────────────────────────────────────
    var _fetch = window.fetch;
    window.fetch = function (input, init) {
        var url    = (typeof input === 'string') ? input : (input && input.url) || '';
        var method = (init && init.method) ? init.method.toUpperCase() : 'GET';

        if (!isPollingUrl(url)) {
            var msg = guessAjaxMessage(method, url);
            showLoader(msg.title, msg.sub);
            return _fetch.apply(this, arguments).then(
                function (res) { hideLoader(); return res; },
                function (err) { hideLoader(); throw err;  }
            );
        }
        return _fetch.apply(this, arguments);
    };

    // ── Helpers ──────────────────────────────────────────────
    function isPollingUrl(url) {
        if (!url) return false;
        var u = url.toString().toLowerCase();
        // Don't show loader for chat polling, status checks, etc.
        return u.includes('/poll') || u.includes('/status') || u.includes('_token');
    }

    function guessAjaxMessage(method, url) {
        var u = (url || '').toLowerCase();
        var m = (method || 'GET').toUpperCase();

        if (u.includes('login'))    return { title: 'Signing in',   sub: 'Authenticating...'     };
        if (u.includes('search'))   return { title: 'Searching',    sub: 'Fetching results...'   };
        if (u.includes('analytic')) return { title: 'Loading',      sub: 'Preparing analytics...' };
        if (u.includes('mail'))     return { title: 'Sending',      sub: 'Delivering email...'   };
        if (u.includes('pay'))      return { title: 'Processing',   sub: 'Completing payment...' };
        if (u.includes('upload'))   return { title: 'Uploading',    sub: 'Transferring file...'  };
        if (u.includes('chat'))     return { title: 'Connecting',   sub: 'Loading chat...'       };
        if (u.includes('report'))   return { title: 'Loading',      sub: 'Building report...'    };
        if (u.includes('export'))   return { title: 'Exporting',    sub: 'Preparing download...' };

        if (m === 'DELETE')         return { title: 'Deleting',     sub: 'Removing record...'    };
        if (m === 'PUT' || m === 'PATCH')
                                    return { title: 'Updating',     sub: 'Saving changes...'     };
        if (m === 'POST')           return { title: 'Saving',       sub: 'Submitting data...'    };

        return { title: 'Loading', sub: 'Fetching data...' };
    }

    // ── Browser back/forward ─────────────────────────────────
    window.addEventListener('popstate', function () {
        showLoader('Loading', 'Restoring page...');
    });

    // ── Hide on page fully loaded (fallback) ─────────────────
    window.addEventListener('pageshow', function (e) {
        // pageshow fires on bfcache restore too
        if (e.persisted) forceHide();
    });

    // Safety: if page is already loaded and loader is stuck, hide it
    if (document.readyState === 'complete') {
        // Don't hide here — loader was intentionally shown for current navigation
    } else {
        window.addEventListener('load', function () {
            // Page fully loaded — schedule a hide in case nothing triggered it
            setTimeout(function () {
                if (activeCount === 0) forceHide();
            }, 500);
        });
    }

    // ── Turbolinks / PJAX support (if present) ───────────────
    document.addEventListener('turbolinks:request-start', function () {
        showLoader('Loading', 'Preparing page...');
    });
    document.addEventListener('turbolinks:load', forceHide);

})();
