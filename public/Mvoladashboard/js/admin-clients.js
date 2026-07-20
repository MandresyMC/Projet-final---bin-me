(function () {
  "use strict";

  var searchInput = document.querySelector("[data-clients-search]");
  var soldeBtn = document.querySelector("[data-clients-solde-filter]");
  var soldeLabel = document.querySelector("[data-clients-solde-label]");
  var statusBtn = document.querySelector("[data-clients-status-filter]");
  var statusLabel = document.querySelector("[data-clients-status-label]");
  var body = document.querySelector("[data-clients-body]");
  var emptyState = document.querySelector("[data-clients-empty]");

  if (!body) {
    return;
  }

  var STATUS_CYCLE = ["tous", "actif", "bloque"];
  var STATUS_LABELS = {
    tous: "Filtrer par statut",
    actif: "Statut : Actif",
    bloque: "Statut : Bloqué",
  };

  var state = {
    search: "",
    status: "tous",
    soldeSort: null,
  };

  function getRows() {
    return Array.prototype.slice.call(body.querySelectorAll("[data-clients-row]"));
  }

  function applyFilters() {
    var rows = getRows();
    var visibleCount = 0;

    rows.forEach(function (row) {
      var matchesSearch = row.getAttribute("data-search").indexOf(state.search) !== -1;
      var matchesStatus = state.status === "tous" || row.getAttribute("data-statut") === state.status;
      var visible = matchesSearch && matchesStatus;

      row.hidden = !visible;
      if (visible) {
        visibleCount += 1;
      }
    });

    if (emptyState) {
      emptyState.hidden = visibleCount !== 0;
    }
  }

  function sortBySolde() {
    if (!state.soldeSort) {
      return;
    }
    var rows = getRows();
    rows.sort(function (a, b) {
      var soldeA = parseFloat(a.getAttribute("data-solde"));
      var soldeB = parseFloat(b.getAttribute("data-solde"));
      return state.soldeSort === "asc" ? soldeA - soldeB : soldeB - soldeA;
    });
    rows.forEach(function (row) {
      body.appendChild(row);
    });
  }

  if (searchInput) {
    searchInput.addEventListener("input", function () {
      state.search = searchInput.value.trim().toLowerCase();
      applyFilters();
    });
  }

  if (soldeBtn && soldeLabel) {
    soldeBtn.addEventListener("click", function () {
      state.soldeSort = state.soldeSort === "desc" ? "asc" : "desc";
      soldeLabel.textContent = state.soldeSort === "desc"
        ? "Solde : plus élevé"
        : "Solde : plus faible";
      sortBySolde();
    });
  }

  if (statusBtn && statusLabel) {
    statusBtn.addEventListener("click", function () {
      var currentIndex = STATUS_CYCLE.indexOf(state.status);
      var nextIndex = (currentIndex + 1) % STATUS_CYCLE.length;
      state.status = STATUS_CYCLE[nextIndex];
      statusLabel.textContent = STATUS_LABELS[state.status];
      statusBtn.classList.toggle("is-active", state.status !== "tous");
      applyFilters();
    });
  }
})();
