// App behaviour layered on the design's site.js (which stays byte-identical to designs/assets/site.js).
// Results page: the filter bar is a GET form; changing a wired control (radius, category, verified, sort)
// reloads results. Removing a category chip (site.js "Showing" row) reloads too. "change" opens a ZIP field.
(function () {
  'use strict';
  var form = document.querySelector('form[data-autosubmit]');
  if (!form) return;
  var LIVE = { radius: 1, cat: 1, verified: 1 };
  form.addEventListener('change', function (e) { if (LIVE[e.target.name]) form.submit(); });
  document.addEventListener('click', function (e) {
    if (e.target.closest && e.target.closest('.ftl-chip')) setTimeout(function () { form.submit(); }, 0);
  });
  // "change" beside the location label: swap the label for a ZIP field; Enter searches, Escape cancels.
  var change = document.querySelector('[data-change-zip]');
  var zip = form.querySelector('[data-zip-input]');
  var label = document.querySelector('[data-zip-label]');
  if (change && zip && label) {
    var original = zip.value;
    var show = function (on) { label.hidden = on; change.hidden = on; zip.hidden = !on; };
    change.addEventListener('click', function (e) { e.preventDefault(); show(true); zip.focus(); zip.select(); });
    zip.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { zip.value = original; show(false); change.focus(); }
    });
  }
  var sort = document.querySelector('select[data-sort]');
  if (sort) sort.addEventListener('change', function () { form.elements.sort.value = sort.value; form.submit(); });
})();
