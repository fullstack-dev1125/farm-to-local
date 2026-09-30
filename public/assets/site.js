
(function () {
  'use strict';
  // Every page opens at the top (browsers and the hosted preview can restore a previous scroll position).
  if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
  if (!location.hash) { window.scrollTo(0, 0); window.addEventListener('pageshow', function () { window.scrollTo(0, 0); }); }
  var DARK = {background: '#1E1B16', color: '#FFFFFF', borderColor: '#1E1B16', fontWeight: '600'};
  var LIGHT = {background: '#FFFFFF', color: '#1E1B16', borderColor: '#E4E1DA', fontWeight: '400'};

  function toast(msg) {
    var t = document.querySelector('.ftl-toast');
    if (!t) { t = document.createElement('div'); t.className = 'ftl-toast'; document.body.appendChild(t); }
    t.textContent = msg; t.classList.add('show');
    clearTimeout(t._h); t._h = setTimeout(function () { t.classList.remove('show'); }, 2600);
  }

  // 1. Pill buttons (filters, chips) toggle. Rows of category chips act single-select; everything else multi.
  document.querySelectorAll('button[type="button"]').forEach(function (b) {
    var st = b.getAttribute('style') || '';
    if (st.indexOf('border-radius: 999px') === -1 || b.closest('[data-toggle]') || b.id || b.className) return;
    b.addEventListener('click', function () {
      var row = b.parentElement;
      var isDark = b.style.background.replace(/\s/g, '').toLowerCase() === 'rgb(30,27,22)' || b.style.background === '#1E1B16';
      var single = row && row.querySelector('button') && /^All/.test(row.querySelector('button').textContent.trim());
      if (single) {
        Array.prototype.forEach.call(row.querySelectorAll('button'), function (x) { Object.assign(x.style, LIGHT); });
        Object.assign(b.style, DARK);
      } else {
        Object.assign(b.style, isDark ? LIGHT : DARK);
      }
    });
  });

  document.addEventListener('click', function (e) { document.querySelectorAll('details.ftl-menu[open]').forEach(function (d) { if (!d.contains(e.target)) d.removeAttribute('open'); }); });
  var active = document.getElementById('active-filters');
  function renderChips() {
    if (!active) return;
    active.innerHTML = '';
    var row = document.getElementById('active-row');
    document.querySelectorAll('details.ftl-menu').forEach(function (d) {
      var sm = d.querySelector('summary'), boxes = d.querySelectorAll('input:checked');
      var old = sm.querySelector('.ftl-count'); if (old) old.remove();
      sm.style.borderColor = boxes.length ? '#1E1B16' : '#E4E1DA';
      if (boxes.length) { var bd = document.createElement('span'); bd.className = 'ftl-count'; bd.setAttribute('style', 'background: #1E1B16; color: #FFFFFF; font-size: 11px; font-weight: 700; padding: 1px 7px; border-radius: 999px;'); bd.textContent = boxes.length; sm.insertBefore(bd, sm.querySelector('svg')); }
      boxes.forEach(function (inp) {
        var chip = document.createElement('button'); chip.type = 'button'; chip.className = 'ftl-chip';
        chip.setAttribute('style', 'display: inline-flex; align-items: center; gap: 8px; border: 1px solid #1E1B16; background: #1E1B16; color: #FFFFFF; border-radius: 999px; height: 36px; padding: 0 12px 0 14px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; font-family: inherit;');
        chip.innerHTML = inp.parentElement.querySelector('span').textContent + ' <span aria-hidden="true" style="opacity: .7;">×</span>';
        chip.title = 'Remove this filter';
        chip.addEventListener('click', function () { inp.checked = false; renderChips(); });
        active.appendChild(chip);
      });
    });
    if (row) row.style.display = active.children.length ? 'flex' : 'none';
  }
  document.querySelectorAll('details.ftl-menu input').forEach(function (inp) { inp.addEventListener('change', function () { renderChips(); if (inp.type === 'radio') inp.closest('details').removeAttribute('open'); }); });
  if (active) renderChips();

  // 2. Switches
  document.querySelectorAll('[data-toggle]').forEach(function (t) {
    function flip() {
      var on = t.getAttribute('data-on') === '1';
      on = !on;
      t.setAttribute('data-on', on ? '1' : '0'); t.setAttribute('aria-checked', on ? 'true' : 'false');
      t.style.background = on ? '#2F6B3F' : '#E4E1DA';
      var knob = t.firstElementChild; if (knob) { knob.style.left = on ? 'auto' : '2px'; knob.style.right = on ? '2px' : 'auto'; }
    }
    t.addEventListener('click', flip);
    t.addEventListener('keydown', function (e) { if (e.key === ' ' || e.key === 'Enter') { e.preventDefault(); flip(); } });
  });

  // 3. ZIP search: any input whose placeholder mentions ZIP. Enter, or the nearest button/link after it, goes to the city page.
  document.querySelectorAll('input[placeholder*="ZIP"], input[placeholder*="zip"]').forEach(function (inp) {
    var saved = localStorage.getItem('ftl.zip'); if (saved && !inp.value && inp.placeholder.indexOf('you@') === -1) inp.value = saved;
    function go() {
      var z = inp.value.trim(); if (z) localStorage.setItem('ftl.zip', z);
      location.href = 'city-austin.html' + (z ? '?zip=' + encodeURIComponent(z) : '');
    }
    inp.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); go(); } });
    var box = inp.closest('div, label'); for (var i = 0; i < 2 && box && !box.querySelector('a, button'); i++) box = box.parentElement;
    var btn = box && Array.prototype.find.call(box.querySelectorAll('a, button'), function (x) { return x.compareDocumentPosition(inp) & Node.DOCUMENT_POSITION_PRECEDING; });
    if (btn) btn.addEventListener('click', function (e) { e.preventDefault(); go(); });
  });
  // City page: reflect the ZIP that was searched, or a ?near=&practice= link from a shop page
  var qs = new URLSearchParams(location.search);
  if (document.body.dataset.page === 'city-austin') {
    var z = qs.get('zip'), near = qs.get('near') || (z ? z : null), practice = qs.get('practice');
    if (near) {
      document.querySelectorAll('span').forEach(function (s) { if (s.textContent.trim() === 'Austin, TX 78701') s.textContent = 'Near ' + near; });
      document.title = 'Farm to Local — ' + (practice ? practice + ' sellers' : 'sellers') + ' near ' + near;
    }
    if (practice) {
      var h1 = document.querySelector('h1');
      if (h1) h1.textContent = '14 ' + practice.toLowerCase() + ' sellers within 25 mi of ' + (near || 'Austin');
      var sub = h1 && h1.nextElementSibling; if (sub) { var sp = sub.querySelector('span') || sub; sp.textContent = 'Widen to 50 mi for 31 more'; }
      document.querySelectorAll('details.ftl-menu label').forEach(function (l) {
        if (l.textContent.trim().toLowerCase() === practice.toLowerCase()) l.querySelector('input').checked = true;
      });
      if (typeof renderChips === 'function') renderChips();
    }
  }

  // 4. Forms: remember what the seller typed across onboarding steps (localStorage), restore on return.
  var page = document.body.dataset.page || '';
  var persist = /^(list-step|share-post|claim|contact|report|sign-in)/.test(page);
  if (persist) {
    document.querySelectorAll('input, select, textarea').forEach(function (el, i) {
      var key = 'ftl.' + page + '.' + i;
      var v = localStorage.getItem(key);
      if (v !== null) { if (el.type === 'checkbox' || el.type === 'radio') el.checked = v === '1'; else el.value = v; }
      el.addEventListener('change', function () { localStorage.setItem(key, (el.type === 'checkbox' || el.type === 'radio') ? (el.checked ? '1' : '0') : el.value); });
    });
  }

  // 5. Primary form actions that have no backend yet: confirm and move on.
  var ACTIONS = {
    'send message': ['contact.html', 'Message sent — a person will reply within a business day.'],
    'send report': ['index.html', 'Report received. A person reviews it within 48 hours.'],
    'post the share': ['share.html', 'Share posted — it expires in 14 days.'],
    'send my code': [null, 'Code sent. (Demo: no SMS is actually sent.)'],
    'subscribe': [null, 'Subscribed to the weekly in-season email.'],
    'add product': [null, 'Product row added.'],
    'add': [null, 'Product added.'],
    'text me the code': [null, 'Code sent to the number on file. (Demo)'],
    'upload a document': [null, 'Uploads need the backend — coming with the app build.'],
    'request a vouch': [null, 'Vouch request sent to the market manager. (Demo)'],
    'save': [null, 'Saved.'],
    'get directions': [null, 'Opens your maps app in the live site.'],
    'print stand sign': [null, 'Generates a printable FTL10 sign in the live site.'],
    'call': [null, 'Dials the seller in the live site.'],
    'shop this →': [null, 'Affiliate link — set per item in the live build.'],
    'add item': [null, 'Adds a gear item to your What we use shelf.'],
    'send booking request': ['messages.html', 'Booking request sent to the provider.'],
    'notify me': [null, 'You\'ll get one email when your county votes. (Demo)']
  };
  document.querySelectorAll('a, button').forEach(function (el) {
    var key = el.textContent.replace(/\s+/g, ' ').trim().toLowerCase();
    var act = ACTIONS[key];
    if (!act) return;
    if (el.tagName === 'A' && el.getAttribute('href') && el.getAttribute('href') !== '#') return;
    el.addEventListener('click', function (e) {
      e.preventDefault(); toast(act[1]);
      if (act[0]) setTimeout(function () { location.href = act[0]; }, 900);
    });
  });

  // 6. Messages: a working composer.
  if (page === 'messages') {
    var input = document.querySelector('input[placeholder="Write a message"]');
    var send = input && Array.prototype.find.call(document.querySelectorAll('a'), function (a) { return a.textContent.trim() === 'Send'; });
    var thread = input && input.closest('div').previousElementSibling;
    function post() {
      var text = input.value.trim(); if (!text) return;
      var wrap = document.createElement('div'); wrap.style.cssText = 'align-self:flex-end;max-width:70%;display:flex;flex-direction:column;gap:4px;align-items:flex-end';
      wrap.innerHTML = '<div style="background:#1E1B16;color:#FAFAF8;padding:12px 16px;border-radius:16px 16px 4px 16px;font-size:15px;line-height:1.45"></div><span style="font-size:11px;color:#6B6357">Just now</span>';
      wrap.firstChild.textContent = text; thread.appendChild(wrap); input.value = ''; thread.scrollTop = thread.scrollHeight;
    }
    if (send) send.addEventListener('click', function (e) { e.preventDefault(); post(); });
    if (input) input.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); post(); } });
  }

  // 7. Placeholder links left over ("#") get a soft explanation instead of a jump to the top.
  document.querySelectorAll('a[href="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) { e.preventDefault(); toast('This link needs the app backend — it is wired in the live build.'); });
  });

  // 7. Carousels: any element with data-carousel="N" (cards per page). Arrows, dots, snap, loop.
  document.querySelectorAll('[data-carousel]').forEach(function (track) {
    var per = parseInt(track.dataset.carousel, 10) || 4, gap = parseInt(track.dataset.gap || '14', 10);
    var wrap = document.createElement('div'); wrap.style.cssText = 'position:relative;';
    if (track.style.flex) { wrap.style.flex = track.style.flex; wrap.style.minWidth = '0'; track.style.flex = ''; }
    track.parentNode.insertBefore(wrap, track); wrap.appendChild(track);
    track.style.cssText += ';display:flex;gap:' + gap + 'px;overflow-x:auto;scroll-snap-type:x mandatory;scroll-behavior:smooth;scrollbar-width:none;';
    track.classList.add('ftl-track');
    Array.prototype.forEach.call(track.children, function (c) { c.style.flex = '0 0 calc((100% - ' + (gap * (per - 1)) + 'px) / ' + per + ')'; c.style.scrollSnapAlign = 'start'; });
    function arrow(dir) {
      var b = document.createElement('button'); b.type = 'button'; b.className = 'ftl-nav'; b.setAttribute('aria-label', dir < 0 ? 'Previous' : 'Next');
      b.style.cssText = 'position:absolute;top:50%;' + (dir < 0 ? 'left:-22px' : 'right:-22px') + ';transform:translateY(-50%);width:44px;height:44px;border-radius:999px;border:1px solid #E4E1DA;background:#FFFFFF;box-shadow:0 6px 16px rgba(30,27,22,.14);cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:2;';
      b.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1E1B16" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="' + (dir < 0 ? 'M15 6l-6 6 6 6' : 'M9 6l6 6-6 6') + '"/></svg>';
      b.addEventListener('click', function () { go(cur() + dir); }); wrap.appendChild(b); return b;
    }
    var dots = document.createElement('div'); dots.style.cssText = 'display:flex;justify-content:center;gap:6px;margin-top:14px;'; wrap.appendChild(dots);
    function step() { return track.firstElementChild.getBoundingClientRect().width + gap; }
    function pages() { return Math.max(1, Math.ceil(track.children.length / per)); }
    function cur() { return Math.round(track.scrollLeft / (step() * per)); }
    function go(i) { var p = (i + pages()) % pages(); track.scrollTo({ left: p * step() * per, behavior: 'smooth' }); }
    function draw() {
      dots.innerHTML = '';
      if (pages() < 2) { dots.style.display = 'none'; prev.style.display = 'none'; next.style.display = 'none'; return; }
      for (var i = 0; i < pages(); i++) (function (i) {
        var d = document.createElement('button'); d.type = 'button'; d.className = 'ftl-dot'; d.setAttribute('aria-label', 'Page ' + (i + 1));
        d.style.cssText = 'width:' + (i === cur() ? '22px' : '8px') + ';height:8px;border-radius:999px;border:0;padding:0;cursor:pointer;background:' + (i === cur() ? (track.dataset.dark ? '#FFFFFF' : '#1E1B16') : (track.dataset.dark ? 'rgba(255,255,255,.35)' : '#D6CEBF')) + ';transition:width .2s;';
        d.addEventListener('click', function () { go(i); }); dots.appendChild(d);
      })(i);
    }
    var prev = arrow(-1), next = arrow(1);
    track.addEventListener('scroll', function () { clearTimeout(track._h); track._h = setTimeout(draw, 80); });
    window.addEventListener('resize', draw); draw();
  });

  // 8. Prototype session: after the mock sign-in, the nav's "Sign in" becomes "Dashboard"; Sign out clears it.
  try {
    if (localStorage.getItem('ftl.session')) {
      document.querySelectorAll('a[href="sign-in.html"]').forEach(function (a) { if (/Sign in/.test(a.textContent)) { a.textContent = 'Dashboard'; a.href = 'dashboard.html'; } else if (/Sign out/.test(a.textContent)) { a.addEventListener('click', function () { localStorage.removeItem('ftl.session'); }); } });
    }
  } catch (e) {}
})();