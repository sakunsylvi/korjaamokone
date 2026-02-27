(function () {
  'use strict';

  var products = [];
  var resultsEl = null;
  var inputEl = null;

  function normalize(str) {
    return str.toLowerCase();
  }

  function search(query) {
    var q = normalize(query.trim());
    if (!q) return [];

    var matches = [];
    for (var i = 0; i < products.length; i++) {
      if (matches.length >= 6) break;
      var p = products[i];
      var haystack = normalize(p.title + ' ' + p.specs.join(' '));
      if (haystack.indexOf(q) !== -1) {
        matches.push(p);
      }
    }
    return matches;
  }

  function renderResults(matches) {
    resultsEl.innerHTML = '';

    if (matches.length === 0) {
      resultsEl.classList.remove('search-results-open');
      return;
    }

    for (var i = 0; i < matches.length; i++) {
      var p = matches[i];
      var item = document.createElement('a');
      item.href = p.slug;
      item.className = 'search-result-item';

      var name = document.createElement('span');
      name.className = 'search-result-title';
      name.textContent = p.title;

      var cat = document.createElement('span');
      cat.className = 'search-result-category';
      cat.textContent = p.categoryLabel;

      item.appendChild(name);
      item.appendChild(cat);
      resultsEl.appendChild(item);
    }

    resultsEl.classList.add('search-results-open');
  }

  function hideResults() {
    resultsEl.innerHTML = '';
    resultsEl.classList.remove('search-results-open');
  }

  function onInput() {
    var matches = search(inputEl.value);
    renderResults(matches);
  }

  function onDocumentClick(e) {
    if (!inputEl.contains(e.target) && !resultsEl.contains(e.target)) {
      hideResults();
    }
  }

  function onKeydown(e) {
    if (e.key === 'Escape') {
      hideResults();
      inputEl.blur();
    }
  }

  function init() {
    inputEl = document.getElementById('search-input');
    resultsEl = document.getElementById('search-results');

    if (!inputEl || !resultsEl) return;

    fetch('products.json')
      .then(function (res) {
        if (!res.ok) throw new Error('Failed to load products.json: ' + res.status);
        return res.json();
      })
      .then(function (data) {
        products = data;
      })
      .catch(function (err) {
        console.error(err);
      });

    inputEl.addEventListener('keyup', onInput);
    inputEl.addEventListener('keydown', onKeydown);
    document.addEventListener('click', onDocumentClick);
  }

  document.addEventListener('DOMContentLoaded', init);
})();
