// App behaviour layered on the design's site.js (which stays byte-identical to designs/assets/site.js).
// Results page: the filter bar is a GET form; changing a wired control (radius, category, verified, sort)
// reloads results. Removing a category chip (site.js "Showing" row) reloads too.
(function () {
  'use strict';
  var form = document.querySelector('form[data-autosubmit]');
  if (!form) return;
  var LIVE = { radius: 1, cat: 1, verified: 1 };
  form.addEventListener('change', function (e) { if (LIVE[e.target.name]) form.submit(); });
  document.addEventListener('click', function (e) {
    if (e.target.closest && e.target.closest('.ftl-chip')) setTimeout(function () { form.submit(); }, 0);
  });
  var sort = document.querySelector('select[data-sort]');
  if (sort) sort.addEventListener('change', function () { form.elements.sort.value = sort.value; form.submit(); });
})();
